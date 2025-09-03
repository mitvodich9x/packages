<?php

namespace Vgplay\Settings\Filament\Resources\Settings\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;

class SettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin chính')
                    ->schema([
                        TextEntry::make('key')
                            ->label('Key')
                            ->copyable()
                            ->badge()
                            ->weight('bold'),

                        TextEntry::make('display_name')
                            ->label('Tên hiển thị'),

                        TextEntry::make('data_type')
                            ->label('Kiểu dữ liệu')
                            ->badge(),


                        KeyValueEntry::make('value')
                            ->label('Giá trị')
                            ->keyLabel('Trường')
                            ->valueLabel('Nội dung')
                            ->columnSpanFull()
                            ->state(function ($record) {
                                if (is_string($record->value) && json_decode($record->value, true) !== null) {
                                    return json_decode($record->value, true);
                                }
                                return [];
                            })
                    ])
                    ->columns(2),

                Section::make('Chi tiết khác')
                    ->schema([
                        TextEntry::make('order')
                            ->numeric()
                            ->label('Thứ tự'),

                        TextEntry::make('group')
                            ->label('Nhóm'),

                        TextEntry::make('type')
                            ->label('Type'),

                        IconEntry::make('is_hidden')
                            ->label('Ẩn?')
                            ->boolean()
                    ])
                    ->columns(2),

                Section::make('Thời gian')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Cập nhật lúc')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
