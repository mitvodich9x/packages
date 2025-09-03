<?php

namespace Vgplay\Games\Filament\Resources\Games;

use Vgplay\Games\Filament\Resources\Games\Pages\CreateGame;
use Vgplay\Games\Filament\Resources\Games\Pages\EditGame;
use Vgplay\Games\Filament\Resources\Games\Pages\ListGames;
use Vgplay\Games\Filament\Resources\Games\Pages\ViewGame;
use Vgplay\Games\Filament\Resources\Games\Schemas\GameForm;
use Vgplay\Games\Filament\Resources\Games\Schemas\GameInfolist;
use Vgplay\Games\Filament\Resources\Games\Tables\GamesTable;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Vgplay\Games\Models\Game;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | UnitEnum | null $navigationGroup = 'Quản lý danh sách game';

    protected static ?string $navigationLabel = 'Danh sách game';

    protected static ?string $breadcrumb = 'Game';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $navigationBadgeTooltip = 'Tổng số game';
    
    public static function form(Schema $schema): Schema
    {
        return GameForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GameInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GamesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGames::route('/'),
            'create' => CreateGame::route('/create'),
            'view' => ViewGame::route('/{record}'),
            'edit' => EditGame::route('/{record}/edit'),
        ];
    }
}
