<?php

namespace App\Modules\TimSosmed\Database\Seeders;

use App\Modules\TimSosmed\Models\Platform;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TimSosmedSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Platforms default
        $platforms = [
            ['name' => 'Facebook', 'slug' => 'facebook', 'icon' => 'facebook'],
            ['name' => 'Instagram', 'slug' => 'instagram', 'icon' => 'instagram'],
            ['name' => 'TikTok', 'slug' => 'tiktok', 'icon' => 'tiktok'],
            ['name' => 'YouTube', 'slug' => 'youtube', 'icon' => 'youtube'],
            ['name' => 'Twitter / X', 'slug' => 'twitter', 'icon' => 'twitter'],
        ];

        foreach ($platforms as $platform) {
            Platform::firstOrCreate(
                ['slug' => $platform['slug']],
                ['name' => $platform['name'], 'icon' => $platform['icon']]
            );
        }

        // 2. Seed Roles Spatie
        $roles = [
            'super_admin',
            'admin_platform',
            'planner',
            'editor',
            'instruktur',
            'pegawai',
            'user',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
    }
}
