<?php

namespace Vgplay\Settings\Filament\Resources\Settings\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Vgplay\Settings\Filament\Resources\Settings\SettingResource;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    protected static ?string $title = 'Cài Đặt Cấu Hình';

    protected ?string $heading = 'Danh sách cài đặt';

    protected ?string $subheading = 'Hiển thị toàn bộ danh sách cài đặt';

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
