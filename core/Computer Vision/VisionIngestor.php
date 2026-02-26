<?php
class VisionIngestor
{
    public function analyzeFile(string $path): ?array
    {
        if (!is_file($path)) {
            return null;
        }

        $info = @getimagesize($path);
        if (!is_array($info)) {
            return null;
        }

        $mime = (string) ($info['mime'] ?? 'image/unknown');
        $width = (int) ($info[0] ?? 0);
        $height = (int) ($info[1] ?? 0);
        $ocr = $this->tryOcr($path);

        return [
            'mime' => $mime,
            'width' => $width,
            'height' => $height,
            'ocr_text' => $ocr,
        ];
    }

    private function tryOcr(string $path): string
    {
        $escaped = '"' . str_replace('"', '\"', $path) . '"';
        $cmd = 'tesseract ' . $escaped . ' stdout 2>NUL';
        $out = @shell_exec($cmd);
        if (!is_string($out)) {
            return '';
        }
        $out = trim($out);
        if ($out === '') {
            return '';
        }
        $out = preg_replace('/\s+/', ' ', $out) ?? $out;
        return mb_substr($out, 0, 400, 'UTF-8');
    }
}
