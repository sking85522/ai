<?php
namespace Core\Training\Feedback;

class LocalTeacherBridge {
    private string $baseUrl;
    private int $timeout;

    public function __construct(?string $baseUrl = null, int $timeout = 8) {
        $this->baseUrl = rtrim($baseUrl ?: (getenv('HRITIK_LOCAL_TEACHER_URL') ?: 'http://127.0.0.1:5000'), '/');
        $this->timeout = $timeout;
    }

    public function isEnabled(): bool {
        return getenv('HRITIK_TEACHER_ENABLED') !== '0';
    }

    public function isAvailable(): bool {
        if (!$this->isEnabled() || !function_exists('curl_init')) {
            return false;
        }

        $ch = curl_init($this->baseUrl . '/health');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 2
        ]);
        $body = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || !$body) {
            return false;
        }

        $data = json_decode($body, true);
        return ($data['status'] ?? '') === 'ok' && ($data['model_loaded'] ?? false);
    }

    public function evaluate(string $prompt, string $phpAnswer, array $history = [], float $confidence = 0.0): array {
        if (!$this->isEnabled() || !function_exists('curl_init')) {
            return ['status' => 'skipped', 'message' => 'Local teacher disabled.'];
        }

        $payload = [
            'prompt' => $prompt,
            'php_answer' => $phpAnswer,
            'history' => $history,
            'confidence' => $confidence
        ];

        $ch = curl_init($this->baseUrl . '/evaluate');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => $this->timeout
        ]);
        $body = curl_exec($ch);
        $error = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || !$body) {
            return [
                'status' => 'error',
                'message' => $error ?: 'Local teacher unavailable.',
                'http_code' => $code
            ];
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            return ['status' => 'error', 'message' => 'Invalid local teacher JSON.'];
        }

        return $data;
    }
}
