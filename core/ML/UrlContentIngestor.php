<?php
class UrlContentIngestor
{
    public function ingest(string $url): ?array
    {
        $url = trim($url);
        if (!preg_match('/^https?:\/\/[^\s]+$/i', $url)) {
            return null;
        }

        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 8,
                'header' => "User-Agent: JyotiAI/0.0.6\r\n",
            ],
        ]);
        $raw = @file_get_contents($url, false, $ctx);
        if (!is_string($raw) || trim($raw) === '') {
            return null;
        }

        $title = '';
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $raw, $m) === 1) {
            $title = trim(html_entity_decode(strip_tags((string) $m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        $text = strip_tags($raw);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        $text = trim($text);
        if ($text === '') {
            return null;
        }

        return [
            'url' => $url,
            'title' => $title,
            'summary' => mb_substr($text, 0, 1200, 'UTF-8'),
        ];
    }
}
