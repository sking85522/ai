<?php
return [
    'app_name' => 'PHP Text AI Engine',
    'app_version' => '0.4.1',
    'env' => 'development',
    'timezone' => 'Asia/Kolkata',
    'training_dir' => __DIR__ . '/../storage/training',
    'memory_file' => __DIR__ . '/../storage/training/memory_store.json',
    'web_knowledge_enabled' => true,
    'web_timeout_ms' => 5000,
];
