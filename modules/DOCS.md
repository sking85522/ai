# 📊 PandaPHP — Data Manipulation Library

> **Python Equivalent:** Pandas
> **Purpose:** DataFrames, CSV/JSON I/O, GroupBy, Merge, Statistics

---

## Creating Data

### `DataFrame(data, index, columns)` — Create DataFrame
```php
$df = PandaPHP::DataFrame(
    [[1, 'Sachin', 95], [2, 'Virat', 88], [3, 'Rohit', 92]],
    null,
    ['id', 'name', 'score']
);
```

### `Series(data, index, name)` — Create Series (1D)
```php
$s = PandaPHP::Series([10, 20, 30], null, 'sales');
```

---

## I/O

### `read_csv(filepath, options)` — Read CSV
```php
$df = PandaPHP::read_csv('data.csv');
$df = PandaPHP::read_csv('data.tsv', ['delimiter' => "\t"]);
```

### `to_csv(df, filepath, options)` — Write CSV
```php
PandaPHP::to_csv($df, 'output.csv');
PandaPHP::to_csv($df, 'output.csv', ['index' => false]); // Without index
```

### `read_json(filepath)` — Read JSON
```php
$df = PandaPHP::read_json('data.json');
```

### `to_json(df, filepath)` — Write JSON
```php
PandaPHP::to_json($df, 'output.json');
```

---

## Operations (via DataFrameOps)

```php
use PandaPHP\Operations\DataFrameOps as ops;
```

### `describe(df)` — Statistical Summary
```php
$stats = ops::describe($df);
// [
//   'score' => ['count'=>3, 'mean'=>91.67, 'std'=>3.51, 'min'=>88, '25%'=>88, '50%'=>92, '75%'=>95, 'max'=>95],
//   'id' => ['count'=>3, 'mean'=>2.0, ...]
// ]
```

### `groupby(df, column, aggregations)` — Group & Aggregate
```php
$result = ops::groupby($df, 'department', [
    'salary' => 'mean',
    'employees' => 'count',
    'revenue' => 'sum',
]);
```

| Aggregation | Description |
|-------------|-------------|
| `sum` | Total |
| `mean` | Average |
| `count` | Number of items |
| `min` | Minimum |
| `max` | Maximum |

### `merge(left, right, on, how)` — SQL-like JOIN
```php
$result = ops::merge($employees, $departments, 'dept_id', 'inner');
$result = ops::merge($employees, $departments, 'dept_id', 'left');
```

### `sortValues(df, column, ascending)` — Sort
```php
$sorted = ops::sortValues($df, 'score', false); // Descending
```

### `valueCounts(df, column)` — Count unique values
```php
$counts = ops::valueCounts($df, 'category');
// ['Electronics' => 15, 'Books' => 8, 'Clothing' => 12]
```

### `apply(df, column, function)` — Apply custom function
```php
$doubled = ops::apply($df, 'price', fn($v) => $v * 2);
$upper = ops::apply($df, 'name', fn($v) => strtoupper($v));
```

---

# 📦 DatasetPHP — Datasets & Encoding

> **Python Equivalent:** sklearn.datasets / sklearn.model_selection / sklearn.preprocessing

### `train_test_split(X, y, test_size)` — Split data
```php
[$X_train, $X_test, $y_train, $y_test] = DatasetPHP::train_test_split($X, $y, 0.2);
```

### Built-in Datasets
```php
$iris = DatasetPHP::load_iris();     // 45 samples, 4 features, 3 classes
$xor = DatasetPHP::load_xor();       // Classic non-linear problem
$linear = DatasetPHP::load_linear(); // y ≈ 2.5x + 3 with noise
```

### `KFold(k, shuffle)` — K-Fold Cross-Validation
```php
$kfold = DatasetPHP::KFold(5, true);
$splits = $kfold->splitData($X, $y);
foreach ($splits as $fold) {
    $model->fit($fold['X_train'], $fold['y_train']);
    $score = MLPHP::accuracy_score($fold['y_test'], $model->predict($fold['X_test']));
}
```

### `LabelEncoder()` — Categorical → Integer
```php
$enc = DatasetPHP::LabelEncoder();
$encoded = $enc->fitTransform(['cat', 'dog', 'cat', 'bird']);
// [1, 2, 1, 0]
$original = $enc->inverseTransform([1, 2, 0]);
// ['cat', 'dog', 'bird']
```

### `OneHotEncoder()` — Integer → Binary vectors
```php
$ohe = DatasetPHP::OneHotEncoder();
$vectors = $ohe->fitTransform([0, 1, 2, 1]);
// [[1,0,0], [0,1,0], [0,0,1], [0,1,0]]
```

---

# 🔍 SearchPHP — Full-Text Search Engine

> **Like:** Elasticsearch / Algolia — but in PHP

### Basic Search (BM25)
```php
$search = new SearchPHP();
$search->addDocument($search->createDocument('1', ['title' => 'PHP Guide', 'body' => 'Learn PHP']));
$results = $search->search("PHP", 10); // BM25 ranked results
```

### Fuzzy Search (Typo-tolerant)
```php
$results = $search->fuzzySearch("PH tutoral", 10, 2);
// maxDistance: 2 = tolerates 2 character edits
```

### Autocomplete
```php
$suggestions = $search->suggest("pro"); // Returns terms starting with "pro"
```

### Persistence
```php
$search->saveIndex('my_index.dat');  // Save to disk
$search->loadIndex('my_index.dat');  // Load later
```

---

# ⚡ ParallelPHP — Parallel Processing

### Task Queue
```php
$queue = ParallelPHP::TaskQueue();
$queue->addTask(fn() => expensive_computation(1));
$queue->addTask(fn() => expensive_computation(2));
$results = $queue->run(); // Executes all tasks, collects results with error handling
```

### Map
```php
$results = ParallelPHP::map(fn($x) => $x * $x, [1,2,3,4,5]);
// [1, 4, 9, 16, 25]
```

### MapReduce
```php
$sum = ParallelPHP::mapReduce(
    fn($x) => $x * $x,          // Map: square each
    fn($carry, $item) => $carry + $item, // Reduce: sum
    [1, 2, 3, 4, 5],
    0
);
// 55
```

---

# 📖 DictionaryPHP — Dictionary & Thesaurus

### `meaning(word)` — Look up definition
```php
$def = DictionaryPHP::meaning('algorithm');
```

### `synonyms(word)` — Find synonyms
```php
$syns = DictionaryPHP::synonyms('good');
// ['great', 'excellent', 'fine', 'wonderful', 'superb', 'outstanding']
```

### `antonyms(word)` — Find antonyms
```php
$ants = DictionaryPHP::antonyms('good');
// ['bad', 'poor', 'terrible']
```

### `similar(word, maxResults)` — Similar words (Levenshtein)
```php
$similar = DictionaryPHP::similar('helo', 5);
// ['hello', 'help', ...]
```

### `translateToHindi(word)` — English → Hindi
```php
$hindi = DictionaryPHP::translateToHindi('water');
// 'पानी'
```

---

# 🌍 LanguagePHP — Language & Script Detection

### `detect(text)` — Detect language
```php
$result = LanguagePHP::detect("Bonjour le monde");
// ['language' => 'fr', 'confidence' => 0.85, 'scores' => [...]]
```

### `detectScript(text)` — Detect writing system
```php
$result = LanguagePHP::detectScript("नमस्ते दुनिया");
// [
//   'primary' => 'Devanagari',
//   'scripts' => ['Devanagari' => 12],
//   'confidence' => 1.0
// ]
```

**Supports 19 scripts:** Latin, Cyrillic, Arabic, Devanagari, Bengali, Gurmukhi, Gujarati, Tamil, Telugu, Kannada, Malayalam, Thai, Georgian, Hangul, Hiragana, Katakana, CJK, Greek, Hebrew.

### `isLanguage(text, langCode)` — Check specific language
```php
if (LanguagePHP::isLanguage($text, 'hi')) {
    echo "Hindi detected!";
}
```

### `supportedLanguages()` — List all supported
```php
$langs = LanguagePHP::supportedLanguages();
```

---

# 💾 ModelIO — Save & Load ML Models

### `save(model, filepath, format)` — Save trained model
```php
ModelIO::save($trainedModel, 'my_model.bin', 'binary');
ModelIO::save($trainedModel, 'my_model.json', 'json');
```

### `load(filepath, format)` — Load saved model
```php
$model = ModelIO::load('my_model.bin', 'binary');
$predictions = $model->predict($newData);
```
