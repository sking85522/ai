<?php

namespace NumPHP\Core;

/**
 * Shape stores array dimensions.
 */
class Shape
{
    /**
     * @var int[]
     */
    private $shape;

    /**
     * Shape constructor.
     * @param int[] $shape
     */
    public function __construct(array $shape)
    {
        $this->shape = $shape;
    }

    /**
     * @return int[]
     */
    public function getShape(): array
    {
        return $this->shape;
    }

    /**
     * @return int
     */
    public function getNumDimensions(): int
    {
        return count($this->shape);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return '[' . implode(', ', $this->shape) . ']';
    }
}