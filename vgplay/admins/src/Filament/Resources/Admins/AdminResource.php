<?php

namespace Vgplay\Admins\Filament\Resources\Admins;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Vgplay\Admins\Models\Admin;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Vgplay\Admins\Filament\Resources\Admins\Pages\EditAdmin;
use Vgplay\Admins\Filament\Resources\Admins\Pages\ViewAdmin;
use Vgplay\Admins\Filament\Resources\Admins\Pages\ListAdmins;
use Vgplay\Admins\Filament\Resources\Admins\Pages\CreateAdmin;
use Vgplay\Admins\Filament\Resources\Admins\Schemas\AdminForm;
use Vgplay\Admins\Filament\Resources\Admins\Tables\AdminsTable;
use Vgplay\Admins\Filament\Resources\Admins\Schemas\AdminInfolist;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static string | UnitEnum | null $navigationGroup = 'Tài khoản quản trị viên';

    protected static ?string $navigationLabel = 'Danh sách quản trị viên';
    
    protected static ?string $recordTitleAttribute = 'Admin';

    protected static ?string $breadcrumb = 'Quản trị viên';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public function getTitle(): string | Htmlable
    {
        return __('Custom Page Title');
    }

    protected static ?string $navigationBadgeTooltip = 'Tổng số quản trị viên';

    public static function form(Schema $schema): Schema
    {
        return AdminForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AdminInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['roles', 'permissions']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdmins::route('/'),
            'create' => CreateAdmin::route('/create'),
            'view' => ViewAdmin::route('/{record}'),
            'edit' => EditAdmin::route('/{record}/edit'),
        ];
    }
}
