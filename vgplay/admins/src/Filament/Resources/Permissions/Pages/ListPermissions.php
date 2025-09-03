<?php

namespace Vgplay\Admins\Filament\Resources\Permissions\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Vgplay\Admins\Filament\Resources\Permissions\PermissionResource;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    protected static ?string $title = 'Phân Quyền';

    protected ?string $heading = 'Danh sách quyền';

    protected ?string $subheading = 'Hiển thị toàn bộ danh sách quyền';

    protected static ?string $breadcrumb = 'Danh sách';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Thêm mới')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->createAnotherAction(
                    fn(Action $action): Action =>
                    $action
                        ->label('Tạo & tạo tiếp')
                        ->icon('heroicon-o-plus-circle')
                ),
        ];
    }
}
