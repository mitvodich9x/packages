<?php

namespace Vgplay\Games\Filament\Resources\Games\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class GamesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('game_id')->searchable()->sortable(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('alias')->searchable()->sortable(),
            ImageColumn::make('banner')->disk('ftp'),
            ImageColumn::make('favicon')->disk('ftp'),
            ImageColumn::make('icon')->disk('ftp')->toggleable(isToggledHiddenByDefault: true),
            ImageColumn::make('logo')->disk('ftp')->toggleable(isToggledHiddenByDefault: true),
            ImageColumn::make('thumb')->disk('ftp')->toggleable(isToggledHiddenByDefault: true),
            ImageColumn::make('bg_detail')->disk('ftp')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('created_at')->dateTime('d-M-Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')->dateTime('d-M-Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])
            ->actions([
                ViewAction::make()->label('Xem'),
                EditAction::make()->label('Sửa'),
                DeleteAction::make()->label('Xoá'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
