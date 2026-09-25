<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\TimSosmed\Filament\Pages\StatistikTim;
use App\Modules\TimSosmed\Models\Content;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StatistikTimSuperAdminHiddenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'planner', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
    }

    public function test_superadmin_user_is_hidden_from_statistik_tim_page()
    {
        // 1. Buat Super Admin
        $superAdmin = User::create([
            'name'     => 'Super Admin Utama',
            'username' => 'superadmin',
            'email'    => 'superadmin@admin.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
        $superAdmin->assignRole('super_admin');

        // 2. Buat Planner
        $planner = User::create([
            'name'     => 'Planner Hebat',
            'username' => 'planner_hebat',
            'email'    => 'planner@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_planner',
        ]);
        $planner->assignRole('planner');

        // 3. Buat konten yang dibuat/dikerjakan oleh superadmin dan planner
        Content::create([
            'nama_kegiatan'    => 'Konten Superadmin',
            'tanggal_kegiatan' => now()->toDateString(),
            'status'           => 'selesai',
            'jenis_konten'     => 'final',
            'planner_id'       => $superAdmin->id,
        ]);

        Content::create([
            'nama_kegiatan'    => 'Konten Planner',
            'tanggal_kegiatan' => now()->toDateString(),
            'status'           => 'selesai',
            'jenis_konten'     => 'final',
            'planner_id'       => $planner->id,
        ]);

        // Verifikasi getTeamSummary: Super Admin tidak masuk ke summary
        $summary = StatistikTim::getTeamSummary();
        $this->assertEquals(1, $summary['total_members']);
        $this->assertCount(1, $summary['top_total']);
        $this->assertEquals('Planner Hebat', $summary['top_total'][0]['name']);

        // Verifikasi Livewire Page: Super Admin tidak tampil di tabel statistik
        Livewire::actingAs($superAdmin)
            ->test(StatistikTim::class)
            ->assertSuccessful()
            ->assertSee('Planner Hebat')
            ->assertDontSee('Super Admin Utama');
    }
}
