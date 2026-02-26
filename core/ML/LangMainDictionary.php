<?php
class LangMainDictionary
{
    private string $langMainDir;
    private array $cache = [];

    public function __construct(?string $langMainDir = null)
    {
        $this->langMainDir = $langMainDir ?? (__DIR__ . '/../../lang-main/locales');
    }

    public function lookup(string $text, string $targetLanguage): ?string
    {
        $targetLanguage = in_array($targetLanguage, ['en', 'hi'], true) ? $targetLanguage : 'en';
        $dict = $this->load($targetLanguage);
        if (!$dict) {
            return null;
        }

        $key = trim($text);
        if ($key === '') {
            return null;
        }

        if (isset($dict[$key]) && is_string($dict[$key]) && $dict[$key] !== '') {
            return (string) $dict[$key];
        }

        $low = mb_strtolower($key, 'UTF-8');
        foreach ($dict as $k => $v) {
            if (!is_string($k) || !is_string($v)) {
                continue;
            }
            if (mb_strtolower($k, 'UTF-8') === $low) {
                return $v;
            }
        }
        return null;
    }

    private function load(string $language): array
    {
        if (isset($this->cache[$language])) {
            return $this->cache[$language];
        }

        $file = $this->langMainDir . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . 'json.json';
        if (!is_file($file)) {
            $this->cache[$language] = [];
            return [];
        }

        $content = (string) file_get_contents($file);
        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            $this->cache[$language] = [];
            return [];
        }

        $this->cache[$language] = $decoded;
        return $decoded;
    }
}
