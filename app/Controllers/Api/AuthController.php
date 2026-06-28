<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use Firebase\JWT\JWT;

/**
 * AuthController
 * ---------------------------------------------------------------------
 * Menangani autentikasi: login (generate JWT token), register user baru,
 * logout, dan mengambil data user yang sedang login (me).
 *
 * Endpoint login & register bersifat PUBLIK (tidak butuh token), karena
 * memang di sinilah token itu pertama kali diterbitkan.
 */
class AuthController extends BaseApiController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * POST /api/login
     * Body:
     * {
     *   "email": "admin@gmail.com",
     *   "password": "admin123"
     * }
     */
    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return $this->errorResponse('Validasi gagal', 422, $this->validator->getErrors());
        }

        $email    = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->userModel
            ->where('email', $email)
            ->first();

        if (! $user) {
            return $this->errorResponse('Email atau password salah.', 401);
        }

        if (! password_verify($password, $user['password'])) {
            return $this->errorResponse('Email atau password salah.', 401);
        }

        $payload = [
            'iss' => getenv('JWT_ISSUER') ?: 'inventory-api',
            'iat' => time(),
            'exp' => time() + (int)(getenv('JWT_EXPIRE_SECONDS') ?: 3600),
            'data' => [
                'id'    => $user['id'],
                'nama'  => $user['nama'],
                'email' => $user['email'],
            ],
        ];

        $secretKey = getenv('JWT_SECRET_KEY') ?: env('JWT_SECRET_KEY');
        $token = JWT::encode($payload, $secretKey, 'HS256');

        unset($user['password']);

        return $this->successResponse([
            'token'      => $token,
            'token_type' => 'Bearer',
            'expires_in' => (int)(getenv('JWT_EXPIRE_SECONDS') ?: 3600),
            'user'       => $user,
        ], 'Login berhasil.');
    }

    /**
     * POST /api/register
     * Body: { "nama": "...", "username": "...", "password": "...", "role": "staff" }
     */
    public function register()
    {
        $rules = [
            'nama'     => 'required|min_length[3]|max_length[100]',
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'role'     => 'permit_empty|in_list[admin,staff]',
        ];

        if (! $this->validate($rules)) {
            return $this->errorResponse('Validasi gagal', 422, $this->validator->getErrors());
        }

        $id = $this->userModel->insert([
            'nama'     => $this->request->getVar('nama'),
            'username' => $this->request->getVar('username'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getVar('role') ?: 'staff',
        ]);

        $user = $this->userModel->find($id);
        unset($user['password']);

        return $this->successResponse($user, 'Registrasi berhasil.', 201);
    }

    /**
     * GET /api/me
     * Mengambil data user yang sedang login berdasarkan token Bearer.
     * Dilindungi authfilter.
     */
    public function me()
    {
        $userPayload = $this->request->user->data ?? null;

        if (! $userPayload) {
            return $this->errorResponse('User tidak ditemukan dari token.', 401);
        }

        $user = $this->userModel->find($userPayload->id);
        unset($user['password']);

        return $this->successResponse($user, 'Data user berhasil diambil.');
    }

    /**
     * POST /api/logout
     * Karena JWT bersifat stateless, "logout" cukup diserahkan ke sisi
     * frontend untuk menghapus token yang tersimpan (misal di localStorage).
     */
    public function logout()
    {
        return $this->successResponse(null, 'Logout berhasil. Silakan hapus token di sisi client.');
    }
}
