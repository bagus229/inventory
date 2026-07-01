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
     *   "id_barang": 1,
     *   "user_id": 1,
     *   "jenis_transaksi": "keluar",
     *   "jumlah": 5,
     *   "keterangan": "Penjualan ke pelanggan A"
     * }
     */
    public function create()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $input['id_user'] = 3;

        if (! $this->historiModel->validate($input)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->historiModel->errors());
        }

        $barang = $this->barangModel->find($input['id_barang']);

        if (! $barang) {
            return $this->errorResponse('Barang tidak ditemukan.', 404);
        }

        // Validasi stok cukup jika transaksi keluar
        if ($input['jenis'] === 'keluar' && $barang['stok'] < $input['jumlah']) {
            return $this->errorResponse('Stok barang tidak cukup untuk transaksi keluar ini.', 400);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $historiId = $this->historiModel->insert($input);

        $stokBaru = $input['jenis'] === 'masuk'
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

        $barang = $this->barangModel->find($histori['id_barang']);

        $db = \Config\Database::connect();
        $db->transStart();

        if ($barang) {
            $stokBaru = $histori['jenis'] === 'masuk'
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

    public function update($id = null)
    {
        $histori = $this->historiModel->find($id);

        if (! $histori) {
            return $this->errorResponse('Histori transaksi tidak ditemukan.', 404);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if (! $this->historiModel->validate($input)) {
            return $this->errorResponse(
                'Validasi gagal.',
                422,
                $this->historiModel->errors()
            );
        }

        $barangLama = $this->barangModel->find($histori['id_barang']);

        if (! $barangLama) {
            return $this->errorResponse('Barang tidak ditemukan.', 404);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Rollback stok lama
        if ($histori['jenis'] === 'masuk') {
            $stok = $barangLama['stok'] - $histori['jumlah'];
        } else {
            $stok = $barangLama['stok'] + $histori['jumlah'];
        }

        $this->barangModel->update($barangLama['id'], [
            'stok' => $stok
        ]);

        // Barang baru
        $barangBaru = $this->barangModel->find($input['id_barang']);

        if (! $barangBaru) {
            $db->transRollback();
            return $this->errorResponse('Barang baru tidak ditemukan.', 404);
        }

        // Hitung stok baru
        if ($input['jenis'] === 'masuk') {
            $stokBaru = $barangBaru['stok'] + $input['jumlah'];
        } else {

            if ($barangBaru['stok'] < $input['jumlah']) {
                $db->transRollback();
                return $this->errorResponse(
                    'Stok barang tidak mencukupi.',
                    400
                );
            }

            $stokBaru = $barangBaru['stok'] - $input['jumlah'];
        }

        $this->barangModel->update($barangBaru['id'], [
            'stok' => $stokBaru
        ]);

        $this->historiModel->update($id, $input);

        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->errorResponse(
                'Gagal memperbarui histori.',
                500
            );
        }

        $data = $this->historiModel->getHistoriWithRelasi($id);

        return $this->successResponse(
            $data,
            'Histori berhasil diperbarui.'
        );
    }
}
