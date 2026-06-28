<?php

namespace App\Controllers\Api;

use App\Models\KategoriModel;

/**
 * KategoriController
 * ---------------------------------------------------------------------
 * Resource Controller CI4 untuk data master Kategori.
 *
 * Endpoint:
 *   GET    /api/kategori        -> index()   (list semua kategori)
 *   GET    /api/kategori/{id}   -> show($id) (detail 1 kategori)
 *   POST   /api/kategori        -> create()  [PROTECTED by authfilter]
 *   PUT    /api/kategori/{id}   -> update($id) [PROTECTED by authfilter]
 *   DELETE /api/kategori/{id}   -> delete($id) [PROTECTED by authfilter]
 */
class KategoriController extends BaseApiController
{
    protected KategoriModel $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    /**
     * GET /api/kategori
     */
    public function index()
    {
        $data = $this->kategoriModel->orderBy('id', 'DESC')->findAll();

        return $this->successResponse($data, 'Daftar kategori berhasil diambil.');
    }

    /**
     * GET /api/kategori/{id}
     */
    public function show($id = null)
    {
        $kategori = $this->kategoriModel->find($id);

        if (! $kategori) {
            return $this->errorResponse('Kategori tidak ditemukan.', 404);
        }

        return $this->successResponse($kategori, 'Detail kategori berhasil diambil.');
    }

    /**
     * POST /api/kategori
     * Protected: butuh Authorization Bearer Token.
     */
    public function create()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        if (! $this->kategoriModel->validate($input)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->kategoriModel->errors());
        }

        $id = $this->kategoriModel->insert($input);
        $kategori = $this->kategoriModel->find($id);

        return $this->successResponse($kategori, 'Kategori berhasil ditambahkan.', 201);
    }

    /**
     * PUT /api/kategori/{id}
     * Protected: butuh Authorization Bearer Token.
     */
    public function update($id = null)
    {
        $kategori = $this->kategoriModel->find($id);

        if (! $kategori) {
            return $this->errorResponse('Kategori tidak ditemukan.', 404);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if (! $this->kategoriModel->validate($input)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->kategoriModel->errors());
        }

        $this->kategoriModel->update($id, $input);
        $updated = $this->kategoriModel->find($id);

        return $this->successResponse($updated, 'Kategori berhasil diperbarui.');
    }

    /**
     * DELETE /api/kategori/{id}
     * Protected: butuh Authorization Bearer Token.
     */
    public function delete($id = null)
    {
        $kategori = $this->kategoriModel->find($id);

        if (! $kategori) {
            return $this->errorResponse('Kategori tidak ditemukan.', 404);
        }

        // Cegah hapus kategori yang masih dipakai oleh barang (FK RESTRICT)
        $usedByBarang = (new \App\Models\BarangModel())->where('id_kategori', $id)->countAllResults();
        if ($usedByBarang > 0) {
            return $this->errorResponse('Kategori tidak dapat dihapus karena masih digunakan oleh data barang.', 409);
        }

        $this->kategoriModel->delete($id);

        return $this->successResponse(null, 'Kategori berhasil dihapus.');
    }
}
