<?php

namespace Vgplay\Admins\Filament\Resources\Admins\Schemas;

use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class AdminInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')->label('Email address'),
                        TextEntry::make('email_verified_at')->dateTime(),
                    ])->columns(3),

                Section::make('Khác')
                    ->schema([
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),
                    ])->columns(2),

                Section::make('Phân quyền (tóm tắt)')
                    ->schema([
                        TextEntry::make('permission_groups_compact')
                            ->label('Nhóm quyền')
                            ->state(function ($record) {
                                $counts = collect($record->getAllPermissions()->pluck('name')->all())
                                    ->map(function (string $p) {
                                        $group = Str::before($p, '.') ?: 'other';
                                        return $group;
                                    })
                                    ->countBy()           
                                    ->sortKeys();

                                return $counts->map(fn($c, $g) => "{$g} ({$c})")->values()->implode(', ');
                            })
                            ->wrap()         
                            ->hint('Mở “Chi tiết quyền” bên dưới để xem đầy đủ.')
                            ->columnSpanFull(),
                    ])->columns(1)->columnSpanFull(),

                Section::make('Chi tiết quyền theo nhóm')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        RepeatableEntry::make('permissions_grouped')
                            ->label(false)
                            ->state(function ($record) {
                                $perms = $record->getAllPermissions()->pluck('name')->all();

                                return collect($perms)
                                    ->map(function (string $p) {
                                        [$group, $action] = array_pad(explode('.', $p, 2), 2, null);
                                        $group  = $group ?: 'other';
                                        $action = $action ?: $group;
                                        return ['group' => $group, 'action' => $action];
                                    })
                                    ->groupBy('group')
                                    ->map(function ($items, $group) {
                                        $actions = collect($items)
                                            ->pluck('action')->unique()->sort()->values()->all();

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
