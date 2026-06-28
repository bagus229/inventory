<?php

namespace App\Controllers\Api;

use App\Models\SupplierModel;
use App\Models\BarangModel;

/**
 * SupplierController
 * ---------------------------------------------------------------------
 * Resource Controller CI4 untuk data master Supplier.
 *
 * Endpoint:
 *   GET    /api/supplier        -> index()
 *   GET    /api/supplier/{id}   -> show($id)
 *   POST   /api/supplier        -> create()  [PROTECTED]
 *   PUT    /api/supplier/{id}   -> update($id) [PROTECTED]
 *   DELETE /api/supplier/{id}   -> delete($id) [PROTECTED]
 */
class SupplierController extends BaseApiController
{
    protected SupplierModel $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
    }

    public function index()
    {
        $data = $this->supplierModel->orderBy('id', 'DESC')->findAll();

        return $this->successResponse($data, 'Daftar supplier berhasil diambil.');
    }

    public function show($id = null)
    {
        $supplier = $this->supplierModel->find($id);

        if (! $supplier) {
            return $this->errorResponse('Supplier tidak ditemukan.', 404);
        }

        return $this->successResponse($supplier, 'Detail supplier berhasil diambil.');
    }

    public function create()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        if (! $this->supplierModel->validate($input)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->supplierModel->errors());
        }

        $id = $this->supplierModel->insert($input);
        $supplier = $this->supplierModel->find($id);

        return $this->successResponse($supplier, 'Supplier berhasil ditambahkan.', 201);
    }

    public function update($id = null)
    {
        $supplier = $this->supplierModel->find($id);

        if (! $supplier) {
            return $this->errorResponse('Supplier tidak ditemukan.', 404);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if (! $this->supplierModel->validate($input)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->supplierModel->errors());
        }

        $this->supplierModel->update($id, $input);
        $updated = $this->supplierModel->find($id);

        return $this->successResponse($updated, 'Supplier berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $supplier = $this->supplierModel->find($id);

        if (! $supplier) {
            return $this->errorResponse('Supplier tidak ditemukan.', 404);
        }

        $usedByBarang = (new BarangModel())->where('id_supplier', $id)->countAllResults();
        if ($usedByBarang > 0) {
            return $this->errorResponse('Supplier tidak dapat dihapus karena masih digunakan oleh data barang.', 409);
        }

        $this->supplierModel->delete($id);

        return $this->successResponse(null, 'Supplier berhasil dihapus.');
    }
}
