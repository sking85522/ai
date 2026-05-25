<?php

namespace DictionaryPHP\Dict;

class LocalDictionary
{
    private static $en_dict = [
        'apple' => [
            'meaning' => 'A round fruit with red or green skin and a whitish interior.',
            'pos' => 'noun',
            'synonyms' => ['fruit', 'pome']
        ],
        'beautiful' => [
            'meaning' => 'Pleasing the senses or mind aesthetically.',
            'pos' => 'adjective',
            'synonyms' => ['attractive', 'pretty', 'handsome', 'lovely']
        ],
        'run' => [
            'meaning' => 'Move at a speed faster than a walk.',
            'pos' => 'verb',
            'synonyms' => ['sprint', 'race', 'dash', 'gallop']
        ],
        'artificial' => [
            'meaning' => 'Made or produced by human beings rather than occurring naturally.',
            'pos' => 'adjective',
            'synonyms' => ['synthetic', 'fake', 'imitation']
        ]
    ];

    private static $en_hi_dict = [
        'apple' => 'सेब',
        'beautiful' => 'सुंदर',
        'run' => 'दौड़ना',
        'artificial' => 'कृत्रिम',
        'sun' => 'सूरज'
    ];

    public static function lookup(string $word): ?array
    {
        $word = strtolower($word);
        if (isset(self::$en_dict[$word])) {
            return self::$en_dict[$word];
        }
        return null;
    }

    public static function translateEnToHi(string $word): ?string
    {
        $word = strtolower($word);
        if (isset(self::$en_hi_dict[$word])) {
            return self::$en_hi_dict[$word];
        }
        return null;
    }
}
