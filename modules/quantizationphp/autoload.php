<?php

spl_autoload_register(function ($className) {
    if (strpos($className, 'QuantizationPHP\\') === 0) {
        $file = __DIR__ . '/QuantizationPHP.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
