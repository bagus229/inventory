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
            ->select('h.*, b.nama_barang, u.nama as nama_user')
            ->join('barang b', 'b.id = h.id_barang')
            ->join('users u', 'u.id = id_user')
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

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        $barangModel = new BarangModel();

        // Ambil histori lama dulu, sebelum diubah
        $historiLama = $this->model->find($id);

        if (!$historiLama) {
            return $this->failNotFound('Histori tidak ditemukan');
        }

        // Ambil data barang LAMA (sebelum diedit) untuk membalikkan efek lama
        $barangLama = $barangModel->find($historiLama['id_barang']);

        if (!$barangLama) {
            return $this->failNotFound('Barang tidak ditemukan');
        }

        // 1. Balikkan efek histori LAMA ke stok barang lama
        if ($historiLama['jenis'] == 'masuk') {
            $stokSetelahDibalikkan = $barangLama['stok'] - $historiLama['jumlah'];
        } else {
            $stokSetelahDibalikkan = $barangLama['stok'] + $historiLama['jumlah'];
        }
        $barangModel->update($historiLama['id_barang'], ['stok' => $stokSetelahDibalikkan]);

        // 2. Ambil data barang BARU (mungkin id_barang berubah saat edit, mungkin juga sama)
        $idBarangBaru = $data['id_barang'] ?? $historiLama['id_barang'];
        $barangBaru = $barangModel->find($idBarangBaru);

        if (!$barangBaru) {
            return $this->failNotFound('Barang baru tidak ditemukan');
        }

        // 3. Terapkan efek histori BARU ke stok barang baru
        if ($data['jenis'] == 'masuk') {
            $stokBaru = $barangBaru['stok'] + $data['jumlah'];
        } else {
            if ($barangBaru['stok'] < $data['jumlah']) {
                return $this->fail(['message' => 'Stok tidak mencukupi']);
            }
            $stokBaru = $barangBaru['stok'] - $data['jumlah'];
        }
        $barangModel->update($idBarangBaru, ['stok' => $stokBaru]);

        // 4. Update record histori dengan data baru
        $data['id_barang'] = $idBarangBaru;
        $this->model->update($id, $data);

        return $this->respond(['message' => 'Histori berhasil diubah']);
    }

    public function delete($id = null)
    {
        $barangModel = new BarangModel();

        // Ambil histori yang mau dihapus
        $histori = $this->model->find($id);

        if (!$histori) {
            return $this->failNotFound('Histori tidak ditemukan');
        }

        $barang = $barangModel->find($histori['id_barang']);

        if ($barang) {
            // Balikkan efek histori ke stok (kebalikan dari saat dibuat)
            if ($histori['jenis'] == 'masuk') {
                $stokBaru = $barang['stok'] - $histori['jumlah'];
            } else {
                $stokBaru = $barang['stok'] + $histori['jumlah'];
            }
            $barangModel->update($histori['id_barang'], ['stok' => $stokBaru]);
        }

        $this->model->delete($id);
        return $this->respond(['message' => 'Histori berhasil dihapus']);
    }
}
