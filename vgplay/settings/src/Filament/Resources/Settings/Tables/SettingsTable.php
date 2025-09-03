<?php

namespace Vgplay\Settings\Filament\Resources\Settings\Tables;

use Carbon\Carbon;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Vgplay\Settings\Models\Setting;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('display_name')
                    ->label('Tên hiển thị'),

                TextColumn::make('value')
                    ->label('Giá trị')
                    ->limit(80)
                    ->formatStateUsing(fn($state) => (string) $state)
                    ->tooltip(
                        fn($state) =>
                        is_string($state) && json_decode($state, true) !== null
                            ? json_encode(json_decode($state, true), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                            : (string) $state
                    ),
                TextColumn::make('group')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_hidden')
                    ->boolean()
                    ->label('Ẩn'),

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
                SelectFilter::make('group')
                    ->label('Nhóm')
                    ->options(fn() => Setting::query()
                        ->whereNotNull('group')
                        ->distinct()
                        ->pluck('group', 'group')
                        ->toArray()),
            ])
            ->recordActions([
                ViewAction::make()->label('Xem'),
                EditAction::make()->label('Sửa'),
                DeleteAction::make()->label('Xoá')->requiresConfirmation()
                    ->modalHeading(fn($record) => 'Xoá key: ' . ($record->key))
                    ->modalDescription('Bạn có chắc chắn muốn xoá? Hành động này không thể hoàn tác.')
                    ->modalSubmitActionLabel('Xác nhận xoá')
                    ->modalCancelActionLabel('Hủy')
                    ->successNotificationTitle('Đã xoá key'),
                Action::make('editValue')
                    ->label('Sửa giá trị')
                    ->icon('heroicon-m-pencil-square')
                    ->fillForm(fn(Setting $record) => [
                        'value' => $record->value ?? '',
                    ])
                    ->form([
                        Textarea::make('value')
                            ->rows(10)
                            ->label('Giá trị')
                            ->required(),
                    ])
                    ->action(function (array $data, Setting $record) {
                        $value = (string) $data['value'];

                        if (json_decode($value, true) !== null) {
                            $value = json_encode(json_decode($value, true), JSON_UNESCAPED_UNICODE);
                        }

                        $record->forceFill(['value' => $value])->save();

                        return null;
                    })
                    ->modalHeading(fn($record) => "Sửa giá trị: {$record->key}")
                    ->modalSubmitActionLabel('Lưu')
                    ->color('primary')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xoá tất cả'),
                ])->label('Nhóm hành động'),
            ]);
    }
}
