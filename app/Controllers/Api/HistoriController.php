<?php

namespace App\Controllers\Api;

use App\Models\HistoriModel;
use App\Models\BarangModel;

/**
 * HistoriController
 * ---------------------------------------------------------------------
 * Resource Controller CI4 untuk Histori Transaksi keluar/masuk barang.
 * Setiap histori berelasi ke 1 barang dan 1 user (siapa yang melakukan
 * transaksi).
 *
 * Saat transaksi baru dibuat (create), stok barang terkait akan ikut
 * diperbarui secara otomatis (jika "masuk" -> stok bertambah,
 * jika "keluar" -> stok berkurang) dalam satu DB transaction agar tetap
 * konsisten.
 *
 * Endpoint:
 *   GET    /api/histori        -> index()
 *   GET    /api/histori/{id}   -> show($id)
 *   POST   /api/histori        -> create()  [PROTECTED]
 *   DELETE /api/histori/{id}   -> delete($id) [PROTECTED] (otomatis rollback stok)
 */
class HistoriController extends BaseApiController
{
    protected HistoriModel $historiModel;
    protected BarangModel $barangModel;

    public function __construct()
    {
        $this->historiModel = new HistoriModel();
        $this->barangModel  = new BarangModel();
    }

    public function index()
    {
        $data = $this->historiModel->getHistoriWithRelasi();

        return $this->successResponse($data, 'Daftar histori transaksi berhasil diambil.');
    }

    public function show($id = null)
    {
        $histori = $this->historiModel->getHistoriWithRelasi((int) $id);

        if (! $histori) {
            return $this->errorResponse('Histori transaksi tidak ditemukan.', 404);
        }

        return $this->successResponse($histori, 'Detail histori berhasil diambil.');
    }

    /**
     * POST /api/histori
     * Protected: butuh Authorization Bearer Token.
     * Body contoh:
     * {
     *   "barang_id": 1,
     *   "user_id": 1,
     *   "jenis_transaksi": "keluar",
     *   "jumlah": 5,
     *   "keterangan": "Penjualan ke pelanggan A"
     * }
     */
    public function create()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        if (! $this->historiModel->validate($input)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->historiModel->errors());
        }

        $barang = $this->barangModel->find($input['barang_id']);

        if (! $barang) {
            return $this->errorResponse('Barang tidak ditemukan.', 404);
        }

        // Validasi stok cukup jika transaksi keluar
        if ($input['jenis_transaksi'] === 'keluar' && $barang['stok'] < $input['jumlah']) {
            return $this->errorResponse('Stok barang tidak cukup untuk transaksi keluar ini.', 400);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $historiId = $this->historiModel->insert($input);

        $stokBaru = $input['jenis_transaksi'] === 'masuk'
            ? $barang['stok'] + $input['jumlah']
            : $barang['stok'] - $input['jumlah'];

        $this->barangModel->update($barang['id'], ['stok' => $stokBaru]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->errorResponse('Gagal menyimpan transaksi, silakan coba lagi.', 500);
        }

        $histori = $this->historiModel->getHistoriWithRelasi($historiId);

        return $this->successResponse($histori, 'Histori transaksi berhasil ditambahkan & stok barang diperbarui.', 201);
    }

    /**
     * DELETE /api/histori/{id}
     * Protected: butuh Authorization Bearer Token.
     * Menghapus histori sekaligus mengembalikan (rollback) stok barang
     * seolah-olah transaksi tersebut tidak pernah terjadi.
     */
    public function delete($id = null)
    {
        $histori = $this->historiModel->find($id);

        if (! $histori) {
            return $this->errorResponse('Histori transaksi tidak ditemukan.', 404);
        }

        $barang = $this->barangModel->find($histori['barang_id']);

        $db = \Config\Database::connect();
        $db->transStart();

        if ($barang) {
            $stokBaru = $histori['jenis_transaksi'] === 'masuk'
                ? $barang['stok'] - $histori['jumlah'] // batalkan penambahan
                : $barang['stok'] + $histori['jumlah']; // batalkan pengurangan

            $this->barangModel->update($barang['id'], ['stok' => max(0, $stokBaru)]);
        }

        $this->historiModel->delete($id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->errorResponse('Gagal menghapus histori, silakan coba lagi.', 500);
        }

        return $this->successResponse(null, 'Histori transaksi berhasil dihapus & stok barang dikembalikan.');
    }
}
