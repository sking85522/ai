<?php

$config = require __DIR__ . '/config/config.php';
$dbConfig = require __DIR__ . '/config/database.php';

spl_autoload_register(function ($class): void {
    static $classMap = null;
    if ($classMap === null) {
        $classMap = [];
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__ . '/core', FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }
            $path = $fileInfo->getPathname();
            if (substr($path, -4) !== '.php') {
                continue;
            }
            $name = pathinfo($path, PATHINFO_FILENAME);
            if (!isset($classMap[$name])) {
                $classMap[$name] = $path;
            }
        }
    }

    $paths = [
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/core/NLP/' . $class . '.php',
        __DIR__ . '/core/ML/' . $class . '.php',
        __DIR__ . '/core/DL/' . $class . '.php',
        __DIR__ . '/core/Memory/' . $class . '.php',
        __DIR__ . '/core/Response/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }

    if (isset($classMap[$class]) && is_file($classMap[$class])) {
        require_once $classMap[$class];
    }
});
