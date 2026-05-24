<?php
namespace Core\Commands;

use Core\Tools\Visualization\ProjectMapper;

class ShowMapCommand implements CommandInterface {
    private ProjectMapper $mapper;

    public function __construct() {
        $this->mapper = new ProjectMapper();
    }

    public function canProcess(string $task): bool {
        $task = strtolower($task);
        return str_contains($task, 'show map') || str_contains($task, 'show tree') || str_contains($task, 'project structure');
    }

    public function process(string $task): string {
        return $this->mapper->generateTree();
    }
}
