<?php

namespace NLPHP\Classification;

use NLPHP\Tokenization\WordTokenizer;
use NLPHP\Corpus\Stopwords;

class NaiveBayes
{
    private $class_counts = [];
    private $vocab = [];
    private $word_counts = [];
    private $total_docs = 0;

    /**
     * Train the Naive Bayes text classifier.
     * @param array $X_texts Array of text documents.
     * @param array $y_labels Array of string labels.
     */
    public function fit(array $X_texts, array $y_labels)
    {
        $this->total_docs = count($X_texts);

        foreach ($X_texts as $i => $text) {
            $label = $y_labels[$i];

            if (!isset($this->class_counts[$label])) {
                $this->class_counts[$label] = 0;
                $this->word_counts[$label] = [];
            }

            $this->class_counts[$label]++;

            // Basic preprocessing: lowercase, tokenize, remove stopwords
            $tokens = WordTokenizer::tokenize(strtolower($text));
            $tokens = Stopwords::remove($tokens);

            foreach ($tokens as $token) {
                if (!isset($this->vocab[$token])) {
                    $this->vocab[$token] = true;
                }

                if (!isset($this->word_counts[$label][$token])) {
                    $this->word_counts[$label][$token] = 0;
                }

                $this->word_counts[$label][$token]++;
            }
        }
        return $this;
    }

    /**
     * Predict class for a single document.
     */
    public function predict(string $text): string
    {
        $tokens = WordTokenizer::tokenize(strtolower($text));
        $tokens = Stopwords::remove($tokens);

        $best_label = null;
        $max_prob = -INF;
        $vocab_size = count($this->vocab);

        foreach ($this->class_counts as $label => $doc_count) {
            // Prior probability (log space to prevent underflow)
            $prob = log($doc_count / $this->total_docs);

            $total_words_in_class = array_sum($this->word_counts[$label]);

            foreach ($tokens as $token) {
                // Laplace smoothing (+1)
                $count = $this->word_counts[$label][$token] ?? 0;
                $word_prob = ($count + 1) / ($total_words_in_class + $vocab_size);
                $prob += log($word_prob);
            }

            if ($prob > $max_prob) {
                $max_prob = $prob;
                $best_label = $label;
            }
        }

        return $best_label;
    }
}
