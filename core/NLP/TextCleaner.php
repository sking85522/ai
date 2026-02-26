<?php
class TextCleaner
{
    public function clean($text)
    {
        $text = mb_strtolower((string) $text, 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', (string) $text);
        return trim((string) $text);
    }
}
