<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\TimSosmed\Filament\Pages\CalendarPage;
use App\Modules\TimSosmed\Livewire\ContentComments;
use App\Modules\TimSosmed\Models\Comment;
use App\Modules\TimSosmed\Models\Content;
use App\Modules\TimSosmed\Models\ContentRead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class CalendarDiscussionAndAutoRefreshTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Pastikan migration selesai di database in-memory
        $this->artisan('migrate');
    }

    public function test_calendar_events_include_discussion_counts_and_unread_state()
    {
        // 1. Buat User A (Planner) dan User B (Editor)
        $userA = User::create([
            'name'     => 'Planner User',
            'username' => 'planner_user',
            'email'    => 'planner@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_planner',
        ]);

        $userB = User::create([
            'name'     => 'Editor User',
            'username' => 'editor_user',
            'email'    => 'editor@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_editor',
        ]);

        // 2. Buat Konten
        $content = Content::create([
            'nama_kegiatan'    => 'Pelatihan Web Development',
            'tanggal_kegiatan' => '2026-09-30',
            'status'           => 'menunggu_editor',
            'jenis_konten'     => 'bahan',
            'planner_id'       => $userA->id,
            'editor_id'        => $userB->id,
        ]);

        // 3. User A menambahkan komentar diskusi
        Comment::create([
            'content_id' => $content->id,
            'user_id'    => $userA->id,
            'body'       => 'Halo editor, mohon di-review bahan ini.',
        ]);

        // 4. Sebagai User A (yang menulis komentar):
        // Tidak boleh ditandai sebagai unread karena komentar ditulis oleh dirinya sendiri
        Auth::login($userA);
        $calendarPage = new CalendarPage();
        $eventsForA = $calendarPage->getCalendarEvents();

        $this->assertCount(1, $eventsForA);
        $this->assertEquals(1, $eventsForA[0]['extendedProps']['comments_count']);
        $this->assertEquals(0, $eventsForA[0]['extendedProps']['unread_comments_count']);
        $this->assertFalse($eventsForA[0]['extendedProps']['has_unread_comments']);

        // 5. Sebagai User B (penerima):
        // Komentar dari User A harus dihitung sebagai unread (1 baru)
        Auth::login($userB);
        $eventsForB = $calendarPage->getCalendarEvents();

        $this->assertCount(1, $eventsForB);
        $this->assertEquals(1, $eventsForB[0]['extendedProps']['comments_count']);
        $this->assertEquals(1, $eventsForB[0]['extendedProps']['unread_comments_count']);
        $this->assertTrue($eventsForB[0]['extendedProps']['has_unread_comments']);

        // 6. User B menandai diskusi sudah dibaca (klik event / popup)
        $calendarPage->markAsRead($content->id);

        $this->assertDatabaseHas('content_reads', [
            'content_id' => $content->id,
            'user_id'    => $userB->id,
        ]);

        // 7. Setelah ditandai baca, unread menjadi 0
        $eventsAfterRead = $calendarPage->getCalendarEvents();
        $this->assertEquals(1, $eventsAfterRead[0]['extendedProps']['comments_count']);
        $this->assertEquals(0, $eventsAfterRead[0]['extendedProps']['unread_comments_count']);
        $this->assertFalse($eventsAfterRead[0]['extendedProps']['has_unread_comments']);
    }

    public function test_calendar_polling_detects_changes_and_dispatches_refresh()
    {
        $user = User::create([
            'name'     => 'Planner User',
            'username' => 'planner_user_2',
            'email'    => 'planner2@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_planner',
        ]);
        Auth::login($user);

        $content = Content::create([
            'nama_kegiatan'    => 'Konten Video Reels',
            'tanggal_kegiatan' => '2026-10-01',
            'status'           => 'draft',
            'jenis_konten'     => 'bahan',
            'planner_id'       => $user->id,
        ]);

        $testable = Livewire::test(CalendarPage::class);

        // Panggilan awal inisialisasi hash versi
        $testable->call('checkCalendarUpdates');
        $testable->assertNotDispatched('calendar-refresh');

        // Tambah komentar baru sehingga timestamp Comment::max('created_at') berubah
        Comment::create([
            'content_id' => $content->id,
            'user_id'    => $user->id,
            'body'       => 'Pesan baru pada konten',
        ]);

        // Panggilan kedua harus mendeteksi perubahan hash dan mendispatch calendar-refresh
        $testable->call('checkCalendarUpdates');
        $testable->assertDispatched('calendar-refresh');
    }

    public function test_content_comments_component_updates_read_at_and_dispatches_calendar_refresh()
    {
        $user = User::create([
            'name'     => 'Admin User',
            'username' => 'admin_user',
            'email'    => 'admin@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
        Auth::login($user);

        $content = Content::create([
            'nama_kegiatan'    => 'Konten Infografis',
            'tanggal_kegiatan' => '2026-10-02',
            'status'           => 'draft',
            'jenis_konten'     => 'final',
        ]);

        // Render komponen ContentComments akan otomatis memperbarui content_reads
        Livewire::test(ContentComments::class, ['contentId' => $content->id])
            ->set('newComment', 'Komentar pengujian dari test suite')
            ->call('addComment')
            ->assertDispatched('comment-added')
            ->assertDispatched('calendar-refresh');

        $this->assertDatabaseHas('comments', [
            'content_id' => $content->id,
            'body'       => 'Komentar pengujian dari test suite',
        ]);

        $this->assertDatabaseHas('content_reads', [
            'content_id' => $content->id,
            'user_id'    => $user->id,
        ]);
    }

    public function test_calendar_opens_view_url_for_completed_content_instead_of_edit_url()
    {
        $editor = User::create([
            'name'     => 'Editor User',
            'username' => 'editor_finish_test',
            'email'    => 'editor_finish@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_editor',
        ]);
        Auth::login($editor);

        $completedContent = Content::create([
            'nama_kegiatan'    => 'Konten Selesai Live',
            'tanggal_kegiatan' => '2026-09-20',
            'status'           => 'selesai',
            'jenis_konten'     => 'final',
        ]);

        $inProgressContent = Content::create([
            'nama_kegiatan'    => 'Konten Sedang Diedit',
            'tanggal_kegiatan' => '2026-09-25',
            'status'           => 'menunggu_editor',
            'jenis_konten'     => 'bahan',
        ]);

        $calendarPage = new CalendarPage();
        $events = collect($calendarPage->getCalendarEvents());

        $completedEvent = $events->firstWhere('id', (string) $completedContent->id);
        $inProgressEvent = $events->firstWhere('id', (string) $inProgressContent->id);

        // Konten berstatus selesai HARUS membuka halaman VIEW (bukan edit yang menyebabkan 403 Forbidden)
        $this->assertNotNull($completedEvent);
        $this->assertStringContainsString('/admin/contents/' . $completedContent->id, $completedEvent['url']);
        $this->assertStringNotContainsString('/edit', $completedEvent['url']);
        $this->assertEquals('view', $completedEvent['extendedProps']['action_type']);
        $this->assertTrue($completedEvent['extendedProps']['is_selesai']);

        // Konten yang masih berjalan membuka halaman EDIT
        $this->assertNotNull($inProgressEvent);
        $this->assertStringContainsString('/admin/contents/' . $inProgressContent->id . '/edit', $inProgressEvent['url']);
        $this->assertEquals('edit', $inProgressEvent['extendedProps']['action_type']);
        $this->assertFalse($inProgressEvent['extendedProps']['is_selesai']);
    }

    public function test_content_model_unread_helper_methods()
    {
        $user1 = User::create([
            'name'     => 'User 1',
            'username' => 'user_1_test',
            'email'    => 'user1@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_planner',
        ]);

        $user2 = User::create([
            'name'     => 'User 2',
            'username' => 'user_2_test',
            'email'    => 'user2@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_editor',
        ]);

        $content = Content::create([
            'nama_kegiatan'    => 'Uji Helper Unread',
            'tanggal_kegiatan' => '2026-10-05',
            'status'           => 'draft',
            'jenis_konten'     => 'bahan',
        ]);

        // Belum ada komentar
        $this->assertEquals(0, $content->getUnreadCommentsCount($user1->id));
        $this->assertFalse($content->hasUnreadComments($user1->id));

        // User 1 menulis komentar
        Comment::create([
            'content_id' => $content->id,
            'user_id'    => $user1->id,
            'body'       => 'Pesan pertama',
        ]);

        // Bagi User 1: 0 unread (pesan sendiri)
        $this->assertEquals(0, $content->getUnreadCommentsCount($user1->id));
        // Bagi User 2: 1 unread
        $this->assertEquals(1, $content->getUnreadCommentsCount($user2->id));
        $this->assertTrue($content->hasUnreadComments($user2->id));

        // User 2 membaca konten
        ContentRead::create([
            'content_id'   => $content->id,
            'user_id'      => $user2->id,
            'last_read_at' => now(),
        ]);

        // Sekarang bagi User 2: 0 unread
        $this->assertEquals(0, $content->getUnreadCommentsCount($user2->id));
        $this->assertFalse($content->hasUnreadComments($user2->id));
    }
}
