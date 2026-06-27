<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use App\Filters\CorsFilter;
use App\Filters\AuthFilter;

class Filters extends BaseFilters
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,

        // Filter custom kita:
        'cors'       => CorsFilter::class,
        'authfilter' => AuthFilter::class,
    ];

    /**
     * PENTING: 'cors' SENGAJA TIDAK dimasukkan ke $required di sini,
     * supaya tidak ada duplikasi/konflik urutan eksekusi dengan
     * CorsFilter milik kita sendiri yang didaftarkan lewat $globals
     * di bawah. PageCache tetap dipertahankan karena bagian fungsi
     * inti framework.
     */
    public array $required = [
        'before' => [
            'pagecache',
        ],
        'after' => [
            'pagecache',
            'performance',
            'toolbar',
        ],
    ];

    public array $globals = [
        'before' => [
            'cors', // <-- CorsFilter custom kita, jalan global di awal
        ],
        'after' => [
            'cors', // <-- pastikan header CORS juga ditambahkan setelah response selesai
        ],
    ];

    public array $methods = [
        'post'   => ['authfilter'],
        'put'    => ['authfilter'],
        'patch'  => ['authfilter'],
        'delete' => ['authfilter'],
    ];

    public array $filters = [];
}