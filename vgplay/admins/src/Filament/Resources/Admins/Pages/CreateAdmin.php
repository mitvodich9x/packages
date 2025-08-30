<?php

namespace Vgplay\Admins\Filament\Resources\Admins\Pages;

use Filament\Resources\Pages\CreateRecord;
use Vgplay\Admins\Filament\Resources\Admins\AdminResource;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    protected static ?string $title = 'Thêm Quản Trị Viên';

    protected ?string $heading = 'Thêm mới quản trị viên';

    protected ?string $subheading = 'Thêm mới quản trị viên';

    protected static ?string $breadcrumb = 'Thêm mới';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Thêm mới quản trị viên thành công';
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
