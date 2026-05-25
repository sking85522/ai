<?php
namespace Core\Engine\Monitoring;

/**
 * HRITIK AI - NEURAL HEALTH MONITOR
 * Monitors system vitals, database health, and neural connectivity.
 */
class NeuralHealthMonitor {
    
    public function getStatus(): array {
        return [
            'database' => $this->checkDatabase(),
            'neural_link' => $this->checkTeacherAPI(),
            'disk_space' => $this->getDiskUsage(),
            'memory_usage' => $this->getMemoryUsage(),
            'engine_uptime' => time() - $_SERVER['REQUEST_TIME'],
            'status' => 'OPTIMAL'
        ];
    }

    private function checkDatabase(): string {
        global $db;
        return (isset($db) && $db !== null) ? 'CONNECTED' : 'OFFLINE';
    }

    private function checkTeacherAPI(): string {
        $ch = curl_init("http://127.0.0.1:5000/evaluate");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['prompt' => 'ping']));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($code === 200) ? 'ACTIVE' : 'LATENCY_DETECTED';
    }

    private function getDiskUsage(): string {
        $free = disk_free_space(".");
        $total = disk_total_space(".");
        return round(($free / $total) * 100, 2) . "% Free";
    }

    private function getMemoryUsage(): string {
        return round(memory_get_usage() / 1024 / 1024, 2) . " MB";
    }
}
