<?php

namespace Vgplay\Games\Filament\Resources\Games\Pages;

use Vgplay\Games\Filament\Resources\Games\GameResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGame extends EditRecord
{
    protected static string $resource = GameResource::class;

    protected static ?string $title = 'Sửa Game';

    protected ?string $heading = 'Sửa game';

    protected ?string $subheading = 'Sửa game';

    protected static ?string $breadcrumb = 'Sửa game';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Sửa game thành công';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Xem')
                ->icon('heroicon-o-eye'),
            DeleteAction::make()->label('Xoá')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading(fn($record) => 'Xoá game: ' . ($record->email))
                ->modalDescription('Bạn có chắc chắn muốn xoá? Hành động này không thể hoàn tác.')
                ->modalSubmitActionLabel('Xác nhận xoá')
                ->modalCancelActionLabel('Hủy')
                ->successNotificationTitle('Đã xoá game'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Lưu')
                ->icon('heroicon-o-document-check')
                ->color('primary'),

            $this->getCancelFormAction()
                ->label('Hủy')
                ->icon('heroicon-o-x-mark')
                ->color('danger'),
        ];
    }
}
