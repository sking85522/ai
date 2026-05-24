<?php

namespace PandaPHP\Core;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class Series
{
    private $data;
    private $index;
    private $name;

    public function __construct($data, $index = null, string $name = '')
    {
        // Extract array from NDArray if provided
        $raw_data = ($data instanceof NDArray) ? $data->getData() : (is_array($data) ? $data : [$data]);

        $this->data = array_values($raw_data);

        if ($index === null) {
            $this->index = array_keys($this->data); // Defaults to 0, 1, 2...
        } else {
            $idx = ($index instanceof NDArray) ? $index->getData() : (is_array($index) ? $index : [$index]);
            if (count($idx) !== count($this->data)) {
                throw new \InvalidArgumentException("Length of passed values is " . count($this->data) . ", index implies " . count($idx));
            }
            $this->index = array_values($idx);
        }
        $this->name = $name;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getIndex(): array
    {
        return $this->index;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function values(): NDArray
    {
        return np::array($this->data);
    }

    public function sum()
    {
        return array_sum($this->data);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function mean()
    {
        $count = count($this->data);
        return $count > 0 ? $this->sum() / $count : 0;
    }

    public function describe(): array
    {
        $numeric_data = array_filter($this->data, 'is_numeric');
        $count = count($numeric_data);
        if ($count == 0) return ['count' => count($this->data)];

        $sum = array_sum($numeric_data);
        $mean = $sum / $count;

        $variance = 0.0;
        foreach ($numeric_data as $val) {
            $variance += pow($val - $mean, 2);
        }
        $std = $count > 1 ? sqrt($variance / ($count - 1)) : 0;

        $sorted = $numeric_data;
        sort($sorted);
        $min = $sorted[0];
        $max = $sorted[$count - 1];

        return [
            'count' => $count,
            'mean' => $mean,
            'std' => $std,
            'min' => $min,
            'max' => $max
        ];
    }

    public function __toString(): string
    {
        $output = "";
        $max_rows = 10;
        $count = count($this->data);

        if ($count > $max_rows) {
            for ($i = 0; $i < 5; $i++) {
                $output .= str_pad($this->index[$i], 10) . " " . $this->data[$i] . "\n";
            }
            $output .= "...\n";
            for ($i = $count - 5; $i < $count; $i++) {
                $output .= str_pad($this->index[$i], 10) . " " . $this->data[$i] . "\n";
            }
        } else {
            for ($i = 0; $i < $count; $i++) {
                $output .= str_pad($this->index[$i], 10) . " " . $this->data[$i] . "\n";
            }
        }

        if ($this->name !== '') {
            $output .= "Name: {$this->name}, ";
        }
        $output .= "Length: {$count}\n";

        return $output;
    }
}
