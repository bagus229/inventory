<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\SupplierModel;
use App\Models\HistoriBarangModel;

class Dashboard extends BaseController
{
    public function summary()
    {
        $barang = new BarangModel();
        $kategori = new KategoriModel();
        $supplier = new SupplierModel();
        $histori = new HistoriBarangModel();

        return $this->response->setJSON([
            'total_barang' => $barang->countAll(),
            'total_kategori' => $kategori->countAll(),
            'total_supplier' => $supplier->countAll(),
            'total_histori' => $histori->countAll(),
            'total_stok' => $barang->selectSum('stok')->first()['stok'] ?? 0
        ]);
    }
}