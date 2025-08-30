<?php

namespace Vgplay\Admins\Filament\Resources\Admins\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Vgplay\Admins\Filament\Resources\Admins\AdminResource;

class ListAdmins extends ListRecords
{
    protected static string $resource = AdminResource::class;

    protected static ?string $title = 'Tài Khoản Quản Trị Viên';

    protected ?string $heading = 'Danh sách quản trị viên';

    protected ?string $subheading = 'Hiển thị toàn bộ danh sách quản trị viên';

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
