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
     *   "kategori_id": 1,
     *   "supplier_id": 1,
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
        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        if (! $this->barangModel->validate($input)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->barangModel->errors());
        }

        $id = $this->barangModel->insert($input);
        $barang = $this->barangModel->getBarangWithRelasi($id);

        return $this->successResponse($barang, 'Barang berhasil ditambahkan.', 201);
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

        // Karena FK histori_transaksi.barang_id -> barang.id bersifat CASCADE,
        // menghapus barang otomatis menghapus histori transaksinya juga.
        $this->barangModel->delete($id);

        return $this->successResponse(null, 'Barang berhasil dihapus.');
    }
}
