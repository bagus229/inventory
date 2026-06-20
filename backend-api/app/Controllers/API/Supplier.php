<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Supplier extends ResourceController
{
    protected $modelName = 'App\Models\SupplierModel';
    protected $format = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        return $this->respond($this->model->find($id));
    }

    public function create()
    {
        $this->model->insert(
            $this->request->getJSON(true)
        );

        return $this->respondCreated([
            'message'=>'Supplier berhasil ditambah'
        ]);
    }

    public function update($id = null)
    {
        $this->model->update(
            $id,
            $this->request->getJSON(true)
        );

        return $this->respond([
            'message'=>'Supplier berhasil diupdate'
        ]);
    }

    public function delete($id = null)
    {
        $this->model->delete($id);

        return $this->respond([
            'message'=>'Supplier berhasil dihapus'
        ]);
    }
}