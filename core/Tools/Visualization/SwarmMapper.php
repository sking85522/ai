<?php
namespace Core\Tools\Visualization;

class SwarmMapper {
    public function generateMap(): string {
        $agentsDir = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'agents';
        if (!is_dir($agentsDir)) {
            return "[SWARM]\nNo spawned agents found.";
        }

        $agents = array_values(array_diff(scandir($agentsDir) ?: [], ['.', '..']));
        return "[SWARM]\n" . (empty($agents) ? 'No spawned agents found.' : implode("\n", array_map(fn($a) => '- ' . $a, $agents)));
    }
}
