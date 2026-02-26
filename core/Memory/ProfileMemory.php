<?php
class ProfileMemory
{
    private string $file;

    public function __construct(?string $file = null)
    {
        $this->file = $file ?: __DIR__ . '/../../storage/training/profile_memory.json';
        if (!is_file($this->file)) {
            file_put_contents($this->file, json_encode(['profiles' => ['default' => []]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    public function inferFromInput(string $input, string $language = 'bilingual'): void
    {
        $profile = $this->readProfile();
        $x = mb_strtolower(trim($input), 'UTF-8');

        if (preg_match('/my name is\s+(.+)$/iu', $input, $m) === 1 || preg_match('/mera naam\s+(.+)$/iu', $input, $m) === 1) {
            $profile['name'] = trim((string) $m[1]);
        }

        if (str_contains($x, 'answer in hindi') || str_contains($x, 'hindi me') || str_contains($x, 'देवनागरी')) {
            $profile['preferred_language'] = 'hi';
        } elseif (str_contains($x, 'answer in english')) {
            $profile['preferred_language'] = 'en';
        } elseif (!isset($profile['preferred_language'])) {
            $profile['preferred_language'] = $language;
        }

        $profile['updated_at'] = date('c');
        $this->writeProfile($profile);
    }

    public function getPreferredLanguage(): ?string
    {
        $profile = $this->readProfile();
        $lang = $profile['preferred_language'] ?? null;
        return in_array($lang, ['en', 'hi', 'bilingual'], true) ? $lang : null;
    }

    public function getProfile(): array
    {
        return $this->readProfile();
    }

    private function readProfile(): array
    {
        $raw = file_get_contents($this->file);
        $json = json_decode((string) $raw, true);
        if (!is_array($json)) {
            return [];
        }
        return $json['profiles']['default'] ?? [];
    }

    private function writeProfile(array $profile): void
    {
        $raw = file_get_contents($this->file);
        $json = json_decode((string) $raw, true);
        if (!is_array($json)) {
            $json = ['profiles' => []];
        }
        $json['profiles']['default'] = $profile;
        file_put_contents($this->file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
