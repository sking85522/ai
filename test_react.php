<?php

require_once __DIR__ . '/online_db.php';
require_once __DIR__ . '/core/Bootstrap.php';

use Core\Engine\AgenticCore;
use Core\Engine\ReasoningEngine;

echo "=====================================\n";
echo "Testing ReAct Loop in ReasoningEngine\n";
echo "=====================================\n\n";

$agent = new AgenticCore();
$reasoningEngine = new ReasoningEngine($agent);

$goal = "Find the PHP version and tell me.";
echo "Goal: " . $goal . "\n\n";

$result = $reasoningEngine->executeComplexTask($goal);

echo $result . "\n";
