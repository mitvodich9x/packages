<?php

namespace Vgplay\Admins\Database\Seeders;

use Vgplay\Admins\Models\Role;
use Vgplay\Admins\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    public function run()
    {
        // Tạo danh sách các permission
        $permissions = collect([
            [
                'name'         => 'admins.index',
                'display_name' => 'Xem danh sách quản trị viên',
            ],
            [
                'name'         => 'admins.create',
                'display_name' => 'Thêm quản trị viên',
            ],
            [
                'name'         => 'admins.update',
                'display_name' => 'Sửa quản trị viên',
            ],
            [
                'name'         => 'admins.delete',
                'display_name' => 'Xoá quản trị viên',
            ],
            [
                'name'         => 'permissions.index',
                'display_name' => 'Xem danh sách phân quyền',
            ],
            [
                'name'         => 'permissions.create',
                'display_name' => 'Thêm phân quyền',
            ],
            [
                'name'         => 'permissions.update',
                'display_name' => 'Sửa phân quyền',
            ],
            [
                'name'         => 'permissions.delete',
                'display_name' => 'Xoá phân quyền',
            ],
            [
                'name'         => 'roles.index',
                'display_name' => 'Xem danh sách nhóm quản trị viên',
            ],
            [
                'name'         => 'roles.create',
                'display_name' => 'Thêm nhóm quản trị viên',
            ],
            [
                'name'         => 'roles.update',
                'display_name' => 'Sửa nhóm quản trị viên',
            ],
            [
                'name'         => 'roles.delete',
                'display_name' => 'Xoá nhóm quản trị viên',
            ],
            [
                'name'         => 'settings.index',
                'display_name' => 'Xem danh sách cấu hình',
            ],
            [
                'name'         => 'settings.create',
                'display_name' => 'Thêm cấu hình',
            ],
            [
                'name'         => 'settings.update',
                'display_name' => 'Sửa cấu hình',
            ],
            [
                'name'         => 'settings.delete',
                'display_name' => 'Xoá cấu hình',
            ],
            [
                'name'         => 'logs.index',
                'display_name' => 'Xem nhật ký hoạt động',
            ],
            [
                'name'         => 'games.index',
                'display_name' => 'Xem danh sách game',
            ],
            [
                'name'         => 'games.create',
                'display_name' => 'Thêm game',
            ],
            [
                'name'         => 'games.update',
                'display_name' => 'Sửa game',
            ],
            [
                'name'         => 'games.delete',
                'display_name' => 'Xoá game',
            ],
            [
                'name'         => 'news.index',
                'display_name' => 'Xem danh sách bài viết tin tức',
            ],
            [
                'name'         => 'news.create',
                'display_name' => 'Thêm bài viết tin tức',
            ],
            [
                'name'         => 'news.update',
                'display_name' => 'Sửa bài viết tin tức',
            ],
            [
                'name'         => 'news.delete',
                'display_name' => 'Xoá bài viết tin tức',
            ],
            [
                'name'         => 'categories.index',
                'display_name' => 'Xem danh sách chuyên mục bài viết',
            ],
            [
                'name'         => 'categories.create',
                'display_name' => 'Thêm chuyên mục bài viết',
            ],
            [
                'name'         => 'categories.update',
                'display_name' => 'Sửa chuyên mục bài viết',
            ],
            [
                'name'         => 'categories.delete',
                'display_name' => 'Xoá chuyên mục bài viết',
            ],
            [
                'name'         => 'giftcodea.index',
                'display_name' => 'Xem danh sách giftcodes',
            ],
            [
                'name'         => 'giftcodea.create',
                'display_name' => 'Thêm giftcodes',
            ],
            [
                'name'         => 'giftcodea.update',
                'display_name' => 'Sửa giftcodes',
            ],
            [
                'name'         => 'giftcodea.delete',
                'display_name' => 'Xoá giftcodes',
            ],
            [
                'name'         => 'giftcodea.import',
                'display_name' => 'Import giftcodes',
            ],
            [
                'name'         => 'giftcodea.detail',
                'display_name' => 'Xem chi tiết giftcodes',
            ],
        ]);

        // Tạo (hoặc cập nhật) các permission trong CSDL
        $permissions->each(function ($permission) {
            Permission::updateOrCreate(
                [
                    'name'       => $permission['name'],
                ],
                [
                    'display_name' => $permission['display_name'],
                    'guard_name' => 'admin'
                ]
            );
        });

        // Định nghĩa roles và các permission được gán cho từng role
        $rolesData = [
            [
                'name'         => 'Admin',
                'display_name' => 'Quản trị viên',
                'permissions'  => $permissions->pluck('name')->toArray(),
            ],
            [
                'name'         => 'Dev',
                'display_name' => 'Nhà phát triển',
                'permissions'  => [
                    'admins.index',
                    'roles.index',
                    'roles.create',
                    'roles.update',
                ],
            ],
            [
                'name'         => 'CE',
                'display_name' => 'Nhân viên cộng đồng Game',
                'permissions'  => [],
            ],
            [
                'name'         => 'GO',
                'display_name' => 'Nhân viên vận hành Game',
                'permissions'  => [],
            ],
            [
                'name'         => 'Maketing',
                'display_name' => 'Nhân viên maketing',
                'permissions'  => [],
            ],
            [
                'name'         => 'CS',
                'display_name' => 'Nhân viên chăm sóc khách hàng',
                'permissions'  => [],
            ],
            [
                'name'         => 'Leader',
                'display_name' => 'Quản lý',
                'permissions'  => [],
            ],
        ];

        foreach ($rolesData as $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'guard_name' => 'admin'
                ]
            );

            if (!empty($roleData['permissions'])) {
                $role->syncPermissions($roleData['permissions']);
            }
        }
    }
}
