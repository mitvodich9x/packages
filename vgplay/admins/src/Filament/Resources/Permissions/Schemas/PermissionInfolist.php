<?php

namespace Vgplay\Admins\Filament\Resources\Permissions\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class PermissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin')
                    ->schema([
                        TextEntry::make('name')->label('Tên quyền'),
                        TextEntry::make('display_name')->label('Tên hiển thị'),
                        TextEntry::make('guard_name'),
                    ])->columns(3)->columnSpanFull(),

                Section::make('Khác')
                    ->schema([
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }
}
