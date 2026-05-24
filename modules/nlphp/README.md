# 📝 NLPHP — Natural Language Processing Library

> **Python Equivalent:** NLTK / spaCy
> **Purpose:** Text analysis, Sentiment, Tokenization, Classification, Vectorization

---

## Tokenization

### `word_tokenize(text)` — Split text into words
```php
$words = NLPHP::word_tokenize("Hello world! How are you?");
// ['Hello', 'world', 'How', 'are', 'you']
```

### `sent_tokenize(text)` — Split text into sentences
```php
$sentences = NLPHP::sent_tokenize("First sentence. Second sentence! Third?");
// ['First sentence.', 'Second sentence!', 'Third?']
```

---

## Preprocessing

### `remove_stopwords(words)` — Remove common words
```php
$filtered = NLPHP::remove_stopwords(['the', 'cat', 'is', 'on', 'table']);
// ['cat', 'table']
```

### `stem(words)` — Porter Stemmer
Reduces words to their root form.
```php
$stemmed = NLPHP::stem(['running', 'runner', 'ran']);
// ['run', 'runner', 'ran']
```

---

## Sentiment Analysis

### `sentiment(text)` — Quick sentiment check
```php
$result = NLPHP::sentiment("This product is absolutely amazing!");
// [
//   'score' => 6.0,
//   'compound' => 0.9285,
//   'label' => 'positive',   ← 'positive', 'negative', or 'neutral'
//   'word_scores_found' => 2
// ]

$result = NLPHP::sentiment("This is terrible and horrible!");
// ['score' => -8.0, 'compound' => -0.9506, 'label' => 'negative']
```

### `SentimentAnalyzer()` — Custom analyzer instance
```php
$analyzer = NLPHP::SentimentAnalyzer();
$analyzer->addWords(['chai' => 3, 'thanda' => -1]); // Add custom words
$result = $analyzer->analyze("Yeh chai bahut acchi hai");
```

**Features:** Handles negation ("not good" → negative), intensifiers ("very good" → more positive).

---

## Vectorization

### `TfIdfVectorizer()` — TF-IDF Vectors
Converts text documents into numerical feature vectors.

```php
$tfidf = NLPHP::TfIdfVectorizer();
$matrix = $tfidf->fitTransform([
    "I love PHP programming",
    "PHP is great for web",
    "Python is popular",
]);
// Returns 2D array: [3 documents × N features]

$vocab = $tfidf->getVocabulary();      // ['i' => 0, 'love' => 1, ...]
$names = $tfidf->getFeatureNames();    // ['i', 'love', 'php', ...]
```

### `CountVectorizer()` — Bag of Words
Word count vectors (simpler than TF-IDF).

```php
$bow = NLPHP::CountVectorizer();
$matrix = $bow->fitTransform(["the cat sat", "the dog sat"]);
```

---

## N-Grams

### `bigrams(text)` — Word pairs
```php
$bigrams = NLPHP::bigrams("the quick brown fox");
// ['the quick', 'quick brown', 'brown fox']
```

### `trigrams(text)` — Word triples
```php
$trigrams = NLPHP::trigrams("one two three four five");
// ['one two three', 'two three four', 'three four five']
```

### `ngrams(text, n)` — Custom n
```php
$fourgrams = NLPHP::ngrams("a b c d e f", 4);
```

### `char_ngrams(text, n)` — Character level
```php
$chars = NLPHP::char_ngrams("hello", 3);
// ['hel', 'ell', 'llo']
```

### `ngram_frequency(text, n)` — Frequency distribution
```php
$freq = NLPHP::ngram_frequency("the cat and the dog and the bird", 2);
// ['and the' => 2, 'the cat' => 1, 'the dog' => 1, ...]
```

---

## Classification

### `NaiveBayes()` — Naive Bayes Text Classifier
```php
$nb = NLPHP::NaiveBayes();
$nb->train('positive', "Great product, love it");
$nb->train('negative', "Terrible quality, waste");
$result = $nb->classify("This is great quality");
// 'positive'
```

### `Chatbot(intents)` — Rule-Based Chatbot
```php
$bot = NLPHP::Chatbot([
    ['patterns' => ['hello', 'hi'], 'response' => 'Hello! How can I help?'],
    ['patterns' => ['price', 'cost'], 'response' => 'Our prices start at ₹999.'],
]);
$reply = $bot->respond("What is the price?");
```
