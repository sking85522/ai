<?php
class MachineLearningAssistant
{
    public function describe(string $language = 'bilingual'): string
    {
        if ($language === 'hi') {
            return 'Machine Learning Algorithms module me supervised, unsupervised, aur reinforcement learning support integrated hai.';
        }
        return 'Machine Learning Algorithms module supports supervised, unsupervised, and reinforcement learning.';
    }
}
