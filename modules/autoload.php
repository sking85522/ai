<?php
/**
 * SciPHP Framework v2.1 — High-Performance Lazy Autoloader
 * 
 * Instead of loading all modules on start, this autoloader only
 * triggers when a specific SciPHP module class is instantiated.
 */

spl_autoload_register(function ($className) {
    // 1. Module directory name → Main class name mapping
    $classMap = [
        'NumPHP\NumPHP'        => 'numphp/NumPHP.php',
        'SciPHP\SciPHP'        => 'sciphp/SciPHP.php',
        'NeuralPHP\NeuralPHP'  => 'neuralphp/NeuralPHP.php',
        'NLPHP\NLPHP'          => 'nlphp/NLPHP.php',
        'PandaPHP\PandaPHP'    => 'pandaphp/PandaPHP.php',
        'PandaPHP\DataFrame'   => 'pandaphp/src/DataFrame.php',
        'DatasetPHP\DatasetPHP' => 'datasetphp/DatasetPHP.php',
        'SearchPHP\SearchPHP'  => 'search/SearchPHP.php',
        'TokenizerPHP\TokenizerPHP' => 'tokenizerphp/TokenizerPHP.php',
        'TransformersPHP\TransformersPHP' => 'transformersphp/TransformersPHP.php',
        'AutogradPHP\AutogradPHP' => 'autogradphp/AutogradPHP.php',
        'OptimizersPHP\OptimizersPHP' => 'optimizersphp/OptimizersPHP.php',
        'DatasetsPHP\DatasetsPHP' => 'datasetsphp/DatasetsPHP.php',
        'QuantizationPHP\QuantizationPHP' => 'quantizationphp/QuantizationPHP.php',
        'LoRAPHP\LoRAPHP' => 'loraphp/LoRAPHP.php',
        'EvalPHP\EvalPHP' => 'evalphp/EvalPHP.php',
        'torch'           => 'phptorch/PHPTorch.php',
    ];

    if (isset($classMap[$className])) {
        $filePath = __DIR__ . '/' . $classMap[$className];
        if (file_exists($filePath)) {
            // Check if module has its own internal autoloader for sub-components
            $moduleDir = dirname($filePath);
            if (file_exists($moduleDir . '/autoload.php')) {
                require_once $moduleDir . '/autoload.php';
            }
            require_once $filePath;
        }
    }
});
