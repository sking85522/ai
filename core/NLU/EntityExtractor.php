<?php
namespace Core\NLU;

/**
 * Entity Extractor
 * Extracts named entities (names, numbers, dates, locations, etc.) from text.
 * Supports both English and Hinglish.
 */
class EntityExtractor {

    /**
     * Extract all entities from text
     * @return array ['names' => [], 'numbers' => [], 'dates' => [], 'locations' => [], 'quantities' => []]
     */
    public function extract(string $text): array {
        return [
            'numbers'    => $this->extractNumbers($text),
            'dates'      => $this->extractDates($text),
            'locations'  => $this->extractLocations($text),
            'names'      => $this->extractNames($text),
            'quantities' => $this->extractQuantities($text),
            'years'      => $this->extractYears($text),
            'keywords'   => $this->extractKeywords($text),
            'programming'=> [
                'language'   => $this->extractProgrammingLanguage($text),
                'structures' => $this->extractCodingStructures($text),
                'variables'  => $this->extractVariables($text)
            ],
            'files'      => $this->extractFilePaths($text)
        ];
    }

    /**
     * Attempts to extract file paths from text
     */
    public function extractFilePaths(string $text): array {
        $paths = [];
        if (preg_match_all('/([\w\.\/\\\\]+\.\w+)/', $text, $matches)) {
            $paths = array_merge($paths, $matches[1]);
        }
        return array_values(array_unique($paths));
    }

    /**
     * Get a flat summary of extracted entities
     */
    public function getSummary(string $text): string {
        $entities = $this->extract($text);
        $parts = [];
        foreach ($entities as $type => $items) {
            if (!empty($items)) {
                $parts[] = $type . ': ' . implode(', ', array_slice($items, 0, 3));
            }
        }
        return implode(' | ', $parts);
    }

    private function extractNumbers(string $text): array {
        preg_match_all('/\b\d+(?:\.\d+)?\b/', $text, $matches);
        return array_values(array_unique($matches[0]));
    }

    private function extractDates(string $text): array {
        $dates = [];
        // ISO dates
        if (preg_match_all('/\b\d{4}[-\/]\d{1,2}[-\/]\d{1,2}\b/', $text, $m)) {
            $dates = array_merge($dates, $m[0]);
        }
        // DD/MM/YYYY or MM/DD/YYYY
        if (preg_match_all('/\b\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}\b/', $text, $m)) {
            $dates = array_merge($dates, $m[0]);
        }
        // Month names
        if (preg_match_all('/\b(january|february|march|april|may|june|july|august|september|october|november|december)\s+\d{1,2}(?:\s*,?\s*\d{4})?\b/i', $text, $m)) {
            $dates = array_merge($dates, $m[0]);
        }
        return array_values(array_unique($dates));
    }

    private function extractYears(string $text): array {
        preg_match_all('/\b(1[89]\d{2}|20[0-2]\d)\b/', $text, $matches);
        return array_values(array_unique($matches[0]));
    }

    private function extractLocations(string $text): array {
        $locations = [];
        // Common country/city detection (Multilingual)
        $known = [
            'india', 'china', 'usa', 'america', 'japan', 'germany', 'france', 'russia',
            'england', 'pakistan', 'nepal', 'bangladesh', 'brazil', 'australia', 'canada',
            'delhi', 'mumbai', 'kolkata', 'chennai', 'bangalore', 'hyderabad', 'pune',
            'london', 'new york', 'tokyo', 'paris', 'berlin', 'moscow', 'dubai',
            'nepal', 'bharat', 'hindustan', 'dilli', 'lucknow', 'jaipur', 'varanasi',
            'भारत', 'दिल्ली', 'मुंबई', 'उत्तर प्रदेश', 'राजस्थान', 'बिहार'
        ];
        $lower = strtolower($text);
        foreach ($known as $loc) {
            if (mb_strpos($lower, $loc) !== false) {
                $locations[] = mb_convert_case($loc, MB_CASE_TITLE, "UTF-8");
            }
        }
        return array_values(array_unique($locations));
    }

    private function extractNames(string $text): array {
        $names = [];
        // Capitalized words that might be names (2+ chars, not at start)
        if (preg_match_all('/(?<=\s)[A-Z][a-z]{2,}(?:\s[A-Z][a-z]{2,})?/', $text, $m)) {
            $names = $m[0];
        }
        // Named person patterns: "kisne", "kaun"
        if (preg_match('/(?:named?|called?|naam)\s+(\w+)/i', $text, $m)) {
            $names[] = $m[1];
        }
        return array_values(array_unique($names));
    }

    private function extractQuantities(string $text): array {
        $quantities = [];
        if (preg_match_all('/\b(\d+(?:\.\d+)?)\s*(kg|km|cm|mm|m|gb|mb|tb|percent|%|crore|lakh|million|billion|thousand|hundred|rupees|rs|dollars|\$|hours|minutes|seconds|years|months|days)/i', $text, $m, PREG_SET_ORDER)) {
            foreach ($m as $match) {
                $quantities[] = $match[0];
            }
        }
        return $quantities;
    }

    private function extractKeywords(string $text): array {
        $text = strtolower($text);
        // Remove stop words and get meaningful keywords
        static $stopWords = ['is'=>true, 'the'=>true, 'a'=>true, 'an'=>true, 'of'=>true, 'in'=>true, 'to'=>true, 'for'=>true, 'and'=>true, 'or'=>true, 'but'=>true,
            'hai'=>true, 'ka'=>true, 'ke'=>true, 'ki'=>true, 'me'=>true, 'se'=>true, 'ko'=>true, 'ne'=>true, 'par'=>true, 'kya'=>true, 'ye'=>true,
            'what'=>true, 'who'=>true, 'when'=>true, 'where'=>true, 'how'=>true, 'which'=>true, 'that'=>true, 'this'=>true, 'with'=>true,
            'है'=>true, 'था'=>true, 'थी'=>true, 'थे'=>true, 'का'=>true, 'के'=>true, 'की'=>true, 'में'=>true, 'को'=>true];
        
        $clean = preg_replace('/[^a-z0-9\s\p{L}]/u', ' ', $text);
        $words = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $keywords = array_filter($words, fn($w) => mb_strlen($w) > 2 && !isset($stopWords[$w]));
        
        return array_values(array_unique($keywords));
    }

    private function extractProgrammingLanguage(string $text): ?string {
        $text = strtolower($text);
        $langs = [
            'php' => 'php',
            'javascript' => 'javascript',
            'js' => 'javascript',
            'python' => 'python',
            'py' => 'python',
            'html' => 'html',
            'css' => 'css',
            'java' => 'java',
            'c++' => 'cpp',
            'cpp' => 'cpp',
            'react' => 'react',
            'node' => 'node'
        ];

        foreach ($langs as $key => $val) {
            if (preg_match('/\b' . preg_quote($key, '/') . '\b/i', $text)) {
                return $val;
            }
        }
        return null; // Default or unknown
    }

    private function extractCodingStructures(string $text): array {
        $text = strtolower($text);
        $structures = [];
        
        $maps = [
            'function' => ['function', 'method', 'routine', 'fun', 'banao ek function'],
            'class'    => ['class', 'object', 'oop', 'module'],
            'loop'     => ['loop', 'for loop', 'while loop', 'foreach'],
            'array'    => ['array', 'list', 'dictionary', 'map'],
            'database' => ['database', 'db', 'sql', 'query', 'mysql', 'connection'],
            'crud'     => ['crud', 'manager', 'insert', 'update', 'delete', 'read', 'select'],
            'auth'     => ['auth', 'login', 'signup', 'register', 'session', 'token', 'password'],
            'api'      => ['api', 'fetch', 'endpoint', 'rest', 'http', 'request'],
            'ui'       => ['ui', 'layout', 'design', 'page', 'form', 'button', 'card', 'dashboard']
        ];

        foreach ($maps as $struct => $keywords) {
            foreach ($keywords as $kw) {
                if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $text)) {
                    $structures[] = $struct;
                    break;
                }
            }
        }
        return array_values(array_unique($structures));
    }

    private function extractVariables(string $text): array {
        $vars = [];
        // Look for common variable indicators like "name", "age", "items" or quoted strings
        if (preg_match_all('/(?:variable|var|param|parameter)\s+([a-zA-Z0-9_]+)/i', $text, $m)) {
            $vars = array_merge($vars, $m[1]);
        }
        // Extract quoted strings which might be table names or variable names
        if (preg_match_all('/[\'"]([a-zA-Z0-9_]+)[\'"]/', $text, $m)) {
            $vars = array_merge($vars, $m[1]);
        }
        return array_values(array_unique($vars));
    }
}
