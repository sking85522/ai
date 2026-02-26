<?php
class TrainingProcessAssistant
{
    public function describe(string $language = 'bilingual'): string
    {
        if ($language === 'hi') {
            return 'Training Process module me forward pass, loss calculation, backpropagation aur optimizers configured hain.';
        }
        return 'Training Process module includes forward pass, loss calculation, backpropagation, and optimizers.';
    }
}
