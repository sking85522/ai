<?php
namespace Core\Memory;

/**
 * RelationshipManager
 * Extracts personal facts from the user conversation to build a long-term "Companion" profile.
 */
class RelationshipManager {
    
    private array $patterns = [
        'user_name' => [
            '/mera naam (?!kya|kon|kaun)(.*?) hai/i',
            '/mujhse (.*?) kehte hain/i',
            '/i am (?!what|who)(.*?)$/i',
            '/my name is (?!what|who)(.*?)$/i',
            '/मेरा नाम (.*?) है/u',
            '/मुझे (.*?) बुलाते हैं/u'
        ],
        'fav_color' => [
            '/mera (?:favourite|pasandida) rang (.*?) hai/i',
            '/my favourite color is (.*?)$/i',
            '/मेरा पसंदीदा रंग (.*?) है/u'
        ],
        'hobby' => [
            '/mujhe (.*?) pasand hai/i',
            '/i like (.*?)$/i',
            '/my hobby is (.*?)$/i',
            '/मुझे (.*?) पसंद है/u'
        ],
        'user_city' => [
            '/main (.*?) mein rehta hoon/i',
            '/main (.*?) se hoon/i',
            '/i live in (.*?)$/i',
            '/i am from (.*?)$/i',
            '/मैं (.*?) में रहता हूँ/u',
            '/मैं (.*?) से हूँ/u'
        ],
        'user_job' => [
            '/main (.*?) hoon/i',
            '/i work as (.*?)$/i',
            '/mera kaam (.*?) hai/i',
            '/मैं एक (.*?) हूँ/u'
        ]
    ];

    /**
     * Scans text for personal facts and returns an array of found data.
     */
    public function extractFacts(string $text): array {
        $found = [];
        foreach ($this->patterns as $key => $regexes) {
            foreach ($regexes as $regex) {
                if (preg_match($regex, $text, $matches)) {
                    $val = trim($matches[1], " .?!");
                    if (!empty($val)) {
                        $found[$key] = $val;
                    }
                }
            }
        }
        return $found;
    }

    /**
     * Checks if the user is asking the AI to "Forget" everything.
     */
    public function isForgetRequest(string $text): bool {
        return preg_match('/(forget everything|sab bhool jao|data delete karo)/i', $text);
    }
}
