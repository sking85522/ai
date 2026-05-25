<?php

namespace PlotPHP\Core;

class Figure
{
    private $width;
    private $height;
    private $elements = [];
    private $title = '';
    private $xlabel = '';
    private $ylabel = '';
    private $grid = false;
    private $x_lim = null;
    private $y_lim = null;

    public function __construct(int $width = 800, int $height = 600)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function addElement(array $element)
    {
        $this->elements[] = $element;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setXLabel(string $xlabel)
    {
        $this->xlabel = $xlabel;
    }

    public function setYLabel(string $ylabel)
    {
        $this->ylabel = $ylabel;
    }

    public function setGrid(bool $grid)
    {
        $this->grid = $grid;
    }

    public function setXLim(float $min, float $max)
    {
        $this->x_lim = ['min' => $min, 'max' => $max];
    }

    public function setYLim(float $min, float $max)
    {
        $this->y_lim = ['min' => $min, 'max' => $max];
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getXLabel(): string
    {
        return $this->xlabel;
    }

    public function getYLabel(): string
    {
        return $this->ylabel;
    }

    public function hasGrid(): bool
    {
        return $this->grid;
    }

    public function getXLim(): ?array
    {
        return $this->x_lim;
    }

    public function getYLim(): ?array
    {
        return $this->y_lim;
    }
}
