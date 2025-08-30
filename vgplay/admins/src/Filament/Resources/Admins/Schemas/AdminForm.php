<?php

namespace Vgplay\Admins\Filament\Resources\Admins\Schemas;

use Filament\Schemas\Schema;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                Select::make('permissions')
                    ->label('Phân quyền')
                    ->multiple()
                    ->relationship(titleAttribute: 'name')
                    ->preload()
            ]);
    }
}
