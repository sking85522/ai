<?php
/**
 * HRITIK AI - SYSTEMIC BOOTSTRAP
 * Centralized autoloader and core system initialization.
 */

spl_autoload_register(function ($class) {
    $class = ltrim($class, '\\');
    $root = realpath(__DIR__ . '/..');
    
    // 1. PSR-4 for Core Namespace
    if (stripos($class, 'Core\\') === 0) {
        $relativeClass = substr($class, 5); 
        $file = $root . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // 2. Mapping for Scientific/AI Modules in /modules/
    $moduleDir = $root . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR;
    $mappings = [
        'NeuralPHP\\'  => 'neuralphp/src/',
        'NumPHP\\'     => 'numphp/src/',
        'SciPHP\\'     => 'sciphp/src/',
        'NLPHP\\'      => 'nlphp/src/',
        'PandaPHP\\'   => 'pandaphp/src/',
        'DatasetPHP\\' => 'datasetphp/src/',
        'PHPTorch\\'   => 'phptorch/src/',
    ];

    $singleFileModules = [
        'TokenizerPHP\\'    => 'tokenizerphp/TokenizerPHP.php',
        'TransformersPHP\\' => 'transformersphp/TransformersPHP.php',
        'AutogradPHP\\'     => 'autogradphp/AutogradPHP.php',
        'OptimizersPHP\\'   => 'optimizersphp/OptimizersPHP.php',
        'DatasetsPHP\\'     => 'datasetsphp/DatasetsPHP.php',
        'QuantizationPHP\\' => 'quantizationphp/QuantizationPHP.php',
        'LoRAPHP\\'         => 'loraphp/LoRAPHP.php',
        'EvalPHP\\'         => 'evalphp/EvalPHP.php',
    ];

    if ($class === 'torch') {
        $file = $moduleDir . 'phptorch/PHPTorch.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    foreach ($singleFileModules as $prefix => $path) {
        if (strpos($class, $prefix) === 0) {
            $file = $moduleDir . $path;
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }

    foreach ($mappings as $prefix => $path) {
        if (strpos($class, $prefix) === 0) {
            $relativeClass = str_replace($prefix, '', $class);
            $file = $moduleDir . $path . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }

    // 3. Fallback for main module classes (e.g. NumPHP\NumPHP)
    $parts = explode('\\', $class);
    if (count($parts) >= 2) {
        $modName = strtolower($parts[0]);
        $mainClass = $parts[1];
        $file = $moduleDir . $modName . DIRECTORY_SEPARATOR . $mainClass . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
