<?php
namespace Core\Response;

/**
 * HRITIK AI - NEURAL RESPONSE BUILDER
 * No hardcoded strings. Everything is synthesized from the neural engine.
 */
class ResponseBuilder {

    public function __construct() {
        // No dependencies needed for static responses anymore
    }

    /**
     * Standardizes the response format without adding hardcoded text.
     */
    public function build(string $intent, array $context = [], array $nluData = []): ?string {
        // We no longer return static strings here. 
        // We return null to force the engine to use the NeuralReasoning database.
        return null;
    }

    /**
     * Cleans and formats the raw response from the database or web.
     */
    public function buildWebResponse(string $query, string $snippet, bool $isLocal = false): string {
        // No prefixes like "Mere analysis ke hisaab se". Just the pure knowledge.
        return trim(preg_replace('/\s+/', ' ', $snippet));
    }

    private TemplateEngine $templateEngine;

    public function __construct()
    {
        $this->templateEngine = new TemplateEngine();
    }

    public function build($intent, $data = []): string
    {
        $language = $data['language'] ?? 'bilingual';
        $templates = $this->templatesByLanguage($language);
        $template = $this->pickTemplate($templates[$intent] ?? $templates['general']);

        $citations = '';
        if (!empty($data['source_citations'])) {
            $sources = [];
            foreach ($data['source_citations'] as $citation) {
                $sources[] = is_array($citation) ? ($citation['source'] ?? '') : (string)$citation;
            }
            $sources = array_unique(array_filter($sources));
            if (!empty($sources)) {
                $prefix = match ($language) {
                    'en' => "\n\nSources: ",
                    'hi' => "\n\nस्रोत: ",
                    default => "\n\nSources/स्रोत: ",
                };
                $citations = $prefix . implode(', ', $sources);
            }
        }

        return $this->templateEngine->render($template, [
            'intent' => $data['intent'] ?? $intent,
            'confidence' => (string) ($data['confidence'] ?? 0),
            'keywords' => implode(', ', $data['keywords'] ?? []),
            'time' => date('Y-m-d H:i:s'),
            'number' => (string) ($data['number'] ?? ''),
            'math_result' => isset($data['math_result']) ? (string) $data['math_result'] : '',
            'memory_key' => (string) ($data['memory_key'] ?? ''),
            'memory_value' => (string) ($data['memory_value'] ?? ''),
            'knowledge_answer' => (string) ($data['knowledge_answer'] ?? ''),
            'citations' => $citations,
            'policy_response' => (string) ($data['policy_response'] ?? ''),
            'chat_reply' => (string) ($data['chat_reply'] ?? ''),
            'code_title' => (string) ($data['code_title'] ?? ''),
            'code_body' => (string) ($data['code_body'] ?? ''),
            'plan_body' => (string) ($data['plan_body'] ?? ''),
            'debug_body' => (string) ($data['debug_body'] ?? ''),
            'quiz_body' => (string) ($data['quiz_body'] ?? ''),
        ]);
    }

    private function templatesByLanguage(string $language): array
    {
        $en = [
            'greeting' => [
                'Hello! I am ready to analyze your text input.',
                'Hi! Send your prompt and I will process it.',
            ],
            'farewell' => 'Goodbye! Session memory has been saved.',
            'time_query' => 'Current server time: {{time}}',
            'number_query' => 'You sent number: {{number}}',
            'math_query' => 'Math result: {{math_result}}',
            'memory_store' => 'Saved. I will remember that {{memory_key}} is {{memory_value}}.',
            'memory_recall' => 'I remember: {{memory_key}} is {{memory_value}}.',
            'memory_blocked' => 'I cannot store sensitive secrets like passwords/OTP/card details.',
            'knowledge_answer' => '{{knowledge_answer}}{{citations}}',
            'normal_chat' => '{{chat_reply}}',
            'quiz_request' => '{{quiz_body}}',
            'code_generate' => '{{code_title}}' . "\n\n" . '{{code_body}}',
            'code_request' => '{{code_title}}' . "\n\n" . '{{code_body}}',
            'engineering_plan' => '{{plan_body}}',
            'engineering_request' => '{{plan_body}}',
            'debug_help' => '{{debug_body}}',
            'debug_request' => '{{debug_body}}',
            'help' => [
                'I can do coding, web design snippets, software engineering plans, debugging help, math, and memory chat.',
                'I can help with code, debugging, math, quiz generation, and bilingual conversation.',
            ],
            'question' => 'I am thinking on this. Share a bit more detail so I can answer better.',
            'general' => [
                'Detected intent: {{intent}} | confidence: {{confidence}} | top keywords: {{keywords}}',
                'Intent guess: {{intent}} | score: {{confidence}} | keywords: {{keywords}}',
            ],
        ];

        $hi = [
            'greeting' => ['नमस्ते! मैं आपके टेक्स्ट इनपुट के लिए तैयार हूं।', 'नमस्कार! आप संदेश भेजिए, मैं जवाब दूंगा।'],
            'farewell' => 'अलविदा! सत्र मेमोरी सेव हो गई है।',
            'time_query' => 'सर्वर का वर्तमान समय: {{time}}',
            'number_query' => 'आपने यह संख्या भेजी: {{number}}',
            'math_query' => 'गणना का परिणाम: {{math_result}}',
            'memory_store' => 'ठीक है, मैंने याद रखा: {{memory_key}} = {{memory_value}}।',
            'memory_recall' => 'मुझे याद है: {{memory_key}} = {{memory_value}}।',
            'memory_blocked' => 'मैं password/OTP/card details जैसी संवेदनशील जानकारी store नहीं कर सकता।',
            'knowledge_answer' => '{{knowledge_answer}}{{citations}}',
            'custom_policy' => '{{policy_response}}',
            'normal_chat' => '{{chat_reply}}',
            'quiz_request' => '{{quiz_body}}',
            'code_generate' => '{{code_title}}' . "\n\n" . '{{code_body}}',
            'code_request' => '{{code_title}}' . "\n\n" . '{{code_body}}',
            'engineering_plan' => '{{plan_body}}',
            'engineering_request' => '{{plan_body}}',
            'debug_help' => '{{debug_body}}',
            'debug_request' => '{{debug_body}}',
            'help' => ['मैं coding, debugging, math, quiz और bilingual chat में मदद कर सकता हूं।', 'मैं coding, engineering plan, debugging और गणित में मदद कर सकता हूं।'],
            'question' => 'मैं इस पर सोच रहा हूं। थोड़ा और विवरण दें।',
            'general' => ['पहचाना गया intent: {{intent}} | confidence: {{confidence}} | keywords: {{keywords}}', 'अनुमानित intent: {{intent}} | score: {{confidence}} | keywords: {{keywords}}'],
        ];

        $bilingual = [
            'greeting' => 'Hello! Namaste! Main Hindi aur English dono me chat kar sakta hoon.',
            'farewell' => 'Goodbye! Alvida! Session memory save ho gayi hai.',
            'knowledge_answer' => '{{knowledge_answer}}{{citations}}',
            'normal_chat' => '{{chat_reply}}',
            'custom_policy' => '{{policy_response}}',
            'math_query' => 'Math result/गणना का परिणाम: {{math_result}}',
            'memory_store' => 'Saved. Maine yaad rakha: {{memory_key}} is {{memory_value}}.',
            'memory_recall' => 'I remember/मुझे याद है: {{memory_key}} is {{memory_value}}.',
            'general' => 'Intent: {{intent}} | Confidence: {{confidence}}',
        ];

        return match ($language) {
            'en' => $en,
            'hi' => $hi,
            default => $bilingual,
        };
    }

    private function pickTemplate(string|array $templates): string
    {
        if (is_array($templates)) {
            return $templates[array_rand($templates)];
        }
        return $templates;
    }
}