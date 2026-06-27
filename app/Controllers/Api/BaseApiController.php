<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

/**
 * BaseApiController
 * ---------------------------------------------------------------------
 * Parent class untuk semua controller API agar punya format response
 * JSON yang konsisten di seluruh endpoint (status, message, data).
 */
class BaseApiController extends ResourceController
{
    use ResponseTrait;

    protected $format = 'json';

    protected function successResponse($data = null, string $message = 'Berhasil', int $code = 200)
    {
        return $this->respond([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function errorResponse(string $message = 'Terjadi kesalahan', int $code = 400, $errors = null)
    {
        return $this->respond([
            'status'  => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }
}
