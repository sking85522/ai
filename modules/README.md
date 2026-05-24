# 🧬 SciPHP Framework v2.0

### **Python ka full AI/ML ecosystem — ab PHP mein!**

> NumPy + SciPy + Scikit-learn + NLTK + OpenCV + Matplotlib + Pandas — all in pure PHP.
> **No external dependencies. No Python needed. Just copy & use.**

---

## ⚡ Quick Start

### Option 1: Simple Copy (Recommended)
```php
// Copy the entire 'modules/' folder into your project, then:
require_once 'modules/index.php';

// That's it! All 15 libraries are now available.
```

### Option 2: Composer
```bash
# From your project root:
composer require sciphp/sciphp-framework
```

### Option 3: Include specific modules only
```php
require_once 'modules/numphp/autoload.php';
require_once 'modules/mlphp/autoload.php';
// Use only what you need
```

---

## 📦 What's Inside — 15 Libraries

| # | Library | Python Equivalent | Purpose |
|---|---------|------------------|---------|
| 1 | **NumPHP** | NumPy | Multi-dimensional arrays, math, linear algebra |
| 2 | **SciPHP** | SciPy | Scientific computing, optimization, integration |
| 3 | **MLPHP** | Scikit-learn | Machine Learning algorithms |
| 4 | **NeuralPHP** | TensorFlow/Keras | Neural Networks & Deep Learning |
| 5 | **NLPHP** | NLTK/spaCy | Natural Language Processing |
| 6 | **VisionPHP** | OpenCV | Computer Vision & Image Processing |
| 7 | **SpeechPHP** | Librosa | Audio Processing & Speech Analysis |
| 8 | **PandaPHP** | Pandas | DataFrames & Data Manipulation |
| 9 | **PlotPHP** | Matplotlib | Charts & Visualization (SVG) |
| 10 | **DatasetPHP** | sklearn.datasets | Datasets, splitting, encoding |
| 11 | **SearchPHP** | Elasticsearch | Full-text search engine (BM25) |
| 12 | **ParallelPHP** | multiprocessing | Task queues & MapReduce |
| 13 | **ModelIO** | pickle/joblib | Save & Load ML models |
| 14 | **DictionaryPHP** | PyDictionary | Dictionary, synonyms, translation |
| 15 | **LanguagePHP** | langdetect | Language & script detection |

---

## 🧠 What Can This Framework Do?

### Machine Learning Pipeline (Complete)
```php
use MLPHP\MLPHP as ml;
use DatasetPHP\DatasetPHP as ds;

// 1. Load dataset
$iris = ds::load_iris();

// 2. Split into train/test
[$X_train, $X_test, $y_train, $y_test] = ds::train_test_split($iris['X'], $iris['y'], 0.3);

// 3. Train model
$model = ml::DecisionTree(maxDepth: 5);
$model->fit($X_train, $y_train);

// 4. Predict
$predictions = $model->predict($X_test);

// 5. Evaluate
echo "Accuracy: " . ml::accuracy_score($y_test, $predictions);
echo "F1 Score: " . ml::f1_score($y_test, $predictions);
print_r(ml::confusion_matrix($y_test, $predictions));
```

### Neural Network Training
```php
use NeuralPHP\NeuralPHP as nn;

$model = nn::Sequential();
$model->add(nn::Dense(4, 8, nn::getActivation('relu')));
$model->add(nn::Dropout(0.3));
$model->add(nn::Dense(8, 3, nn::getActivation('softmax')));
$model->compile(nn::getLoss('cross_entropy'), nn::getOptimizer('adam', 0.001));
$model->fit($X_train, $y_train, epochs: 100);
```

### NLP — Sentiment Analysis
```php
use NLPHP\NLPHP as nlp;

$result = nlp::sentiment("This product is absolutely amazing! I love it.");
// ['score' => 10.5, 'compound' => 0.9412, 'label' => 'positive']

// TF-IDF Vectorization
$tfidf = nlp::TfIdfVectorizer();
$matrix = $tfidf->fitTransform(["I love PHP", "PHP is great", "Python is slow"]);

// N-grams
$bigrams = nlp::bigrams("the quick brown fox jumps over");
// ["the quick", "quick brown", "brown fox", "fox jumps", "jumps over"]
```

### Computer Vision — Image Processing
```php
use VisionPHP\VisionPHP as cv;

$img = cv::imread('photo.jpg');
$gray = cv::cvtColor($img, 'GRAY');
$edges = cv::Sobel($gray);
$resized = cv::resize($img, 640, 480);
$binary = cv::thresholdOtsu($gray);
$annotated = cv::rectangle($img, 10, 10, 200, 200, [0,255,0], 3);
$annotated = cv::putText($annotated, "Detected!", 20, 220, [255,0,0]);
cv::imwrite('output.jpg', $annotated);
```

### Audio & Speech Processing
```php
use SpeechPHP\SpeechPHP as sp;

$audio = sp::read('voice.wav');
$mfcc = sp::mfcc($audio['data'], $audio['rate'], numCoeffs: 13);
$spec = sp::spectrogram($audio['data']);
$pitch = sp::detect_pitch($audio['data'], $audio['rate']);
echo "Pitch: {$pitch['frequency']} Hz, Confidence: {$pitch['confidence']}";
```

### Data Analysis (Pandas-like)
```php
use PandaPHP\PandaPHP as pd;

$df = pd::read_csv('sales.csv');
$stats = \PandaPHP\Operations\DataFrameOps::describe($df);
$grouped = \PandaPHP\Operations\DataFrameOps::groupby($df, 'category', ['revenue' => 'sum', 'orders' => 'count']);
$sorted = \PandaPHP\Operations\DataFrameOps::sortValues($df, 'revenue', false);
```

### Scientific Computing
```php
use SciPHP\SciPHP as sci;

// Minimize: find x that minimizes f(x) = (x-3)^2
$result = sci::optimize_minimize(fn($x) => ($x-3)**2, 0.0);

// Integrate: ∫₀¹ x² dx
$integral = sci::integrate_quad(fn($x) => $x**2, 0, 1);

// Normal Distribution
$pdf = sci::stats_norm_pdf(0.0); // PDF at x=0
$cdf = sci::stats_norm_cdf(1.96); // CDF at z=1.96
```

### Charts & Visualization
```php
use PlotPHP\PlotPHP as plt;

plt::plot([1,2,3,4], [10,20,15,30], 'blue');
plt::scatter([1,2,3], [5,3,8], 4.0, 'red');
plt::title("Sales Trends");
plt::xlabel("Month");
plt::ylabel("Revenue");
plt::grid(true);
plt::savefig("chart.svg");
```

### Full-Text Search Engine
```php
use SearchPHP\SearchPHP;

$search = new SearchPHP();
$search->addDocument($search->createDocument('1', ['title' => 'PHP Guide', 'body' => 'Learn PHP fast']));
$search->addDocument($search->createDocument('2', ['title' => 'Python Tutorial', 'body' => 'Python for beginners']));

$results = $search->search("PHP tutorial"); // BM25 ranking
$fuzzy = $search->fuzzySearch("PH tutoral"); // Typo-tolerant!
$suggestions = $search->suggest("py"); // Autocomplete

$search->saveIndex('search_index.dat'); // Persist to disk
```

---

## 📖 Requirements

- **PHP 7.4+**
- **ext-gd** — Required for VisionPHP (image processing)
- **ext-json** — Required for IO operations
- **ext-mbstring** — Required for NLPHP unicode support
- **ext-pcntl** — Optional, for ParallelPHP process forking

---

## 📂 Folder Structure

```
modules/
├── index.php          ← Single entry point (include this = get everything)
├── autoload.php       ← Central autoloader
├── modules.php        ← Module registry
├── composer.json      ← For Composer installation
├── README.md          ← This file
│
├── numphp/            ← 🔢 NumPHP (108KB, 960 files) — NumPy equivalent
├── sciphp/            ← 🔬 SciPHP — SciPy equivalent
├── mlphp/             ← 🤖 MLPHP — Scikit-learn equivalent
├── neuralphp/         ← 🧠 NeuralPHP — TensorFlow/Keras equivalent
├── nlphp/             ← 📝 NLPHP — NLTK/spaCy equivalent
├── visionphp/         ← 📷 VisionPHP — OpenCV equivalent
├── speechphp/         ← 🎵 SpeechPHP — Librosa equivalent
├── pandaphp/          ← 📊 PandaPHP — Pandas equivalent
├── plotphp/           ← 📈 PlotPHP — Matplotlib equivalent
├── datasetphp/        ← 📦 DatasetPHP — sklearn.datasets equivalent
├── search/            ← 🔍 SearchPHP — Elasticsearch-like search
├── parallelphp/       ← ⚡ ParallelPHP — multiprocessing equivalent
├── modelio/           ← 💾 ModelIO — pickle/joblib equivalent
├── dictionaryphp/     ← 📖 DictionaryPHP — Dictionary & Thesaurus
└── languagephp/       ← 🌍 LanguagePHP — Language detection
```

---

## 📄 License

MIT License — Free to use, modify, and distribute.

**Built with ❤️ by Tech Elevate X**
