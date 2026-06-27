<?php

namespace App\Controllers\Api;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\SupplierModel;
use App\Models\HistoriModel;

/**
 * DashboardController
 * ---------------------------------------------------------------------
 * Endpoint non-CRUD khusus untuk mengisi data ringkasan di halaman
 * Dashboard.js pada frontend (total barang, total kategori, total
 * supplier, barang dengan stok menipis, dan transaksi terbaru).
 *
 * GET /api/dashboard/summary  (publik, hanya GET / read-only)
 */
class DashboardController extends BaseApiController
{
    public function summary()
    {
        $barangModel    = new BarangModel();
        $kategoriModel  = new KategoriModel();
        $supplierModel  = new SupplierModel();
        $historiModel   = new HistoriModel();

        $totalBarang   = $barangModel->countAllResults();
        $totalKategori = $kategoriModel->countAllResults();
        $totalSupplier = $supplierModel->countAllResults();

        // Barang dengan stok di bawah 10 dianggap menipis
        $stokMenipis = $barangModel->getBarangWithRelasi();
        $stokMenipis = array_values(array_filter($stokMenipis, static fn ($b) => (int) $b['stok'] <= 10));

        $transaksiTerbaru = array_slice($historiModel->getHistoriWithRelasi(), 0, 5);

        return $this->successResponse([
            'total_barang'       => $totalBarang,
            'total_kategori'     => $totalKategori,
            'total_supplier'     => $totalSupplier,
            'barang_stok_menipis'=> $stokMenipis,
            'transaksi_terbaru'  => $transaksiTerbaru,
        ], 'Ringkasan dashboard berhasil diambil.');
    }
}
