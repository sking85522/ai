<?php
class ConversationalFallback
{
    public function reply(string $input, string $language, array $recent = []): string
    {
        $input = trim($input);
        $lower = mb_strtolower($input, 'UTF-8');

        if ($this->looksLikeNonsense($lower)) {
            return $this->pick($language, [
                'en' => 'I could not understand that. Please rephrase or teach me this topic in 2-4 lines.',
                'hi' => 'Yeh input mujhe clear nahi hua. Isko dobara likho ya 2-4 line me samjhao, main seekh lunga.',
                'bilingual' => 'Yeh clear nahi hua. Dobara likho ya 2-4 lines me sikhao, main seekh kar answer dunga.',
            ]);
        }

        if ($this->containsAny($lower, ['kaise ho', 'kese ho', 'how are you'])) {
            return $this->pick($language, [
                'en' => [
                    'I am doing well. Tell me what you want to explore next.',
                    'Doing good. Share your next task and I will handle it.',
                ],
                'hi' => [
                    'Main theek hoon. Aaj kis topic par baat karein?',
                    'Main badhiya hoon. Agla kaam batao, main start karta hoon.',
                ],
                'bilingual' => [
                    'Main bilkul theek hoon. Aap batao, what do you want to discuss?',
                    'Main ready hoon. Share your next task.',
                ],
            ]);
        }

        if ($this->containsAny($lower, ['kya haal', 'haal chal', 'kya haal h'])) {
            return $this->pick($language, [
                'en' => 'I am doing good. You can ask coding, math, design, or general questions.',
                'hi' => 'Main badhiya hoon. Aap coding, math, design ya normal sawal puch sakte ho.',
                'bilingual' => 'Main badhiya hoon. You can ask coding, math, design ya general questions.',
            ]);
        }

        if ($this->containsAny($lower, ['kya kya kr skte ho', 'kya kar sakte ho', 'what can you do'])) {
            return $this->pick($language, [
                'en' => 'I can do coding help, debugging, quiz creation, math solving, web lookup, and memory-based chat.',
                'hi' => 'Main coding help, debugging, quiz, math solving, web lookup aur memory-based chat kar sakta hoon.',
                'bilingual' => 'Main coding, debugging, quiz, math, web lookup aur memory chat kar sakta hoon.',
            ]);
        }

        if ($this->containsAny($lower, ['mujhse kuch sikhna', 'mujese kuch sikhna', 'mujhse siko', 'mujse siko', 'learn from me', 'kya tuko mujese kuch sikhna h'])) {
            return $this->pick($language, [
                'en' => 'Yes. Send your notes, docs, or links. I will learn and answer from them.',
                'hi' => 'Haan, notes, docs ya links bhejo. Main unse seekh kar jawab dunga.',
                'bilingual' => 'Haan, notes/docs/links bhejo. Main learn karke answers dunga.',
            ]);
        }

        if ($this->containsAny($lower, ['joke', 'ek joke'])) {
            return $this->pick($language, [
                'en' => [
                    'Why do programmers mix up Halloween and Christmas? Because OCT 31 == DEC 25.',
                    'I told my code to take a break. It said: I will return after compile.',
                ],
                'hi' => [
                    'Programmer ka bug chhota hota hai, par raat badi kar deta hai.',
                    'Teacher: Late kyu aaye? Student: Sir, sapne me code debug kar raha tha.',
                ],
                'bilingual' => [
                    'Programmer joke: OCT 31 == DEC 25.',
                    'Bug chhota hota hai, lekin raat badi kar deta hai.',
                ],
            ]);
        }

        if ($this->containsAny($lower, ['who are you', 'tum kaun ho', 'aap kaun ho'])) {
            return $this->pick($language, [
                'en' => 'I am your local PHP AI assistant with memory and training support.',
                'hi' => 'Main aapka local PHP AI assistant hoon jo memory aur training support karta hai.',
                'bilingual' => 'Main aapka local PHP AI assistant hoon with memory and training support.',
            ]);
        }

        if ($this->containsAny($lower, ['what is your name', 'whats your name', "what's your name", 'tumhara naam'])) {
            return $this->pick($language, [
                'en' => 'My name is Jyoti AI assistant.',
                'hi' => 'Mera naam Jyoti AI assistant hai.',
                'bilingual' => 'Mera naam Jyoti AI assistant hai.',
            ]);
        }

        if (str_ends_with($lower, '?')) {
            $lastHint = '';
            if (!empty($recent)) {
                $last = end($recent);
                $lastHint = isset($last['input']) ? ' Last topic: ' . mb_substr((string) $last['input'], 0, 60, 'UTF-8') . '.' : '';
            }
            return $this->pick($language, [
                'en' => 'Good question. Add one more detail for a sharper answer.' . $lastHint,
                'hi' => 'Accha sawal hai. Ek aur detail doge to main better jawab dunga.' . $lastHint,
                'bilingual' => 'Good question. Thoda aur context doge to main better jawab dunga.' . $lastHint,
            ]);
        }

        return $this->pick($language, [
            'en' => [
                'I am ready. Ask a specific question, or teach me the topic and I will learn it.',
                'Send your next prompt. If this is a new topic, explain it in short and I will learn.',
            ],
            'hi' => [
                'Main ready hoon. Specific sawal bhejo, ya naya topic samjhao to main seekh lunga.',
                'Aap next prompt bhejo. Agar new topic hai to short notes bhejo, main usse learn karunga.',
            ],
            'bilingual' => [
                'Main ready hoon. Specific question bhejo, ya topic sikhao and I will learn.',
                'Next prompt bhejo. New topic hoga to main usse learn karke answer dunga.',
            ],
        ]);
    }

    private function containsAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (mb_stripos($text, $needle, 0, 'UTF-8') !== false) {
                return true;
            }
        }
        return false;
    }

    private function pick(string $language, array $map): string
    {
        $candidate = $map[$language] ?? $map['bilingual'];
        if (is_array($candidate) && $candidate) {
            return (string) $candidate[array_rand($candidate)];
        }
        return (string) $candidate;
    }

    private function looksLikeNonsense(string $text): bool
    {
        $text = trim($text);
        if ($text === '') {
            return false;
        }
        if (preg_match('/^[a-z]{8,}$/u', $text) === 1) {
            return true;
        }
        $parts = preg_split('/\s+/u', $text) ?: [];
        if (count($parts) === 1) {
            $word = $parts[0];
            if (mb_strlen($word, 'UTF-8') >= 9 && preg_match('/[aeiou]/i', $word) !== 1) {
                return true;
            }
        }
        return false;
    }
}
