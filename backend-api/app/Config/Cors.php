<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Cross-Origin Resource Sharing (CORS) Configuration
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
 */
class Cors extends BaseConfig
{
    public array $default = [
        'allowedOrigins' => [
            'https://uas-web2-312410382-bagus-aditya-her.vercel.app',
        ],

        'allowedOriginsPatterns' => [
            '.*',
        ],

        'supportsCredentials' => false,

        /**
         * PENTING: harus didaftarkan eksplisit satu per satu.
         * Wildcard '*' tidak ditafsirkan sebagai "semua header" oleh
         * CORS filter CodeIgniter saat mengecek Access-Control-Request-Headers,
         * sehingga preflight request dengan header Content-Type / Authorization
         * akan ditolak kalau tidak didaftarkan di sini.
         */
        'allowedHeaders' => [
            'Content-Type',
            'Authorization',
            'X-Requested-With',
            'Accept',
            'Origin',
        ],

        'exposedHeaders' => [],

        'allowedMethods' => [
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'OPTIONS',
        ],

        'maxAge' => 7200,
    ];
}