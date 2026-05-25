<?php

namespace ParallelPHP\Core;

class Pool
{
    private $concurrency;
    private $tasks = [];
    private $running = [];
    private $results = [];
    private $workerScript;

    public function __construct(int $concurrency = 4)
    {
        $this->concurrency = $concurrency;
        $this->workerScript = __DIR__ . '/../Worker/execute.php';
    }

    public function submit($callable, array $args = []): int
    {
        $taskId = count($this->tasks) + count($this->running) + count($this->results);

        $debugBacktrace = debug_backtrace();
        $callerScript = $debugBacktrace[0]['file'] ?? null;

        $payload = base64_encode(serialize([
            'callable' => $callable,
            'args' => $args,
            'callerScript' => $callerScript
        ]));

        $this->tasks[$taskId] = $payload;
        return $taskId;
    }

    public function wait(): array
    {
        while (count($this->tasks) > 0 || count($this->running) > 0) {

            // Fill available slots
            while (count($this->running) < $this->concurrency && count($this->tasks) > 0) {
                reset($this->tasks);
                $taskId = key($this->tasks);
                $payload = $this->tasks[$taskId];
                unset($this->tasks[$taskId]);

                $cmd = "php " . escapeshellarg($this->workerScript) . " " . escapeshellarg($payload);

                $descriptorspec = [
                    0 => ["pipe", "r"],
                    1 => ["pipe", "w"],
                    2 => ["pipe", "w"]
                ];

                $process = proc_open($cmd, $descriptorspec, $pipes);

                if (is_resource($process)) {
                    stream_set_blocking($pipes[1], false);
                    stream_set_blocking($pipes[2], false);

                    $this->running[$taskId] = [
                        'process' => $process,
                        'pipes' => $pipes,
                        'stdout' => '',
                        'stderr' => ''
                    ];
                } else {
                    $this->results[$taskId] = ['error' => 'Failed to start process'];
                }
            }

            // Check running processes
            foreach ($this->running as $taskId => &$run) {
                $status = proc_get_status($run['process']);

                $run['stdout'] .= stream_get_contents($run['pipes'][1]);
                $run['stderr'] .= stream_get_contents($run['pipes'][2]);

                if (!$status['running']) {
                    fclose($run['pipes'][0]);
                    fclose($run['pipes'][1]);
                    fclose($run['pipes'][2]);
                    proc_close($run['process']);

                    if (!empty($run['stderr'])) {
                        // The worker script outputted an error but also might have outputted base64. Let's try to extract.
                        $err = trim($run['stderr']);
                        $this->results[$taskId] = ['error' => $err];
                    } else {
                        $output = trim($run['stdout']);
                        // Sometimes PHP displays notices/warnings which ruins base64_decode.
                        // Try to find the last string that looks like a base64 encoded serialized array.
                        preg_match('/[a-zA-Z0-9+\/]+={0,2}$/', $output, $matches);
                        $cleanOutput = $matches[0] ?? $output;

                        $result = @unserialize(base64_decode($cleanOutput));

                        if ($result !== false || $cleanOutput === base64_encode(serialize(false))) {
                            if (isset($result['error'])) {
                                $this->results[$taskId] = ['error' => $result['error']];
                            } else {
                                $this->results[$taskId] = $result['result'] ?? null;
                            }
                        } else {
                            $this->results[$taskId] = ['error' => 'Failed to parse worker output: ' . $output];
                        }
                    }

                    unset($this->running[$taskId]);
                }
            }

            usleep(10000); // 10ms
        }

        return $this->results;
    }
}
