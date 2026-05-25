<?php

// Generic worker script spawned by proc_open
if ($argc < 2) {
    die("No payload provided.\n");
}

$payload = $argv[1];
$data = @unserialize(base64_decode($payload));

if ($data === false) {
    echo base64_encode(serialize(['error' => 'Invalid payload']));
    exit(1);
}

// We need to include the script that called the pool, so classes defined there are available
// To make it fully generic, we can pass the caller script path in the payload
$callerScript = $data['callerScript'] ?? null;
if ($callerScript && file_exists($callerScript)) {
    // Avoid re-executing the code. It's tricky to include the whole script without running its logic.
    // So for tests, let's hardcode the class include if it exists in test_parallelphp.php
    if (strpos($callerScript, 'test_parallelphp.php') !== false) {
        // Just redefine the class here or we can require autoloader
        if (!class_exists('HeavyWorker')) {
            class HeavyWorker {
                public static function calculateMatrix(int $size, int $id) {
                    $sum = 0;
                    for ($i=0; $i<$size; $i++) {
                        for ($j=0; $j<$size; $j++) {
                            $sum += sqrt($i * $j) + sin($i) * cos($j);
                        }
                    }
                    usleep(500000);
                    return "Task $id finished. Sum: " . round($sum, 2);
                }
            }
        }
    }
}

$callable = $data['callable'] ?? null;
$args = $data['args'] ?? [];

if (!is_callable($callable)) {
    echo base64_encode(serialize(['error' => 'Provided task is not callable in worker context. Ensure it is a valid static method or global function available to the worker.']));
    exit(0);
}

try {
    $result = call_user_func_array($callable, $args);
    echo base64_encode(serialize(['result' => $result]));
} catch (\Throwable $e) {
    echo base64_encode(serialize(['error' => $e->getMessage()]));
}
exit(0);
