# 📊 PandaPHP — Data Manipulation Library

> **Python Equivalent:** Pandas
> **Purpose:** DataFrames, CSV/JSON I/O, GroupBy, Merge, Statistics

---

## Quick Start
```php
use PandaPHP\PandaPHP as pd;

$df = pd::read_csv('data.csv');
$stats = \PandaPHP\Operations\DataFrameOps::describe($df);
```

---

## Creating Data

### `DataFrame(data, index, columns)`
```php
$df = pd::DataFrame(
    [[1, 'Sachin', 95], [2, 'Virat', 88]],
    null,
    ['id', 'name', 'score']
);
```

### `Series(data, index, name)`
```php
$s = pd::Series([10, 20, 30], null, 'sales');
```

---

## I/O

| Function | Description |
|----------|-------------|
| `read_csv(path, options)` | Read CSV file → DataFrame |
| `to_csv(df, path, options)` | Write DataFrame → CSV |
| `read_json(path)` | Read JSON file → DataFrame |
| `to_json(df, path)` | Write DataFrame → JSON |

---

## Operations (`PandaPHP\Operations\DataFrameOps`)

| Function | Description | Example |
|----------|-------------|---------|
| `describe(df)` | Statistical summary (mean, std, min, max, percentiles) | `ops::describe($df)` |
| `groupby(df, col, aggs)` | Group & aggregate (sum, mean, count, min, max) | `ops::groupby($df, 'dept', ['salary'=>'mean'])` |
| `merge(left, right, on, how)` | SQL JOIN (inner/left) | `ops::merge($df1, $df2, 'id', 'left')` |
| `sortValues(df, col, asc)` | Sort by column | `ops::sortValues($df, 'price', false)` |
| `valueCounts(df, col)` | Count unique values | `ops::valueCounts($df, 'category')` |
| `apply(df, col, func)` | Apply custom function | `ops::apply($df, 'name', 'strtoupper')` |
