<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['nama', 'username', 'password', 'role'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama'     => 'required|min_length[3]|max_length[100]',
        'username' => 'required|min_length[4]|max_length[50]|is_unique[users.username,id,{id}]',
        'password' => 'permit_empty|min_length[6]',
        'role'     => 'permit_empty|in_list[admin,staff]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Username sudah digunakan, silakan pilih username lain.',
        ],
    ];

    /**
     * Cari user berdasarkan username, dipakai saat proses login.
     */
    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }
}
