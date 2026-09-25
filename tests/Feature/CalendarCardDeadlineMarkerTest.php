<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\TimSosmed\Filament\Pages\CalendarPage;
use App\Modules\TimSosmed\Models\Content;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class CalendarCardDeadlineMarkerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_calendar_event_props_include_deadline_indicators_only_when_deadline_is_reached()
    {
        $planner = User::create([
            'name'     => 'Planner Medsos',
            'username' => 'planner_user',
            'email'    => 'planner@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_planner',
        ]);

        Auth::login($planner);

        // 1. Content overdue by 2 days (unfinished)
        $overdueContent = Content::create([
            'nama_kegiatan'    => 'Konten Telat Deadline',
            'tanggal_kegiatan' => Carbon::today()->subDays(2)->toDateString(),
            'status'           => 'menunggu_editor',
            'jenis_konten'     => 'bahan',
            'planner_id'       => $planner->id,
        ]);

        // 2. Content due today (unfinished)
        $todayContent = Content::create([
            'nama_kegiatan'    => 'Konten Deadline Hari Ini',
            'tanggal_kegiatan' => Carbon::today()->toDateString(),
            'status'           => 'revisi_editor',
            'jenis_konten'     => 'bahan',
            'planner_id'       => $planner->id,
        ]);

        // 3. Content in future (not deadline)
        $futureContent = Content::create([
            'nama_kegiatan'    => 'Konten Masih Jauh',
            'tanggal_kegiatan' => Carbon::today()->addDays(5)->toDateString(),
            'status'           => 'draft',
            'jenis_konten'     => 'bahan',
            'planner_id'       => $planner->id,
        ]);

        // 4. Content completed (status selesai, even if past date -> not deadline)
        $completedContent = Content::create([
            'nama_kegiatan'    => 'Konten Sudah Selesai',
            'tanggal_kegiatan' => Carbon::today()->subDays(3)->toDateString(),
            'status'           => 'selesai',
            'jenis_konten'     => 'final',
            'planner_id'       => $planner->id,
        ]);

        $page = new CalendarPage();
        $events = collect($page->getCalendarEvents());

        // Verify overdue event props
        $overdueEvent = $events->firstWhere('id', (string) $overdueContent->id);
        $this->assertNotNull($overdueEvent);
        $this->assertTrue($overdueEvent['extendedProps']['is_deadline']);
        $this->assertTrue($overdueEvent['extendedProps']['is_overdue']);
        $this->assertFalse($overdueEvent['extendedProps']['is_today']);
        $this->assertEquals('⚠️ Lewat 2 hr', $overdueEvent['extendedProps']['deadline_label']);
        $this->assertEquals(2, $overdueEvent['extendedProps']['deadline_days']);

        // Verify today event props
        $todayEvent = $events->firstWhere('id', (string) $todayContent->id);
        $this->assertNotNull($todayEvent);
        $this->assertTrue($todayEvent['extendedProps']['is_deadline']);
        $this->assertFalse($todayEvent['extendedProps']['is_overdue']);
        $this->assertTrue($todayEvent['extendedProps']['is_today']);
        $this->assertEquals('🔥 Hari Ini', $todayEvent['extendedProps']['deadline_label']);

        // Verify future event props (no deadline)
        $futureEvent = $events->firstWhere('id', (string) $futureContent->id);
        $this->assertNotNull($futureEvent);
        $this->assertFalse($futureEvent['extendedProps']['is_deadline']);
        $this->assertFalse($futureEvent['extendedProps']['is_overdue']);
        $this->assertFalse($futureEvent['extendedProps']['is_today']);
        $this->assertNull($futureEvent['extendedProps']['deadline_label']);

        // Verify completed event props (no deadline marker)
        $completedEvent = $events->firstWhere('id', (string) $completedContent->id);
        $this->assertNotNull($completedEvent);
        $this->assertFalse($completedEvent['extendedProps']['is_deadline']);
        $this->assertFalse($completedEvent['extendedProps']['is_overdue']);
        $this->assertNull($completedEvent['extendedProps']['deadline_label']);
    }

    public function test_calendar_page_does_not_render_standalone_bottom_card()
    {
        $planner = User::create([
            'name'     => 'Planner Medsos',
            'username' => 'planner_user_2',
            'email'    => 'planner2@bpvp.test',
            'password' => bcrypt('password'),
            'role'     => 'medsos_planner',
        ]);

        Content::create([
            'nama_kegiatan'    => 'Konten Telat',
            'tanggal_kegiatan' => Carbon::yesterday()->toDateString(),
            'status'           => 'menunggu_editor',
            'jenis_konten'     => 'bahan',
            'planner_id'       => $planner->id,
        ]);

        Livewire::actingAs($planner)
            ->test(CalendarPage::class)
            ->assertSuccessful()
            ->assertDontSee('PERHATIAN:')
            ->assertDontSee('cal-deadline-card');
    }
}
