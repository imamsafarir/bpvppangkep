<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\TimSosmed\Filament\Widgets\BebanKerjaOverview;
use App\Modules\TimSosmed\Models\Content;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class BebanKerjaOverviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_beban_kerja_overview_displays_3_stages_clean_state()
    {
        $planner = User::create([
            'name'     => 'Planner Medsos',
            'username' => 'planner_user',
            'email'    => 'planner@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_planner',
        ]);

        // Content scheduled for the future
        Content::create([
            'nama_kegiatan'    => 'Konten Masa Depan',
            'tanggal_kegiatan' => Carbon::now()->addDays(5)->toDateString(),
            'status'           => 'draft',
            'jenis_konten'     => 'bahan',
            'planner_id'       => $planner->id,
        ]);

        Livewire::actingAs($planner)
            ->test(BebanKerjaOverview::class)
            ->assertSuccessful()
            ->assertSee('1. Perencanaan')
            ->assertSee('2. Produksi Konten (Editor)')
            ->assertSee('3. Siap Tayang (Admin Platform)')
            ->assertSee('Tepat waktu');
    }

    public function test_beban_kerja_overview_alerts_when_content_is_overdue_in_stage()
    {
        $planner = User::create([
            'name'     => 'Planner Medsos',
            'username' => 'planner_user_2',
            'email'    => 'planner2@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_planner',
        ]);

        $editor = User::create([
            'name'     => 'Editor Medsos',
            'username' => 'editor_user',
            'email'    => 'editor@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_editor',
        ]);

        // 1. Content overdue in editor queue
        Content::create([
            'nama_kegiatan'    => 'Video Terlambat Editing',
            'tanggal_kegiatan' => Carbon::yesterday()->toDateString(),
            'status'           => 'menunggu_editor',
            'jenis_konten'     => 'bahan',
            'planner_id'       => $planner->id,
            'editor_id'        => $editor->id,
        ]);

        // 2. Content due today in planner queue
        Content::create([
            'nama_kegiatan'    => 'Konsep Deadline Hari Ini',
            'tanggal_kegiatan' => Carbon::today()->toDateString(),
            'status'           => 'draft',
            'jenis_konten'     => 'final',
            'planner_id'       => $planner->id,
        ]);

        Livewire::actingAs($planner)
            ->test(BebanKerjaOverview::class)
            ->assertSuccessful()
            ->assertSee('1. Perencanaan (🔥 1 Deadline Hari Ini)')
            ->assertSee('2. Produksi Editor (🚨 1 Lewat Deadline)')
            ->assertSee('1 konten editing melewati batas deadline!');
    }
}
