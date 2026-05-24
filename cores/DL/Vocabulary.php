<?php
namespace Core\DL;

/**
 * HRITIK AI - VOCABULARY
 * Maps words to integer indices and back.
 * Supports special tokens, frequency-based vocabulary building, and Hinglish text.
 *
 * Special tokens:
 *   0 = <PAD>  (padding)
 *   1 = <UNK>  (unknown word)
 *   2 = <BOS>  (beginning of sequence)
 *   3 = <EOS>  (end of sequence)
 */
class Vocabulary {

    /** @var array<string, int> Word to index mapping */
    private array $word2idx = [];

    /** @var array<int, string> Index to word mapping */
    private array $idx2word = [];

    /** @var int Current vocabulary size */
    private int $size = 0;

    /** @var int Maximum vocabulary size */
    private int $maxSize;

    // Special token constants
    public const PAD_TOKEN = '<PAD>';
    public const UNK_TOKEN = '<UNK>';
    public const BOS_TOKEN = '<BOS>';
    public const EOS_TOKEN = '<EOS>';

    public const PAD_ID = 0;
    public const UNK_ID = 1;
    public const BOS_ID = 2;
    public const EOS_ID = 3;

    public function __construct(int $maxSize = 8000) {
        $this->maxSize = $maxSize;
        $this->initSpecialTokens();
    }

    /**
     * Initialize with the 4 special tokens.
     */
    private function initSpecialTokens(): void {
        $this->word2idx = [];
        $this->idx2word = [];
        $this->size = 0;

        $this->addWord(self::PAD_TOKEN); // 0
        $this->addWord(self::UNK_TOKEN); // 1
        $this->addWord(self::BOS_TOKEN); // 2
        $this->addWord(self::EOS_TOKEN); // 3
    }

    /**
     * Add a word to the vocabulary.
     * Returns its index (existing or new).
     */
    public function addWord(string $word): int {
        $word = strtolower(trim($word));
        if ($word === '') return self::UNK_ID;

        if (isset($this->word2idx[$word])) {
            return $this->word2idx[$word];
        }

        if ($this->size >= $this->maxSize) {
            return self::UNK_ID;
        }

        $idx = $this->size;
        $this->word2idx[$word] = $idx;
        $this->idx2word[$idx] = $word;
        $this->size++;

        return $idx;
    }

    /**
     * Build vocabulary from a corpus of text documents.
     * Keeps the most frequent words up to maxSize.
     *
     * @param array  $documents Array of text strings
     * @param int    $minFreq   Minimum frequency to include a word
     * @return int Number of unique words found
     */
    public function buildFromCorpus(array $documents, int $minFreq = 2): int {
        // Count word frequencies
        $freq = [];
        foreach ($documents as $doc) {
            $words = $this->tokenize($doc);
            foreach ($words as $word) {
                $freq[$word] = ($freq[$word] ?? 0) + 1;
            }
        }

        // Sort by frequency (descending)
        arsort($freq);

        // Reset and rebuild
        $this->initSpecialTokens();

        $added = 0;
        foreach ($freq as $word => $count) {
            if ($count < $minFreq) continue;
            if ($this->size >= $this->maxSize) break;

            $this->addWord($word);
            $added++;
        }

        return $added;
    }

    /**
     * Tokenize text into words.
     * Handles Hinglish, punctuation, and basic cleaning.
     *
     * @param string $text Raw text
     * @return string[] Array of lowercase words
     */
    public function tokenize(string $text): array {
        $text = strtolower(trim($text));

        // Separate punctuation as tokens
        $text = preg_replace('/([.!?,;:"\'\(\)])/', ' $1 ', $text);

        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Split on whitespace
        $words = explode(' ', $text);

        // Filter empty and very short noise
        return array_values(array_filter($words, fn($w) => strlen($w) > 0));
    }

    /**
     * Encode text into a sequence of token IDs.
     *
     * @param string $text     Raw text
     * @param bool   $addBOS   Prepend <BOS> token
     * @param bool   $addEOS   Append <EOS> token
     * @return int[] Token IDs
     */
    public function encode(string $text, bool $addBOS = false, bool $addEOS = false): array {
        $words = $this->tokenize($text);
        $ids = [];

        if ($addBOS) {
            $ids[] = self::BOS_ID;
        }

        foreach ($words as $word) {
            $ids[] = $this->word2idx[$word] ?? self::UNK_ID;
        }

        if ($addEOS) {
            $ids[] = self::EOS_ID;
        }

        return $ids;
    }

    /**
     * Decode token IDs back to text.
     *
     * @param int[] $ids          Token IDs
     * @param bool  $skipSpecial  Skip special tokens in output
     * @return string Decoded text
     */
    public function decode(array $ids, bool $skipSpecial = true): string {
        $words = [];
        foreach ($ids as $id) {
            if ($skipSpecial && $id <= self::EOS_ID) continue;
            $word = $this->idx2word[$id] ?? self::UNK_TOKEN;
            if ($skipSpecial && $word === self::UNK_TOKEN) continue;
            $words[] = $word;
        }

        $text = implode(' ', $words);

        // Clean up punctuation spacing
        $text = preg_replace('/\s+([.!?,;:])/', '$1', $text);
        $text = preg_replace('/(["\'])\s+/', '$1', $text);

        return ucfirst(trim($text));
    }

    /**
     * Get word index (UNK if not found).
     */
    public function getIndex(string $word): int {
        return $this->word2idx[strtolower(trim($word))] ?? self::UNK_ID;
    }

    /**
     * Get word for an index.
     */
    public function getWord(int $idx): string {
        return $this->idx2word[$idx] ?? self::UNK_TOKEN;
    }

    /**
     * Check if a word exists in vocabulary.
     */
    public function hasWord(string $word): bool {
        return isset($this->word2idx[strtolower(trim($word))]);
    }

    /**
     * Current vocabulary size.
     */
    public function getSize(): int {
        return $this->size;
    }

    /**
     * Get the EOS token ID.
     */
    public function eosId(): int {
        return self::EOS_ID;
    }

    /**
     * Get the BOS token ID.
     */
    public function bosId(): int {
        return self::BOS_ID;
    }

    /**
     * Save vocabulary to JSON file.
     */
    public function save(string $path): void {
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $data = [
            'max_size' => $this->maxSize,
            'size' => $this->size,
            'word2idx' => $this->word2idx,
        ];

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Load vocabulary from JSON file.
     */
    public function load(string $path): bool {
        if (!file_exists($path)) return false;

        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data) || empty($data['word2idx'])) return false;

        $this->maxSize = $data['max_size'] ?? 8000;
        $this->size = $data['size'] ?? 0;
        $this->word2idx = $data['word2idx'];

        // Rebuild reverse mapping
        $this->idx2word = [];
        foreach ($this->word2idx as $word => $idx) {
            $this->idx2word[$idx] = $word;
        }

        return true;
    }

    /**
     * Get the default vocabulary file path.
     */
    public static function defaultPath(): string {
        return dirname(__DIR__, 2) . '/storage/models/vocabulary.json';
    }
}
