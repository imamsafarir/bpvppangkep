<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Shortlink\Models\Shortlink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortlinkCustomFieldsTest extends TestCase
{
    public function test_shortlink_default_display_attributes()
    {
        $shortlink = new Shortlink([
            'pegawai_name'    => 'Pegawai Test',
            'code'            => 'abcde',
            'destination_url' => 'https://example.com',
        ]);

        $this->assertEquals('Selamat Datang!', $shortlink->display_title);
        $this->assertEquals('Silakan lengkapi informasi singkat di bawah ini sebelum melanjutkan ke tautan tujuan.', $shortlink->display_description);
        $this->assertEquals('Lanjutkan ke Tautan', $shortlink->display_button_text);
    }

    public function test_shortlink_custom_display_attributes()
    {
        $shortlink = new Shortlink([
            'pegawai_name'       => 'Pegawai Test',
            'code'               => 'abcde',
            'destination_url'    => 'https://example.com',
            'custom_title'       => 'Formulir Sertifikat Pelatihan',
            'custom_description' => 'Harap isi nama dan nomor WA untuk klaim sertifikat.',
            'custom_button_text' => 'Klaim Sertifikat Sekarang',
        ]);

        $this->assertEquals('Formulir Sertifikat Pelatihan', $shortlink->display_title);
        $this->assertEquals('Harap isi nama dan nomor WA untuk klaim sertifikat.', $shortlink->display_description);
        $this->assertEquals('Klaim Sertifikat Sekarang', $shortlink->display_button_text);
    }

    public function test_capture_view_renders_custom_texts()
    {
        $shortlink = new Shortlink([
            'pegawai_name'       => 'Pegawai Test',
            'code'               => 'test1',
            'destination_url'    => 'https://example.com',
            'is_capture_active'  => true,
            'capture_fields'     => ['nama', 'whatsapp'],
            'custom_title'       => 'Halo Alumni BPVP',
            'custom_description' => 'Silakan isi data alumni di bawah ini.',
            'custom_button_text' => 'Kirim & Lanjutkan',
        ]);

        $fields = $shortlink->capture_fields;
        $view = $this->view('shortlink::capture', compact('shortlink', 'fields'));

        $view->assertSee('Halo Alumni BPVP');
        $view->assertSee('Silakan isi data alumni di bawah ini.');
        $view->assertSee('Kirim & Lanjutkan');
    }

    public function test_capture_view_renders_default_texts_when_custom_is_null()
    {
        $shortlink = new Shortlink([
            'pegawai_name'       => 'Pegawai Test',
            'code'               => 'test2',
            'destination_url'    => 'https://example.com',
            'is_capture_active'  => true,
            'capture_fields'     => ['nama'],
            'custom_title'       => null,
            'custom_description' => null,
            'custom_button_text' => null,
        ]);

        $fields = $shortlink->capture_fields;
        $view = $this->view('shortlink::capture', compact('shortlink', 'fields'));

        $view->assertSee('Selamat Datang!');
        $view->assertSee('Silakan lengkapi informasi singkat di bawah ini sebelum melanjutkan ke tautan tujuan.');
        $view->assertSee('Lanjutkan ke Tautan');
    }
}
