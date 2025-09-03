<?php

namespace Vgplay\Admins\Filament\Resources\Roles\Tables;

use Carbon\Carbon;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Vgplay\Admins\Models\Role;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\HtmlString;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;                            
use Filament\Actions\BulkActionGroup;       

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('STT')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Tên nhóm')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('display_name')
                    ->label('Tên hiển thị')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('permissions_summary')
                    ->label('Quyền')
                    ->getStateUsing(function (Role $record) {
                        $names   = $record->permissions()->pluck('name');          // hoặc $record->permissions->pluck('name')
                        $groups  = $names->map(fn($p) => Str::before($p, '.') ?: 'other')->unique();
                        return "{$groups->count()} nhóm · {$names->count()} quyền";
                    })
                    ->badge()
                    ->color('info')
                    ->tooltip('Bấm "Xem quyền" để mở danh sách quyền theo nhóm')
                    ->sortable(false)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d-M-Y')
                    ->formatStateUsing(function ($state) {
                        if (! $state) return null;

                        return Carbon::parse($state)
                            ->timezone('Asia/Ho_Chi_Minh')
                            ->locale('vi')
                            ->translatedFormat('d F Y');
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d-M-Y')
                    ->formatStateUsing(function ($state) {
                        if (! $state) return null;

                        return Carbon::parse($state)
                            ->timezone('Asia/Ho_Chi_Minh')
                            ->locale('vi')
                            ->translatedFormat('d F Y');
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->modifyQueryUsing(fn($query) => $query->with('permissions:id,name,guard_name'))
            ->filters([
                //
            ])
            ->recordUrl(null)
            ->recordActions([
                Action::make('showPerms')
                    ->label('Xem quyền')
                    ->modalHeading(fn(Role $record) => 'Quyền của vai trò: ' . ($record->display_name ?? $record->name))
                    ->modalWidth('xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng')
                    ->modalContent(function (Role $record) {
                        $lines = $record->permissions->pluck('name')
                            ->map(function (string $p) {
                                [$g, $a] = array_pad(explode('.', $p, 2), 2, null);
                                $g = $g ?: 'other';
                                $a = $a ?: $g;
                                return ['group' => $g, 'action' => $a];
                            })
                            ->groupBy('group')
                            ->map(function ($items, $g) {
                                $actions = collect($items)->pluck('action')->unique()->sort()->values()->all();
                                return '<div class="mb-2"><span class="font-semibold">' . e(Str::headline($g)) .
                                    ':</span> ' . e(implode(' · ', $actions)) . '</div>';
                            })
                            ->sortKeys()
                            ->values()
                            ->implode('');

                        return new HtmlString('<div class="space-y-1">' . $lines . '</div>');
                    }),
                ViewAction::make()->label('Xem'),
                EditAction::make()->label('Sửa'),
                DeleteAction::make()
                    ->label('Xoá')
                    ->requiresConfirmation()
                    ->modalHeading(fn(Role $record) => 'Xoá nhóm quyền: ' . ($record->display_name ?? $record->name))
                    ->modalDescription('Bạn có chắc chắn muốn xoá? Hành động này không thể hoàn tác.')
                    ->modalSubmitActionLabel('Xác nhận xoá')
                    ->modalCancelActionLabel('Huỷ')
                    ->successNotificationTitle('Đã xoá nhóm quyền'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xoá tất cả'),
                ])->label('Nhóm hành động'),
            ]);
    }
}
