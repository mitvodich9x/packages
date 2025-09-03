<?php

namespace Vgplay\Admins\Filament\Resources\Roles\Schemas;

use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Vgplay\Admins\Models\Permission;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Facades\Schema as DbSchema;

class RoleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin')
                    ->schema([
                        TextEntry::make('name')->label('Tên nhóm'),
                        TextEntry::make('display_name')->label('Tên hiển thị'),
                    ])->columns(3),
                Section::make('Khác')
                    ->schema([
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),
                    ])->columns(2),
                Section::make('Chi tiết quyền theo nhóm')
                    ->collapsible()
                    ->collapsed()
                    ->schema([

                        RepeatableEntry::make('permissions_grouped')
                            ->label(false)
                            ->state(function ($record) {
                                // Lấy tên bảng từ config để không lệ thuộc tên cứng
                                $tables = config('permission.table_names', []);
                                $pivotTable = $tables['role_has_permissions'] ?? 'role_has_permissions';
                                $permTable  = $tables['permissions']          ?? 'permissions';

                                // Guard (nếu bạn dùng multi-guard)
                                $guard = $record->guard_name ?? config('auth.defaults.guard', 'web');

                                // Lấy tất cả permission name GẮN CHO ROLE hiện tại thông qua pivot
                                $names = Permission::query()
                                    ->join($pivotTable, "{$permTable}.id", '=', "{$pivotTable}.permission_id")
                                    ->where("{$pivotTable}.role_id", $record->getKey())
                                    ->when(
                                        DbSchema::hasColumn($permTable, 'guard_name'),
                                        fn($q) =>
                                        $q->where("{$permTable}.guard_name", $guard)
                                    )
                                    ->orderBy("{$permTable}.name")
                                    ->pluck("{$permTable}.name");

                                // Gom theo nhóm.action (prefix trước dấu '.')
                                return collect($names)
                                    ->map(function (string $p) {
                                        [$group, $action] = array_pad(explode('.', $p, 2), 2, null);
                                        $group  = $group ?: 'other';
                                        $action = $action ?: $group;
                                        return ['group' => $group, 'action' => $action];
                                    })
                                    ->groupBy('group')
                                    ->map(function ($items, $group) {
                                        $actions = collect($items)->pluck('action')->unique()->sort()->values()->all();

                                        return [
                                            'group' => $group,
                                            'line'  => implode(' · ', $actions),
                                        ];
                                    })
                                    ->sortKeys()
                                    ->values()
                                    ->all();
                            })
                            ->grid(4)
                            ->schema([
                                TextEntry::make('group')
                                    ->label('Nhóm')
                                    ->badge()
                                    ->icon('heroicon-o-folder')
                                    ->weight('bold')
                                    ->extraAttributes(['class' => 'whitespace-nowrap']),

                                TextEntry::make('line')
                                    ->label('Quyền')
                                    ->wrap()
                                    ->extraAttributes(['class' => 'text-sm']),
                            ])
                            ->columns(1),
                    ])->columnSpanFull(),
            ]);
    }
}
