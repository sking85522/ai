<?php

spl_autoload_register(function ($className) {
    if (strpos($className, 'TransformersPHP\\') === 0) {
        // Simple mapping, since we defined everything in TransformersPHP.php
        $file = __DIR__ . '/TransformersPHP.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
