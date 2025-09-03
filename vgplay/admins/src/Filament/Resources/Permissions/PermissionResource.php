<?php

namespace Vgplay\Admins\Filament\Resources\Permissions;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Vgplay\Admins\Models\Permission;
use Vgplay\Admins\Filament\Resources\Permissions\Pages\EditPermission;
use Vgplay\Admins\Filament\Resources\Permissions\Pages\ViewPermission;
use Vgplay\Admins\Filament\Resources\Permissions\Pages\ListPermissions;
use Vgplay\Admins\Filament\Resources\Permissions\Pages\CreatePermission;
use Vgplay\Admins\Filament\Resources\Permissions\Schemas\PermissionForm;
use Vgplay\Admins\Filament\Resources\Permissions\Tables\PermissionsTable;
use Vgplay\Admins\Filament\Resources\Permissions\Schemas\PermissionInfolist;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $recordTitleAttribute = 'display_name';

     protected static string | UnitEnum | null $navigationGroup = 'Tài khoản quản trị viên';

    protected static ?string $navigationLabel = 'Danh sách phân quyền';
    
    protected static ?string $breadcrumb = 'Phân quyền';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $navigationBadgeTooltip = 'Tổng số quyền';

    public static function form(Schema $schema): Schema
    {
        return PermissionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PermissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionsTable::configure($table);
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
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'view' => ViewPermission::route('/{record}'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }
}
