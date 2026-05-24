<?php

namespace DatasetPHP;

use DatasetPHP\Utils\Splitter;
use DatasetPHP\Loaders\CSVLoader;
use DatasetPHP\Loaders\BuiltinDatasets;
use DatasetPHP\Validation\KFold;
use DatasetPHP\Encoding\LabelEncoder;
use DatasetPHP\Encoding\OneHotEncoder;

class DatasetPHP
{
    // ──────────── Splitting ────────────

    public static function train_test_split(array $X, array $y, float $test_size = 0.25): array
    {
        return Splitter::train_test_split($X, $y, $test_size);
    }

    // ──────────── Loading ────────────

    public static function load_csv(string $filepath, $target_column): array
    {
        return CSVLoader::load($filepath, $target_column);
    }

    // ──────────── Built-in Datasets ────────────

    public static function load_iris(): array
    {
        return BuiltinDatasets::iris();
    }

    public static function load_xor(): array
    {
        return BuiltinDatasets::xor();
    }

    public static function load_linear(): array
    {
        return BuiltinDatasets::linear();
    }

    // ──────────── Cross-Validation ────────────

    public static function KFold(int $k = 5, bool $shuffle = true): KFold
    {
        return new KFold($k, $shuffle);
    }

    // ──────────── Encoding ────────────

    public static function LabelEncoder(): LabelEncoder
    {
        return new LabelEncoder();
    }

    public static function OneHotEncoder(): OneHotEncoder
    {
        return new OneHotEncoder();
    }
}
