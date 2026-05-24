<?php

namespace NumPHP\Core;

/**
 * DType defines the data type of elements.
 */
class DType
{
    const INT = 'int';
    const FLOAT = 'float';
    const BOOL = 'bool';

    /**
     * @var string
     */
    private $type;

    /**
     * DType constructor.
     * @param string|null $type
     */
    public function __construct(string $type = null)
    {
        if (is_null($type)) {
            $this->type = self::FLOAT;
        } elseif ($this->isValidType($type)) {
            $this->type = $type;
        } else {
            // Throw an exception for invalid type
            throw new \InvalidArgumentException("Invalid dtype: " . $type);
        }
    }

    /**
     * @param string $type
     * @return bool
     */
    private function isValidType(string $type): bool
    {
        return in_array($type, [self::INT, self::FLOAT, self::BOOL]);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->type;
    }
}