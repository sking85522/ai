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
}
