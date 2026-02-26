<?php
class JsonTrainingPackImporter
{
    public function import(string $packDir, string $source = 'json_pack_import'): array
    {
        if (!is_dir($packDir)) {
            return ['ok' => false, 'message' => 'Pack folder not found'];
        }
        $pdo = Db::pdo();
        if (!$pdo) {
            return ['ok' => false, 'message' => 'DB connection failed'];
        }

        $files = [
            'training_data' => $packDir . '/training_data.jsonl',
            'patterns' => $packDir . '/patterns.jsonl',
            'neural_weights' => $packDir . '/neural_weights.jsonl',
            'knowledge_entities' => $packDir . '/knowledge_entities.jsonl',
            'knowledge_relations' => $packDir . '/knowledge_relations.jsonl',
        ];

        $counts = [
            'training_data' => 0,
            'patterns' => 0,
            'neural_weights' => 0,
            'knowledge_entities' => 0,
            'knowledge_relations' => 0,
        ];

        $pdo->beginTransaction();
        try {
            if (is_file($files['training_data'])) {
                $stmt = $pdo->prepare(
                    'INSERT INTO training_data (input_text, expected_intent, language_tag, confidence_hint, source, created_at, updated_at)
                     VALUES (:i, :e, :l, :c, :s, NOW(), NOW())'
                );
                $this->consumeJsonl($files['training_data'], function (array $row) use ($stmt, $source, &$counts): void {
                    $stmt->execute([
                        ':i' => (string) ($row['input_text'] ?? ''),
                        ':e' => (string) ($row['expected_intent'] ?? 'general'),
                        ':l' => (string) ($row['language_tag'] ?? 'unknown'),
                        ':c' => (float) ($row['confidence_hint'] ?? 0.7),
                        ':s' => (string) ($row['source'] ?? $source),
                    ]);
                    $counts['training_data']++;
                });
            }

            if (is_file($files['patterns'])) {
                $stmt = $pdo->prepare(
                    'INSERT INTO patterns (intent, token, token_language, weight, hit_count, created_at, updated_at)
                     VALUES (:i, :t, :l, :w, 1, NOW(), NOW())
                     ON DUPLICATE KEY UPDATE
                        hit_count = hit_count + 1,
                        weight = LEAST(50, GREATEST(weight, VALUES(weight)) + 1),
                        updated_at = NOW()'
                );
                $this->consumeJsonl($files['patterns'], function (array $row) use ($stmt, &$counts): void {
                    $stmt->execute([
                        ':i' => (string) ($row['intent'] ?? 'general'),
                        ':t' => (string) ($row['token'] ?? ''),
                        ':l' => (string) ($row['token_language'] ?? 'unknown'),
                        ':w' => (int) ($row['weight'] ?? 1),
                    ]);
                    $counts['patterns']++;
                });
            }

            if (is_file($files['neural_weights'])) {
                $stmt = $pdo->prepare(
                    'INSERT INTO neural_weights (layer_name, from_node, to_node, weight_value, bias_value, activation, version_no, created_at, updated_at)
                     VALUES (:ln, :f, :t, :w, :b, :a, :v, NOW(), NOW())
                     ON DUPLICATE KEY UPDATE weight_value = VALUES(weight_value), bias_value = VALUES(bias_value), updated_at = NOW()'
                );
                $this->consumeJsonl($files['neural_weights'], function (array $row) use ($stmt, &$counts): void {
                    $stmt->execute([
                        ':ln' => (string) ($row['layer_name'] ?? 'input_hidden'),
                        ':f' => (int) ($row['from_node'] ?? 0),
                        ':t' => (int) ($row['to_node'] ?? 0),
                        ':w' => (float) ($row['weight_value'] ?? 0),
                        ':b' => (float) ($row['bias_value'] ?? 0),
                        ':a' => (string) ($row['activation'] ?? 'relu'),
                        ':v' => (int) ($row['version_no'] ?? 1),
                    ]);
                    $counts['neural_weights']++;
                });
            }

            if (is_file($files['knowledge_entities'])) {
                $stmt = $pdo->prepare(
                    'INSERT INTO knowledge_entities (name, type, language_tag, metadata, created_at, updated_at)
                     VALUES (:n, :t, :l, :m, NOW(), NOW())
                     ON DUPLICATE KEY UPDATE metadata = VALUES(metadata), language_tag = VALUES(language_tag), updated_at = NOW()'
                );
                $this->consumeJsonl($files['knowledge_entities'], function (array $row) use ($stmt, &$counts): void {
                    $meta = $row['metadata'] ?? null;
                    $metaJson = is_array($meta) ? json_encode($meta, JSON_UNESCAPED_UNICODE) : (is_string($meta) ? $meta : null);
                    $stmt->execute([
                        ':n' => (string) ($row['name'] ?? ''),
                        ':t' => (string) ($row['type'] ?? 'general'),
                        ':l' => (string) ($row['language_tag'] ?? 'unknown'),
                        ':m' => $metaJson,
                    ]);
                    $counts['knowledge_entities']++;
                });
            }

            if (is_file($files['knowledge_relations'])) {
                $stmt = $pdo->prepare(
                    'INSERT INTO knowledge_relations (source_entity_id, relation, target_entity_id, relation_weight, created_at, updated_at)
                     VALUES (:s, :r, :t, :w, NOW(), NOW())
                     ON DUPLICATE KEY UPDATE relation_weight = VALUES(relation_weight), updated_at = NOW()'
                );
                $this->consumeJsonl($files['knowledge_relations'], function (array $row) use ($pdo, $stmt, &$counts): void {
                    $src = (string) ($row['source_name'] ?? '');
                    $tgt = (string) ($row['target_name'] ?? '');
                    if ($src === '' || $tgt === '') {
                        return;
                    }
                    $srcId = (int) $pdo->query('SELECT id FROM knowledge_entities WHERE name = ' . $pdo->quote($src) . ' LIMIT 1')->fetchColumn();
                    $tgtId = (int) $pdo->query('SELECT id FROM knowledge_entities WHERE name = ' . $pdo->quote($tgt) . ' LIMIT 1')->fetchColumn();
                    if ($srcId <= 0 || $tgtId <= 0) {
                        return;
                    }
                    $stmt->execute([
                        ':s' => $srcId,
                        ':r' => (string) ($row['relation'] ?? 'related_to'),
                        ':t' => $tgtId,
                        ':w' => (float) ($row['relation_weight'] ?? 1.0),
                    ]);
                    $counts['knowledge_relations']++;
                });
            }

            $pdo->commit();
            return ['ok' => true, 'message' => 'JSON pack imported', 'counts' => $counts];
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => $e->getMessage(), 'counts' => $counts];
        }
    }

    private function consumeJsonl(string $file, callable $handler): void
    {
        $fh = fopen($file, 'rb');
        if (!$fh) {
            return;
        }
        while (($line = fgets($fh)) !== false) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $obj = json_decode($line, true);
            if (!is_array($obj)) {
                continue;
            }
            $handler($obj);
        }
        fclose($fh);
    }
}
