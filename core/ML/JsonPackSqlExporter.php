<?php
class JsonPackSqlExporter
{
    private const TABLE_CONFIG = [
        'training_data' => [
            'inputs' => ['training_data.jsonl', 'training_data.json'],
            'output' => 'training_data.sql',
            'columns' => ['input_text', 'expected_intent', 'language_tag', 'confidence_hint', 'source'],
        ],
        'patterns' => [
            'inputs' => ['patterns.jsonl', 'patterns.json'],
            'output' => 'patterns.sql',
            'columns' => ['intent', 'token', 'token_language', 'weight', 'hit_count'],
        ],
        'neural_weights' => [
            'inputs' => ['neural_weights.jsonl', 'neural_weights.json'],
            'output' => 'neural_weights.sql',
            'columns' => ['layer_name', 'from_node', 'to_node', 'weight_value', 'bias_value', 'activation', 'version_no'],
        ],
        'knowledge_entities' => [
            'inputs' => ['knowledge_entities.jsonl', 'knowledge_entities.json'],
            'output' => 'knowledge_entities.sql',
            'columns' => ['name', 'type', 'language_tag', 'metadata'],
        ],
        'knowledge_relations' => [
            'inputs' => ['knowledge_relations.jsonl', 'knowledge_relations.json'],
            'output' => 'knowledge_relations.sql',
            'columns' => ['source_name', 'relation', 'target_name', 'relation_weight'],
        ],
    ];

    public function export(string $packDir, int $batchSize = 250): array
    {
        if (!is_dir($packDir)) {
            return ['ok' => false, 'message' => 'json_pack directory not found', 'files' => []];
        }

        $stats = [];
        foreach (self::TABLE_CONFIG as $table => $config) {
            $inputFile = $this->resolveInputFile($packDir, $config['inputs']);
            $outputPath = rtrim($packDir, '/\\') . DIRECTORY_SEPARATOR . $config['output'];
            $stats[$table] = $this->exportTable($table, $config['columns'], $inputFile, $outputPath, $batchSize);
        }

        $masterSql = rtrim($packDir, '/\\') . DIRECTORY_SEPARATOR . 'json_pack_all.sql';
        $this->writeMasterFile($masterSql, $packDir, $stats);

        return ['ok' => true, 'message' => 'SQL files generated', 'files' => $stats, 'master' => basename($masterSql)];
    }

    private function exportTable(string $table, array $columns, ?string $inputFile, string $outputPath, int $batchSize): array
    {
        $meta = [
            'input' => $inputFile ? basename($inputFile) : null,
            'output' => basename($outputPath),
            'rows' => 0,
            'skipped' => 0,
            'written' => false,
            'reason' => null,
        ];

        $fh = fopen($outputPath, 'wb');
        if (!$fh) {
            $meta['reason'] = 'Cannot open output file';
            return $meta;
        }

        fwrite($fh, "-- Auto-generated SQL for {$table}\n");
        fwrite($fh, '-- Generated at: ' . date('c') . "\n");
        fwrite($fh, "SET NAMES utf8mb4;\n");
        fwrite($fh, "START TRANSACTION;\n\n");

        if (!$inputFile || !is_file($inputFile) || filesize($inputFile) === 0) {
            fwrite($fh, "-- Source file missing or empty, nothing to insert.\n");
            fwrite($fh, "COMMIT;\n");
            fclose($fh);
            $meta['reason'] = 'Input file missing or empty';
            $meta['written'] = true;
            return $meta;
        }

        $rows = [];
        $flush = function () use (&$rows, $table, $columns, $fh, &$meta): void {
            if (!$rows) {
                return;
            }
            $sql = $this->buildInsertSql($table, $columns, $rows);
            if ($sql !== null) {
                fwrite($fh, $sql . "\n\n");
                $meta['rows'] += count($rows);
            } else {
                $meta['skipped'] += count($rows);
            }
            $rows = [];
        };

        $iterator = new SplFileObject($inputFile, 'rb');
        while (!$iterator->eof()) {
            $line = trim((string) $iterator->fgets());
            if ($line === '') {
                continue;
            }

            $json = json_decode($line, true);
            if (!is_array($json)) {
                $meta['skipped']++;
                continue;
            }

            $rows[] = $json;
            if (count($rows) >= $batchSize) {
                $flush();
            }
        }
        $flush();

        fwrite($fh, "COMMIT;\n");
        fclose($fh);

        $meta['written'] = true;
        if ($meta['rows'] === 0 && $meta['skipped'] > 0) {
            $meta['reason'] = 'No valid JSON rows found';
        }
        return $meta;
    }

    private function buildInsertSql(string $table, array $columns, array $rows): ?string
    {
        if ($table === 'knowledge_relations') {
            return $this->buildRelationsInsertSql($rows);
        }

        $values = [];
        foreach ($rows as $row) {
            $prepared = $this->prepareRow($table, $row);
            if ($prepared === null) {
                continue;
            }
            $cells = [];
            foreach ($columns as $col) {
                $cells[] = $this->sqlValue($prepared[$col] ?? null);
            }
            $values[] = '(' . implode(', ', $cells) . ')';
        }

        if (!$values) {
            return null;
        }

        $base = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ', created_at, updated_at) VALUES' . "\n";
        $timestamped = [];
        foreach ($values as $rowValues) {
            $timestamped[] = substr($rowValues, 0, -1) . ', NOW(), NOW())';
        }

        $sql = $base . implode(",\n", $timestamped);
        return $sql . $this->onDuplicateClause($table);
    }

    private function prepareRow(string $table, array $row): ?array
    {
        return match ($table) {
            'training_data' => [
                'input_text' => (string) ($row['input_text'] ?? ''),
                'expected_intent' => (string) ($row['expected_intent'] ?? 'general'),
                'language_tag' => $this->normalizeLanguage((string) ($row['language_tag'] ?? 'unknown')),
                'confidence_hint' => is_numeric($row['confidence_hint'] ?? null) ? (float) $row['confidence_hint'] : 0.70000,
                'source' => (string) ($row['source'] ?? 'json_pack_sql'),
            ],
            'patterns' => [
                'intent' => (string) ($row['intent'] ?? 'general'),
                'token' => (string) ($row['token'] ?? ''),
                'token_language' => $this->normalizeLanguage((string) ($row['token_language'] ?? 'unknown')),
                'weight' => max(1, (int) ($row['weight'] ?? 1)),
                'hit_count' => max(1, (int) ($row['hit_count'] ?? 1)),
            ],
            'neural_weights' => [
                'layer_name' => (string) ($row['layer_name'] ?? 'input_hidden'),
                'from_node' => (int) ($row['from_node'] ?? 0),
                'to_node' => (int) ($row['to_node'] ?? 0),
                'weight_value' => (float) ($row['weight_value'] ?? 0.0),
                'bias_value' => (float) ($row['bias_value'] ?? 0.0),
                'activation' => (string) ($row['activation'] ?? 'relu'),
                'version_no' => max(1, (int) ($row['version_no'] ?? 1)),
            ],
            'knowledge_entities' => [
                'name' => (string) ($row['name'] ?? ''),
                'type' => (string) ($row['type'] ?? 'general'),
                'language_tag' => $this->normalizeLanguage((string) ($row['language_tag'] ?? 'unknown')),
                'metadata' => $this->encodeMetadata($row['metadata'] ?? null),
            ],
            'knowledge_relations' => [
                'source_name' => (string) ($row['source_name'] ?? ''),
                'relation' => (string) ($row['relation'] ?? 'related_to'),
                'target_name' => (string) ($row['target_name'] ?? ''),
                'relation_weight' => is_numeric($row['relation_weight'] ?? null) ? (float) $row['relation_weight'] : 1.0,
            ],
            default => null,
        };
    }

    private function onDuplicateClause(string $table): string
    {
        return match ($table) {
            'training_data' => '',
            'patterns' => "\nON DUPLICATE KEY UPDATE weight = GREATEST(weight, VALUES(weight)), hit_count = hit_count + VALUES(hit_count), updated_at = NOW();",
            'neural_weights' => "\nON DUPLICATE KEY UPDATE weight_value = VALUES(weight_value), bias_value = VALUES(bias_value), activation = VALUES(activation), updated_at = NOW();",
            'knowledge_entities' => "\nON DUPLICATE KEY UPDATE language_tag = VALUES(language_tag), metadata = VALUES(metadata), updated_at = NOW();",
            'knowledge_relations' => '',
            default => ';',
        };
    }

    private function buildRelationsInsertSql(array $rows): ?string
    {
        $statements = [];
        foreach ($rows as $row) {
            $prepared = $this->prepareRow('knowledge_relations', $row);
            if (!$prepared) {
                continue;
            }
            $sourceName = trim((string) ($prepared['source_name'] ?? ''));
            $targetName = trim((string) ($prepared['target_name'] ?? ''));
            if ($sourceName === '' || $targetName === '') {
                continue;
            }

            $relation = (string) ($prepared['relation'] ?? 'related_to');
            $weight = (float) ($prepared['relation_weight'] ?? 1.0);

            $statements[] =
                'INSERT INTO knowledge_relations (source_entity_id, relation, target_entity_id, relation_weight, created_at, updated_at)' . "\n" .
                'SELECT e1.id, ' . $this->sqlValue($relation) . ', e2.id, ' . $this->sqlValue($weight) . ', NOW(), NOW()' . "\n" .
                'FROM knowledge_entities e1' . "\n" .
                'JOIN knowledge_entities e2 ON e1.name = ' . $this->sqlValue($sourceName) . ' AND e2.name = ' . $this->sqlValue($targetName) . "\n" .
                'ON DUPLICATE KEY UPDATE relation_weight = VALUES(relation_weight), updated_at = NOW();';
        }

        if (!$statements) {
            return null;
        }
        return implode("\n\n", $statements);
    }

    private function sqlValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        $value = str_replace('\\', '\\\\', (string) $value);
        $value = str_replace("'", "\\'", $value);
        return "'" . $value . "'";
    }

    private function encodeMetadata(mixed $metadata): ?string
    {
        if ($metadata === null) {
            return null;
        }
        if (is_string($metadata)) {
            return $metadata;
        }
        if (is_array($metadata)) {
            return json_encode($metadata, JSON_UNESCAPED_UNICODE);
        }
        return null;
    }

    private function normalizeLanguage(string $language): string
    {
        $language = strtolower(trim($language));
        return in_array($language, ['en', 'hi', 'bilingual', 'unknown'], true) ? $language : 'unknown';
    }

    private function resolveInputFile(string $packDir, array $candidates): ?string
    {
        foreach ($candidates as $name) {
            $path = rtrim($packDir, '/\\') . DIRECTORY_SEPARATOR . $name;
            if (is_file($path) && filesize($path) > 0) {
                return $path;
            }
        }
        return null;
    }

    private function writeMasterFile(string $masterSql, string $packDir, array $stats): void
    {
        $lines = [];
        $lines[] = '-- Master SQL for json_pack';
        $lines[] = '-- Generated at: ' . date('c');
        $lines[] = 'SET NAMES utf8mb4;';
        $lines[] = '';

        foreach (self::TABLE_CONFIG as $table => $config) {
            $summary = $stats[$table] ?? null;
            if (!$summary || !$summary['written']) {
                continue;
            }
            $rows = (int) ($summary['rows'] ?? 0);
            $skipped = (int) ($summary['skipped'] ?? 0);
            $lines[] = '-- ' . $table . ': rows=' . $rows . ', skipped=' . $skipped;
            $filePath = str_replace('\\', '/', rtrim($packDir, '/\\') . '/' . $config['output']);
            $lines[] = 'SOURCE ' . $filePath . ';';
            $lines[] = '';
        }

        file_put_contents($masterSql, implode("\n", $lines) . "\n");
    }
}
