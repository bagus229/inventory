<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        $model = new UserModel();

        $email = $this->request->getJSON()->email;
        $password = $this->request->getJSON()->password;

        $user = $model
            ->where('email', $email)
            ->first();

        if (!$user) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'message' => 'Email tidak ditemukan'
                ]);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'message' => 'Password salah'
                ]);
        }

        $token = bin2hex(random_bytes(32));

        $model->update(
            $user['id'],
            ['token' => $token]
        );

        return $this->response->setJSON([
            'token' => $token
        ]);
    }

    public function logout()
    {
        $user = $this->request->user;
        $model = new UserModel();
        $model->update(
            $user['id'],
            ['token' => null]
        );

        return $this->response->setJSON([
            'message' => 'Logout berhasil'
        ]);
    }
}