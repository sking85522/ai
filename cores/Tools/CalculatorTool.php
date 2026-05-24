<?php
namespace Core\Tools;

class CalculatorTool implements ToolInterface {
    private const OPERATORS = ['+' => 1, '-' => 1, '*' => 2, '/' => 2, '^' => 3, 'u-' => 4];

    public function execute(array $input = []): array {
        $expression = trim((string)($input['expression'] ?? ''));
        if ($expression === '') {
            return ['status' => 'error', 'message' => 'Expression is empty.'];
        }

        $expression = str_replace(['x', 'X'], '*', $expression);
        if (!preg_match('/^[0-9+\-*\/^().\s]+$/', $expression)) {
            return ['status' => 'error', 'message' => 'Only arithmetic expressions are supported.'];
        }

        try {
            $tokens = $this->tokenize($expression);
            $rpn = $this->toReversePolish($tokens);
            $result = $this->evaluateReversePolish($rpn);
            return [
                'status' => 'success',
                'expression' => preg_replace('/\s+/', '', $expression),
                'result' => $this->formatNumber($result)
            ];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function tokenize(string $expression): array {
        preg_match_all('/\d+(?:\.\d+)?|[+\-*\/^()]|\S/', $expression, $matches);
        return $matches[0] ?? [];
    }

    private function toReversePolish(array $tokens): array {
        $output = [];
        $stack = [];
        $previous = null;

        foreach ($tokens as $token) {
            if (is_numeric($token)) {
                $output[] = $token;
                $previous = 'number';
                continue;
            }

            if ($token === '(') {
                $stack[] = $token;
                $previous = '(';
                continue;
            }

            if ($token === ')') {
                while (!empty($stack) && end($stack) !== '(') {
                    $output[] = array_pop($stack);
                }
                if (empty($stack)) {
                    throw new \InvalidArgumentException('Mismatched parentheses.');
                }
                array_pop($stack);
                $previous = 'number';
                continue;
            }

            if (!isset(self::OPERATORS[$token])) {
                throw new \InvalidArgumentException('Invalid token in expression.');
            }

            $op = ($token === '-' && ($previous === null || $previous === '(' || $previous === 'operator')) ? 'u-' : $token;
            while (!empty($stack) && isset(self::OPERATORS[end($stack)])) {
                $top = end($stack);
                $leftAssoc = $op !== '^' && $op !== 'u-';
                if (($leftAssoc && self::OPERATORS[$op] <= self::OPERATORS[$top]) ||
                    (!$leftAssoc && self::OPERATORS[$op] < self::OPERATORS[$top])) {
                    $output[] = array_pop($stack);
                } else {
                    break;
                }
            }
            $stack[] = $op;
            $previous = 'operator';
        }

        while (!empty($stack)) {
            $op = array_pop($stack);
            if ($op === '(' || $op === ')') {
                throw new \InvalidArgumentException('Mismatched parentheses.');
            }
            $output[] = $op;
        }

        return $output;
    }

    private function evaluateReversePolish(array $tokens): float {
        $stack = [];
        foreach ($tokens as $token) {
            if (is_numeric($token)) {
                $stack[] = (float)$token;
                continue;
            }

            if ($token === 'u-') {
                if (empty($stack)) {
                    throw new \InvalidArgumentException('Invalid unary operator.');
                }
                $stack[] = -array_pop($stack);
                continue;
            }

            if (count($stack) < 2) {
                throw new \InvalidArgumentException('Invalid expression.');
            }
            $b = array_pop($stack);
            $a = array_pop($stack);

            switch ($token) {
                case '+': $stack[] = $a + $b; break;
                case '-': $stack[] = $a - $b; break;
                case '*': $stack[] = $a * $b; break;
                case '/':
                    if (abs($b) < 1e-12) {
                        throw new \InvalidArgumentException('Division by zero.');
                    }
                    $stack[] = $a / $b;
                    break;
                case '^': $stack[] = $a ** $b; break;
                default:
                    throw new \InvalidArgumentException('Invalid operator.');
            }
        }

        if (count($stack) !== 1) {
            throw new \InvalidArgumentException('Invalid expression.');
        }

        return (float)$stack[0];
    }

    private function formatNumber(float $value): string {
        if (abs($value - round($value)) < 1e-10) {
            return (string)(int)round($value);
        }
        return rtrim(rtrim(number_format($value, 10, '.', ''), '0'), '.');
    }
}
