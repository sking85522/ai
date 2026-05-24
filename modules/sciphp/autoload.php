<?php

spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'SciPHP\\';

    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});