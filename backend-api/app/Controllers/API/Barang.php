<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Barang extends ResourceController
{
    protected $modelName = 'App\Models\BarangModel';
    protected $format = 'json';

    public function index()
{
    $db = \Config\Database::connect();

    $data = $db->table('barang b')
        ->select('b.*, k.nama_kategori, s.nama_supplier')
        ->join('kategori k', 'k.id = b.id_kategori')
        ->join('supplier s', 's.id = b.id_supplier')
        ->orderBy('b.id', 'DESC')
        ->get()
        ->getResult();

    return $this->respond($data);
}

    public function create()
    {
        $data = $this->request->getJSON(true);

        $this->model->insert($data);

        return $this->respondCreated([
            'message' => 'Barang berhasil ditambahkan'
        ]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        $this->model->update($id, $data);

        return $this->respond([
            'message' => 'Barang berhasil diupdate'
        ]);
    }

    public function delete($id = null)
    {
        $this->model->delete($id);

        return $this->respond([
            'message' => 'Barang berhasil dihapus'
        ]);
    }
}