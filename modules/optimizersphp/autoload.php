<?php

spl_autoload_register(function ($className) {
    if (strpos($className, 'OptimizersPHP\\') === 0) {
        $file = __DIR__ . '/OptimizersPHP.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
