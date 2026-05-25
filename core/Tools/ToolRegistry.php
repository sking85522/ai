<?php
namespace Core\Tools;

class ToolRegistry {
    /** @var array<string, ToolInterface> */
    private array $tools = [];

    public function __construct() {
        $this->register('calculator', new CalculatorTool());
        $this->register('read_file', new ReadFileTool());
        $this->register('write_file', new WriteFileTool());
        $this->register('execute_command', new ExecuteCommandTool());
        $this->register('learn_fact', new LearnFactTool());
        $this->register('analyze_topology', new AnalyzeTopologyTool());
        $this->register('debug', new DebuggerTool());
        $this->register('data_analyze', new DataAnalyzerTool());
    }

    public function register(string $name, ToolInterface $tool): void {
        $this->tools[strtolower($name)] = $tool;
    }

    public function getTool(string $name): ?ToolInterface {
        return $this->tools[strtolower($name)] ?? null;
    }

    public function names(): array {
        return array_keys($this->tools);
    }
}
