<?php
namespace Core\Evolution;

/**
 * HRITIK AI - CROSS-AGENT COLLABORATION
 * Enables communication and task delegation between different sub-agents.
 */
class CrossAgentCollab {
    
    private array $activeAgents = [];

    public function __construct() {
        $agentsDir = dirname(__DIR__, 2) . '/agents';
        if (is_dir($agentsDir)) {
            $this->activeAgents = array_diff(scandir($agentsDir), ['.', '..']);
        }
    }

    /**
     * Delegates a task through a sequence of specialized agents.
     */
    public function executeCollaborativeTask(string $task, array $agentSequence): string {
        $result = $task;
        $log = "[COLLABORATION] Starting task: '$task'\n";

        foreach ($agentSequence as $agentName) {
            $agentName = strtolower($agentName);
            if (in_array($agentName, $this->activeAgents)) {
                $log .= " - Passing to Agent: " . ucfirst($agentName) . "...\n";
                
                // Simulating agent processing
                $result = "[RESULT from " . ucfirst($agentName) . "] " . $result;
            }
        }

        return $log . "[COLLABORATION] Task completed by swarm. Final Result: " . $result;
    }
}
