<?php

spl_autoload_register(function ($className) {
    if (strpos($className, 'EvalPHP\\') === 0) {
        $file = __DIR__ . '/EvalPHP.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
