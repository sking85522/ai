<?php

require_once __DIR__ . '/../../../core/Bootstrap.php';

spl_autoload_register(function ($class) {
    if (strpos($class, 'SearchPHP\\') === 0) {
        $relativeClass = str_replace('SearchPHP\\', '', $class);
        $file = __DIR__ . '/../src/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
        $file = __DIR__ . '/../' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

use SearchPHP\Scoring\BM25;

class BM25Test
{
    public function run()
    {
        $this->testCalculateIDFEdgeCases();
        $this->testCalculateTFEdgeCases();
        echo "BM25 tests passed!\n";
    }

    private function testCalculateIDFEdgeCases()
    {
        $bm25 = new BM25(100, 50.0);

        // Edge case: docFreq = 0
        $idfZero = $bm25->calculateIDF(0);
        assert($idfZero > 0, "IDF should be positive for docFreq=0");

        // Edge case: docFreq = totalDocs
        $idfAll = $bm25->calculateIDF(100);
        assert($idfAll < $idfZero, "IDF should be smaller for a term appearing in all docs compared to none");
    }

    private function testCalculateTFEdgeCases()
    {
        // Edge case: avgDocLength = 0
        $bm25 = new BM25(100, 0.0);
        $tf = $bm25->calculateTF(1, 10);
        assert($tf === 0.0, "TF should be 0.0 when avgDocLength is 0 to avoid division by zero");
    }
}

$tester = new BM25Test();
$tester->run();
