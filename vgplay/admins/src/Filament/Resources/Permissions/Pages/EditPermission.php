<?php

namespace Vgplay\Admins\Filament\Resources\Permissions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Vgplay\Admins\Filament\Resources\Permissions\PermissionResource;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    protected static ?string $title = 'Sửa Quyền';

    protected ?string $heading = 'Sửa quyền';

    protected ?string $subheading = 'Sửa quyền';

    protected static ?string $breadcrumb = 'Sửa quyền';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Sửa quyền thành công';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Xem')
                ->icon('heroicon-o-eye'),
            DeleteAction::make()->label('Xoá')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading(fn($record) => 'Xoá quyền: ' . ($record->display_name))
                ->modalDescription('Bạn có chắc chắn muốn xoá? Hành động này không thể hoàn tác.')
                ->modalSubmitActionLabel('Xác nhận xoá')
                ->modalCancelActionLabel('Hủy')
                ->successNotificationTitle('Đã xoá quyền'),
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
