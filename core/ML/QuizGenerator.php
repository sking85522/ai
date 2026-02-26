<?php
class QuizGenerator
{
    public function generate(string $input, string $language): string
    {
        $topic = $this->detectTopic($input);
        $questions = $this->questionsByTopic($topic, $language);
        shuffle($questions);
        $picked = array_slice($questions, 0, 5);

        $lines = [];
        $lines[] = $language === 'hi' ? 'क्विज शुरू: 5 प्रश्न' : 'Quiz started: 5 questions';
        $index = 1;
        foreach ($picked as $q) {
            $lines[] = $index . '. ' . $q['q'];
            $lines[] = '   A) ' . $q['a'];
            $lines[] = '   B) ' . $q['b'];
            $lines[] = '   C) ' . $q['c'];
            $lines[] = '   D) ' . $q['d'];
            $lines[] = $language === 'hi' ? ('   सही उत्तर: ' . $q['ans']) : ('   Correct answer: ' . $q['ans']);
            $index++;
        }
        return implode(PHP_EOL, $lines);
    }

    private function detectTopic(string $input): string
    {
        $x = mb_strtolower($input, 'UTF-8');
        if (str_contains($x, 'physics') || str_contains($x, 'भौतिक')) {
            return 'physics';
        }
        if (str_contains($x, 'math') || str_contains($x, 'गणित')) {
            return 'math';
        }
        return 'mixed';
    }

    private function questionsByTopic(string $topic, string $language): array
    {
        $hi = $language === 'hi';
        $bank = [
            'physics' => [
                ['q' => $hi ? 'बल की SI इकाई क्या है?' : 'What is the SI unit of force?', 'a' => 'Newton', 'b' => 'Joule', 'c' => 'Watt', 'd' => 'Pascal', 'ans' => 'A'],
                ['q' => $hi ? 'प्रकाश की गति लगभग कितनी है?' : 'What is the speed of light (approx)?', 'a' => '3 x 10^8 m/s', 'b' => '3 x 10^6 m/s', 'c' => '1.5 x 10^8 m/s', 'd' => '9.8 m/s^2', 'ans' => 'A'],
                ['q' => $hi ? 'F = m x ?' : 'F = m x ?', 'a' => 'a', 'b' => 'v', 'c' => 'p', 'd' => 't', 'ans' => 'A'],
                ['q' => $hi ? 'विद्युत धारा की इकाई क्या है?' : 'Unit of electric current?', 'a' => 'Volt', 'b' => 'Ohm', 'c' => 'Ampere', 'd' => 'Coulomb', 'ans' => 'C'],
                ['q' => $hi ? 'गुरुत्व त्वरण पृथ्वी पर लगभग?' : 'Acceleration due to gravity on Earth?', 'a' => '9.8 m/s^2', 'b' => '8.9 m/s^2', 'c' => '10.8 m/s^2', 'd' => '7.8 m/s^2', 'ans' => 'A'],
            ],
            'math' => [
                ['q' => $hi ? 'sin(90°) का मान?' : 'Value of sin(90°)?', 'a' => '0', 'b' => '1', 'c' => '0.5', 'd' => '-1', 'ans' => 'B'],
                ['q' => $hi ? 'त्रिभुज के कोणों का योग?' : 'Sum of angles in a triangle?', 'a' => '90°', 'b' => '180°', 'c' => '270°', 'd' => '360°', 'ans' => 'B'],
                ['q' => $hi ? '2x + 4 = 10, x = ?' : '2x + 4 = 10, x = ?', 'a' => '2', 'b' => '3', 'c' => '4', 'd' => '5', 'ans' => 'B'],
                ['q' => $hi ? 'वृत्त का क्षेत्रफल सूत्र?' : 'Area of circle formula?', 'a' => 'pi*r^2', 'b' => '2*pi*r', 'c' => 'l*w', 'd' => 'b*h', 'ans' => 'A'],
                ['q' => $hi ? '3,4 वेक्टर का परिमाण?' : 'Magnitude of vector (3,4)?', 'a' => '4', 'b' => '5', 'c' => '6', 'd' => '7', 'ans' => 'B'],
            ],
        ];

        if ($topic === 'mixed') {
            return array_merge($bank['physics'], $bank['math']);
        }
        return $bank[$topic] ?? $bank['math'];
    }
}
