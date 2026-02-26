<?php
class Stemmer
{
    public function stem($token)
    {
        $token = (string) $token;
        $suffixes = ['ing', 'edly', 'edly', 'ed', 'ly', 's'];
        foreach ($suffixes as $suffix) {
            if (strlen($token) > strlen($suffix) + 2 && str_ends_with($token, $suffix)) {
                return substr($token, 0, -strlen($suffix));
            }
        }
        return $token;
    }

    public function stemTokens(array $tokens): array
    {
        return array_map(fn($token) => $this->stem($token), $tokens);
    }
}
