<?php
namespace Core\Tools;

class DataAnalyzerTool implements ToolInterface {
    public function execute(array $input = []): array {
        $task = $input['task'] ?? '';

        // Extract filename from task (e.g., "analyze data.csv")
        if (preg_match('/([\w\.\/]+\.csv)/i', $task, $m)) {
            $path = $m[1];
            if (!file_exists($path)) {
                return ['status' => 'error', 'response' => "[DATA_ANALYZER] File not found: $path"];
            }

            try {
                $df = \PandaPHP\PandaPHP::read_csv($path);
                $stats = \PandaPHP\Operations\DataFrameOps::describe($df);
                $response = "[DATA_ANALYZER] Analysis for $path:\n" . print_r($stats, true);
                return ['status' => 'success', 'response' => $response];
            } catch (\Exception $e) {
                return ['status' => 'error', 'response' => "[DATA_ANALYZER] Error analyzing $path: " . $e->getMessage()];
            }
        }

        return ['status' => 'error', 'response' => "[DATA_ANALYZER] Please provide a valid CSV file path to analyze."];
    }
}
