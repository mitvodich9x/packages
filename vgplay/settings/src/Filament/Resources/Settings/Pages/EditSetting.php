<?php

namespace Vgplay\Settings\Filament\Resources\Settings\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Vgplay\Settings\Filament\Resources\Settings\SettingResource;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $title = 'Sửa Cài Đặt';

    protected ?string $heading = 'Sửa cài đặt';

    protected ?string $subheading = 'Sửa cài đặt';

    protected static ?string $breadcrumb = 'Sửa cài đặt';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Sửa cài đặt thành công';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Xem')
                ->icon('heroicon-o-eye'),
            DeleteAction::make()->label('Xoá')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading(fn($record) => 'Xoá cài đặt: ' . ($record->key))
                ->modalDescription('Bạn có chắc chắn muốn xoá? Hành động này không thể hoàn tác.')
                ->modalSubmitActionLabel('Xác nhận xoá')
                ->modalCancelActionLabel('Hủy')
                ->successNotificationTitle('Đã xoá cài đặt'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Lưu')
                ->icon('heroicon-o-document-check')
                ->color('primary'),

            $this->getCancelFormAction()
                ->label('Hủy')
                ->icon('heroicon-o-x-mark')
                ->color('danger'),
        ];
    }
}
