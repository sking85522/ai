<?php

namespace MLPHP;

use MLPHP\LinearModel\LinearRegression;
use MLPHP\LinearModel\LogisticRegression;
use MLPHP\Cluster\KMeans;
use MLPHP\Metrics\RegressionMetrics;
use MLPHP\Metrics\ClassificationMetrics;
use MLPHP\PreProcessing\StandardScaler;
use MLPHP\Tree\DecisionTree;
use MLPHP\Tree\RandomForest;
use MLPHP\Neighbors\KNNClassifier;
use MLPHP\SVM\SVC;

class MLPHP
{
    // ──────────── Models ────────────

    public static function LinearRegression(): LinearRegression
    {
        return new LinearRegression();
    }

    public static function LogisticRegression(): LogisticRegression
    {
        return new LogisticRegression();
    }

    public static function KMeans(int $n_clusters = 8, int $max_iter = 300): KMeans
    {
        return new KMeans($n_clusters, $max_iter);
    }

    public static function DecisionTree(int $maxDepth = 10, int $minSamples = 2): DecisionTree
    {
        return new DecisionTree($maxDepth, $minSamples);
    }

    public static function RandomForest(int $nEstimators = 10, int $maxDepth = 10): RandomForest
    {
        return new RandomForest($nEstimators, $maxDepth);
    }

    public static function KNNClassifier(int $k = 5, string $weights = 'uniform'): KNNClassifier
    {
        return new KNNClassifier($k, $weights);
    }

    public static function SVC(float $C = 1.0, float $lr = 0.001, int $epochs = 1000): SVC
    {
        return new SVC($C, $lr, $epochs);
    }

    // ──────────── Preprocessing ────────────

    public static function StandardScaler(): StandardScaler
    {
        return new StandardScaler();
    }

    // ──────────── Regression Metrics ────────────

    public static function mean_squared_error(array $y_true, array $y_pred): float
    {
        return RegressionMetrics::mean_squared_error($y_true, $y_pred);
    }

    public static function mean_absolute_error(array $y_true, array $y_pred): float
    {
        return RegressionMetrics::mean_absolute_error($y_true, $y_pred);
    }

    public static function rmse(array $y_true, array $y_pred): float
    {
        return RegressionMetrics::root_mean_squared_error($y_true, $y_pred);
    }

    public static function r2_score(array $y_true, array $y_pred): float
    {
        return RegressionMetrics::r2_score($y_true, $y_pred);
    }

    // ──────────── Classification Metrics ────────────

    public static function accuracy_score(array $y_true, array $y_pred): float
    {
        return ClassificationMetrics::accuracy_score($y_true, $y_pred);
    }

    public static function precision_score(array $y_true, array $y_pred, $positive = 1): float
    {
        return ClassificationMetrics::precision_score($y_true, $y_pred, $positive);
    }

    public static function recall_score(array $y_true, array $y_pred, $positive = 1): float
    {
        return ClassificationMetrics::recall_score($y_true, $y_pred, $positive);
    }

    public static function f1_score(array $y_true, array $y_pred, $positive = 1): float
    {
        return ClassificationMetrics::f1_score($y_true, $y_pred, $positive);
    }

    public static function confusion_matrix(array $y_true, array $y_pred): array
    {
        return ClassificationMetrics::confusion_matrix($y_true, $y_pred);
    }

    public static function classification_report(array $y_true, array $y_pred): array
    {
        return ClassificationMetrics::classification_report($y_true, $y_pred);
    }
}
