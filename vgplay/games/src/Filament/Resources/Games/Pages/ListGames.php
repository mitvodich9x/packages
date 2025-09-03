<?php

namespace Vgplay\Games\Filament\Resources\Games\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Vgplay\Games\Filament\Resources\Games\GameResource;

class ListGames extends ListRecords
{
    protected static string $resource = GameResource::class;

    protected static ?string $title = 'Quản Lý Danh Sách Game';

    protected ?string $heading = 'Danh sách game';

    protected ?string $subheading = 'Hiển thị toàn bộ danh sách game';

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
