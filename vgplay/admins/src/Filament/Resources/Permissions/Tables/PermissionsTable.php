<?php

namespace Vgplay\Admins\Filament\Resources\Permissions\Tables;

use Carbon\Carbon;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Vgplay\Admins\Models\Permission;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('STT')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Tên quyền')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('display_name')
                    ->label('Tên hiển thị')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d-M-Y')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return null;

                        return Carbon::parse($state)
                            ->timezone('Asia/Ho_Chi_Minh')
                            ->locale('vi')
                            ->translatedFormat('d F Y');
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d-M-Y')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return null;

                        return Carbon::parse($state)
                            ->timezone('Asia/Ho_Chi_Minh')
                            ->locale('vi')
                            ->translatedFormat('d F Y');
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('Xem'),
                EditAction::make()->label('Sửa'),
                DeleteAction::make()
                    ->label('Xoá')
                    ->requiresConfirmation()
                    ->modalHeading(fn(Permission $record) => 'Xoá quyền: ' . ($record->display_name ?? $record->name))
                    ->modalDescription('Bạn có chắc chắn muốn xoá? Hành động này không thể hoàn tác.')
                    ->modalSubmitActionLabel('Xác nhận xoá')
                    ->modalCancelActionLabel('Huỷ')
                    ->successNotificationTitle('Đã xoá quyền'),    
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xoá tất cả'),
                ])->label('Nhóm hành động'),
            ]);
    }
}
