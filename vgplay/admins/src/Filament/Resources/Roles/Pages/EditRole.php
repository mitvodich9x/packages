<?php

namespace Vgplay\Admins\Filament\Resources\Roles\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Vgplay\Admins\Filament\Resources\Roles\RoleResource;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected static ?string $title = 'Sửa Nhóm Quản Trị Viên';

    protected ?string $heading = 'Sửa nhóm quản trị viên';

    protected ?string $subheading = 'Sửa nhóm quản trị viên';

    protected static ?string $breadcrumb = 'Sửa nhóm quản trị viên';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Sửa nhóm quản trị viên thành công';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Xem')
                ->icon('heroicon-o-eye'),
            DeleteAction::make()->label('Xoá')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading(fn($record) => 'Xoá nhóm quản trị viên: ' . ($record->email))
                ->modalDescription('Bạn có chắc chắn muốn xoá? Hành động này không thể hoàn tác.')
                ->modalSubmitActionLabel('Xác nhận xoá')
                ->modalCancelActionLabel('Hủy')
                ->successNotificationTitle('Đã xoá nhóm quản trị viên'),
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
    
    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $selectedRoleIds = collect($data['roles'] ?? [])->map(fn($v) => (int) $v)->all();

    //     $selectedPermissionIds = collect($data['permissions'] ?? [])->map(fn($v) => (int) $v)->all();

    //     $inheritedAtHydrate = collect($data['inherited_permission_ids'] ?? [])->map(fn($v) => (int) $v);

    //     if (! empty($selectedRoleIds)) {
    //         $roleInheritedNow = Role::query()
    //             ->whereKey($selectedRoleIds)
    //             ->with('permissions:id')
    //             ->get()
    //             ->flatMap(fn($r) => $r->permissions->pluck('id'))
    //             ->unique();

    //         $inherited = $roleInheritedNow->isNotEmpty() ? $roleInheritedNow : $inheritedAtHydrate;
    //     } else {
    //         $inherited = $inheritedAtHydrate;
    //     }

    //     $directToSync = collect($selectedPermissionIds)->diff($inherited)->values()->all();

    //     $data['permissions'] = $directToSync;

    //     unset($data['inherited_permission_ids']);

    //     return $data;
    // }
}
