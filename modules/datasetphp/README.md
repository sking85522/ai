# 📦 DatasetPHP — Datasets, Splitting & Encoding

> **Python Equivalent:** sklearn.datasets / sklearn.model_selection / sklearn.preprocessing
> **Purpose:** Load datasets, train-test split, cross-validation, label encoding

---

## Data Splitting

### `train_test_split(X, y, test_size)`
```php
[$X_train, $X_test, $y_train, $y_test] = DatasetPHP::train_test_split($X, $y, 0.25);
// 75% train, 25% test (randomized)
```

---

## Built-in Datasets

### `load_iris()` — Fisher's Iris Dataset
```php
$iris = DatasetPHP::load_iris();
// $iris['X'] → 45 samples × 4 features (sepal_length, sepal_width, petal_length, petal_width)
// $iris['y'] → labels (0=setosa, 1=versicolor, 2=virginica)
// $iris['feature_names'] → ['sepal_length', 'sepal_width', ...]
// $iris['target_names'] → ['setosa', 'versicolor', 'virginica']
```

### `load_xor()` — XOR Gate Dataset
```php
$xor = DatasetPHP::load_xor();
// Non-linearly separable problem for testing neural networks
```

### `load_linear()` — Linear Regression Dataset
```php
$data = DatasetPHP::load_linear();
// y ≈ 2.5x + 3 with random noise — 50 samples
```

---

## Cross-Validation

### `KFold(k, shuffle)` — K-Fold Cross-Validation
```php
$kfold = DatasetPHP::KFold(5, true);

// Method 1: Get index splits
$folds = $kfold->split($X);

// Method 2: Get actual data splits
$splits = $kfold->splitData($X, $y);
foreach ($splits as $fold) {
    $model->fit($fold['X_train'], $fold['y_train']);
    $acc = MLPHP::accuracy_score($fold['y_test'], $model->predict($fold['X_test']));
    echo "Fold accuracy: $acc\n";
}
```

---

## Encoding

### `LabelEncoder()` — Text Labels → Numbers
```php
$enc = DatasetPHP::LabelEncoder();
$encoded = $enc->fitTransform(['cat', 'dog', 'cat', 'bird']);
// [1, 2, 1, 0] (alphabetical order)

$original = $enc->inverseTransform([0, 1, 2]);
// ['bird', 'cat', 'dog']

$classes = $enc->getClasses(); // ['bird', 'cat', 'dog']
```

### `OneHotEncoder()` — Numbers → Binary Vectors
```php
$ohe = DatasetPHP::OneHotEncoder();
$vectors = $ohe->fitTransform([0, 1, 2, 1]);
// [[1,0,0], [0,1,0], [0,0,1], [0,1,0]]

$labels = $ohe->inverseTransform($vectors);
// [0, 1, 2, 1]
```
