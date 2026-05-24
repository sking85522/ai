<?php

namespace NumPHP\Core;

/**
 * Tensor is a multi-dimensional matrix containing elements of a single data type.
 */
abstract class Tensor
{
    /**
     * @var Buffer
     */
    protected $data;

    /**
     * @var Shape
     */
    protected $shape;

    /**
     * @var DType
     */
    protected $dtype;

    /**
     * @return Shape
     */
    public function shape(): Shape
    {
        return $this->shape;
    }

    /**
     * @return DType
     */
    public function dtype(): DType
    {
        return $this->dtype;
    }

    /**
     * @return Buffer
     */
    public function data(): Buffer
    {
        return $this->data;
    }
}