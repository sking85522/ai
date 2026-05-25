# 🤖 MLPHP — Machine Learning Library

> **Python Equivalent:** Scikit-learn
> **Purpose:** Classification, Regression, Clustering, Metrics

---

## Models

### 1. `LinearRegression` — Linear Regression
Fits a line y = mx + b to data using least squares.

```php
$model = MLPHP::LinearRegression();
$model->fit([[1],[2],[3]], [2,4,6]);
$predictions = $model->predict([[4],[5]]);
// [8, 10]
```

### 2. `LogisticRegression` — Logistic Regression Classifier
Binary/multi-class classification using sigmoid function.

```php
$model = MLPHP::LogisticRegression();
$model->fit($X_train, $y_train);
$predictions = $model->predict($X_test);
```

### 3. `DecisionTree(maxDepth, minSamples)` — Decision Tree Classifier
Splits data using Information Gain (entropy-based).

```php
$tree = MLPHP::DecisionTree(maxDepth: 5, minSamples: 2);
$tree->fit($X_train, $y_train);
$predictions = $tree->predict($X_test);
$treeStructure = $tree->getTree(); // Get internal tree structure
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `maxDepth` | 10 | Maximum tree depth |
| `minSamples` | 2 | Minimum samples to split a node |

### 4. `RandomForest(nEstimators, maxDepth)` — Random Forest Classifier
Ensemble of Decision Trees with bootstrap sampling + feature bagging.

```php
$rf = MLPHP::RandomForest(nEstimators: 20, maxDepth: 8);
$rf->fit($X_train, $y_train);
$predictions = $rf->predict($X_test);
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `nEstimators` | 10 | Number of trees in the forest |
| `maxDepth` | 10 | Maximum depth per tree |

### 5. `KNNClassifier(k, weights)` — K-Nearest Neighbors
Classifies based on closest training examples in feature space.

```php
$knn = MLPHP::KNNClassifier(k: 5, weights: 'distance');
$knn->fit($X_train, $y_train);
$predictions = $knn->predict($X_test);
$probabilities = $knn->predictProba($X_test); // Class probabilities
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `k` | 5 | Number of neighbors |
| `weights` | 'uniform' | 'uniform' or 'distance' (weighted by inverse distance) |

### 6. `SVC(C, lr, epochs)` — Support Vector Machine
Binary classifier with hinge loss optimization.

```php
// Labels must be -1 or 1
$svm = MLPHP::SVC(C: 1.0, lr: 0.001, epochs: 1000);
$svm->fit($X_train, $y_train);
$predictions = $svm->predict($X_test);
$scores = $svm->decisionFunction($X_test); // Raw scores
```

### 7. `KMeans(n_clusters, max_iter)` — K-Means Clustering
Unsupervised clustering into K groups.

```php
$kmeans = MLPHP::KMeans(n_clusters: 3, max_iter: 300);
$kmeans->fit($X);
$labels = $kmeans->predict($X_new);
```

### 8. `StandardScaler()` — Feature Normalization
Scales features to zero mean and unit variance.

```php
$scaler = MLPHP::StandardScaler();
$scaler->fit($X_train);
$X_train_scaled = $scaler->transform($X_train);
$X_test_scaled = $scaler->transform($X_test);
```

---

## Metrics

### Regression Metrics
```php
MLPHP::mean_squared_error($y_true, $y_pred);     // MSE
MLPHP::mean_absolute_error($y_true, $y_pred);     // MAE
MLPHP::rmse($y_true, $y_pred);                     // Root MSE
MLPHP::r2_score($y_true, $y_pred);                 // R² (0-1, higher = better)
```

### Classification Metrics
```php
MLPHP::accuracy_score($y_true, $y_pred);           // Overall accuracy
MLPHP::precision_score($y_true, $y_pred, 1);       // Precision for class 1
MLPHP::recall_score($y_true, $y_pred, 1);          // Recall for class 1
MLPHP::f1_score($y_true, $y_pred, 1);              // F1 Score (harmonic mean)
MLPHP::confusion_matrix($y_true, $y_pred);         // Confusion Matrix
MLPHP::classification_report($y_true, $y_pred);    // Full report (all classes)
```
