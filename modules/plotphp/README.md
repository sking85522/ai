# 📈 PlotPHP — Charts & Visualization

> **Python Equivalent:** Matplotlib
> **Purpose:** Generate SVG charts — line, scatter, bar

---

## Quick Start
```php
use PlotPHP\PlotPHP as plt;

plt::plot([1,2,3,4], [10,20,15,30], 'blue');
plt::title("Sales");
plt::savefig("chart.svg");
```

---

## Chart Types

### `plot(x, y, color, linewidth)` — Line Chart
```php
plt::plot([1,2,3,4,5], [2,4,3,5,7], 'blue', 2.0);
```

### `scatter(x, y, size, color)` — Scatter Plot
```php
plt::scatter([1,2,3], [5,3,8], 4.0, 'red');
```

### `bar(x, height, width, color)` — Bar Chart
```php
plt::bar(['Q1','Q2','Q3'], [100, 150, 120], 0.8, 'green');
```

---

## Labels & Styling

| Function | Description |
|----------|-------------|
| `title(label)` | Set chart title |
| `xlabel(label)` | Set X axis label |
| `ylabel(label)` | Set Y axis label |
| `grid(visible)` | Show/hide grid lines |

---

## Output

### `savefig(filename)` — Save as SVG file
```php
plt::savefig("output.svg");
```

### `show(return)` — Display or return SVG
```php
plt::show();              // Echo SVG to browser
$svg = plt::show(true);   // Return SVG string
```

### `clf()` — Clear current figure
```php
plt::clf(); // Reset for new chart
```

---

## Full Example
```php
use PlotPHP\PlotPHP as plt;

$x = range(0, 10);
$y = array_map(fn($v) => sin($v), $x);

plt::plot($x, $y, 'blue', 2);
plt::scatter($x, $y, 3, 'red');
plt::title("Sine Wave");
plt::xlabel("X");
plt::ylabel("sin(X)");
plt::grid(true);
plt::savefig("sine.svg");
plt::clf();
```
