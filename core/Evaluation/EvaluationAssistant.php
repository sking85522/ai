<?php
class EvaluationAssistant
{
    public function describe(string $language = 'bilingual'): string
    {
        if ($language === 'hi') {
            return 'Evaluation module me accuracy, precision, recall, F1-score, confusion matrix aur validation workflow ready hai.';
        }
        return 'Evaluation module covers accuracy, precision, recall, F1-score, confusion matrix, and validation workflow.';
    }
}
