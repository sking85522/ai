<?php
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

    private function landingPageSnippet(): string
    {
        return <<<MD
```html
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Product Landing</title>
  <style>
    :root { --bg:#0f172a; --accent:#22c55e; --text:#e2e8f0; }
    body { margin:0; font-family:Segoe UI,sans-serif; background:linear-gradient(140deg,#0f172a,#1e293b); color:var(--text); }
    .hero { max-width:900px; margin:0 auto; padding:5rem 1rem; text-align:center; }
    .btn { display:inline-block; background:var(--accent); color:#052e16; padding:.8rem 1.2rem; border-radius:8px; text-decoration:none; font-weight:700; }
    .cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1rem; margin-top:2rem; }
    .card { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:12px; padding:1rem; }
  </style>
</head>
<body>
  <section class="hero">
    <h1>Ship Faster With Smart Automation</h1>
    <p>Design, build, and deploy your product workflow in minutes.</p>
    <a class="btn" href="#start">Get Started</a>
    <div class="cards">
      <div class="card">Realtime Analytics</div>
      <div class="card">Secure API Layer</div>
      <div class="card">Auto Scaling</div>
    </div>
  </section>
</body>
</html>
```
MD;
    }

    private function htmlStarterSnippet(): string
    {
        return <<<MD
```html
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My App</title>
</head>
<body>
  <h1>Hello, World</h1>
  <p>Start building your page here.</p>
</body>
</html>
```
MD;
    }

    private function phpStarterSnippet(): string
    {
        return <<<'MD'
```php
<?php
function greetUser(string $name): string
{
    $name = trim($name);
    if ($name === '') {
        return 'Hello, Guest!';
    }
    return 'Hello, ' . ucfirst($name) . '!';
}
```
MD;
    }

    private function phpLoginSnippet(): string
    {
        return <<<'MD'
```php
<?php
// login.php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, name, password_hash FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid credentials';
}
```
MD;
    }

    private function sqlSnippet(): string
    {
        return <<<MD
```sql
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```
MD;
    }

    private function jsSnippet(): string
    {
        return <<<MD
```javascript
function groupBy(items, keyFn) {
  return items.reduce((acc, item) => {
    const key = keyFn(item);
    if (!acc[key]) acc[key] = [];
    acc[key].push(item);
    return acc;
  }, {});
}
```
MD;
    }

    private function javaSnippet(): string
    {
        return <<<MD
```java
public class Main {
    public static void main(String[] args) {
        System.out.println("Hello, Java");
    }
}
```
MD;
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
