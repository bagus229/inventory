<?php

namespace App\Controllers\Api;

use App\Models\BarangModel;
use App\Models\HistoriModel;

/**
 * BarangController
 * ---------------------------------------------------------------------
 * Resource Controller CI4 untuk data master Barang.
 * Setiap barang berelasi ke 1 kategori dan 1 supplier (Many-to-One).
 *
 * Endpoint:
 *   GET    /api/barang        -> index()   (list barang + nama kategori & supplier)
 *   GET    /api/barang/{id}   -> show($id) (detail barang + relasi)
 *   POST   /api/barang        -> create()  [PROTECTED]
 *   PUT    /api/barang/{id}   -> update($id) [PROTECTED]
 *   DELETE /api/barang/{id}   -> delete($id) [PROTECTED]
 */
class BarangController extends BaseApiController
{
    protected BarangModel $barangModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
    }

    /**
     * GET /api/barang
     * Mengembalikan seluruh barang dengan JOIN ke kategori & supplier.
     */
    public function index()
    {
        $data = $this->barangModel->getBarangWithRelasi();

        return $this->successResponse($data, 'Daftar barang berhasil diambil.');
    }

    /**
     * GET /api/barang/{id}
     */
    public function show($id = null)
    {
        $barang = $this->barangModel->getBarangWithRelasi((int) $id);

        if (! $barang) {
            return $this->errorResponse('Barang tidak ditemukan.', 404);
        }

        return $this->successResponse($barang, 'Detail barang berhasil diambil.');
    }

    /**
     * POST /api/barang
     * Protected: butuh Authorization Bearer Token.
     * Body contoh:
     * {
     *   "id_kategori": 1,
     *   "id_supplier": 1,
     *   "kode_barang": "BRG-010",
     *   "nama_barang": "Mouse Wireless",
     *   "harga_beli": 50000,
     *   "harga_jual": 75000,
     *   "stok": 20,
     *   "satuan": "pcs"
     * }
     */
    public function create()
{
    try {

        $input = $this->request->getJSON(true);

        if (! $this->barangModel->validate($input)) {
            return $this->respond([
                'validation' => $this->barangModel->errors()
            ], 422);
        }

        $id = $this->barangModel->insert($input);

        if ($id === false) {
            return $this->respond([
                'db_error' => $this->barangModel->errors(),
                'database_error' => $this->barangModel->db->error()
            ], 500);
        }

        return $this->respond([
            'success' => true,
            'id' => $id
        ]);

    } catch (\Throwable $e) {

        return $this->respond([
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);

    }
}

    /**
     * PUT /api/barang/{id}
     * Protected: butuh Authorization Bearer Token.
     */
    public function update($id = null)
    {
        $barang = $this->barangModel->find($id);

        if (! $barang) {
            return $this->errorResponse('Barang tidak ditemukan.', 404);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getRawInput();
        return $this->respond($input);

        // Saat update, kode_barang boleh sama dengan data lama (handled oleh rule is_unique[...,id,{id}])
        $rules = $this->barangModel->getValidationRules();
        $rules['kode_barang'] = "required|max_length[30]|is_unique[barang.kode_barang,id,{$id}]";

        if (! $this->validateData($input, $rules)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->validator->getErrors());
        }

        $this->barangModel->update($id, $input);
        $updated = $this->barangModel->getBarangWithRelasi((int) $id);

        return $this->successResponse($updated, 'Barang berhasil diperbarui.');
    }

    /**
     * DELETE /api/barang/{id}
     * Protected: butuh Authorization Bearer Token.
     */
    public function delete($id = null)
    {
        $barang = $this->barangModel->find($id);

        if (! $barang) {
            return $this->errorResponse('Barang tidak ditemukan.', 404);
        }

        // Karena FK histori_barang.barang_id -> barang.id bersifat CASCADE,
        // menghapus barang otomatis menghapus histori transaksinya juga.
        $this->barangModel->delete($id);

        return $this->successResponse(null, 'Barang berhasil dihapus.');
    }
}
