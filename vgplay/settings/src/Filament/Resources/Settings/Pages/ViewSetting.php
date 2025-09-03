<?php

namespace Vgplay\Settings\Filament\Resources\Settings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Vgplay\Settings\Filament\Resources\Settings\SettingResource;

class ViewSetting extends ViewRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $title = 'Xem Quản Trị Viên';

    protected ?string $heading = 'Chi tiết quản trị viên';

    protected ?string $subheading = 'Hiển thị toàn bộ thông tin chi tiết quản trị viên';

    protected static ?string $breadcrumb = 'Chi tiết quản trị viên';

    protected function getBreadcrumbRecordTitle(): string
    {
        $r = $this->getRecord();
        return 'Tài khoản: ' . ($r->name ?? $r->email ?? ('ID ' . $r->getKey()));
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
