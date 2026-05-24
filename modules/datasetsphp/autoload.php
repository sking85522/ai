<?php

spl_autoload_register(function ($className) {
    if (strpos($className, 'DatasetsPHP\\') === 0) {
        $file = __DIR__ . '/DatasetsPHP.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
