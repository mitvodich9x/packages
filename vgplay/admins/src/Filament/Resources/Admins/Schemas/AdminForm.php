<?php

namespace Vgplay\Admins\Filament\Resources\Admins\Schemas;

use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Vgplay\Admins\Models\Permission;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Vgplay\Admins\Filament\Resources\Admins\Pages\CreateAdmin;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Tên admin')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Mật khẩu')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(Page $livewire) => ($livewire instanceof CreateAdmin))
                    ->maxLength(255),
                Select::make('roles')
                    ->label('Nhóm người dùng')
                    ->multiple()
                    ->relationship(titleAttribute: 'name')
                    ->preload(),
                Section::make('Phân quyền')
                    ->schema([
                        Hidden::make('inherited_permission_ids')->dehydrated(false),

                        CheckboxList::make('permission_groups')
                            ->label('Nhóm quyền')
                            ->options(function () {
                                $guard = config('auth.defaults.guard', 'admin');

                                return Permission::query()
                                    ->where('guard_name', $guard)
                                    ->get()
                                    ->groupBy(fn($p) => Str::before($p->name, '.'))
                                    ->keys()
                                    ->mapWithKeys(fn($g) => [$g => Str::headline($g ?: 'Khác')])
                                    ->toArray();
                            })
                            ->columns(5)
                            ->dehydrated(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $guard = config('auth.defaults.guard', 'admin');

                                $allByGroup = Permission::query()
                                    ->where('guard_name', $guard)
                                    ->get()
                                    ->groupBy(fn($p) => Str::before($p->name, '.'));

                                $current         = collect($get('permissions') ?? []);
                                $selectedGroups  = collect($state ?? []);

                                $idsInSelected   = $selectedGroups->flatMap(
                                    fn($g) => optional($allByGroup->get($g))->pluck('id') ?? collect()
                                );

                                $idsInUnselected = $allByGroup->keys()->diff($selectedGroups)->flatMap(
                                    fn($g) => optional($allByGroup->get($g))->pluck('id') ?? collect()
                                );

                                $new = $current->diff($idsInUnselected)->merge($idsInSelected)->unique()->values()->all();
                                $set('permissions', $new);
                            })
                            ->columnSpanFull(),

                        CheckboxList::make('permissions')
                            ->label('Phân quyền')
                            ->options(function () {
                                $guard = config('auth.defaults.guard', 'admin');

                                return Permission::query()
                                    ->where('guard_name', $guard)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->columns(5)
                            ->bulkToggleable()
                            ->reactive()

                            ->afterStateHydrated(function (Set $set, Get $get, ?EloquentModel $record) {
                                if (! $record) return;

                                $guard       = config('auth.defaults.guard', 'admin');
                                $directIds   = $record->permissions()->pluck('id');
                                $effectiveIds = $record->getAllPermissions()->where('guard_name', $guard)->pluck('id');
                                $inherited   = $effectiveIds->diff($directIds)->values();

                                $set('permissions', $effectiveIds->values()->all());
                                $set('inherited_permission_ids', $inherited->all());

                                $allByGroup = Permission::query()
                                    ->where('guard_name', $guard)
                                    ->get()
                                    ->groupBy(fn($p) => Str::before($p->name, '.'));

                                $fullGroups = $allByGroup
                                    ->filter(fn($perms) => $perms->pluck('id')->every(fn($id) => $effectiveIds->contains($id)))
                                    ->keys()
                                    ->values()
                                    ->all();

                                $set('permission_groups', $fullGroups);
                            })

                            ->afterStateUpdated(function ($state, Set $set) {
                                $guard = config('auth.defaults.guard', 'admin');

                                $selected  = collect($state ?? []);
                                $allByGroup = Permission::query()
                                    ->where('guard_name', $guard)
                                    ->get()
                                    ->groupBy(fn($p) => Str::before($p->name, '.'));

                                $fullGroups = $allByGroup
                                    ->filter(function ($perms) use ($selected) {
                                        $ids = $perms->pluck('id');
                                        return $ids->count() > 0 && $ids->every(fn($id) => $selected->contains($id));
                                    })
                                    ->keys()
                                    ->values()
                                    ->all();

                                $set('permission_groups', $fullGroups);
                            })
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
