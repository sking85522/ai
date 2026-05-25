<?php
namespace Core\Engine;

/**
 * HRITIK AI - COMMAND ROUTER
 * Handles system-level commands like self-scanning, training, and privacy.
 */
class CommandRouter {
    private $introspection;
    private $ingestor;
    private $relationships;
    private $profile;
    private $memory;

    public function __construct($introspection, $ingestor, $relationships, $profile, $memory) {
        $this->introspection = $introspection;
        $this->ingestor = $ingestor;
        $this->relationships = $relationships;
        $this->profile = $profile;
        $this->memory = $memory;
    }

    public function handle(string $prompt, string $sessionId): ?array {
        // 1. Forget Everything
        if ($this->relationships->isForgetRequest($prompt)) {
            $this->profile->set($sessionId, 'user_name', null);
            $this->profile->set($sessionId, 'fav_color', null);
            $this->profile->set($sessionId, 'hobby', null);
            $this->memory->clear($sessionId);
            return [
                'status' => 'success',
                'response' => "Theek hai, maine aapki saari personal memories delete kar di hain. Main ab aapko ek naye dost ki tarah treat karunga.",
                'intent' => 'privacy_reset'
            ];
        }

        // 2. Self-Introspection
        if (preg_match('/(scan yourself|know your code|about your files|internal jankari)/i', $prompt)) {
            $stats = $this->introspection->learnSelf();
            return [
                'status' => 'success',
                'response' => "maine apna poora architecture aur source code scan kar liya hai. Maine {$stats['files_scanned']} files ka analysis kiya. Ab mujhe pta hai ki main kaise bana hoon!",
                'intent' => 'self_introspection'
            ];
        }

        // 3. Knowledge Ingestion
        if (preg_match('/(learn from|ingest|analyze) (files|storage|project)/i', $prompt)) {
            $result = $this->ingestor->ingestDirectory(__DIR__ . '/../../storage/training/');
            return [
                'status' => 'success',
                'response' => "maine project storage scan kar liya hai. Maine {$result['files_processed']} files analyze ki hain.",
                'intent' => 'knowledge_ingestion'
            ];
        }

        // 4. Start Mass Training
        if (preg_match('/(mass_train.php start|shuru kro training|start training)/i', $prompt)) {
            return [
                'status' => 'success',
                'response' => "Theek hai main training shuru kar raha hoon. Aapko screen par live progress dikhegi...\n\n[CMD_EXEC] mass_train.php",
                'intent' => 'start_training'
            ];
        }

        return null; // Not a system command
    }
}
