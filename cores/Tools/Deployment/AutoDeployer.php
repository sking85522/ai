<?php
namespace Core\Tools\Deployment;

class AutoDeployer {
    public function deploy(string $project): string {
        $project = trim($project);
        if ($project === '') {
            return '[DEPLOY] Project path missing.';
        }

        $root = dirname(__DIR__, 3);
        $path = preg_match('/^[A-Za-z]:[\\\\\/]/', $project) ? $project : $root . DIRECTORY_SEPARATOR . $project;
        if (!is_dir($path)) {
            return '[DEPLOY] Project folder not found: ' . $project;
        }

        $buildDir = $root . DIRECTORY_SEPARATOR . 'builds';
        if (!is_dir($buildDir)) {
            mkdir($buildDir, 0777, true);
        }

        $manifest = $buildDir . DIRECTORY_SEPARATOR . basename($project) . '_deploy_manifest.txt';
        file_put_contents($manifest, "Deploy manifest for {$project}\nGenerated: " . date('c') . "\n");

        return '[DEPLOY] Manifest created: builds/' . basename($manifest);
    }
}
