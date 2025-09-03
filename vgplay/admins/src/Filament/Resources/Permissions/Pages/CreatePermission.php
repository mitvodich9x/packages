<?php

namespace Vgplay\Admins\Filament\Resources\Permissions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Vgplay\Admins\Filament\Resources\Permissions\PermissionResource;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

     protected static ?string $title = 'Thêm Quyền';

    protected ?string $heading = 'Thêm mới quyền';

    protected ?string $subheading = 'Thêm mới quyền';

    protected static ?string $breadcrumb = 'Thêm mới';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Thêm mới quyền thành công';
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
