<?php

namespace Vgplay\Games\Filament\Resources\Games\Pages;

use Vgplay\Games\Filament\Resources\Games\GameResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGame extends CreateRecord
{
    protected static string $resource = GameResource::class;

    protected static ?string $title = 'Thêm Game';

    protected ?string $heading = 'Thêm mới game';

    protected ?string $subheading = 'Thêm mới game';

    protected static ?string $breadcrumb = 'Thêm mới';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Thêm mới game thành công';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Tạo mới')
                ->icon('heroicon-o-check-circle')
                ->color('primary'),

            $this->getCreateAnotherFormAction()
                ->label('Tạo & tạo tiếp')
                ->color('info'),

            $this->getCancelFormAction()
                ->label('Hủy')
                ->icon('heroicon-o-x-mark')
                ->color('danger'),
        ];
    }
}
