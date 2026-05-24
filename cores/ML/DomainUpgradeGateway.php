<?php
namespace Core\ML;

class DomainUpgradeGateway {
    /**
     * Bridges between a generic prompt and a specialized domain module.
     */
    public function routeToDomain(string $prompt): string {
        $domains = [
            'coding' => ['php', 'js', 'code', 'function', 'class'],
            'scientific' => ['math', 'quantum', 'physics', 'energy'],
            'linguistic' => ['translate', 'grammar', 'meaning']
        ];

        foreach ($domains as $domain => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains(strtolower($prompt), $kw)) return $domain;
            }
        }

        return 'general';
    }
}
