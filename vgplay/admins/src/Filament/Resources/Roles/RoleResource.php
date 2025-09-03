<?php

namespace Vgplay\Admins\Filament\Resources\Roles;

use Vgplay\Admins\Models\Role;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Vgplay\Admins\Filament\Resources\Roles\Pages\CreateRole;
use Vgplay\Admins\Filament\Resources\Roles\Pages\EditRole;
use Vgplay\Admins\Filament\Resources\Roles\Pages\ListRoles;
use Vgplay\Admins\Filament\Resources\Roles\Pages\ViewRole;
use Vgplay\Admins\Filament\Resources\Roles\Schemas\RoleForm;
use Vgplay\Admins\Filament\Resources\Roles\Schemas\RoleInfolist;
use Vgplay\Admins\Filament\Resources\Roles\Tables\RolesTable;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string | UnitEnum | null $navigationGroup = 'Tài khoản quản trị viên';

    protected static ?string $navigationLabel = 'Danh sách nhóm quản trị viên';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $breadcrumb = 'Nhóm quản trị viên';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $navigationBadgeTooltip = 'Tổng số nhóm quản trị viên';

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
