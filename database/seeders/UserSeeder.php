<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \Illuminate\Support\Facades\DB::table('users')->where('email', 'admin@gmail.com')->first();
        
        if (!$user) {
            $userId = \Illuminate\Support\Str::uuid()->toString();
            \Illuminate\Support\Facades\DB::table('users')->insert([
                'id' => $userId,
                'name' => 'Admin Jacket Store',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('Bajocero-0'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $userId = $user->id;
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $userId)
                ->update([
                    'password' => bcrypt('Bajocero-0'),
                    'updated_at' => now()
                ]);
        }

        //Usuario administrador - crear rol sin especificar ID (auto-increment)
        $roleId = \Illuminate\Support\Facades\DB::table('roles')->insertGetId([
            'name' => 'administrador',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $permisos = \Illuminate\Support\Facades\DB::table('permissions')->get();
        
        foreach($permisos as $permiso) {
            \Illuminate\Support\Facades\DB::table('role_has_permissions')->insert([
                'permission_id' => $permiso->id,
                'role_id' => $roleId
            ]);
        }
        
        // Manual assignment to avoid foreign key issues with UUIDs in SQLite
        \Illuminate\Support\Facades\DB::table('model_has_roles')->insert([
            'role_id' => $roleId,
            'model_type' => \App\Models\User::class,
            'model_id' => $userId
        ]);
    }
}
