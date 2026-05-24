<?php

spl_autoload_register(function ($className) {
    if (strpos($className, 'AutogradPHP\\') === 0) {
        $file = __DIR__ . '/AutogradPHP.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
