<?php
namespace Core\ML;

class WebKnowledgeClient {
    /**
     * Fetches real-time knowledge from Wikipedia and Google News.
     */
    public function fetchSnippet(string $query): string {
        $query = strtolower(trim($query));
        
        // News intent check
        if (str_contains($query, 'news') || str_contains($query, 'samachar') || str_contains($query, 'khabar')) {
            $topic = str_replace(['news', 'samachar', 'khabar', 'taza', 'latest'], '', $query);
            return $this->fetchLatestNews(trim($topic));
        }

        // Weather intent check
        if (str_contains($query, 'weather') || str_contains($query, 'mausam') || str_contains($query, 'temperature')) {
            $city = str_replace(['weather', 'mausam', 'temperature', 'kaisa hai', 'of', 'in'], '', $query);
            return $this->fetchWeather(trim($city));
        }

        // Default to Wikipedia lookup
        return $this->fetchWikipedia($query);
    }

    private function fetchWikipedia(string $query): string {
        // Step 1: Try Direct Title Match
        $api_url = "https://en.wikipedia.org/w/api.php?action=query&prop=extracts&exintro&explaintext&titles=" . urlencode($query) . "&format=json&redirects=1";
        
        try {
            $response = @file_get_contents($api_url);
            if ($response) {
                $data = json_decode($response, true);
                $pages = $data['query']['pages'] ?? [];
                foreach ($pages as $page) {
                    if (isset($page['extract']) && !empty($page['extract'])) {
                        return $this->cleanText($page['extract'], 400);
                    }
                }
            }

            // Step 2: Fallback to Search API (Better for typos like "ingia")
            $search_url = "https://en.wikipedia.org/w/api.php?action=query&list=search&srsearch=" . urlencode($query) . "&format=json&srlimit=1";
            $search_res = @file_get_contents($search_url);
            if ($search_res) {
                $search_data = json_decode($search_res, true);
                $bestMatch = $search_data['query']['search'][0]['title'] ?? null;
                
                if ($bestMatch && strtolower($bestMatch) !== strtolower($query)) {
                    // Try to fetch the best match instead
                    return $this->fetchWikipedia($bestMatch);
                }
            }

        } catch (\Exception $e) {
            return "Mujhe '{$query}' ke liye online connection mein thodi dikkat ho rahi hai. Par main apne local experience se search kar raha hoon.";
        }

        return "Maine '{$query}' ke baare mein internet par kafi dhoonda, par mujhe koi solid answer nahi mila. Kya aap ise mujhe 'Teach Hritik' se sikha sakte hain?";
    }

    public function fetchLatestNews(string $topic = ""): string {
        $baseUrl = "https://news.google.com/rss";
        $rss_url = empty($topic) 
            ? "$baseUrl?hl=en-IN&gl=IN&ceid=IN:en" 
            : "$baseUrl/search?q=" . urlencode($topic) . "&hl=en-IN&gl=IN&ceid=IN:en";
        
        try {
            $xml = @simplexml_load_file($rss_url);
            if (!$xml) throw new \Exception("RSS unreachable");

            $items = $xml->channel->item;
            $headlines = [];
            for ($i = 0; $i < 3; $i++) {
                if (isset($items[$i])) {
                    $title = (string)$items[$i]->title;
                    // Extract source from title (usually ends with " - Source Name")
                    $parts = explode(' - ', $title);
                    $headline = $parts[0];
                    $source = isset($parts[1]) ? " [Source: {$parts[1]}]" : "";
                    $headlines[] = "• $headline" . $source;
                }
            }

            $topicHeader = empty($topic) ? "Aaj ki Taza Khabrein" : "Latest Updates on " . ucfirst($topic);
            return $topicHeader . ":\n" . implode("\n", $headlines);
        } catch (\Exception $e) {
            return "News feed currently offline. " . (!empty($topic) ? "Topic ($topic)" : "General") . " trends unavailable.";
        }
    }

    public function fetchWeather(string $city = "Delhi"): string {
        if (empty($city)) $city = "Delhi";
        $url = "https://wttr.in/" . urlencode($city) . "?format=j1";
        
        try {
            $res = @file_get_contents($url);
            if (!$res) throw new \Exception("Weather offline");
            
            $data = json_decode($res, true);
            $current = $data['current_condition'][0] ?? null;
            if (!$current) throw new \Exception("Mausam ka data nahi mila.");

            $temp = $current['temp_C'];
            $feel = $current['FeelsLikeC'];
            $desc = $current['weatherDesc'][0]['value'] ?? 'Clear';
            $humidity = $current['humidity'];

            return "Mausam Report ({$city}):\n" .
                   "• Temperature: {$temp}°C (Lekin mahsoos {$feel}°C ho raha hai)\n" .
                   "• Condition: {$desc}\n" .
                   "• Humidity: {$humidity}%\n" .
                   "Aaj bahar jane se pehle apna dhyan rakhein!";
        } catch (\Exception $e) {
            return "Mujhe '{$city}' ka live mausam dhoondne mein dikkat ho rahi hai. Par umeed hai wahan mausam suhana hoga!";
        }
    }

    /**
     * Cleans up raw API text by removing citations like [1], [2] and HTML.
     */
    private function cleanText(string $text, int $limit = 350): string {
        // Strip HTML tags
        $text = strip_tags($text);
        // Remove Wikipedia citations [1], [edit], [citation needed]
        $text = preg_replace('/\[[0-9]+\]|\[edit\]|\[.*?\]/', '', $text);
        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', trim($text));
        
        if (strlen($text) > $limit) {
            $text = substr($text, 0, $limit) . "...";
        }
        return $text;
    }

    private int $timeoutMs;
    private bool $enabled;
    private ?ShellHttpAdapter $shellAdapter;

    public function __construct(bool $enabled = true, int $timeoutMs = 5000)
    {
        $this->enabled = $enabled;
        $this->timeoutMs = max(1000, $timeoutMs);
        $this->shellAdapter = class_exists('ShellHttpAdapter') ? new ShellHttpAdapter() : null;
    }

    public function answerCapitalQuery(string $query): ?string
    {
        $country = $this->extractCountryFromCapitalQuery($query);
        if ($country === null) {
            return null;
        }

        $known = $this->knownCapital($country);
        if ($known !== null) {
            return $known;
        }

        if (!$this->enabled) {
            return null;
        }

        $url = 'https://restcountries.com/v3.1/name/' . rawurlencode($country) . '?fields=name,capital';
        $json = $this->getJson($url);
        if (!is_array($json) || !$json) {
            return null;
        }

        $row = $json[0] ?? null;
        if (!is_array($row)) {
            return null;
        }
        $capital = $row['capital'][0] ?? null;
        $name = $row['name']['common'] ?? $country;
        if (!is_string($capital) || $capital === '') {
            return null;
        }
        return 'Capital of ' . $name . ' is ' . $capital . '.';
    }

    // Browser-style no-key federated search over free providers.
    public function answerWebSnippet(string $query): ?string
    {
        if (!$this->enabled) {
            return null;
        }

        $q = trim($query);
        if ($q === '') {
            return null;
        }

        // 1) DuckDuckGo instant answer
        $ddg = $this->fromDuckDuckGo($q);
        if ($ddg !== null) {
            return $ddg;
        }

        // 2) Wikipedia direct search
        $wiki = $this->fromWikipedia($q);
        if ($wiki !== null) {
            return $wiki;
        }

        // 3) Wikidata entities
        $wikidata = $this->fromWikidata($q);
        if ($wikidata !== null) {
            return $wikidata;
        }

        // 4) StackExchange for programming questions
        $stack = $this->fromStackExchange($q);
        if ($stack !== null) {
            return $stack;
        }

        // 5) OpenLibrary for book/author lookups
        $openLib = $this->fromOpenLibrary($q);
        if ($openLib !== null) {
            return $openLib;
        }

        // 6) Public SearXNG instance fallback (no key)
        $searx = $this->fromSearXng($q);
        if ($searx !== null) {
            return $searx;
        }

        return null;
    }

    private function fromDuckDuckGo(string $query): ?string
    {
        $url = 'https://api.duckduckgo.com/?q=' . rawurlencode($query) . '&format=json&no_html=1&skip_disambig=1';
        $json = $this->getJson($url);
        if (!is_array($json)) {
            return null;
        }

        $abstract = trim((string) ($json['AbstractText'] ?? ''));
        if ($abstract !== '') {
            return $this->withSource($abstract, 'DuckDuckGo');
        }

        $related = $json['RelatedTopics'] ?? [];
        if (is_array($related)) {
            foreach ($related as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $text = trim((string) ($item['Text'] ?? ''));
                if ($text !== '') {
                    return $this->withSource($text, 'DuckDuckGo');
                }
            }
        }
        return null;
    }

    private function fromWikipedia(string $query): ?string
    {
        $searchUrl = 'https://en.wikipedia.org/w/api.php?action=query&list=search&srsearch=' . rawurlencode($query) . '&utf8=1&format=json';
        $search = $this->getJson($searchUrl);
        if (!is_array($search)) {
            return null;
        }
        $title = $search['query']['search'][0]['title'] ?? null;
        if (!is_string($title) || $title === '') {
            return null;
        }

        $summaryUrl = 'https://en.wikipedia.org/api/rest_v1/page/summary/' . rawurlencode($title);
        $summary = $this->getJson($summaryUrl);
        if (!is_array($summary)) {
            return null;
        }
        $extract = trim((string) ($summary['extract'] ?? ''));
        if ($extract === '') {
            return null;
        }
        return $this->withSource($extract, 'Wikipedia');
    }

    private function fromWikidata(string $query): ?string
    {
        $url = 'https://www.wikidata.org/w/api.php?action=wbsearchentities&format=json&language=en&type=item&search=' . rawurlencode($query);
        $json = $this->getJson($url);
        if (!is_array($json)) {
            return null;
        }
        $first = $json['search'][0] ?? null;
        if (!is_array($first)) {
            return null;
        }
        $label = trim((string) ($first['label'] ?? ''));
        $desc = trim((string) ($first['description'] ?? ''));
        if ($label === '' && $desc === '') {
            return null;
        }
        $text = $label !== '' && $desc !== '' ? ($label . ': ' . $desc) : ($label !== '' ? $label : $desc);
        return $this->withSource($text, 'Wikidata');
    }

    private function fromStackExchange(string $query): ?string
    {
        if (!preg_match('/\b(php|javascript|json|sql|api|programming|code|bug|error)\b/i', $query)) {
            return null;
        }
        $url = 'https://api.stackexchange.com/2.3/search/advanced?order=desc&sort=relevance&site=stackoverflow&accepted=True&title=' . rawurlencode($query);
        $json = $this->getJson($url);
        if (!is_array($json)) {
            return null;
        }
        $item = $json['items'][0] ?? null;
        if (!is_array($item)) {
            return null;
        }
        $title = trim((string) ($item['title'] ?? ''));
        if ($title === '') {
            return null;
        }
        return $this->withSource($title, 'StackOverflow');
    }

    private function fromOpenLibrary(string $query): ?string
    {
        if (!preg_match('/\b(book|author|novel|publication|writer)\b/i', $query)) {
            return null;
        }
        $url = 'https://openlibrary.org/search.json?q=' . rawurlencode($query) . '&limit=1';
        $json = $this->getJson($url);
        if (!is_array($json)) {
            return null;
        }
        $doc = $json['docs'][0] ?? null;
        if (!is_array($doc)) {
            return null;
        }
        $title = trim((string) ($doc['title'] ?? ''));
        $author = trim((string) (($doc['author_name'][0] ?? '')));
        if ($title === '') {
            return null;
        }
        $text = $author !== '' ? ($title . ' by ' . $author) : $title;
        return $this->withSource($text, 'OpenLibrary');
    }

    private function fromSearXng(string $query): ?string
    {
        $instances = [
            'https://search.sapti.me',
            'https://priv.au',
        ];
        foreach ($instances as $host) {
            $url = rtrim($host, '/') . '/search?q=' . rawurlencode($query) . '&format=json';
            $json = $this->getJson($url);
            if (!is_array($json)) {
                continue;
            }
            $item = $json['results'][0] ?? null;
            if (!is_array($item)) {
                continue;
            }
            $title = trim((string) ($item['title'] ?? ''));
            $content = trim((string) ($item['content'] ?? ''));
            $resp = trim($title . ($content !== '' ? ' - ' . $content : ''));
            if ($resp !== '') {
                return $this->withSource($resp, 'SearXNG');
            }
        }
        return null;
    }

    private function extractCountryFromCapitalQuery(string $query): ?string
    {
        $q = mb_strtolower(trim($query), 'UTF-8');
        if (preg_match('/capi[a-z]*\s+of\s+([a-z\.\- ]{2,60})/iu', $q, $m) === 1) {
            return $this->normalizePlace((string) $m[1]);
        }
        if (preg_match('/capi[a-z]*\s+([a-z\.\- ]{2,60})/iu', $q, $m) === 1) {
            $country = $this->normalizePlace((string) $m[1]);
            if ($country !== '' && $country !== 'of') {
                return $country;
            }
        }
        if (preg_match('/([a-z\.\- ]{2,60})\s+ki\s+capi[a-z]*/iu', $q, $m) === 1) {
            return $this->normalizePlace((string) $m[1]);
        }
        if (preg_match('/([a-z\.\- ]{2,60})\s+capi[a-z]*/iu', $q, $m) === 1) {
            return $this->normalizePlace((string) $m[1]);
        }
        if (preg_match('/(.+)\s+की\s+राजधानी/u', $query, $m) === 1) {
            return $this->normalizePlace((string) $m[1]);
        }
        if (preg_match('/(.+)\s+ki\s+rajdhani/iu', $q, $m) === 1) {
            return $this->normalizePlace((string) $m[1]);
        }
        return null;
    }

    private function knownCapital(string $country): ?string
    {
        $map = [
            'india' => 'Capital of India is New Delhi.',
            'bharat' => 'Capital of India is New Delhi.',
            'usa' => 'Capital of USA is Washington, D.C.',
            'us' => 'Capital of USA is Washington, D.C.',
            'united states' => 'Capital of United States is Washington, D.C.',
            'japan' => 'Capital of Japan is Tokyo.',
            'sri lanka' => 'Capital of Sri Lanka is Sri Jayawardenepura Kotte.',
            'uttar pradesh' => 'Capital of Uttar Pradesh is Lucknow.',
            'nepal' => 'Capital of Nepal is Kathmandu.',
            'pakistan' => 'Capital of Pakistan is Islamabad.',
            'bangladesh' => 'Capital of Bangladesh is Dhaka.',
            'china' => 'Capital of China is Beijing.',
            'russia' => 'Capital of Russia is Moscow.',
            'france' => 'Capital of France is Paris.',
            'germany' => 'Capital of Germany is Berlin.',
            'uk' => 'Capital of United Kingdom is London.',
            'united kingdom' => 'Capital of United Kingdom is London.',
        ];
        $key = preg_replace('/\s+/', ' ', mb_strtolower(trim($country), 'UTF-8')) ?? '';
        return $map[$key] ?? null;
    }

    private function normalizePlace(string $place): string
    {
        $p = mb_strtolower(trim($place), 'UTF-8');
        $p = preg_replace('/\s+/', ' ', $p) ?? $p;
        $p = preg_replace('/\b(and|aur|please|briefly|explain|about|tell)\b.*$/u', '', $p) ?? $p;
        $p = trim($p, " \t\n\r\0\x0B,.-");
        $typo = [
            'pakisatan' => 'pakistan',
            'pakstan' => 'pakistan',
            'bhart' => 'bharat',
            'utter pradesh' => 'uttar pradesh',
            'uttarpradesh' => 'uttar pradesh',
        ];
        return strtr($p, $typo);
    }

    private function getJson(string $url): ?array
    {
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $this->timeoutMs / 1000,
                'header' => "Accept: application/json\r\nUser-Agent: JyotiAI/0.0.4\r\n",
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $raw = @file_get_contents($url, false, $ctx);
        if (!is_string($raw) || $raw === '') {
            if ($this->shellAdapter !== null) {
                return $this->shellAdapter->getJson($url, $this->timeoutMs);
            }
            return null;
        }

        $json = json_decode($raw, true);
        return is_array($json) ? $json : null;
    }

    private function withSource(string $text, string $source): string
    {
        $text = preg_replace('/\s+/', ' ', trim($text)) ?? trim($text);
        if (mb_strlen($text, 'UTF-8') > 320) {
            $text = mb_substr($text, 0, 320, 'UTF-8') . '...';
        }
        return $text . " [source: {$source}]";
    }
}
