<?php
namespace Core\Training\LanguageModel;

class NGramLanguageModel {
    private int $order;
    private array $transitions = [];
    private array $startStates = [];
    private array $vocabulary = [];

    public function __construct(int $order = 3) {
        $this->order = max(2, $order);
    }

    public function observe(array $tokens): void {
        if (count($tokens) < $this->order) {
            return;
        }

        $this->startStates[] = implode(' ', array_slice($tokens, 0, $this->order - 1));
        foreach ($tokens as $token) {
            $this->vocabulary[$token] = true;
        }

        for ($i = 0; $i <= count($tokens) - $this->order; $i++) {
            $stateTokens = array_slice($tokens, $i, $this->order - 1);
            $nextToken = $tokens[$i + $this->order - 1];
            $state = implode(' ', $stateTokens);
            $this->transitions[$state][$nextToken] = ($this->transitions[$state][$nextToken] ?? 0) + 1;
        }
    }

    public function generate(array $seedTokens = [], int $maxTokens = 40): string {
        if (empty($this->startStates)) {
            return '';
        }

        $stateTokens = array_slice($seedTokens, -($this->order - 1));
        if (count($stateTokens) < $this->order - 1) {
            $randomState = $this->startStates[array_rand($this->startStates)];
            $stateTokens = explode(' ', $randomState);
        }

        $generated = $stateTokens;
        for ($i = 0; $i < $maxTokens; $i++) {
            $state = implode(' ', array_slice($generated, -($this->order - 1)));
            if (!isset($this->transitions[$state])) {
                break;
            }

            $nextToken = $this->pickWeightedToken($this->transitions[$state]);
            $generated[] = $nextToken;
            if (in_array($nextToken, ['.', '?', '!'], true)) {
                break;
            }
        }

        return trim(preg_replace('/\s+([?.!,;:])/u', '$1', implode(' ', $generated)));
    }

    public function export(): array {
        return [
            'order' => $this->order,
            'transitions' => $this->transitions,
            'startStates' => $this->startStates,
            'vocabulary' => array_keys($this->vocabulary),
        ];
    }

    public function import(array $data): void {
        $this->order = (int)($data['order'] ?? 3);
        $this->transitions = $data['transitions'] ?? [];
        $this->startStates = $data['startStates'] ?? [];
        $this->vocabulary = array_fill_keys($data['vocabulary'] ?? [], true);
    }

    private function pickWeightedToken(array $weights): string {
        $total = array_sum($weights);
        if ($total <= 0) {
            return array_key_first($weights);
        }

        $target = mt_rand(1, $total);
        $running = 0;
        foreach ($weights as $token => $weight) {
            $running += $weight;
            if ($running >= $target) {
                return (string)$token;
            }
        }

        return (string)array_key_first($weights);
    }
}
