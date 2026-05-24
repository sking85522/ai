<?php
namespace Core\DataHandling\Datasets;

$datasetDir = dirname(__DIR__, 3) . '/modules/datasetphp';
if (file_exists($datasetDir . '/autoload.php')) {
    require_once $datasetDir . '/autoload.php';
}
if (file_exists($datasetDir . '/DatasetPHP.php')) {
    require_once $datasetDir . '/DatasetPHP.php';
}

use DatasetPHP\DatasetPHP;

class Corpus {

    /**
     * Loads the IRIS dataset using DatasetPHP
     */
    public function getIris(): array {
        if (!class_exists('DatasetPHP\DatasetPHP')) {
            throw new \Exception("DatasetPHP module missing.");
        }
        return DatasetPHP::load_iris();
    }

    /**
     * Loads the XOR dataset
     */
    public function getXOR(): array {
        if (!class_exists('DatasetPHP\DatasetPHP')) {
            throw new \Exception("DatasetPHP module missing.");
        }
        return DatasetPHP::load_xor();
    }

    /**
     * Loads linear regression sample data
     */
    public function getLinearData(): array {
         if (!class_exists('DatasetPHP\DatasetPHP')) {
            throw new \Exception("DatasetPHP module missing.");
        }
        return DatasetPHP::load_linear();
    }
}
