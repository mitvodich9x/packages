<?php

namespace Vgplay\Admins\Filament\Resources\Roles\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Vgplay\Admins\Filament\Resources\Roles\RoleResource;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected static ?string $title = 'Nhóm Quản Trị Viên';

    protected ?string $heading = 'Danh nhóm sách quản trị viên';

    protected ?string $subheading = 'Hiển thị toàn bộ danh sách nhóm quản trị viên';

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
