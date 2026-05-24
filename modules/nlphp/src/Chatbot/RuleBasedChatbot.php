<?php

namespace NLPHP\Chatbot;

use NLPHP\Tokenization\WordTokenizer;
use NLPHP\Stemming\PorterStemmer;

class RuleBasedChatbot
{
    private $intents = [];

    /**
     * @param array $intents Format: [['patterns' => ['hi', 'hello'], 'responses' => ['Hi there!', 'Hello!']]]
     */
    public function __construct(array $intents = [])
    {
        $this->intents = $intents;
    }

    public function addIntent(array $patterns, array $responses)
    {
        $this->intents[] = [
            'patterns' => array_map([PorterStemmer::class, 'stem'], $patterns),
            'responses' => $responses
        ];
    }

    /**
     * Get a response for user input.
     */
    public function respond(string $userInput): string
    {
        $tokens = WordTokenizer::tokenize(strtolower($userInput));
        $stemmed_tokens = PorterStemmer::stem($tokens);
        $input_str = implode(" ", $stemmed_tokens);

        foreach ($this->intents as $intent) {
            foreach ($intent['patterns'] as $pattern) {
                $stemmed_pattern = implode(" ", PorterStemmer::stem(WordTokenizer::tokenize(strtolower($pattern))));

                // If user input contains the pattern, pick a random response
                if (strpos($input_str, $stemmed_pattern) !== false) {
                    $responses = $intent['responses'];
                    return $responses[array_rand($responses)];
                }
            }
        }

        return "I'm sorry, I don't understand that.";
    }
}
