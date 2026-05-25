<?php
namespace Core\ML;

class CodeComposer {
    /**
     * Generates PHP/JS boilerplate code based on patterns.
     */
    public function compose(string $type, array $params): string {
        if ($type === 'php_class') {
            $name = $params['name'] ?? 'GeneratedClass';
            return "<?php\nclass {$name} {\n    public function __construct() {\n        // Code here\n    }\n}";
        }
        
        if ($type === 'js_component') {
            $name = $params['name'] ?? 'Component';
            return "function {$name}() {\n    console.log('Component Loaded');\n}";
        }

        return "// Template not found";
    }
}
class CodeComposer
{
    public function compose(string $input, string $languageMode): array
    {
        $text = mb_strtolower(trim($input), 'UTF-8');

        if ($this->has($text, ['index.html', 'html code', 'what is html', 'write html'])) {
            return [
                'intent' => 'code_generate',
                'title' => 'HTML Starter (index.html)',
                'body' => $this->htmlStarterSnippet(),
            ];
        }

        if ($this->has($text, ['write php', 'php code', 'php function'])) {
            return [
                'intent' => 'code_generate',
                'title' => 'PHP Starter Function',
                'body' => $this->phpStarterSnippet(),
            ];
        }
        if (preg_match('/^\s*php\s*$/u', $text) === 1) {
            return [
                'intent' => 'code_generate',
                'title' => 'PHP Starter Function',
                'body' => $this->phpStarterSnippet(),
            ];
        }

        if ($this->has($text, ['javascript code', 'js code', 'write javascript'])) {
            return [
                'intent' => 'code_generate',
                'title' => 'JavaScript Utility Function',
                'body' => $this->jsSnippet(),
            ];
        }
        if (preg_match('/^\s*(javascript|js)\s*$/u', $text) === 1) {
            return [
                'intent' => 'code_generate',
                'title' => 'JavaScript Utility Function',
                'body' => $this->jsSnippet(),
            ];
        }
        if (preg_match('/^\s*java\s*$/u', $text) === 1 || $this->has($text, ['java code', 'write java'])) {
            return [
                'intent' => 'code_generate',
                'title' => 'Java Starter Class',
                'body' => $this->javaSnippet(),
            ];
        }

        if ($this->has($text, ['web design', 'landing page', 'html css', 'website'])) {
            return [
                'intent' => 'code_generate',
                'title' => 'Responsive Landing Page (HTML/CSS)',
                'body' => $this->landingPageSnippet(),
            ];
        }

        if ($this->has($text, ['login', 'php login', 'authentication'])) {
            return [
                'intent' => 'code_generate',
                'title' => 'PHP Login Handler (PDO)',
                'body' => $this->phpLoginSnippet(),
            ];
        }

        if ($this->has($text, ['sql table', 'create table', 'database schema'])) {
            return [
                'intent' => 'code_generate',
                'title' => 'SQL User Table',
                'body' => $this->sqlSnippet(),
            ];
        }

        if ($this->has($text, ['software design', 'software engineering', 'system design', 'architecture'])) {
            return [
                'intent' => 'engineering_plan',
                'title' => 'Software Engineering Blueprint',
                'body' => $this->engineeringPlan($languageMode),
            ];
        }

        if ($this->has($text, ['self upgrade', 'auto upgrade', 'upgrade your code', 'khud update', 'khud upgrade'])) {
            return [
                'intent' => 'engineering_plan',
                'title' => 'Self-Upgrade Protocol',
                'body' => $this->selfUpgradePlan($languageMode),
            ];
        }

        if ($this->has($text, ['debug', 'error', 'bug', 'fix'])) {
            return [
                'intent' => 'debug_help',
                'title' => 'Debug Playbook',
                'body' => $this->debugPlaybook($languageMode),
            ];
        }

        return [
            'intent' => 'code_generate',
            'title' => 'Starter Function (JavaScript)',
            'body' => $this->jsSnippet(),
        ];
    }

    private function has(string $text, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (mb_stripos($text, $needle, 0, 'UTF-8') !== false) {
                return true;
            }
        }
        return false;
    }

    

    private function engineeringPlan(string $languageMode): string
    {
        if ($languageMode === 'hi') {
            return "1. Requirements freeze\n2. Architecture + data model\n3. API contract\n4. Implementation by module\n5. Tests + monitoring\n6. Release + rollback plan";
        }
        if ($languageMode === 'en') {
            return "1. Define requirements\n2. Model architecture and data flows\n3. Lock API contracts\n4. Implement by module with code review\n5. Add tests and observability\n6. Release with rollback strategy";
        }
        return "1. Requirement clear karo\n2. Architecture + DB model banao\n3. API contract lock karo\n4. Module-wise implementation karo\n5. Tests + logs add karo\n6. Safe release + rollback ready rakho";
    }

    private function debugPlaybook(string $languageMode): string
    {
        if ($languageMode === 'hi') {
            return "Debug steps: error reproduce karo, logs collect karo, root cause isolate karo, fix likho, regression test karo.";
        }
        if ($languageMode === 'en') {
            return "Debug steps: reproduce issue, gather logs, isolate root cause, patch safely, add regression test.";
        }
        return "Debug flow: issue reproduce karo, logs dekho, root-cause pakdo, fix karo, test rerun karo.";
    }

    private function selfUpgradePlan(string $languageMode): string
    {
        if ($languageMode === 'hi') {
            return "1. Code health scan\n2. Failing tests identify\n3. Safe patch branch\n4. Regression checks\n5. Version tag and rollback snapshot";
        }
        if ($languageMode === 'en') {
            return "1. Scan code quality and hotspots\n2. Identify failing tests and weak coverage\n3. Patch in a safe branch\n4. Run regression + performance checks\n5. Tag version and keep rollback snapshot";
        }
        return "1. Code scan karo\n2. Weak modules identify karo\n3. Safe patch karo\n4. Regression tests chalao\n5. Version tag + rollback ready rakho";
    }
}
