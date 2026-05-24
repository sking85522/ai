# 🔍 SearchPHP — Full-Text Search Engine

> **Like:** Elasticsearch / Algolia — but pure PHP
> **Purpose:** BM25 search, fuzzy matching, autocomplete, index persistence

---

## Quick Start
```php
$search = new SearchPHP();
$search->addDocument($search->createDocument('1', ['title' => 'PHP Guide']));
$results = $search->search("PHP");
```

---

## Functions

### `createDocument(id, fields)` — Create searchable document
```php
$doc = $search->createDocument('product_123', [
    'title' => 'iPhone 15 Pro',
    'description' => 'Latest Apple smartphone with A17 chip',
    'category' => 'Electronics',
]);
```

### `addDocument(document)` — Add to index
```php
$search->addDocument($doc);
```

### `search(query, limit)` — BM25 Search
```php
$results = $search->search("Apple smartphone", 10);
// Returns ranked results with BM25 scores
```

### `fuzzySearch(query, limit, maxDistance)` — Typo-tolerant
```php
$results = $search->fuzzySearch("Appl smarphone", 10, 2);
// maxDistance: 1-3 character edits tolerated
```

### `suggest(prefix, limit)` — Autocomplete
```php
$suggestions = $search->suggest("pro", 5);
// Returns terms starting with "pro"
```

### `saveIndex(filepath)` / `loadIndex(filepath)` — Persistence
```php
$search->saveIndex('products.idx');
// Later...
$search->loadIndex('products.idx'); // Restored!
```
