<?php

namespace PlotPHP\Render;

use PlotPHP\Core\Figure;

class SVGRenderer
{
    private $fig;
    private $margin = ['top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 70];
    private $plot_width;
    private $plot_height;

    public function __construct(Figure $fig)
    {
        $this->fig = $fig;
        $this->plot_width = $this->fig->getWidth() - $this->margin['left'] - $this->margin['right'];
        $this->plot_height = $this->fig->getHeight() - $this->margin['top'] - $this->margin['bottom'];
    }

    public function render(): string
    {
        $svg = [];
        $svg[] = sprintf(
            '<svg width="%d" height="%d" xmlns="http://www.w3.org/2000/svg" style="background-color: white; font-family: Arial, sans-serif;">',
            $this->fig->getWidth(),
            $this->fig->getHeight()
        );

        // Draw Title
        if ($this->fig->getTitle() !== '') {
            $svg[] = sprintf(
                '<text x="%d" y="%d" text-anchor="middle" font-size="20" font-weight="bold">%s</text>',
                $this->fig->getWidth() / 2,
                30,
                htmlspecialchars($this->fig->getTitle())
            );
        }

        // Draw Labels
        if ($this->fig->getXLabel() !== '') {
            $svg[] = sprintf(
                '<text x="%d" y="%d" text-anchor="middle" font-size="14">%s</text>',
                $this->margin['left'] + ($this->plot_width / 2),
                $this->fig->getHeight() - 10,
                htmlspecialchars($this->fig->getXLabel())
            );
        }
        if ($this->fig->getYLabel() !== '') {
            $svg[] = sprintf(
                '<text x="%d" y="%d" text-anchor="middle" font-size="14" transform="rotate(-90, %d, %d)">%s</text>',
                20,
                $this->margin['top'] + ($this->plot_height / 2),
                20,
                $this->margin['top'] + ($this->plot_height / 2),
                htmlspecialchars($this->fig->getYLabel())
            );
        }

        // Calculate data bounds
        $bounds = $this->calculateBounds();

        // Draw Grid
        if ($this->fig->hasGrid()) {
            // Very basic grid
            for ($i = 1; $i < 5; $i++) {
                $y = $this->margin['top'] + ($this->plot_height / 5) * $i;
                $svg[] = sprintf('<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="#e0e0e0" stroke-width="1" />',
                    $this->margin['left'], $y,
                    $this->margin['left'] + $this->plot_width, $y
                );

                $x = $this->margin['left'] + ($this->plot_width / 5) * $i;
                $svg[] = sprintf('<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="#e0e0e0" stroke-width="1" />',
                    $x, $this->margin['top'],
                    $x, $this->margin['top'] + $this->plot_height
                );
            }
        }

        // Draw Axes (Box)
        $svg[] = sprintf(
            '<rect x="%d" y="%d" width="%d" height="%d" fill="none" stroke="black" stroke-width="1" />',
            $this->margin['left'],
            $this->margin['top'],
            $this->plot_width,
            $this->plot_height
        );

        // Draw Elements
        foreach ($this->fig->getElements() as $element) {
            $type = $element['type'];
            $data = $element['data'];
            $color = $element['color'] ?? 'blue';
            $linewidth = $element['linewidth'] ?? 2;

            if ($type === 'line') {
                $points = [];
                foreach ($data as $idx => $point) {
                    $x_px = $this->mapX($point[0], $bounds);
                    $y_px = $this->mapY($point[1], $bounds);
                    $points[] = "$x_px,$y_px";
                }
                $svg[] = sprintf(
                    '<polyline points="%s" fill="none" stroke="%s" stroke-width="%f" />',
                    implode(' ', $points),
                    htmlspecialchars($color),
                    $linewidth
                );
            } elseif ($type === 'scatter') {
                $size = $element['size'] ?? 4;
                foreach ($data as $point) {
                    $x_px = $this->mapX($point[0], $bounds);
                    $y_px = $this->mapY($point[1], $bounds);
                    $svg[] = sprintf(
                        '<circle cx="%f" cy="%f" r="%f" fill="%s" />',
                        $x_px,
                        $y_px,
                        $size,
                        htmlspecialchars($color)
                    );
                }
            } elseif ($type === 'bar') {
                $bar_width = ($this->plot_width / max(1, count($data))) * 0.8;
                foreach ($data as $point) {
                    $x_px = $this->mapX($point[0], $bounds);
                    // Map Y from 0 (if positive) to the value
                    $y0_px = $this->mapY(min(0, max($bounds['y_min'], 0)), $bounds);
                    $y1_px = $this->mapY($point[1], $bounds);

                    $h = abs($y1_px - $y0_px);
                    $y_start = min($y1_px, $y0_px);

                    $svg[] = sprintf(
                        '<rect x="%f" y="%f" width="%f" height="%f" fill="%s" />',
                        $x_px - ($bar_width / 2),
                        $y_start,
                        $bar_width,
                        $h,
                        htmlspecialchars($color)
                    );
                }
            }
        }

        $svg[] = '</svg>';
        return implode("\n", $svg);
    }

    private function calculateBounds(): array
    {
        $x_min = PHP_FLOAT_MAX;
        $x_max = -PHP_FLOAT_MAX;
        $y_min = PHP_FLOAT_MAX;
        $y_max = -PHP_FLOAT_MAX;

        foreach ($this->fig->getElements() as $element) {
            foreach ($element['data'] as $point) {
                if ($point[0] < $x_min) $x_min = $point[0];
                if ($point[0] > $x_max) $x_max = $point[0];
                if ($point[1] < $y_min) $y_min = $point[1];
                if ($point[1] > $y_max) $y_max = $point[1];
            }
        }

        if ($x_min == $x_max) { $x_min -= 1; $x_max += 1; }
        if ($y_min == $y_max) { $y_min -= 1; $y_max += 1; }

        // Override with user limits
        if ($this->fig->getXLim() !== null) {
            $x_min = $this->fig->getXLim()['min'];
            $x_max = $this->fig->getXLim()['max'];
        }
        if ($this->fig->getYLim() !== null) {
            $y_min = $this->fig->getYLim()['min'];
            $y_max = $this->fig->getYLim()['max'];
        }

        // Add a little padding to the data bounds if not explicitly set
        if ($this->fig->getYLim() === null) {
            $y_range = $y_max - $y_min;
            $y_max += $y_range * 0.05;
            $y_min -= $y_range * 0.05;
        }

        return ['x_min' => $x_min, 'x_max' => $x_max, 'y_min' => $y_min, 'y_max' => $y_max];
    }

    private function mapX(float $x, array $bounds): float
    {
        $range = $bounds['x_max'] - $bounds['x_min'];
        if ($range == 0) $range = 1;
        $fraction = ($x - $bounds['x_min']) / $range;
        return $this->margin['left'] + ($fraction * $this->plot_width);
    }

    private function mapY(float $y, array $bounds): float
    {
        $range = $bounds['y_max'] - $bounds['y_min'];
        if ($range == 0) $range = 1;
        $fraction = ($y - $bounds['y_min']) / $range;
        // In SVG, Y=0 is at the top, so invert the fraction
        return $this->margin['top'] + ((1 - $fraction) * $this->plot_height);
    }
}
