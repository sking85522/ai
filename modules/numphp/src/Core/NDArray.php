<?php

namespace NumPHP\Core;

class NDArray
{
    private $data;
    private $shape;
    private $dtype;
    private $size;

    public function __construct($data, $dtype = null)
    {
        if (!is_array($data) && !is_numeric($data)) {
            throw new \InvalidArgumentException("Data must be an array or a number.");
        }

        $this->data = $data;
        $this->shape = $this->calculateShape($data);
        $this->size = $this->calculateSize($this->shape);
        $this->dtype = $dtype ?? $this->detectDType($data);
    }

    private function calculateShape($data)
    {
        if (!is_array($data) ) {
            return [];
        }

        $shape = [];
        $level = $data;
        while (is_array($level)) {
            $shape[] = count($level);
            $level = $level[0] ?? null;
        }
        return $shape;
    }

    private function calculateSize($shape)
    {
        if (empty($shape)) {
            return 1;
        }
        return array_product($shape);
    }

    private function isHomogeneousArray(array $arr): bool {
        if (empty($arr)) return true;
        $firstType = gettype($arr[0]);
        foreach ($arr as $item) {
            if (gettype($item) !== $firstType) return false;
            if (is_array($item) && !$this->isHomogeneousArray($item)) return false;
        }
        return true;
    }

    private function detectDType($data)
    {
       if (is_numeric($data)) {
           return is_int($data) ? 'int' : 'float';
       }
        if (is_bool($data)) {
            return 'bool';
        }
        if (is_array($data)) {
            $firstValue = $this->getFirstValue($data);
            return $this->detectDType($firstValue);
        }
        return 'mixed';
    }

    private function getFirstValue($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        if (empty($data)) {
            return null;
        }
        $firstValue = $data[0];
        while (is_array($firstValue)) {
            if (empty($firstValue)) {
                return null;
            }
            $firstValue = $firstValue[0];
        }
        return $firstValue;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getShape()
    {
        return $this->shape;
    }

    public function getDType()
    {
        return $this->dtype;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function ndim(): int
    {
        return count($this->shape);
    }

    public function setData($data): void
    {
        $this->data = $data;
        $this->shape = $this->calculateShape($data);
        $this->size = $this->calculateSize($this->shape);
        $this->dtype = $this->detectDType($data);
    }

    public function __toString()
    {
        return '[' . implode(', ', $this->shape) . ']';
    }
}
