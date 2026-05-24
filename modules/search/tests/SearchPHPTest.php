<?php

require_once __DIR__ . '/../../../core/Bootstrap.php';

// Temporarily map SearchPHP if autoloader doesn't do it automatically
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

use SearchPHP\SearchPHP;
use SearchPHP\Core\Document;

class SearchPHPTest
{
    public function run()
    {
        $this->testAddDocumentAndSearch();
        echo "SearchPHP tests passed!\n";
    }

    private function testAddDocumentAndSearch()
    {
        $search = new SearchPHP();

        $doc1 = $search->createDocument('doc1', ['title' => 'Hello World', 'body' => 'This is a test document.']);
        $doc2 = $search->createDocument('doc2', ['title' => 'Another Document', 'body' => 'More testing context is provided here.']);
        $doc3 = $search->createDocument('doc3', ['title' => 'Test Hello', 'body' => 'This is test test test.']);

        $search->addDocument($doc1);
        $search->addDocument($doc2);
        $search->addDocument($doc3);

        // Search for 'test'
        $results = $search->search('test');

        // Ensure at least 2 documents are returned (doc1 and doc3 contain 'test' or 'testing')
        assert(count($results) >= 2, "Search for 'test' should return at least 2 results");

        // Ensure ranking (doc3 should have a higher score for 'test' because it repeats the word)
        $score3 = 0;
        $score1 = 0;
        foreach ($results as $res) {
            if ($res['document']->getId() === 'doc3') $score3 = $res['score'];
            if ($res['document']->getId() === 'doc1') $score1 = $res['score'];
        }

        assert($score3 > $score1, "Doc3 should have a higher score for 'test' than Doc1");
    }
}

$tester = new SearchPHPTest();
$tester->run();
