<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SeedRolesAndPermissionsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        遗忘授权
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

//        创建权限
        Permission::create(['name' => 'manage_contents']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'edit_settings']);
//        创建站长角色
        $founder = Role::create(['name' => 'Founder']);
        $founder->givePermissionTo('manage_contents');
        $founder->givePermissionTo('manage_users');
        $founder->givePermissionTo('edit_settings');

//        创建管理员角色
        $maintainer = Role::create(['name' => 'Maintainer']);
        $maintainer->givePermissionTo('manage_contents');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        遗忘授权
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

//       清空所有数据表数据
        $tableNames = config('permission.table_names');

        Model::unguard();
        \DB::table($tableNames['roles_has_permissions'])->delete();
        \DB::table($tableNames['model_has_roles'])->delete();
        \DB::table($tableNames['model_has_permissions'])->delete();
        \DB::table($tableNames['roles'])->delete();
        \DB::table($tableNames['permissions'])->delete();
        Model::reguard();
    }
}
