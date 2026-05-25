# ⚡ ParallelPHP — Parallel Processing

> **Python Equivalent:** multiprocessing / concurrent.futures
> **Purpose:** Task queues, Map operations, MapReduce

---

## Functions

### `Pool(concurrency)` — Process Pool
```php
$pool = ParallelPHP::Pool(4);
$pool->submit(fn() => heavy_computation());
$results = $pool->wait();
```

### `TaskQueue()` — Task Queue with Error Handling
```php
$queue = ParallelPHP::TaskQueue();
$queue->addTask(fn() => file_get_contents('https://api.example.com/1'));
$queue->addTask(fn() => file_get_contents('https://api.example.com/2'));
$queue->addTask(fn() => 42 / 0); // This will error

$results = $queue->run();
// [
//   0 => ['status' => 'success', 'result' => '...'],
//   1 => ['status' => 'success', 'result' => '...'],
//   2 => ['status' => 'error', 'error' => 'Division by zero'],
// ]
```

### `map(func, data, chunkSize)` — Apply function to all items
```php
$squares = ParallelPHP::map(fn($x) => $x ** 2, [1,2,3,4,5]);
// [1, 4, 9, 16, 25]

$lengths = ParallelPHP::map('strlen', ['hello', 'world', 'PHP']);
// [5, 5, 3]
```

### `mapReduce(mapFunc, reduceFunc, data, initial)` — Map then Reduce
```php
// Sum of squares:
$result = ParallelPHP::mapReduce(
    fn($x) => $x * $x,                       // Map
    fn($carry, $item) => $carry + $item,       // Reduce
    [1, 2, 3, 4, 5],                           // Data
    0                                           // Initial value
);
// 55

// Word count:
$totalWords = ParallelPHP::mapReduce(
    fn($line) => str_word_count($line),
    fn($carry, $count) => $carry + $count,
    ["hello world", "foo bar baz"],
    0
);
// 5
```

---

# 📖 DictionaryPHP — Dictionary & Thesaurus

> **Purpose:** Word meanings, synonyms, antonyms, translation, similar words

## Functions

| Function | Description | Example |
|----------|-------------|---------|
| `meaning(word)` | Look up definition | `DictionaryPHP::meaning('algorithm')` |
| `synonyms(word)` | Get synonyms (20 word sets) | `DictionaryPHP::synonyms('happy')` → `['joyful','cheerful',...]` |
| `antonyms(word)` | Get antonyms (21 pairs) | `DictionaryPHP::antonyms('happy')` → `['sad','unhappy',...]` |
| `similar(word, max)` | Similar words (Levenshtein) | `DictionaryPHP::similar('helo', 5)` |
| `translateToHindi(word)` | English → Hindi | `DictionaryPHP::translateToHindi('water')` → `'पानी'` |
| `exists(word)` | Check if word exists | `DictionaryPHP::exists('algorithm')` → `true` |
| `wordType(word)` | Get part of speech | `DictionaryPHP::wordType('run')` → `'verb'` |

---

# 🌍 LanguagePHP — Language & Script Detection

> **Purpose:** Detect language and writing system of any text

## Functions

### `detect(text)` — Detect language
```php
LanguagePHP::detect("Bonjour le monde");
// ['language' => 'fr', 'confidence' => 0.85]

LanguagePHP::detect("こんにちは世界");
// ['language' => 'ja', 'confidence' => 0.92]
```

### `detectScript(text)` — Detect writing system
```php
LanguagePHP::detectScript("नमस्ते");
// ['primary' => 'Devanagari', 'confidence' => 1.0]

LanguagePHP::detectScript("Hello مرحبا");
// ['primary' => 'Latin', 'scripts' => ['Latin' => 5, 'Arabic' => 5]]
```

**19 scripts supported:** Latin, Cyrillic, Arabic, Devanagari, Bengali, Gurmukhi, Gujarati, Tamil, Telugu, Kannada, Malayalam, Thai, Georgian, Hangul, Hiragana, Katakana, CJK, Greek, Hebrew

### `isLanguage(text, code)` — Check specific language
```php
if (LanguagePHP::isLanguage($text, 'hi')) echo "Hindi!";
```

### `supportedLanguages()` — List all
```php
$langs = LanguagePHP::supportedLanguages();
```

---

# 💾 ModelIO — Save & Load ML Models

> **Python Equivalent:** pickle / joblib
> **Purpose:** Serialize trained ML/Neural models to disk

## Functions

| Function | Description |
|----------|-------------|
| `save(model, path, format)` | Save model ('binary' or 'json') |
| `load(path, format)` | Load saved model |

```php
// Train once
$model = MLPHP::RandomForest(20);
$model->fit($X_train, $y_train);

// Save
ModelIO::save($model, 'forest.bin');

// Load anywhere (even different project)
$model = ModelIO::load('forest.bin');
$predictions = $model->predict($X_new);
```
