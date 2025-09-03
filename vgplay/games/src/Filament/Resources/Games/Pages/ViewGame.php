<?php

namespace Vgplay\Games\Filament\Resources\Games\Pages;

use Vgplay\Games\Filament\Resources\Games\GameResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGame extends ViewRecord
{
    protected static string $resource = GameResource::class;

    protected static ?string $title = 'Xem Game';

    protected ?string $heading = 'Chi tiết game';

    protected ?string $subheading = 'Hiển thị toàn bộ thông tin chi tiết game';

    protected static ?string $breadcrumb = 'Chi tiết game';

    protected function getBreadcrumbRecordTitle(): string
    {
        $r = $this->getRecord();
        return 'Game: ' . ($r->name ?? $r->game_id ?? ('ID ' . $r->getKey()));
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
