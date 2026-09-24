<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Shortlink\Filament\Pages\DaftarShortlinkTable;
use App\Modules\Shortlink\Filament\Pages\DaftarLeadsTable;
use App\Modules\Shortlink\Models\Shortlink;
use Filament\Tables\Enums\PaginationMode;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ShortlinkTableSortingAndLoadingTest extends TestCase
{
    public function test_shortlink_table_widget_has_loading_view()
    {
        $viewContent = file_get_contents(resource_path('../app/Modules/Shortlink/Views/filament/table-widget.blade.php'));

        $this->assertStringContainsString('wire:loading', $viewContent);
        $this->assertStringContainsString('Memuat Data...', $viewContent);
        $this->assertStringContainsString('bpvp-table-spin', $viewContent);
    }

    public function test_manage_shortlink_page_has_form_submit_loading_state()
    {
        $viewContent = file_get_contents(resource_path('../app/Modules/Shortlink/Views/filament/manage-shortlink.blade.php'));

        $this->assertStringContainsString('wire:loading', $viewContent);
        $this->assertStringContainsString('wire:target="save"', $viewContent);
        $this->assertStringContainsString('Membuat Shortlink & Barcode...', $viewContent);
    }

    public function test_shortlink_query_does_not_bake_order_by_before_filament_sorting()
    {
        $user = new User([
            'name' => 'Admin Test',
            'role' => 'admin_system',
        ]);
        Auth::login($user);

        $widget = new DaftarShortlinkTable();
        $table = $widget->table(new \Filament\Tables\Table($widget));

        // Default sort must be configured on table level, not hardcoded in the query
        $this->assertEquals('created_at', $table->getDefaultSortColumn());
        $this->assertEquals('desc', $table->getDefaultSortDirection());

        // Pagination mode must be Default so users can pick 10, 25, 50, 100
        $this->assertEquals(PaginationMode::Default, $table->getPaginationMode());
        $this->assertEquals([10, 25, 50, 100], $table->getPaginationPageOptions());
    }

    public function test_leads_table_has_proper_sorting_and_pagination()
    {
        $user = new User([
            'name' => 'Admin Test',
            'role' => 'admin_system',
        ]);
        Auth::login($user);

        $widget = new DaftarLeadsTable();
        $table = $widget->table(new \Filament\Tables\Table($widget));

        $this->assertEquals('created_at', $table->getDefaultSortColumn());
        $this->assertEquals('desc', $table->getDefaultSortDirection());
        $this->assertEquals(PaginationMode::Default, $table->getPaginationMode());
        $this->assertEquals([10, 25, 50, 100], $table->getPaginationPageOptions());
    }

    public function test_shortlink_columns_are_sortable()
    {
        $user = new User([
            'name' => 'Admin Test',
            'role' => 'admin_system',
        ]);
        Auth::login($user);

        $widget = new DaftarShortlinkTable();
        $table = $widget->table(new \Filament\Tables\Table($widget));

        $pegawaiCol = $table->getColumn('pegawai_name');
        $this->assertNotNull($pegawaiCol);
        $this->assertTrue($pegawaiCol->isSortable());

        $clicksCol = $table->getColumn('clicks_count');
        $this->assertNotNull($clicksCol);
        $this->assertTrue($clicksCol->isSortable());

        $leadsCol = $table->getColumn('leads_count');
        $this->assertNotNull($leadsCol);
        $this->assertTrue($leadsCol->isSortable());

        $codeCol = $table->getColumn('code');
        $this->assertNotNull($codeCol);
        $this->assertTrue($codeCol->isSortable());
    }
}
