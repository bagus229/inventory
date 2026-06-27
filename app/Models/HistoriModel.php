<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriModel extends Model
{
    protected $table         = 'histori_barang';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_barang',
        'id_user',
        'jenis',
        'jumlah',
        'keterangan',
        'tanggal',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_barang'       => 'required|integer|is_not_unique[barang.id]',
        'id_user'         => 'required|integer|is_not_unique[users.id]',
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
                histori_builder.*,
                barang.nama_barang,
                barang.kode_barang,
                users.nama as nama_user
            ')
            ->join('barang', 'barang.id = histori_barang.barang_id')
            ->join('users', 'users.id = histori_barang.user_id')
            ->orderBy('histori_barang.created_at', 'DESC');

        if ($id !== null) {
            return $builder->where('histori_barang.id', $id)->first();
        }

        return $builder->findAll();
    }
}
