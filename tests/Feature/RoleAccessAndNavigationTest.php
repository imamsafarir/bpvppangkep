<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\ManageUser;
use App\Models\User;
use App\Modules\Shortlink\Filament\Pages\ManageShortlink;
use App\Modules\TimSosmed\Filament\Pages\CalendarPage;
use App\Modules\TimSosmed\Filament\Pages\ReportCenter;
use App\Modules\TimSosmed\Filament\Pages\StatistikMedsos;
use App\Modules\TimSosmed\Filament\Pages\StatistikTim;
use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Modules\TimSosmed\Filament\Resources\Platforms\PlatformResource;
use App\Modules\Website\Filament\Pages\ManageBeritaDanGaleri;
use App\Modules\Website\Filament\Pages\ManageProfil;
use App\Modules\Website\Filament\Pages\ManageWebsiteSettings;
use App\Modules\Website\Filament\Widgets\WebsiteStatsOverview;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RoleAccessAndNavigationTest extends TestCase
{
    public function test_shortlink_only_role_permissions_and_home_url()
    {
        $user = new User([
            'name'  => 'Admin Shortlink Only',
            'email' => 'shortlink@bpvp.test',
            'role'  => 'shortlink',
        ]);
        Auth::login($user);

        $this->assertTrue($user->isShortlink());
        $this->assertFalse($user->isMedsosTeam());
        $this->assertFalse($user->isWebsite());
        $this->assertFalse($user->isAdmin());

        $this->assertEquals(url('/admin/manage-shortlink'), $user->getDefaultDashboardUrl());
        $this->assertEquals(url('/admin/manage-shortlink'), Filament::getPanel('admin')->getHomeUrl());

        // TimSosmed must be strictly forbidden
        $this->assertFalse(CalendarPage::canAccess());
        $this->assertFalse(ReportCenter::canAccess());
        $this->assertFalse(StatistikMedsos::canAccess());
        $this->assertFalse(StatistikTim::canAccess());
        $this->assertFalse(ContentResource::canAccess());
        $this->assertFalse(PlatformResource::canAccess());

        // Website must be strictly forbidden
        $this->assertFalse(ManageWebsiteSettings::canAccess());
        $this->assertFalse(ManageBeritaDanGaleri::canAccess());
        $this->assertFalse(ManageProfil::canAccess());
        $this->assertFalse(WebsiteStatsOverview::canView());

        // Core admin must be forbidden
        $this->assertFalse(Dashboard::canAccess());
        $this->assertFalse(ManageUser::canAccess());

        // Shortlink must be allowed
        $this->assertTrue(ManageShortlink::canAccess());
    }

    public function test_multi_role_shortlink_and_medsos_planner()
    {
        $user = new User([
            'name'  => 'Shortlink and Medsos Planner',
            'email' => 'multi_medsos@bpvp.test',
            'role'  => 'shortlink,medsos_planner',
        ]);
        Auth::login($user);

        $this->assertTrue($user->isShortlink());
        $this->assertTrue($user->isMedsosTeam());
        $this->assertFalse($user->isWebsite());
        $this->assertFalse($user->isAdmin());

        // TimSosmed & Shortlink should both be allowed
        $this->assertTrue(CalendarPage::canAccess());
        $this->assertTrue(ContentResource::canAccess());
        $this->assertTrue(ReportCenter::canAccess());
        $this->assertTrue(ManageShortlink::canAccess());

        // Website and Core admin must remain forbidden
        $this->assertFalse(ManageWebsiteSettings::canAccess());
        $this->assertFalse(ManageBeritaDanGaleri::canAccess());
        $this->assertFalse(Dashboard::canAccess());
        $this->assertFalse(ManageUser::canAccess());

        $this->assertEquals(url('/admin/calendar-page'), $user->getDefaultDashboardUrl());
    }

    public function test_multi_role_shortlink_and_website()
    {
        $user = new User([
            'name'  => 'Shortlink and Website Manager',
            'email' => 'multi_web@bpvp.test',
            'role'  => 'shortlink,website',
        ]);
        Auth::login($user);

        $this->assertTrue($user->isShortlink());
        $this->assertFalse($user->isMedsosTeam());
        $this->assertTrue($user->isWebsite());
        $this->assertFalse($user->isAdmin());

        // Shortlink & Website should both be allowed
        $this->assertTrue(ManageShortlink::canAccess());
        $this->assertTrue(ManageWebsiteSettings::canAccess());
        $this->assertTrue(ManageBeritaDanGaleri::canAccess());
        $this->assertTrue(ManageProfil::canAccess());
        $this->assertTrue(WebsiteStatsOverview::canView());

        // TimSosmed and Core admin must remain forbidden
        $this->assertFalse(CalendarPage::canAccess());
        $this->assertFalse(ContentResource::canAccess());
        $this->assertFalse(Dashboard::canAccess());
        $this->assertFalse(ManageUser::canAccess());

        $this->assertEquals(url('/admin/manage-shortlink'), $user->getDefaultDashboardUrl());
    }

    public function test_admin_system_has_full_access()
    {
        $user = new User([
            'name'  => 'Super Admin',
            'email' => 'admin@bpvp.test',
            'role'  => 'admin',
        ]);
        Auth::login($user);

        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->isShortlink());
        $this->assertTrue($user->isMedsosTeam());
        $this->assertTrue($user->isWebsite());

        $this->assertTrue(Dashboard::canAccess());
        $this->assertTrue(ManageUser::canAccess());
        $this->assertTrue(CalendarPage::canAccess());
        $this->assertTrue(ContentResource::canAccess());
        $this->assertTrue(PlatformResource::canAccess());
        $this->assertTrue(ManageShortlink::canAccess());
        $this->assertTrue(ManageWebsiteSettings::canAccess());
        $this->assertTrue(WebsiteStatsOverview::canView());

        $this->assertEquals(url('/admin'), $user->getDefaultDashboardUrl());
    }
}
