<?php
class Unsupervised
{
    public function cluster(array $tokens): array
    {
        $clusters = ['short' => [], 'medium' => [], 'long' => []];
        foreach ($tokens as $token) {
            $len = strlen($token);
            if ($len <= 3) {
                $clusters['short'][] = $token;
            } elseif ($len <= 6) {
                $clusters['medium'][] = $token;
            } else {
                $clusters['long'][] = $token;
            }
        }
        return $clusters;
    }

    public function extractKeywords(array $tokens): array
    {
        $freq = array_count_values($tokens);
        arsort($freq);
        return array_slice(array_keys($freq), 0, 10);
    }
}
