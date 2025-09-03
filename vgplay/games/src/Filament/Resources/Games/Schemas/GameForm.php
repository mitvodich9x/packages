<?php

namespace Vgplay\Games\Filament\Resources\Games\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\CheckboxList;

class GameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin game')
                ->schema([
                    TextInput::make('game_id')->label('Định danh game')->required()->unique(ignoreRecord: true),
                    TextInput::make('name')->label('Tên game')->required(),
                    TextInput::make('alias')->label('Bí danh game')->required(),
                ])->columns(3)->columnSpanFull(),

            Section::make('Quản lý hình ảnh game')
                ->schema([
                    FileUpload::make('banner')
                        ->label('Banner game')
                        ->disk('ftp')
                        ->visibility('public')
                        ->directory('images')
                        ->acceptedFileTypes(['image/*'])
                        ->image()
                        ->previewable(false)
                        ->multiple(false)
                        ->preserveFilenames()
                        ->nullable()
                        ->dehydrated(fn($state) => filled($state))
                        ->default(fn($record) => $record?->banner),
                    Placeholder::make('banner_preview')
                        ->label('Banner hiện tại')
                        ->content(
                            fn($record) =>
                            $record?->banner
                                ? '<img src="' . Storage::disk('ftp')->url($record->banner) . '" class="h-16 rounded-md border">'
                                : 'Chưa có ảnh'
                        )->html(),

                    FileUpload::make('favicon')
                        ->disk('ftp')
                        ->visibility('public')
                        ->directory('images')
                        ->acceptedFileTypes(['image/*'])
                        ->image()
                        ->previewable(false)
                        ->multiple(false)
                        ->preserveFilenames()
                        ->nullable()
                        ->dehydrated(fn($state) => filled($state))
                        ->default(fn($record) => $record?->favicon),
                    Placeholder::make('favicon_preview')
                        ->label('Favicon hiện tại')
                        ->content(
                            fn($record) =>
                            $record?->favicon
                                ? '<img src="' . Storage::disk('ftp')->url($record->favicon) . '" class="h-16 rounded-md border">'
                                : 'Chưa có ảnh'
                        )->html(),

                    FileUpload::make('icon')
                        ->disk('ftp')
                        ->visibility('public')
                        ->directory('images')
                        ->acceptedFileTypes(['image/*'])
                        ->image()
                        ->previewable(false)
                        ->multiple(false)
                        ->preserveFilenames()
                        ->nullable()
                        ->dehydrated(fn($state) => filled($state))
                        ->default(fn($record) => $record?->icon),
                    Placeholder::make('icon_preview')
                        ->label('Icon hiện tại')
                        ->content(
                            fn($record) =>
                            $record?->icon
                                ? '<img src="' . Storage::disk('ftp')->url($record->icon) . '" class="h-16 rounded-md border">'
                                : 'Chưa có ảnh'
                        )->html(),

                    FileUpload::make('logo')
                        ->disk('ftp')
                        ->visibility('public')
                        ->directory('images')
                        ->acceptedFileTypes(['image/*'])
                        ->image()
                        ->previewable(false)
                        ->multiple(false)
                        ->preserveFilenames()
                        ->nullable()
                        ->dehydrated(fn($state) => filled($state))
                        ->default(fn($record) => $record?->logo),
                    Placeholder::make('logo_preview')
                        ->label('Logo hiện tại')
                        ->content(
                            fn($record) =>
                            $record?->logo
                                ? '<img src="' . Storage::disk('ftp')->url($record->logo) . '" class="h-16 rounded-md border">'
                                : 'Chưa có ảnh'
                        )->html(),

                    FileUpload::make('thumb')
                        ->disk('ftp')
                        ->visibility('public')
                        ->directory('images')
                        ->acceptedFileTypes(['image/*'])
                        ->image()
                        ->previewable(false)
                        ->multiple(false)
                        ->preserveFilenames()
                        ->nullable()
                        ->dehydrated(fn($state) => filled($state))
                        ->default(fn($record) => $record?->thumb),
                    Placeholder::make('thumb_preview')
                        ->label('Thumb hiện tại')
                        ->content(
                            fn($record) =>
                            $record?->thumb
                                ? '<img src="' . Storage::disk('ftp')->url($record->thumb) . '" class="h-16 rounded-md border">'
                                : 'Chưa có ảnh'
                        )->html(),

                    FileUpload::make('bg_detail')
                        ->disk('ftp')
                        ->visibility('public')
                        ->directory('images')
                        ->acceptedFileTypes(['image/*'])
                        ->image()
                        ->previewable(false)
                        ->multiple(false)
                        ->preserveFilenames()
                        ->nullable()
                        ->dehydrated(fn($state) => filled($state))
                        ->default(fn($record) => $record?->bg_detail),
                    Placeholder::make('bg_detail_preview')
                        ->label('Bg Detail hiện tại')
                        ->content(
                            fn($record) =>
                            $record?->bg_detail
                                ? '<img src="' . Storage::disk('ftp')->url($record->bg_detail) . '" class="h-16 rounded-md border">'
                                : 'Chưa có ảnh'
                        )->html(),

                    Toggle::make('status')->label('Bật / Tắt game'),
                ])->columns(4)->collapsible()->columnSpanFull(),

            Section::make('Cấu hình CSKH VIP')
                ->relationship('admins')
                ->schema([
                    TextInput::make('name')->label('Tên CSKH'),
                    TextInput::make('desc')->label('Mô tả'),
                    FileUpload::make('avatar')
                        ->disk('ftp')
                        ->visibility('public')
                        ->directory('images')
                        ->acceptedFileTypes(['image/*'])
                        ->image()
                        ->previewable(false)
                        ->multiple(false)
                        ->preserveFilenames()
                        ->nullable()
                        ->dehydrated(fn($state) => filled($state))
                        ->default(fn($record) => $record?->avatar),
                    Placeholder::make('avatar_preview')
                        ->label('Avatar hiện tại')
                        ->content(
                            fn($record) =>
                            $record?->avatar
                                ? '<img src="' . Storage::disk('ftp')->url($record->avatar) . '" class="h-16 rounded-md border">'
                                : 'Chưa có ảnh'
                        )->html(),
                    FileUpload::make('zalo_url')
                        ->label('Ảnh Zalo')
                        ->disk('ftp')
                        ->visibility('public')
                        ->directory('images')
                        ->acceptedFileTypes(['image/*'])
                        ->image()
                        ->previewable(false)
                        ->multiple(false)
                        ->preserveFilenames()
                        ->nullable()
                        ->dehydrated(fn($state) => filled($state))
                        ->default(fn($record) => $record?->zalo_url),
                    Placeholder::make('zalo_url_preview')
                        ->label('Zalo Url hiện tại')
                        ->content(
                            fn($record) =>
                            $record?->zalo_url
                                ? '<img src="' . Storage::disk('ftp')->url($record->zalo_url) . '" class="h-16 rounded-md border">'
                                : 'Chưa có ảnh'
                        )->html(),
                    TextInput::make('telegram')->label('Telegram'),
                    TextInput::make('phone')->label('Số điện thoại'),
                    TextInput::make('facebook_url')->label('Facebook Admin'),
                ])->columns(3)->collapsible()->collapsed()->columnSpanFull(),

            Section::make('Cấu hình Mạng Xã Hội')
                ->relationship('socials')
                ->schema([
                    TextInput::make('app_id')->label('Facebook App ID'),
                    TextInput::make('app_secret')->label('Facebook App Secret'),
                    TextInput::make('fanpage_url')->label('Fanpage'),
                    TextInput::make('group_url')->label('Group'),
                    TextInput::make('messenger_url')->label('Messenger'),
                    TextInput::make('zalo_oa')->label('Zalo OA'),
                ])->columns(3)->collapsible()->collapsed()->columnSpanFull(),

            Section::make('Cấu hình Flags')
                ->relationship('flags')
                ->schema([
                    KeyValue::make('flags')
                        ->label('Danh sách Flags')
                        ->keyLabel('Tên flag')
                        ->valueLabel('Giá trị (true/false)')
                        ->default([]),
                ])->collapsible()->collapsed()->columnSpanFull(),

            Section::make('Cấu hình chung')
                ->relationship('settings')
                ->schema([
                    TextInput::make('required_vxu')->label('Hạn mức VXU tối thiểu'),
                    Textarea::make('description')->label('Mô tả ngắn'),
                    RichEditor::make('content')->label('Nội dung giới thiệu')->columnSpanFull(),
                    TextInput::make('homepage_url')->label('Trang chủ'),
                    TextInput::make('appstore_url')->label('App Store'),
                    TextInput::make('google_play_url')->label('Google Play'),
                    TextInput::make('apk_url')->label('APK'),
                    TextInput::make('support_url')->label('CSKH'),
                    TextInput::make('cdn_url')->label('CDN'),
                ])->columns(2)->collapsible()->collapsed()->columnSpanFull(),

            Section::make('Cấu hình danh sách API')
                ->schema([
                    Group::make()
                        ->relationship('apis')
                        ->schema([
                            Repeater::make('api_config')
                                ->label('API Configurations')
                                ->schema([
                                    TextInput::make('type')
                                        ->label('Type (VD: giftcodes, servers, v.v.)')
                                        ->required(),

                                    TextInput::make('url')
                                        ->label('API URL')
                                        ->url()
                                        ->required(),

                                    Select::make('method')
                                        ->label('HTTP Method')
                                        ->options([
                                            'GET' => 'GET',
                                            'POST' => 'POST',
                                            'PUT' => 'PUT',
                                            'DELETE' => 'DELETE',
                                        ])
                                        ->required(),

                                    Repeater::make('params')
                                        ->label('Params')
                                        ->schema([
                                            Grid::make(2)->schema([
                                                TextInput::make('key')
                                                    ->label('Key')
                                                    ->required(),
                                                TextInput::make('value')
                                                    ->label('Value')
                                                    ->required(),
                                            ])
                                        ])
                                        ->columns(1)
                                        ->addActionLabel('Thêm param')
                                        ->default([]),
                                ])
                                ->label('Danh sách API Type')
                                ->addActionLabel('Thêm Type')
                                ->default([])
                                ->columns(1),
                        ])
                        ->columns(1)
                ])
                ->collapsible()
                ->collapsed()
                ->columnSpanFull(),
        ]);
    }

    protected static function imagePreview(string $field, int $height = 64): Placeholder
    {
        return Placeholder::make("{$field}_preview")
            ->label(ucfirst($field) . ' hiện tại')
            ->content(
                fn($record) =>
                $record && $record->{$field}
                    ? '<img src="' . Storage::disk('ftp')->url($record->{$field}) . '" class="h-' . $height . ' rounded-md border">'
                    : 'Chưa có ảnh'
            )
            ->html()
            ->dehydrated(false);
    }
}
