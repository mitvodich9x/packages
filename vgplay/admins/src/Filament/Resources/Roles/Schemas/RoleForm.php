<?php

namespace Vgplay\Admins\Filament\Resources\Roles\Schemas;

use Vgplay\Admins\Models\Permission;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Tên nhóm')
                    ->required()
                    ->maxLength(255),
                TextInput::make('display_name')
                    ->label('Tên hiển thị')
                    ->required()
                    ->maxLength(255),
                Section::make('Phân quyền')
                    ->columns(1)
                    ->schema([
                        CheckboxList::make('permission_groups')
                            ->label('Nhóm quyền')
                            ->options(function () {
                                $guard = config('auth.defaults.guard', 'web');

                                return Permission::query()
                                    ->where('guard_name', $guard)
                                    ->get()
                                    ->groupBy(fn($p) => Str::before($p->name, '.'))
                                    ->keys()
                                    ->mapWithKeys(fn($g) => [$g => Str::headline($g ?: 'Khác')])
                                    ->toArray();
                            })
                            ->columns([
                                'default' => 4,
                            ])
                            ->dehydrated(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $guard = config('auth.defaults.guard', 'web');

                                $allByGroup = Permission::query()
                                    ->where('guard_name', $guard)
                                    ->get()
                                    ->groupBy(fn($p) => Str::before($p->name, '.'));

                                $current        = collect($get('permissions') ?? []);
                                $selectedGroups = collect($state ?? []);

                                $idsInSelected = $selectedGroups
                                    ->flatMap(fn($g) => optional($allByGroup->get($g))->pluck('id') ?? collect());

                                $idsInUnselected = $allByGroup->keys()
                                    ->diff($selectedGroups)
                                    ->flatMap(fn($g) => optional($allByGroup->get($g))->pluck('id') ?? collect());

                                $new = $current->diff($idsInUnselected)->merge($idsInSelected)->unique()->values()->all();

                                $set('permissions', $new);
                            })
                            ->columnSpanFull(),

                        CheckboxList::make('permissions')
                            ->label('Phân quyền')
                            ->relationship('permissions', 'name')
                            ->options(function () {
                                $guard = config('auth.defaults.guard', 'web');
                                return Permission::query()
                                    ->where('guard_name', $guard)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->columns([
                                'default' => 4,
                            ])
                            ->bulkToggleable()
                            ->reactive()
                            ->afterStateHydrated(function (Set $set, Get $get) {
                                $guard = config('auth.defaults.guard', 'web');

                                $selected  = collect($get('permissions') ?? []);
                                $allByGroup = Permission::query()
                                    ->where('guard_name', $guard)
                                    ->get()
                                    ->groupBy(fn($p) => Str::before($p->name, '.'));

                                $fullGroups = $allByGroup
                                    ->filter(fn($perms) => $perms->pluck('id')->every(fn($id) => $selected->contains($id)))
                                    ->keys()
                                    ->values()
                                    ->all();

                                $set('permission_groups', $fullGroups);
                            })
                            ->afterStateUpdated(function ($state, Set $set) {
                                $guard = config('auth.defaults.guard', 'web');

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
                    ])->columnSpanFull()
            ]);
    }
}
