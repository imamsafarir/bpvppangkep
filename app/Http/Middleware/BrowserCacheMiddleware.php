<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BrowserCacheMiddleware
{
    /**
     * Handle an incoming request.
     * Mengatur HTTP Browser Caching dan ETag untuk mempercepat akses di browser masing-masing pengunjung.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya terapkan pada request GET yang sukses dan bukan area admin/livewire/login
        if (
            $request->isMethod('GET') &&
            $response->getStatusCode() === 200 &&
            ! $request->is('admin*') &&
            ! $request->is('livewire*') &&
            ! $request->is('login*') &&
            ! auth()->check()
        ) {
            $content = $response->getContent();

            if (is_string($content) && strlen($content) > 0) {
                $etag = '"' . md5($content) . '"';
                $response->headers->set('ETag', $etag);

                // Periksa apakah browser pengunjung sudah memiliki versi ini di cache lokalnya
                $ifNoneMatch = $request->header('If-None-Match');
                if ($ifNoneMatch && (trim($ifNoneMatch) === $etag || trim($ifNoneMatch, '"') === trim($etag, '"'))) {
                    // Browser pengunjung sudah punya versi terbaru, kirim 304 Not Modified (hemat 100% bandwidth & super cepat)
                    return response('', 304, $response->headers->all());
                }

                // Instruksikan browser pengunjung untuk menyimpan cache lokal selama 3 menit dan stale-while-revalidate 5 menit
                $response->headers->set('Cache-Control', 'public, max-age=180, stale-while-revalidate=300');
            }
        }

        return $response;
    }
}
