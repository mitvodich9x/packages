<?php

namespace Vgplay\Settings\Filament\Resources\Settings;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Vgplay\Settings\Models\Setting;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Vgplay\Settings\Filament\Resources\Settings\Pages\EditSetting;
use Vgplay\Settings\Filament\Resources\Settings\Pages\ViewSetting;
use Vgplay\Settings\Filament\Resources\Settings\Pages\ListSettings;
use Vgplay\Settings\Filament\Resources\Settings\Pages\CreateSetting;
use Vgplay\Settings\Filament\Resources\Settings\Schemas\SettingForm;
use Vgplay\Settings\Filament\Resources\Settings\Tables\SettingsTable;
use Vgplay\Settings\Filament\Resources\Settings\Schemas\SettingInfolist;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog8Tooth;

    protected static string | UnitEnum | null $navigationGroup = 'Quản lý cài đặt cấu hình';

    protected static ?string $navigationLabel = 'Danh sách cài đặt';
    
    protected static ?string $recordTitleAttribute = 'key';

    protected static ?string $breadcrumb = 'Cài đặt';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $navigationBadgeTooltip = 'Tổng số cài đặt';

    public static function form(Schema $schema): Schema
    {
        return SettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
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
            'index' => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'view' => ViewSetting::route('/{record}'),
            'edit' => EditSetting::route('/{record}/edit'),
        ];
    }
}
