<?php
class StopWords
{
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
