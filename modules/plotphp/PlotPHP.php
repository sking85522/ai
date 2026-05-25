<?php

namespace PlotPHP;

use PlotPHP\Core\Figure;
use PlotPHP\Render\SVGRenderer;
use NumPHP\Core\NDArray;

class PlotPHP
{
    /** @var Figure|null */
    private static $current_fig = null;

    /**
     * Get or create current figure.
     */
    private static function gcf(): Figure
    {
        if (self::$current_fig === null) {
            self::$current_fig = new Figure();
        }
        return self::$current_fig;
    }

    /**
     * Clear the current figure.
     */
    public static function clf()
    {
        self::$current_fig = null;
    }

    /**
     * Plot lines and/or markers to the Axes.
     * Approximates matplotlib.pyplot.plot
     *
     * @param array|NDArray $x
     * @param array|NDArray $y
     * @param string $color
     * @param float $linewidth
     */
    public static function plot($x, $y = null, string $color = 'blue', float $linewidth = 2.0)
    {
        $fig = self::gcf();

        $x_data = ($x instanceof NDArray) ? $x->getData() : $x;

        if ($y === null) {
            $y_data = $x_data;
            $x_data = range(0, count($y_data) - 1);
        } else {
            $y_data = ($y instanceof NDArray) ? $y->getData() : $y;
        }

        $data = [];
        for ($i = 0; $i < count($x_data); $i++) {
            $data[] = [$x_data[$i], $y_data[$i]];
        }

        $fig->addElement([
            'type' => 'line',
            'data' => $data,
            'color' => $color,
            'linewidth' => $linewidth
        ]);
    }

    /**
     * A scatter plot of y vs. x with varying marker size and/or color.
     * Approximates matplotlib.pyplot.scatter
     */
    public static function scatter($x, $y, float $size = 4.0, string $color = 'red')
    {
        $fig = self::gcf();
        $x_data = ($x instanceof NDArray) ? $x->getData() : $x;
        $y_data = ($y instanceof NDArray) ? $y->getData() : $y;

        $data = [];
        for ($i = 0; $i < count($x_data); $i++) {
            $data[] = [$x_data[$i], $y_data[$i]];
        }

        $fig->addElement([
            'type' => 'scatter',
            'data' => $data,
            'color' => $color,
            'size' => $size
        ]);
    }

    /**
     * Make a bar plot.
     * Approximates matplotlib.pyplot.bar
     */
    public static function bar($x, $height, float $width = 0.8, string $color = 'green')
    {
        $fig = self::gcf();
        $x_data = ($x instanceof NDArray) ? $x->getData() : $x;
        $h_data = ($height instanceof NDArray) ? $height->getData() : $height;

        $data = [];
        for ($i = 0; $i < count($x_data); $i++) {
            $data[] = [$x_data[$i], $h_data[$i]];
        }

        $fig->addElement([
            'type' => 'bar',
            'data' => $data,
            'color' => $color,
            'width' => $width
        ]);
    }

    /**
     * Set a title for the axes.
     */
    public static function title(string $label)
    {
        self::gcf()->setTitle($label);
    }

    /**
     * Set the x axis label of the current axis.
     */
    public static function xlabel(string $xlabel)
    {
        self::gcf()->setXLabel($xlabel);
    }

    /**
     * Set the y axis label of the current axis.
     */
    public static function ylabel(string $ylabel)
    {
        self::gcf()->setYLabel($ylabel);
    }

    /**
     * Configure the grid lines.
     */
    public static function grid(bool $visible = true)
    {
        self::gcf()->setGrid($visible);
    }

    /**
     * Save the current figure.
     * Approximates matplotlib.pyplot.savefig
     */
    public static function savefig(string $fname)
    {
        $renderer = new SVGRenderer(self::gcf());
        file_put_contents($fname, $renderer->render());
    }

    /**
     * Display all open figures (In PHP, this returns the SVG string or echo it).
     */
    public static function show(bool $return = false)
    {
        $renderer = new SVGRenderer(self::gcf());
        $svg = $renderer->render();
        self::clf(); // Clear after showing

        if ($return) {
            return $svg;
        }
        echo $svg;
    }
}
