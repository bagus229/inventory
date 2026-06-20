<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\HistoriBarangModel;
use App\Models\BarangModel;

class HistoriBarang extends ResourceController
{
    protected $modelName = 'App\Models\HistoriBarangModel';
    protected $format = 'json';

    public function index()
    {
        $db = db_connect();

        $data = $db->table('histori_barang h')
            ->select('h.*, b.nama_barang, u.nama')
            ->join('barang b', 'b.id = h.id_barang')
            ->join('users u', 'u.id = h.id_user')
            ->orderBy('h.id', 'DESC')
            ->get()
            ->getResult();

        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $data['id_user'] = $this->request->user['id'];
        $data['tanggal'] = $data['tanggal'] ?? date('Y-m-d');

        // Cek barang ada atau tidak
        $barangModel = new BarangModel();
        $barang = $barangModel->find($data['id_barang']);

        if (!$barang) {
            return $this->failNotFound('Barang tidak ditemukan');
        }

        // Cek dan hitung stok baru
        if ($data['jenis'] == 'masuk') {
            $stokBaru = $barang['stok'] + $data['jumlah'];
        } else {
            if ($barang['stok'] < $data['jumlah']) {
                return $this->fail(['message' => 'Stok tidak mencukupi']);
            }
            $stokBaru = $barang['stok'] - $data['jumlah'];
        }

        // Update stok barang
        $barangModel->update($data['id_barang'], ['stok' => $stokBaru]);

        // Simpan histori
        $this->model->insert($data);

        return $this->respondCreated(['message' => 'Histori berhasil ditambahkan']);
    }

    public function delete($id = null)
    {
        $this->model->delete($id);
        return $this->respond(['message' => 'Histori berhasil dihapus']);
    }
}