<?php

namespace Vgplay\Settings\Filament\Resources\Settings\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')->required()->disabled(fn($record) => $record !== null),
                TextInput::make('display_name')->label('Tên hiển thị'),
                TextInput::make('order')->numeric(),
                Textarea::make('value')
                    ->rows(10)
                    ->label('Giá trị')
                    ->required(),
                Select::make('group')
                    ->label('Nhóm')
                    ->options([
                        'site' => 'Site',
                        'payment' => 'Cấu hình trang nạp',
                    ])
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('type')
                    ->label('Phân loại')
                    ->options([
                        'Access Control' => 'Cấu hình hệ thống',
                        'General'        => 'Cấu hình chung',
                        'Event'         => 'Cấu hình Event',
                        'Game'         => 'Cấu hình trang chủ game',
                        'Home'         => 'Cấu hình trang chủ vgplay',
                        'Payment'        => 'Cấu hình trang nạp',
                    ])
                    ->searchable()
                    ->preload()
                    ->required(),
                Toggle::make('is_hidden')->label('Ẩn cấu hình'),
            ]);
    }
}
