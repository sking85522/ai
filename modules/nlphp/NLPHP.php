<?php

namespace NLPHP;

use NLPHP\Tokenization\WordTokenizer;
use NLPHP\Tokenization\SentenceTokenizer;
use NLPHP\Corpus\Stopwords;
use NLPHP\Stemming\PorterStemmer;
use NLPHP\Classification\NaiveBayes;
use NLPHP\Chatbot\RuleBasedChatbot;
use NLPHP\Sentiment\SentimentAnalyzer;
use NLPHP\Vectorization\TfIdfVectorizer;
use NLPHP\Vectorization\CountVectorizer;
use NLPHP\NGrams\NGramGenerator;

class NLPHP
{
    // ──────────── Tokenization ────────────

    public static function word_tokenize(string $text): array
    {
        return WordTokenizer::tokenize($text);
    }

    public static function sent_tokenize(string $text): array
    {
        return SentenceTokenizer::tokenize($text);
    }

    // ──────────── Preprocessing ────────────

    public static function remove_stopwords(array $words): array
    {
        return Stopwords::remove($words);
    }

    public static function stem($words)
    {
        return PorterStemmer::stem($words);
    }

    // ──────────── Sentiment Analysis ────────────

    public static function SentimentAnalyzer(): SentimentAnalyzer
    {
        return new SentimentAnalyzer();
    }

    /**
     * Quick sentiment analysis — returns label directly.
     */
    public static function sentiment(string $text): array
    {
        $analyzer = new SentimentAnalyzer();
        return $analyzer->analyze($text);
    }

    // ──────────── Vectorization ────────────

    public static function TfIdfVectorizer(): TfIdfVectorizer
    {
        return new TfIdfVectorizer();
    }

    public static function CountVectorizer(): CountVectorizer
    {
        return new CountVectorizer();
    }

    // ──────────── N-Grams ────────────

    public static function bigrams(string $text): array
    {
        return NGramGenerator::wordNgrams($text, 2);
    }

    public static function trigrams(string $text): array
    {
        return NGramGenerator::wordNgrams($text, 3);
    }

    public static function ngrams(string $text, int $n = 2): array
    {
        return NGramGenerator::wordNgrams($text, $n);
    }

    public static function char_ngrams(string $text, int $n = 3): array
    {
        return NGramGenerator::charNgrams($text, $n);
    }

    public static function ngram_frequency(string $text, int $n = 2): array
    {
        $ngrams = NGramGenerator::wordNgrams($text, $n);
        return NGramGenerator::frequency($ngrams);
    }

    // ──────────── Classification ────────────

    public static function NaiveBayes(): NaiveBayes
    {
        return new NaiveBayes();
    }

    // ──────────── Chatbot ────────────

    public static function Chatbot(array $intents = []): RuleBasedChatbot
    {
        return new RuleBasedChatbot($intents);
    }
}
