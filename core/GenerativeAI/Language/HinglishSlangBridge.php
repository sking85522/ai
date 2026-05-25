<?php
namespace Core\GenerativeAI\Language;

/**
 * HRITIK AI - HINGLISH SLANG BRIDGE
 * Injects natural Indian conversational markers to make the AI sound more human.
 */
class HinglishSlangBridge {
    
    private array $fillers = [
        'start' => ['Arre, ', 'Dekho, ', 'Theek hai, ', 'Bhai, ', 'Suno, ', 'Vaise, '],
        'middle' => [' matlab ', ' jaise ki ', ' waise ', ' toh ', ' basically '],
        'end' => [', samjhe?', ', theek hai na?', ', bas wahi.', '...', ' na?']
    ];

    /**
     * Inject slangs into a sentence based on context.
     */
    public function inject(string $text): string {
        // Only inject if the sentence is very short and lacks natural markers
        if (strlen($text) < 30 && !str_contains($text, ',') && rand(1, 3) === 1) {
            $starter = $this->fillers['start'][array_rand($this->fillers['start'])];
            $text = $starter . $text;
        }

        // Add a natural end marker sometimes (1 in 6 chance)
        if (rand(1, 6) === 2) {
            $ender = $this->fillers['end'][array_rand($this->fillers['end'])];
            $text = rtrim($text, '.') . $ender;
        }

        return $text;
    }
}
