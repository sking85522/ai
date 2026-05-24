<?php

spl_autoload_register(function ($className) {
    if (strpos($className, 'LoRAPHP\\') === 0) {
        $file = __DIR__ . '/LoRAPHP.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
