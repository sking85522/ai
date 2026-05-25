# 📷 VisionPHP — Computer Vision Library

> **Python Equivalent:** OpenCV (cv2)
> **Purpose:** Image processing, filtering, transformation, drawing

---

## Image I/O

### `imread(filepath)` — Load image
```php
$img = VisionPHP::imread('photo.jpg');
```

### `create(width, height)` — Create blank image
```php
$canvas = VisionPHP::create(800, 600); // 800×600 blank image
```

### `imwrite(filepath, img)` — Save image
```php
VisionPHP::imwrite('output.png', $img);
```

---

## Filters

### `cvtColor(img, mode)` — Color conversion
```php
$gray = VisionPHP::cvtColor($img, 'GRAY'); // Convert to grayscale
```

### `GaussianBlur(img, radius)` — Blur
```php
$blurred = VisionPHP::GaussianBlur($img, 5);
```

### `Sobel(img)` — Edge Detection
```php
$edges = VisionPHP::Sobel($gray);
```

---

## Thresholding

### `threshold(img, value)` — Binary Threshold
Pixels above value → 255 (white), below → 0 (black).
```php
$binary = VisionPHP::threshold($gray, 128);
```

### `thresholdInverse(img, value)` — Inverse Binary
```php
$inverse = VisionPHP::thresholdInverse($gray, 128);
```

### `thresholdOtsu(img)` — Otsu's Automatic Threshold
Finds optimal threshold automatically using histogram analysis.
```php
$auto = VisionPHP::thresholdOtsu($gray); // No value needed!
```

---

## Transformations

### `resize(img, width, height)` — Resize image
```php
$small = VisionPHP::resize($img, 320, 240);
$large = VisionPHP::resize($img, 1920, 1080);
```

### `crop(img, x, y, width, height)` — Crop region
```php
$face = VisionPHP::crop($img, 100, 50, 200, 200);
```

### `rotate(img, angle)` — Rotate image
```php
$rotated = VisionPHP::rotate($img, 90);   // 90 degrees
$rotated = VisionPHP::rotate($img, 45);   // 45 degrees
```

### `flipH(img)` / `flipV(img)` — Flip
```php
$mirror = VisionPHP::flipH($img);   // Horizontal flip (mirror)
$flipped = VisionPHP::flipV($img);  // Vertical flip
```

---

## Drawing

### `rectangle(img, x1, y1, x2, y2, color, thickness)`
```php
$img = VisionPHP::rectangle($img, 10, 10, 200, 150, [0, 255, 0], 3);
// Green rectangle, 3px border
```

### `circle(img, cx, cy, radius, color, thickness)`
```php
$img = VisionPHP::circle($img, 100, 100, 50, [255, 0, 0], 2);
// Red circle at (100,100), radius 50
```

### `line(img, x1, y1, x2, y2, color, thickness)`
```php
$img = VisionPHP::line($img, 0, 0, 400, 300, [255, 255, 0], 2);
```

### `putText(img, text, x, y, color, fontSize)`
```php
$img = VisionPHP::putText($img, "Hello World!", 50, 50, [255, 255, 255], 5);
```

---

## Full Example — Image Processing Pipeline
```php
use VisionPHP\VisionPHP as cv;

$img = cv::imread('photo.jpg');
$resized = cv::resize($img, 640, 480);
$gray = cv::cvtColor($resized, 'GRAY');
$edges = cv::Sobel($gray);
$binary = cv::thresholdOtsu($gray);

// Annotate
$annotated = cv::rectangle($resized, 50, 50, 300, 200, [0,255,0], 3);
$annotated = cv::putText($annotated, "Detected!", 60, 220, [0,255,0]);
cv::imwrite('result.jpg', $annotated);
```
