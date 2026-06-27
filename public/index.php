<?php

// --------------------------------------------------------------------
// FORCE CORS BYPASS (Letakkan tepat di bawah tag <?php )
// --------------------------------------------------------------------
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Mengizinkan domain Vercel Anda secara spesifik
    $allowed_origins = [
        'https://uas-web2-312410382-bagus-aditya-her.vercel.app/#/'
    ];

    if (in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
        header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Credentials: true");
    }
}

// Jika browser mengirim Preflight (OPTIONS), langsung jawab di sini tanpa masuk ke core CI4
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0); // Matikan eksekusi, kirim status 200 ke browser
}
// --------------------------------------------------------------------


// ... Sisa kode bawaan public/index.php CI4 Anda (seperti baris di bawah ini) ...
// define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
// ... dst


use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );


    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
