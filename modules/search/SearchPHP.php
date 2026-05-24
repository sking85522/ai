<?php

namespace SearchPHP;

use SearchPHP\Core\Index;
use SearchPHP\Core\Document;

class SearchPHP
{
    private $index;

    public function __construct()
    {
        $this->index = new Index();
    }

    /**
     * Create a new document to be indexed
     *
     * @param string $id Unique identifier for the document
     * @param array $fields Associative array of field names to text content
     * @return Document
     */
    public function createDocument(string $id, array $fields): Document
    {
        return new Document($id, $fields);
    }

    /**
     * Add a document to the index
     *
     * @param Document $document
     */
    public function addDocument(Document $document)
    {
        $this->index->addDocument($document);
    }

    /**
     * Perform a search query and return ranked results using BM25
     *
     * @param string $query
     * @param int $limit Max results to return
     * @return array Ranked documents with their scores
     */
    public function search(string $query, int $limit = 10): array
    {
        return $this->index->search($query, $limit);
    }
    public function saveIndex(string $filePath)
    {
        $this->index->save($filePath);
    }

    public function loadIndex(string $filePath)
    {
        $this->index->load($filePath);
    }
}
