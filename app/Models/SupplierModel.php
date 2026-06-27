<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table         = 'supplier';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nama_supplier', 'alamat', 'telepon', 'email'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama_supplier' => 'required|min_length[3]|max_length[150]',
        'telepon'       => 'permit_empty|max_length[20]',
        'email'         => 'permit_empty|valid_email',
    ];
}
