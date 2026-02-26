<?php
class ShellHttpAdapter
{
    public function getJson(string $url, int $timeoutMs = 5000): ?array
    {
        $urlEscaped = str_replace("'", "''", $url);
        $timeoutSec = max(1, (int) ceil($timeoutMs / 1000));

        $ps = '$ProgressPreference="SilentlyContinue";'
            . 'try{'
            . '$r=Invoke-WebRequest -UseBasicParsing -TimeoutSec ' . $timeoutSec . ' -Uri \'' . $urlEscaped . '\';'
            . '[Console]::OutputEncoding=[Text.Encoding]::UTF8;'
            . 'Write-Output $r.Content;'
            . '}catch{Write-Output "";}';

        $cmd = 'powershell -NoProfile -ExecutionPolicy Bypass -Command "' . str_replace('"', '\"', $ps) . '"';
        $raw = @shell_exec($cmd);
        if (!is_string($raw) || trim($raw) === '') {
            return null;
        }

        $json = json_decode($raw, true);
        return is_array($json) ? $json : null;
    }
}
