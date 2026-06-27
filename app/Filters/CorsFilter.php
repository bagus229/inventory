<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CorsFilter
 * ---------------------------------------------------------------------
 * Menangani Cross-Origin Resource Sharing (CORS) secara global.
 *
 * Tujuan:
 *  - Mengizinkan frontend SPA (Vite/React/Vue di origin lain, misal
 *    http://localhost:5173) untuk mengakses API ini tanpa diblokir browser.
 *  - Menangani preflight request (HTTP OPTIONS) yang otomatis dikirim
 *    browser sebelum request POST/PUT/DELETE dengan header custom
 *    (seperti Authorization atau Content-Type: application/json).
 *
 * Strategi keamanan:
 *  - Daftar origin yang diizinkan didefinisikan secara eksplisit (whitelist),
 *    BUKAN wildcard "*", karena kita juga mengirim Authorization header
 *    (credentials), sehingga wildcard "*" tidak aman & tidak valid oleh spec.
 *  - Origin yang tidak ada di whitelist tidak akan mendapat header
 *    Access-Control-Allow-Origin sehingga browser akan otomatis menolak
 *    response tersebut (cross-origin blocked).
 */
class CorsFilter implements FilterInterface
{
    /**
     * Daftar origin frontend yang diizinkan mengakses API ini.
     * Tambahkan domain production Anda di sini saat deploy.
     */
    protected array $allowedOrigins = [
        'http://localhost:5173',   // Vite dev server default
        'http://127.0.0.1:5173',
        'http://localhost:3000',   // fallback CRA/dev server lain
        'http://localhost:8080',
        'https://uas-web2-312410382-bagus-aditya-her.vercel.app', // ← ini
    ];

    /**
     * Dijalankan SEBELUM request mencapai controller.
     *
     * @param array|null $arguments
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $origin = $request->getHeaderLine('Origin');

        // Simpan origin yang divalidasi agar bisa dipakai lagi di method after()
        $allowOrigin = $this->resolveAllowedOrigin($origin);

        // Tangani preflight request (OPTIONS) langsung di sini,
        // tanpa perlu meneruskan ke controller, karena browser hanya
        // butuh header CORS-nya saja sebagai jawaban preflight.
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            $response = service('response');

            $response->setHeader('Access-Control-Allow-Origin', $allowOrigin)
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept')
                ->setHeader('Access-Control-Allow-Credentials', 'true')
                ->setHeader('Access-Control-Max-Age', '3600')
                ->setStatusCode(204);

            return $response;
        }
    }

    /**
     * Dijalankan SETELAH controller selesai memproses request.
     * Menambahkan header CORS ke response normal (GET/POST/PUT/DELETE).
     *
     * @param array|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $origin      = $request->getHeaderLine('Origin');
        $allowOrigin = $this->resolveAllowedOrigin($origin);

        $response->setHeader('Access-Control-Allow-Origin', $allowOrigin)
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept')
            ->setHeader('Access-Control-Allow-Credentials', 'true')
            ->setHeader('Vary', 'Origin');

        return $response;
    }

    /**
     * Mengembalikan origin yang diizinkan jika cocok dengan whitelist,
     * atau string kosong jika tidak (sehingga browser akan menolaknya).
     */
    protected function resolveAllowedOrigin(string $origin): string
    {
        if ($origin !== '' && in_array($origin, $this->allowedOrigins, true)) {
            return $origin;
        }

        // Tidak match whitelist -> kembalikan origin pertama sebagai default
        // aman untuk development. Di production, sebaiknya kembalikan ''.
        return $this->allowedOrigins[0] ?? '';
    }
}
