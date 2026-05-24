<?php

namespace PandaPHP\Core;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class DataFrame
{
    private $data;
    private $index;
    private $columns;

    /**
     * @param array|NDArray $data A dictionary-like structure ['col1' => [1,2,3], 'col2' => [4,5,6]] or list of lists
     * @param array|null $index
     * @param array|null $columns
     */
    public function __construct($data = [], $index = null, $columns = null)
    {
        if ($data instanceof NDArray) {
            $data = $data->getData();
        }

        $this->data = [];
        $this->columns = [];
        $this->index = [];

        if (empty($data)) return;

        // Is it a dict format? (associative array where keys are strings)
        if ($this->isAssoc($data) && $columns === null) {
            $this->columns = array_keys($data);
            $length = -1;
            foreach ($data as $col => $values) {
                if ($values instanceof Series) {
                    $this->data[$col] = clone $values;
                    $len = count($values->getData());
                } else {
                    // Try to catch nested describe returns safely
                    if (!is_array($values) && !($values instanceof NDArray)) {
                        $values = [$values];
                    }
                    $this->data[$col] = new Series($values, null, (string)$col);
                    $len = count($this->data[$col]->getData());
                }

                if ($length === -1) {
                    $length = $len;
                } elseif ($length !== $len) {
                    throw new \InvalidArgumentException("All arrays must be of the same length.");
                }
            }
            $this->index = $index ?? range(0, $length - 1);
        } else {
            // It's a list of lists (2D array)
            if ($columns !== null) {
                $this->columns = $columns;
            } else {
                $this->columns = range(0, count($data[0]) - 1);
            }

            $length = count($data);
            $this->index = $index ?? range(0, $length - 1);

            // Transpose and assign
            for ($c = 0; $c < count($this->columns); $c++) {
                $col_name = $this->columns[$c];
                $col_data = [];
                for ($r = 0; $r < $length; $r++) {
                    $col_data[] = $data[$r][$c] ?? null;
                }
                $this->data[$col_name] = new Series($col_data, $this->index, (string)$col_name);
            }
        }

        // Apply index to all series
        foreach ($this->data as $col => $series) {
            $this->data[$col] = new Series($series->getData(), $this->index, $series->getName());
        }
    }

    private function isAssoc(array $arr): bool
    {
        if (array() === $arr) return false;
        // Check if any key is a string (meaning it's likely column names)
        foreach (array_keys($arr) as $key) {
            if (is_string($key)) return true;
        }
        return false;
    }

    public function head(int $n = 5): DataFrame
    {
        return $this->iloc(0, $n);
    }

    public function tail(int $n = 5): DataFrame
    {
        $len = count($this->index);
        return $this->iloc(max(0, $len - $n), $len);
    }

    public function shape(): array
    {
        return [count($this->index), count($this->columns)];
    }

    public function getColumn(string $name): Series
    {
        if (!isset($this->data[$name])) {
            throw new \InvalidArgumentException("Column '$name' not found.");
        }
        return clone $this->data[$name];
    }

    // Select subset of rows by integer location
    public function iloc(int $start, ?int $end = null): DataFrame
    {
        if ($end === null) $end = count($this->index);
        if ($start < 0) $start = count($this->index) + $start;
        if ($end < 0) $end = count($this->index) + $end;
        if ($end > count($this->index)) $end = count($this->index);

        $new_data = [];
        $new_index = array_slice($this->index, $start, $end - $start);

        foreach ($this->columns as $col) {
            $col_data = array_slice($this->data[$col]->getData(), $start, $end - $start);
            $new_data[$col] = clone new Series($col_data, $new_index, $col);
        }

        // Use a trick to pass pre-built series to DataFrame
        $df = new DataFrame();
        $df->columns = $this->columns;
        $df->index = $new_index;
        $df->data = $new_data;
        return $df;
    }

    // Select subset of columns
    public function getColumns(array $cols): DataFrame
    {
        $new_data = [];
        foreach ($cols as $col) {
            if (isset($this->data[$col])) {
                $new_data[$col] = $this->data[$col]->getData();
            }
        }
        return new DataFrame($new_data, $this->index, $cols);
    }

    public function describe(): DataFrame
    {
        $stats = [];
        $stat_names = ['count', 'mean', 'std', 'min', 'max'];
        $valid_cols = [];

        foreach ($this->columns as $col) {
            $desc = $this->data[$col]->describe();
            if (isset($desc['mean'])) { // If numeric
                $stats[$col] = [
                    $desc['count'],
                    $desc['mean'],
                    $desc['std'],
                    $desc['min'],
                    $desc['max']
                ];
                $valid_cols[] = $col;
            }
        }

        // Return a DataFrame where rows are stats and columns are original columns
        // $stats is ['age' => [count, mean, std, min, max], 'score' => [count, mean, std, min, max]]
        $df = new DataFrame($stats, $stat_names);
        return $df;
    }

    public function to_csv(string $filepath, array $options = [])
    {
        $delimiter = $options['delimiter'] ?? ',';
        $index = $options['index'] ?? true;

        $file = fopen($filepath, 'w');

        // Write header
        $header = $this->columns;
        if ($index) {
            array_unshift($header, '');
        }
        fputcsv($file, $header, $delimiter);

        // Write rows
        $shape = $this->shape();
        for ($i = 0; $i < $shape[0]; $i++) {
            $row = [];
            if ($index) {
                $row[] = $this->index[$i];
            }
            foreach ($this->columns as $col) {
                $row[] = $this->data[$col]->getData()[$i];
            }
            fputcsv($file, $row, $delimiter);
        }

        fclose($file);
    }

    public function __get(string $name)
    {
        if (in_array($name, $this->columns)) {
            return $this->getColumn($name);
        }
        throw new \Exception("Property $name does not exist.");
    }

    public function __toString(): string
    {
        $output = "";
        $max_rows = 10;
        $count = count($this->index);

        $header = str_pad("", 10) . " ";
        foreach ($this->columns as $col) {
            $header .= str_pad((string)$col, 15) . " ";
        }
        $output .= "\n" . rtrim($header) . "\n";

        if ($count > $max_rows) {
            for ($i = 0; $i < 5; $i++) {
                $output .= $this->rowToString($i);
            }
            $output .= "...\n";
            for ($i = $count - 5; $i < $count; $i++) {
                $output .= $this->rowToString($i);
            }
        } else {
            for ($i = 0; $i < $count; $i++) {
                $output .= $this->rowToString($i);
            }
        }

        $output .= "\n[{$count} rows x " . count($this->columns) . " columns]\n";
        return $output;
    }

    private function rowToString(int $rowIdx): string
    {
        $str = str_pad((string)$this->index[$rowIdx], 10) . " ";
        foreach ($this->columns as $col) {
            $val = $this->data[$col]->getData()[$rowIdx];
            if (is_numeric($val) && is_float($val)) {
                $val = number_format($val, 4, '.', '');
            }
            $str .= str_pad((string)$val, 15) . " ";
        }
        return rtrim($str) . "\n";
    }
}
