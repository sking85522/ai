<?php
class TableGenerator
{
    public function generate(string $input, string $language = 'bilingual'): ?string
    {
        $n = $this->extractNumber($input);
        if ($n === null) {
            return null;
        }

        $max = 10;
        if (preg_match('/\b(12|15|20)\b/u', $input, $m) === 1) {
            $max = (int) $m[1];
        }

        $lines = [];
        if ($language === 'hi') {
            $lines[] = $n . ' का पहाड़ा:';
        } elseif ($language === 'en') {
            $lines[] = 'Table of ' . $n . ':';
        } else {
            $lines[] = 'Table / पहाड़ा of ' . $n . ':';
        }

        for ($i = 1; $i <= $max; $i++) {
            $lines[] = $n . ' x ' . $i . ' = ' . ($n * $i);
        }

        return implode(PHP_EOL, $lines);
    }

    private function extractNumber(string $input): ?int
    {
        if (preg_match('/(\d{1,3})/u', $input, $m) === 1) {
            $n = (int) $m[1];
            if ($n > 0 && $n <= 100) {
                return $n;
            }
        }
        return null;
    }
}
