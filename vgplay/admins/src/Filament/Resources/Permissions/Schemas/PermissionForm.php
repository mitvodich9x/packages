<?php

namespace Vgplay\Admins\Filament\Resources\Permissions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Tên quyền')
                    ->required()
                    ->maxLength(255),
                TextInput::make('display_name')
                    ->label('Tên hiển thị')
                    ->required()
                    ->maxLength(255),
                TextInput::make('guard_name')
                    ->label('Guard')
                    ->default('admin')
                    ->disabled()
                    ->maxLength(255),
            ]);
    }
}
