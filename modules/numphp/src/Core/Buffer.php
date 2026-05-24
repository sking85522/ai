<?php

namespace NumPHP\Core;

/**
 * Buffer handles raw data storage.
 */
class Buffer
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var DType
     */
    private $dtype;

    /**
     * Buffer constructor.
     * @param mixed $data
     * @param DType $dtype
     */
    public function __construct($data, DType $dtype)
    {
        $this->dtype = $dtype;
        $this->data = $this->castData($data);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return int[]
     */
    public function getShape(): array
    {
        return $this->calculateShape($this->data);
    }

    /**
     * @param mixed $data
     * @return array
     */
    private function castData($data): array
    {
        if (is_array($data)) {
            return array_map([$this, 'castData'], $data);
        }

        settype($data, (string) $this->dtype);
        return $data;
    }

    /**
     * @param mixed $data
     * @return int[]
     */
    private function calculateShape($data): array
    {
        $shape = [];
        if (is_array($data)) {
            $shape[] = count($data);
            if (isset($data[0])) {
                $shape = array_merge($shape, $this->calculateShape($data[0]));
            }
        }
        return $shape;
    }
}