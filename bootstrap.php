<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require_once __DIR__ . '/src/Support/helpers.php';

if (!defined('BASE_URL')) {
    $publicFsPath = str_replace('\\', '/', realpath(__DIR__ . '/public'));
    $scriptFsPath = str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME']));
    $relative = substr($scriptFsPath, strlen($publicFsPath));
    $scriptUrlPath = $_SERVER['SCRIPT_NAME'];
    $base = substr($scriptUrlPath, 0, strlen($scriptUrlPath) - strlen($relative));
    define('BASE_URL', rtrim($base, '/'));
}
