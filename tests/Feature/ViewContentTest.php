<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Modules\TimSosmed\Filament\Resources\Contents\Pages\ViewContent;
use App\Modules\TimSosmed\Models\Comment;
use App\Modules\TimSosmed\Models\Content;
use App\Modules\TimSosmed\Models\ContentRead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class ViewContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_view_content_page_renders_with_executive_dashboard()
    {
        $planner = User::create([
            'name'     => 'Planner Medsos',
            'username' => 'planner_medsos',
            'email'    => 'planner@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_planner',
        ]);

        $content = Content::create([
            'nama_kegiatan'    => 'Workshop Pembuatan Konten Digital',
            'tanggal_kegiatan' => '2026-10-15',
            'status'           => 'menunggu_editor',
            'jenis_konten'     => 'bahan',
            'planner_id'       => $planner->id,
            'brief'            => '<p>Liputan workshop <strong>digital marketing</strong> hari ke-2.</p>',
            'caption'          => '<p>Semangat belajar rekan-rekan peserta workshop! #pelatihan</p>',
            'link_media_mentah' => 'https://drive.google.com/test-mentah',
        ]);

        Livewire::actingAs($planner)
            ->test(ViewContent::class, ['record' => $content->id])
            ->assertSuccessful()
            ->assertSee('Workshop Pembuatan Konten Digital')
            ->assertSee('Menunggu Produksi Editor')
            ->assertSee('digital marketing')
            ->assertSee('Semangat belajar rekan-rekan peserta workshop!')
            ->assertSee('Planner Medsos')
            ->assertSee('Progres Produksi Konten')
            ->assertSee('Diskusi & Obrolan Tim', false)
            ->assertDontSee('Kembali / Tutup')
            ->assertDontSee('Kembali ke Kalender');
    }

    public function test_view_content_marks_discussion_as_read_on_mount()
    {
        $planner = User::create([
            'name'     => 'Planner Medsos',
            'username' => 'planner_user',
            'email'    => 'planner@bpvp.test',
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

        $content = Content::create([
            'nama_kegiatan'    => 'Posting Video Sosialisasi',
            'tanggal_kegiatan' => '2026-10-20',
            'status'           => 'menunggu_editor',
            'jenis_konten'     => 'final',
            'planner_id'       => $planner->id,
            'editor_id'        => $editor->id,
        ]);

        // Editor leaves a comment
        Comment::create([
            'content_id' => $content->id,
            'user_id'    => $editor->id,
            'body'       => 'File hasil editing sudah di-upload ke drive ya.',
            'created_at' => Carbon::now()->subMinutes(5),
        ]);

        // Prior to viewing, planner has 1 unread comment
        $this->assertEquals(1, $content->getUnreadCommentsCount($planner->id));

        // Planner opens ViewContent page
        Livewire::actingAs($planner)
            ->test(ViewContent::class, ['record' => $content->id])
            ->assertSuccessful();

        // Check that ContentRead record is created/updated for planner
        $userRead = ContentRead::where('content_id', $content->id)
            ->where('user_id', $planner->id)
            ->first();

        $this->assertNotNull($userRead);
        $this->assertNotNull($userRead->last_read_at);

        // Now planner has 0 unread comments
        $content->refresh();
        $this->assertEquals(0, $content->getUnreadCommentsCount($planner->id));
    }

    public function test_download_zip_handles_empty_media_with_notification()
    {
        $planner = User::create([
            'name'     => 'Planner Medsos',
            'username' => 'planner_user_2',
            'email'    => 'planner2@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_planner',
        ]);

        $content = Content::create([
            'nama_kegiatan'    => 'Test Download Kosong',
            'tanggal_kegiatan' => '2026-10-20',
            'status'           => 'draft',
            'jenis_konten'     => 'bahan',
            'planner_id'       => $planner->id,
        ]);

        Livewire::actingAs($planner)
            ->test(ViewContent::class, ['record' => $content->id])
            ->call('downloadZip', 'mentah')
            ->assertNotified();
    }
}
