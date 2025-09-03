<?php

namespace Vgplay\Admins\Filament\Resources\Permissions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Vgplay\Admins\Filament\Resources\Permissions\PermissionResource;

class ViewPermission extends ViewRecord
{
    protected static string $resource = PermissionResource::class;

    protected static ?string $title = 'Xem Quyền';

    protected ?string $heading = 'Chi tiết quyền';

    protected ?string $subheading = 'Hiển thị toàn bộ thông tin chi tiết quyền';

    protected static ?string $breadcrumb = 'Chi tiết quyền';

    protected static ?string $recordTitleAttribute = 'name';

    protected function getBreadcrumbRecordTitle(): string
    {
        $r = $this->getRecord();
        return 'Quyền: ' . ($r->name ?? $r->display_name ?? ('ID ' . $r->getKey()));
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
