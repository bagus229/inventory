<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriModel extends Model
{
    protected $table         = 'histori_barang';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'barang_id',
        'user_id',
        'jenis_transaksi',
        'jumlah',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'barang_id'       => 'required|integer|is_not_unique[barang.id]',
        'user_id'         => 'required|integer|is_not_unique[users.id]',
        'jenis_transaksi' => 'required|in_list[masuk,keluar]',
        'jumlah'          => 'required|integer|greater_than[0]',
        'keterangan'      => 'permit_empty|string',
    ];

    /**
     * Ambil histori transaksi lengkap dengan nama barang & nama user (JOIN).
     */
    public function getHistoriWithRelasi(?int $id = null)
    {
        $builder = $this->select('
                histori_transaksi.*,
                barang.nama_barang,
                barang.kode_barang,
                users.nama as nama_user
            ')
            ->join('barang', 'barang.id = histori_transaksi.barang_id')
            ->join('users', 'users.id = histori_transaksi.user_id')
            ->orderBy('histori_transaksi.created_at', 'DESC');

        if ($id !== null) {
            return $builder->where('histori_transaksi.id', $id)->first();
        }

        return $builder->findAll();
    }
}
