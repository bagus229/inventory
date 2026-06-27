<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

/**
 * AuthFilter
 * ---------------------------------------------------------------------
 * Filter ini bertugas memproteksi endpoint yang melakukan manipulasi
 * data (POST, PUT, PATCH, DELETE) sesuai requirement "Server-Side
 * Security (Token)".
 *
 * Cara kerja:
 *  1. Mengambil header "Authorization" dari request.
 *  2. Memvalidasi format header harus "Bearer <token>".
 *  3. Men-decode & memverifikasi JWT menggunakan secret key di .env.
 *  4. Jika valid -> request diteruskan ke controller, dan payload user
 *     disimpan di request (bisa diakses controller via $request->user).
 *  5. Jika tidak valid / tidak ada / expired -> langsung dihentikan
 *     dengan response 401 Unauthorized berformat JSON, TANPA pernah
 *     mencapai controller / database.
 */
class AuthFilter implements FilterInterface
{
    /**
     * @param array|null $arguments
     * @return ResponseInterface|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader) || stripos($authHeader, 'Bearer ') !== 0) {
            return $this->unauthorizedResponse('Header Authorization Bearer Token tidak ditemukan.');
        }

        // Ambil token setelah kata "Bearer "
        $token = trim(substr($authHeader, 7));

        if ($token === '') {
            return $this->unauthorizedResponse('Token kosong.');
        }

        try {
            $secretKey = getenv('JWT_SECRET_KEY') ?: env('JWT_SECRET_KEY');

            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // Simpan payload user yang sudah terverifikasi ke object request,
            // sehingga bisa diakses di controller lewat $this->request->user
            $request->user = $decoded;
        } catch (ExpiredException $e) {
            return $this->unauthorizedResponse('Token sudah expired, silakan login kembali.');
        } catch (SignatureInvalidException $e) {
            return $this->unauthorizedResponse('Signature token tidak valid.');
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Token tidak valid: ' . $e->getMessage());
        }
    }

    /**
     * @param array|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada proses khusus setelah controller dijalankan.
        return $response;
    }

    /**
     * Helper untuk membuat response 401 Unauthorized berformat JSON konsisten.
     */
    protected function unauthorizedResponse(string $message): ResponseInterface
    {
        $response = service('response');

        return $response->setStatusCode(401)->setJSON([
            'status'  => false,
            'message' => $message,
        ]);
    }
}
