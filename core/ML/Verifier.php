<?php
class Verifier
{
    public function verify(string $input, array $toolOutputs): array
    {
        $nonEmpty = array_values(array_filter($toolOutputs, static fn($x) => is_string($x) && trim($x) !== ''));
        $ok = count($nonEmpty) > 0;
        $notes = [];

        if (!$ok) {
            $notes[] = 'No reliable tool output found.';
        }

        $joined = mb_strtolower(implode("\n", $nonEmpty), 'UTF-8');
        if (str_contains($joined, 'fatal error') || str_contains($joined, 'uncaught')) {
            $ok = false;
            $notes[] = 'Execution produced runtime error text.';
        }

        if (mb_strlen($joined, 'UTF-8') > 2500) {
            $notes[] = 'Output truncated for readability.';
        }

        return [
            'ok' => $ok,
            'notes' => $notes,
            'confidence' => $ok ? 0.9 : 0.45,
        ];
    }
}
