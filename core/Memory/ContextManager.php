<?php
class ContextManager
{
    public function buildContext(array $tokens, array $recentInteractions, array $longTermHint): array
    {
        return [
            'tokens' => $tokens,
            'recent' => $recentInteractions,
            'memory_hint' => $longTermHint,
            'keyword_focus' => array_slice(array_values(array_unique($tokens)), 0, 5),
        ];
    }
}
