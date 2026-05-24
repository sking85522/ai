<?php
namespace TokenizerPHP;

class TokenizerPHP {
    private $vocab;
    private $merges;

    public function __construct() {
        $this->vocab = [];
        $this->merges = [];
    }

    public function train(string $text, int $vocabSize) {
        // Simple BPE mock training
        $chars = array_unique(str_split($text));
        foreach ($chars as $i => $char) {
            $this->vocab[$char] = $i;
        }
        $this->merges = ["e n" => "en", "t h" => "th"];
    }

    public function encode(string $text): array {
        // Mock encode
        $tokens = [];
        for ($i = 0; $i < strlen($text); $i++) {
            $tokens[] = ord($text[$i]);
        }
        return $tokens;
    }

    public function decode(array $tokens): string {
        // Mock decode
        $text = "";
        foreach ($tokens as $token) {
            $text .= chr($token);
        }
        return $text;
    }
}
