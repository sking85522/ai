<?php

namespace NeuralPHP\Tokenizer;

class TextTokenizer
{
    private $word_to_id = [];
    private $id_to_word = [];
    private $vocab_size = 0;
    private $unk_token = '<UNK>';
    private $unk_id = 0;

    public function __construct()
    {
        $this->addWord($this->unk_token);
    }

    public function fit(array $texts)
    {
        foreach ($texts as $text) {
            $words = $this->tokenize($text);
            foreach ($words as $word) {
                if (!isset($this->word_to_id[$word])) {
                    $this->addWord($word);
                }
            }
        }
    }

    public function encode(string $text): array
    {
        $words = $this->tokenize($text);
        $ids = [];
        foreach ($words as $word) {
            if (isset($this->word_to_id[$word])) {
                $ids[] = $this->word_to_id[$word];
            } else {
                $ids[] = $this->unk_id;
            }
        }
        return $ids;
    }

    public function decode(array $ids): string
    {
        $words = [];
        foreach ($ids as $id) {
            if (isset($this->id_to_word[$id])) {
                $words[] = $this->id_to_word[$id];
            } else {
                $words[] = $this->unk_token;
            }
        }
        return implode(' ', $words);
    }

    private function tokenize(string $text): array
    {
        $text = strtolower(preg_replace('/[^\p{L}\p{N} \']/u', ' ', $text));
        $words = explode(' ', $text);
        return array_values(array_filter($words, function($w) {
            return trim($w) !== '';
        }));
    }

    private function addWord(string $word)
    {
        $this->word_to_id[$word] = $this->vocab_size;
        $this->id_to_word[$this->vocab_size] = $word;
        $this->vocab_size++;
    }

    public function getVocabSize(): int
    {
        return $this->vocab_size;
    }
}
