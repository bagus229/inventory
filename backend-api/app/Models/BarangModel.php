<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table = 'barang';

    protected $allowedFields = [
        'kode_barang',
        'nama_barang',
        'id_kategori',
        'id_supplier',
        'stok',
        'satuan',
        'harga_beli',
        'harga_jual',
        'deskripsi'
    ];

    protected $useTimestamps = true;
}