<?php
namespace Core\NLP;

/**
 * HRITIK AI - STOP WORDS FILTER
 * Contains common English and Hindi stop words to clean neural tokens.
 */
class StopWords {
    private array $words = [
        'is' => true, 'am' => true, 'are' => true, 'was' => true, 'were' => true, 'the' => true, 'a' => true, 'an' => true, 'and' => true, 'but' => true,
        'if' => true, 'or' => true, 'of' => true, 'at' => true, 'by' => true, 'for' => true, 'with' => true, 'to' => true, 'from' => true, 'in' => true,
        'out' => true, 'on' => true, 'off' => true, 'over' => true, 'under' => true, 'again' => true, 'further' => true, 'then' => true, 'once' => true, 'here' => true,
        'there' => true, 'when' => true, 'where' => true, 'why' => true, 'how' => true, 'all' => true, 'any' => true, 'both' => true, 'each' => true, 'few' => true,
        'more' => true, 'most' => true, 'other' => true, 'some' => true, 'such' => true, 'no' => true, 'nor' => true, 'not' => true, 'only' => true, 'own' => true,
        'same' => true, 'so' => true, 'than' => true, 'too' => true, 'very' => true, 'can' => true, 'will' => true, 'just' => true, 'should' => true, 'now' => true,
        'it' => true, 'its' => true, 'they' => true, 'them' => true, 'their' => true, 'we' => true, 'us' => true, 'our' => true, 'hai' => true, 'tha' => true,
        'thi' => true, 'the' => true, 'ka' => true, 'ke' => true, 'ki' => true, 'main' => true, 'ko' => true, 'me' => true, 'hi' => true, 'he' => true,
        'hu' => true, 'ho' => true, 'se' => true, 'ya' => true, 'neeche' => true, 'upar' => true, 'liye' => true, 'ne' => true, 'par' => true, 'per' => true,
        'ek' => true, 'do' => true, 'teen' => true, 'aur' => true, 'toh' => true, 'to' => true, 'raha' => true, 'rahe' => true, 'rahi' => true, 'kuch' => true,
        'bhi' => true, 'ab' => true, 'jab' => true, 'tab' => true, 'kaun' => true, 'kis' => true, 'jis' => true, 'unka' => true, 'inka' => true, 'isne' => true,
        'usne' => true
    ];

    /**
     * Alias for remove() to maintain compatibility with NLPPipeline.
     */
    public function filter(array $tokens): array {
        return $this->remove($tokens);
    }

    /**
     * Removes stop words from a token array.
     */
    public function remove(array $tokens): array {
        return array_values(array_filter($tokens, function($token) {
            return !isset($this->words[strtolower($token)]);
        }));
    }

    public static function list()
    {
        return [
            'a', 'an', 'the', 'is', 'are', 'am', 'to', 'of', 'for', 'on', 'in', 'with',
            'and', 'or', 'as', 'at', 'be', 'by', 'from', 'this', 'that', 'it', 'you',
            'i', 'we', 'they', 'he', 'she', 'was', 'were', 'will', 'can', 'could',
            'hai', 'ho', 'ka', 'ki', 'ke', 'ko', 'se', 'me', 'aur', 'ya', 'par',
        ];
    }

    public static function remove(array $tokens): array
    {
        $lookup = array_flip(self::list());
        return array_values(array_filter($tokens, static fn($token) => !isset($lookup[$token])));
    }
}