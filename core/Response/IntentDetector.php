<?php
namespace Core\Response;

class IntentDetector {
    private static ?string $lastIntent = null;

    /**
     * Detect intent from text with Hinglish support + context awareness
     * Returns intent string
     */
    /**
     * Get confidence score for the last detection (0.0 - 1.0)
     */
    public function getLastIntent(): ?string {
        return self::$lastIntent;
    }
    public function detect(string $text): string {
        $text = strtolower(trim($text));
        $candidates = $this->detectMany((string) $text);
        return $candidates[0]['intent'] ?? 'general';
        
        // Continue-conversation patterns → use last intent
        if (self::$lastIntent && preg_match('/^(aur|or|and|btao|batao|more|continue|agge|aage|phir|fir)\b/i', $text)) {
            return self::$lastIntent;
        }

        // Image Generation
        if (preg_match('/(image|photo|pic|bnao|banao|generate|drawing|sketch|visualize|portrait|landscape|tasveer|dikha|draw|paint)/i', $text)) {
            self::$lastIntent = 'image_gen';
            return 'image_gen';
        }
        
        // Programming/Code
        if (preg_match('/(code|program|script|coding|develop|function|class|html|css|javascript|python|java|php|mysql|react|node|api|backend|frontend|database|sql|algorithm|loop|array|variable|debug|compile|syntax|git|framework)/i', $text)) {
            self::$lastIntent = 'coding';
            return 'coding';
        }
        
        // Math / Calculation
        if (preg_match('/(calculate|hisab|math|maths|solve|equation|plus|minus|multiply|divide|sum|average|percentage|factorial|root|algebra|geometry|trigonometry|\d+[\+\-\*\/]\d+)/i', $text)) {
            self::$lastIntent = 'math';
            return 'math';
        }
        
        // Weather
        if (preg_match('/(weather|mausam|temperature|barish|rain|garmi|sardi|humidity|forecast)/i', $text)) {
            self::$lastIntent = 'weather';
            return 'weather';
        }
        
        // News
        if (preg_match('/(news|khabar|samachar|headlines|taza|latest|trending|breaking)/i', $text)) {
            self::$lastIntent = 'news';
            return 'news';
        }
        
        // Translation
        if (preg_match('/(translate|anuvad|hindi mein|english mein|meaning of|matlab|iska matlab)/i', $text)) {
            self::$lastIntent = 'translation';
            return 'translation';
        }
        
        // Identity / Awareness
        if (preg_match('/(naam|who are you|identity|name|tera naam|tumhara naam|made you|creator|developer|hritik ai|kaun ho|kaun hai tu|kisne banaya)/i', $text)) {
            self::$lastIntent = 'identity';
            return 'identity';
        }
        
        // Greetings / Conversational  
        if (preg_match('/^(hello|hi|hey|namaste|helo|hlo|yo)$/i', $text) || preg_match('/(kese ho|kaise ho|how are you|kya haal|good morning|good night|good evening|salam|assalam)/i', $text)) {
            self::$lastIntent = 'greeting';
            return 'greeting';
        }
        
        // General Chat / "What's up"
        if (preg_match('/(or btao|aur batao|kya chal raha|whats up|sup|^nothing$|bore|kuch sunao|baat karo|timepass|mazak|joke|hasao)/i', $text)) {
            self::$lastIntent = 'chat';
            return 'chat';
        }
        
        // Informational / Search / Knowledge Questions
        if (preg_match('/(what is|who is|where is|kaun hai|kahan hai|kab|when|how many|how much|how to|translate|population|capital|rajdhani|define|meaning|matlab|Nobel|prize|oscar|winner|president|prime minister|country|world|history|science|geography|explain|samjhao|btao kya|batao kya)/i', $text)) {
            self::$lastIntent = 'informational';
            return 'informational';
        }
        
        // Farewell
        if (preg_match('/(^bye$|goodbye|cya|see you|alvida|chalo|band karo|bas|shukria|dhanyawad|thank)/i', $text)) {
            self::$lastIntent = 'farewell';
            return 'farewell';
        }
        
        // Tool Queries
        if (preg_match('/(convert|badlo|password|json|format|calculator|qr|scan|ip|lookup)/i', $text)) {
            self::$lastIntent = 'tool_query';
            return 'tool_query';
        }

        // If query has a question mark or question words, it's probably informational
        if (str_contains($text, '?') || preg_match('/^(kya|kaun|kab|kahan|kaha|kitna|kitne|kyu|kyun|kon|kiske|kiski|kiska)/i', $text)) {
            self::$lastIntent = 'informational';
            return 'informational';
        }

        self::$lastIntent = 'unknown';
        return 'unknown';
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
