<?php

namespace Vgplay\Admins\Filament\Resources\Roles\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Vgplay\Admins\Filament\Resources\Roles\RoleResource;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected static ?string $title = 'Xem Nhóm Quản Trị Viên';

    protected ?string $heading = 'Chi tiết nhóm quản trị viên';

    protected ?string $subheading = 'Hiển thị toàn bộ thông tin chi tiết nhóm quản trị viên';

    protected static ?string $breadcrumb = 'Chi tiết nhóm quản trị viên';

    protected function getBreadcrumbRecordTitle(): string
    {
        $r = $this->getRecord();
        return 'Nhóm: ' . ($r->name ?? $r->name ?? ('ID ' . $r->getKey()));
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Sửa')
                ->icon('heroicon-o-pencil')
                ->color('primary'),
        ];
    }
}
