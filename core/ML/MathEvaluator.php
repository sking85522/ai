<?php
namespace Core\ML;

// Include SciPHP if available
if (file_exists(dirname(__DIR__, 2) . '/modules/sciphp/autoload.php')) {
    require_once dirname(__DIR__, 2) . '/modules/sciphp/autoload.php';
}

class MathEvaluator {
    
    /**
     * Evaluates a mathematical string expression.
     * Support for basic arithmetic and SciPHP advanced functions.
     */
    public function evaluate(string $expression): string {
        $expression = preg_replace('/[^0-9\+\-\*\/\(\)\. ]/', '', $expression);
        
        try {
            // Using a safe eval wrapper (simplified for local execution)
            $result = eval("return ($expression);");
            return (string)$result;
        } catch (\Throwable $e) {
            return "Math Error: Invalid Expression";
        }
    }

    /**
     * Performs vector operations using NumPHP or SciPHP.
     */
    public function crossProduct(array $a, array $b): array {
        // Mock cross product or use SciPHP/NumPHP if loaded
        return [];
    }

    public function evaluateMathQuery(string $input): float|string|null
    {
        $basic = $this->evaluate($input);
        if ($basic !== null) {
            return $basic;
        }

        $eq = $this->evaluateEquality($input);
        if ($eq !== null) {
            return $eq;
        }

        $square = $this->evaluateSquareQuery($input);
        if ($square !== null) {
            return $square;
        }

        $advanced = $this->evaluateAdvanced($input);
        if ($advanced !== null) {
            return $advanced;
        }

        return null;
    }

    public function evaluate(string $input): ?float
    {
        $text = trim($input);
        if (!preg_match('/^\s*-?\d+(\.\d+)?\s*[\+\-\*\/]\s*-?\d+(\.\d+)?\s*$/', $text)) {
            return null;
        }

        if (!preg_match('/^\s*(-?\d+(?:\.\d+)?)\s*([\+\-\*\/])\s*(-?\d+(?:\.\d+)?)\s*$/', $text, $m)) {
            return null;
        }

        $a = (float) $m[1];
        $op = $m[2];
        $b = (float) $m[3];

        return match ($op) {
            '+' => $a + $b,
            '-' => $a - $b,
            '*' => $a * $b,
            '/' => $b == 0.0 ? null : $a / $b,
            default => null,
        };
    }

    public function evaluateSquareQuery(string $input): ?float
    {
        $text = mb_strtolower(trim($input), 'UTF-8');
        if (preg_match('/(?:square|squre|sqr)\s+(?:of\s+)?(-?\d+(?:\.\d+)?)/u', $text, $m) === 1) {
            $n = (float) $m[1];
            return $n * $n;
        }
        if (preg_match('/(-?\d+(?:\.\d+)?)\s*(?:ka|का)?\s*(?:square|squre|sqr)/u', $text, $m) === 1) {
            $n = (float) $m[1];
            return $n * $n;
        }
        return null;
    }

    public function evaluateAdvanced(string $input): float|string|null
    {
        $text = mb_strtolower(trim($input), 'UTF-8');

        if (preg_match('/(?:sin|cos|tan)\s*\(?\s*(-?\d+(?:\.\d+)?)\s*(?:deg|degree|degrees|°)?\s*\)?/u', $text, $m) === 1) {
            $angle = deg2rad((float) $m[1]);
            if (str_contains($text, 'sin')) {
                return round(sin($angle), 6);
            }
            if (str_contains($text, 'cos')) {
                return round(cos($angle), 6);
            }
            return round(tan($angle), 6);
        }

        if (preg_match('/(?:magnitude|modulus)\s+(?:of\s+)?vector\s*\(?\s*(-?\d+(?:\.\d+)?)\s*[, ]\s*(-?\d+(?:\.\d+)?)\s*\)?/u', $text, $m) === 1) {
            $x = (float) $m[1];
            $y = (float) $m[2];
            return round(sqrt(($x * $x) + ($y * $y)), 6);
        }

        if (preg_match('/dot\s*product\s*\(?\s*(-?\d+(?:\.\d+)?)\s*[, ]\s*(-?\d+(?:\.\d+)?)\s*\)?\s*(?:and|,)\s*\(?\s*(-?\d+(?:\.\d+)?)\s*[, ]\s*(-?\d+(?:\.\d+)?)\s*\)?/u', $text, $m) === 1) {
            $a1 = (float) $m[1];
            $a2 = (float) $m[2];
            $b1 = (float) $m[3];
            $b2 = (float) $m[4];
            return round(($a1 * $b1) + ($a2 * $b2), 6);
        }

        if (preg_match('/area\s+(?:of\s+)?circle\s+(?:radius|r)\s*(-?\d+(?:\.\d+)?)/u', $text, $m) === 1) {
            $r = (float) $m[1];
            return round(M_PI * $r * $r, 6);
        }

        if (preg_match('/area\s+(?:of\s+)?triangle\s+(?:base|b)\s*(-?\d+(?:\.\d+)?)\s*(?:height|h)\s*(-?\d+(?:\.\d+)?)/u', $text, $m) === 1) {
            $b = (float) $m[1];
            $h = (float) $m[2];
            return round(0.5 * $b * $h, 6);
        }

        if (preg_match('/(?:derivative|differentiate|avkalan|अवकलन)\s+(?:of\s+)?(-?\d+(?:\.\d+)?)x\^(-?\d+(?:\.\d+)?)/u', $text, $m) === 1) {
            $a = (float) $m[1];
            $n = (float) $m[2];
            $coef = $a * $n;
            $pow = $n - 1;
            return $coef . 'x^' . $pow;
        }

        if (preg_match('/(?:integral|integrate|samakalan|समाकलन)\s+(?:of\s+)?(-?\d+(?:\.\d+)?)x\^(-?\d+(?:\.\d+)?)/u', $text, $m) === 1) {
            $a = (float) $m[1];
            $n = (float) $m[2];
            $pow = $n + 1;
            if ($pow == 0.0) {
                return 'undefined (division by zero)';
            }
            $coef = $a / $pow;
            return $coef . 'x^' . $pow . ' + C';
        }

        return null;
    }

    public function evaluateEquality(string $input): ?string
    {
        $text = trim($input);
        if (preg_match('/^\s*(-?\d+(?:\.\d+)?)\s*=\s*(-?\d+(?:\.\d+)?)\s*$/', $text, $m) !== 1) {
            return null;
        }
        $a = (float) $m[1];
        $b = (float) $m[2];
        return $a === $b ? 'True (equal)' : 'False (not equal)';
    }
}