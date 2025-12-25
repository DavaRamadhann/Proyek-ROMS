<?php

// Vercel read-only filesystem fix
if (isset($_SERVER['VERCEL'])) {
    $storagePath = '/tmp/storage';
    $dirs = [
        $storagePath . '/framework/views',
        $storagePath . '/framework/cache',
        $storagePath . '/framework/sessions',
        $storagePath . '/logs',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
    putenv("APP_CONFIG_CACHE={$storagePath}/framework/config.php");
    putenv("APP_ROUTES_CACHE={$storagePath}/framework/routes.php");
    putenv("APP_SERVICES_CACHE={$storagePath}/framework/services.php");
    putenv("APP_PACKAGES_CACHE={$storagePath}/framework/packages.php");
    putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");
}

require __DIR__ . '/../public/index.php';
