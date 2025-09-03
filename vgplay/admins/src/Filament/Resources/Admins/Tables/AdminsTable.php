<?php

namespace Vgplay\Admins\Filament\Resources\Admins\Tables;

use Carbon\Carbon;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

class AdminsTable
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
                    ->label('Tên')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->label('Xác thực email')
                    ->dateTime('d-M-Y')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return null;

                        return Carbon::parse($state)
                            ->timezone('Asia/Ho_Chi_Minh')
                            ->locale('vi')
                            ->translatedFormat('d F Y');
                    })
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Nhóm người dùng')
                    ->formatStateUsing(function ($record) {
                        return $record->roles->pluck('name')->join(', ');
                    })
                    ->searchable()
                    ->sortable(),
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
                DeleteAction::make()->label('Xoá')->requiresConfirmation()
                    ->modalHeading(fn($record) => 'Xoá quản trị viên: ' . ($record->email))
                    ->modalDescription('Bạn có chắc chắn muốn xoá? Hành động này không thể hoàn tác.')
                    ->modalSubmitActionLabel('Xác nhận xoá')
                    ->modalCancelActionLabel('Hủy')
                    ->successNotificationTitle('Đã xoá quản trị viên'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xoá tất cả'),
                ])->label('Nhóm hành động'),
            ]);
    }
}
