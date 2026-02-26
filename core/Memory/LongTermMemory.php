<?php
class LongTermMemory
{
    private array $patterns = [];

    public function remember(string $intent, array $tokens): void
    {
        $this->patterns[$intent] = array_values(array_unique(array_merge(
            $this->patterns[$intent] ?? [],
            $tokens
        )));
    }

    public function recall(string $intent): array
    {
        return $this->patterns[$intent] ?? [];
    }

    public function snapshot(): array
    {
        return $this->patterns;
    }
}
