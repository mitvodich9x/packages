<?php

namespace Vgplay\Settings\Filament\Resources\Settings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Vgplay\Settings\Filament\Resources\Settings\SettingResource;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $title = 'Thêm Cài Đặt Cấu Hình';

    protected ?string $heading = 'Thêm mới cài đặt cấu hình';

    protected ?string $subheading = 'Thêm mới cài đặt cấu hình';

    protected static ?string $breadcrumb = 'Thêm mới';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Thêm mới cài đặt cấu hình thành công';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Tạo mới')
                ->icon('heroicon-o-check-circle')
                ->color('primary'),

            $this->getCreateAnotherFormAction()
                ->label('Tạo & tạo tiếp')
                ->color('info'),

            $this->getCancelFormAction()
                ->label('Hủy')
                ->icon('heroicon-o-x-mark')
                ->color('danger'),
        ];
    }
}
