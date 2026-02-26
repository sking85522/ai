<?php
class IntentDetector
{
    public function detect($text): string
    {
        $candidates = $this->detectMany((string) $text);
        return $candidates[0]['intent'] ?? 'general';
    }

    public function detectMany(string $text): array
    {
        $text = trim($text);
        $lower = mb_strtolower($text, 'UTF-8');
        $candidates = [];

        if (preg_match('/^\s*-?\d+(\.\d+)?\s*$/', $text) === 1) {
            $candidates[] = ['intent' => 'number_query', 'score' => 1.0];
        }
        if (preg_match('/^\s*-?\d+(\.\d+)?\s*[\+\-\*\/]\s*-?\d+(\.\d+)?\s*$/', $text) === 1) {
            $candidates[] = ['intent' => 'math_query', 'score' => 1.0];
        }
        if (preg_match('/^\s*-?\d+(\.\d+)?\s*=\s*-?\d+(\.\d+)?\s*$/', $text) === 1) {
            $candidates[] = ['intent' => 'math_query', 'score' => 0.98];
        }
        if ($this->containsAny($lower, [
            'square of', 'squre', 'square', 'ka square',
            'sin', 'cos', 'tan', 'dot product', 'vector',
            'derivative', 'integral', 'avkalan', 'samakalan',
            'geometry', 'trigonometry'
        ])) {
            $candidates[] = ['intent' => 'math_query', 'score' => 0.92];
        }
        if (preg_match('/^\s*(php|java|javascript|js|json|python|c\+\+|c#)\s*$/iu', $lower) === 1) {
            $candidates[] = ['intent' => 'code_generate', 'score' => 0.98];
        }
        if (preg_match('/^\s*what is\s+(php|java|javascript|json|python)\s*\??\s*$/iu', $lower) === 1) {
            $candidates[] = ['intent' => 'question', 'score' => 0.99];
        }

        if ($this->containsAny($lower, ['table of', 'table', 'tabel', 'pahada', 'phada', 'pahda', ' पहाड़ा', ' पहाडा', 'multiplication table'])) {
            $candidates[] = ['intent' => 'table_request', 'score' => 0.96];
        }

        if (
            preg_match('/capi[a-z]*\s+of\s+[a-z\.\- ]{2,60}/iu', $lower) === 1
            || preg_match('/capi[a-z]*\s+[a-z\.\- ]{2,60}/iu', $lower) === 1
            || preg_match('/[a-z\.\- ]{2,60}\s+ki\s+capi[a-z]*/iu', $lower) === 1
            || preg_match('/[a-z\.\- ]{2,60}\s+capi[a-z]*/iu', $lower) === 1
            || preg_match('/[a-z\.\- ]{2,60}\s+ki\s+rajdhani/iu', $lower) === 1
        ) {
            $candidates[] = ['intent' => 'capital_query', 'score' => 0.97];
        }

        if ($this->containsAny($lower, ['quiz', 'mcq', 'test me', 'quiz bna', 'quiz bana', 'प्रश्नोत्तरी', 'क्विज', 'मैथ्स के लिए क्विज', 'physics quiz'])) {
            $candidates[] = ['intent' => 'quiz_request', 'score' => 0.95];
        }
        if ($this->containsAny($lower, ['do it fully', 'complete task', 'task mode', 'step by step and execute', 'पूरा करके दो', 'पूरा काम करो'])) {
            $candidates[] = ['intent' => 'task_mode', 'score' => 0.99];
        }
        if (preg_match('/\b(math|maths|physics).*(quiz|mcq|test)\b/iu', $lower) === 1) {
            $candidates[] = ['intent' => 'quiz_request', 'score' => 0.97];
        }
        if (preg_match('/https?:\/\/[^\s]+/i', $text) === 1) {
            $candidates[] = ['intent' => 'url_ingest', 'score' => 0.99];
        }

        if ($this->containsAnyWord($lower, ['code', 'coding', 'program', 'function', 'api', 'sql', 'html', 'css', 'javascript', 'php'])) {
            $candidates[] = ['intent' => 'code_generate', 'score' => 0.9];
        }
        if ($this->containsAny($lower, ['web design', 'website design', 'landing page', 'ui design'])) {
            $candidates[] = ['intent' => 'code_generate', 'score' => 0.94];
        }
        if ($this->containsAny($lower, ['software engineering', 'system design', 'architecture', 'engineering plan'])) {
            $candidates[] = ['intent' => 'engineering_plan', 'score' => 0.9];
        }
        if ($this->containsAny($lower, ['debug', 'bug', 'error', 'fix issue'])) {
            $candidates[] = ['intent' => 'debug_help', 'score' => 0.9];
        }

        if ($this->containsAny($lower, ['how are you', 'kaise ho', 'kya haal hai', 'kya haal h', 'how is it going'])) {
            $candidates[] = ['intent' => 'normal_chat', 'score' => 0.85];
        }
        if ($this->containsAny($lower, ['joke', 'jok', 'चुटकुला', 'ek joke'])) {
            $candidates[] = ['intent' => 'normal_chat', 'score' => 0.92];
        }
        if ($this->containsAny($lower, ['kya kya kr skte ho', 'kya kar sakte ho', 'what can you do', 'tum kya kar sakte ho'])) {
            $candidates[] = ['intent' => 'help', 'score' => 0.95];
        }

        if ($this->containsAny($lower, ['remember that', 'remember this', 'yaad rakho', 'note that', 'save this', 'मेरा नाम'])) {
            $candidates[] = ['intent' => 'memory_store', 'score' => 0.9];
        }
        if (
            preg_match('/\b(what is my name|my name kya|mera naam kya)\b/iu', $lower) === 1
            || preg_match('/मेेरा नाम क्या|मेरा नाम क्या/u', $text) === 1
        ) {
            $candidates[] = ['intent' => 'memory_recall', 'score' => 0.99];
        }
        if (
            preg_match('/\b(my name is|mera naam)\s+.+/iu', $lower) === 1
            || preg_match('/मेरा नाम\s+.+/u', $text) === 1
        ) {
            $candidates[] = ['intent' => 'memory_store', 'score' => 0.98];
        }
        if ($this->containsAny($lower, ['my name is', 'mera naam', 'i live in', 'main rehta hun', 'main rehti hun', 'my friend name is', 'mere friend ka naam'])) {
            $candidates[] = ['intent' => 'memory_store', 'score' => 0.93];
        }
        if ($this->containsAny($lower, ['what is my', 'do you remember', 'yaad hai', 'my friend name', 'mera naam'])) {
            $candidates[] = ['intent' => 'memory_recall', 'score' => 0.9];
        }

        $rules = [
            'greeting' => ['hello', 'hi', 'hey', 'namaste', 'namaskar'],
            'farewell' => ['bye', 'goodbye', 'see you', 'phir milenge'],
            'time_query' => ['time', 'date', 'day', 'today', 'samay', 'tareekh'],
            'help' => ['help', 'how', 'what can you do', 'madad'],
        ];

        foreach ($rules as $intent => $keywords) {
            if ($this->containsAnyKeyword($lower, $keywords)) {
                $candidates[] = ['intent' => $intent, 'score' => 0.75];
            }
        }

        if (str_ends_with($lower, '?')) {
            $candidates[] = ['intent' => 'question', 'score' => 0.6];
        }
        if (preg_match('/^about\s+[a-z0-9 \-]+$/iu', $lower) === 1) {
            $candidates[] = ['intent' => 'question', 'score' => 0.68];
        }

        if (!$candidates) {
            $candidates[] = ['intent' => 'general', 'score' => 0.55];
        }

        usort($candidates, static fn(array $a, array $b): int => ($b['score'] <=> $a['score']));
        return $this->dedupe($candidates);
    }

    private function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (mb_stripos($text, $keyword, 0, 'UTF-8') !== false) {
                return true;
            }
        }
        return false;
    }

    private function containsAnyKeyword(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            $pattern = '/\b' . preg_quote($keyword, '/') . '\b/u';
            if (preg_match($pattern, $text) === 1) {
                return true;
            }
        }
        return false;
    }

    private function containsAnyWord(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            $pattern = '/(^|[^\p{L}\p{N}_])' . preg_quote($keyword, '/') . '([^\p{L}\p{N}_]|$)/u';
            if (preg_match($pattern, $text) === 1) {
                return true;
            }
        }
        return false;
    }

    private function dedupe(array $candidates): array
    {
        $seen = [];
        $out = [];
        foreach ($candidates as $candidate) {
            $intent = (string) ($candidate['intent'] ?? 'general');
            if (isset($seen[$intent])) {
                continue;
            }
            $seen[$intent] = true;
            $out[] = $candidate;
        }
        return $out;
    }
}
