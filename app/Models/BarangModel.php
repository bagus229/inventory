<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table         = 'barang';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_kategori',
        'id_supplier',
        'kode_barang',
        'nama_barang',
        'harga_beli',
        'harga_jual',
        'stok',
        'satuan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_kategori' => 'required|integer|is_not_unique[kategori.id]',
        'id_supplier' => 'required|integer|is_not_unique[supplier.id]',
        'kode_barang' => 'required|max_length[30]|is_unique[barang.kode_barang,id,{id}]',
        'nama_barang' => 'required|min_length[3]|max_length[150]',
        'harga_beli'  => 'permit_empty|decimal',
        'harga_jual'  => 'permit_empty|decimal',
        'stok'        => 'permit_empty|integer',
        'satuan'      => 'permit_empty|max_length[20]',
    ];

    protected $validationMessages = [
        'id_kategori' => [
            'is_not_unique' => 'Kategori yang dipilih tidak ditemukan.',
        ],
        'id_supplier' => [
            'is_not_unique' => 'Supplier yang dipilih tidak ditemukan.',
        ],
        'kode_barang' => [
            'is_unique' => 'Kode barang sudah digunakan.',
        ],
    ];

    /**
     * Ambil semua barang lengkap dengan nama kategori & nama supplier (JOIN).
     * Dipakai oleh BarangController::index() & show() agar response API
     * langsung berisi data relasinya, tidak hanya foreign key id saja.
     */
    public function getBarangWithRelasi(?int $id = null)
    {
        $builder = $this->select('
                barang.*,
                kategori.nama_kategori,
                supplier.nama_supplier
            ')
            ->join('kategori', 'kategori.id = barang.id_kategori')
            ->join('supplier', 'supplier.id = barang.id_supplier');

        if ($id !== null) {
            return $builder->where('barang.id', $id)->first();
        }

        return $builder->findAll();
    }
}
