<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriBarangModel extends Model
{
    protected $table = 'histori_barang';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_barang',
        'id_user',
        'jenis',
        'jumlah',
        'keterangan',
        'tanggal'
    ];

    protected $useTimestamps = true;
}