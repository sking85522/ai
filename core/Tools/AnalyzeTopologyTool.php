<?php
namespace Core\Tools;

use Core\Tools\Visualization\ProjectMapper;

class AnalyzeTopologyTool implements ToolInterface {
    private ProjectMapper $mapper;

    public function __construct() {
        $this->mapper = new ProjectMapper();
    }

    public function execute(array $input = []): array {
        $depth = max(1, (int)($input['depth'] ?? 2));
        return [
            'status' => 'success',
            'payload' => $this->mapper->generateTree($depth)
        ];
    }
}
