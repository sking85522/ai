<?php
class SystemSeedImporter
{
    public function importBundle(string $jsonFile, string $source = 'system_bundle'): array
    {
        if (!is_file($jsonFile)) {
            return ['ok' => false, 'message' => 'Bundle file not found'];
        }

        $pdo = Db::pdo();
        if (!$pdo) {
            return ['ok' => false, 'message' => 'DB connection failed'];
        }

        $data = json_decode((string) file_get_contents($jsonFile), true);
        if (!is_array($data)) {
            return ['ok' => false, 'message' => 'Invalid JSON bundle'];
        }

        $counts = [
            'training_data' => 0,
            'patterns' => 0,
            'neural_weights' => 0,
            'knowledge_entities' => 0,
            'knowledge_relations' => 0,
        ];

        $pdo->beginTransaction();
        try {
            foreach (($data['training_data'] ?? []) as $row) {
                $stmt = $pdo->prepare(
                    'INSERT INTO training_data (input_text, expected_intent, language_tag, confidence_hint, source, created_at, updated_at)
                     VALUES (:i, :e, :l, :c, :s, NOW(), NOW())'
                );
                $stmt->execute([
                    ':i' => (string) ($row['input_text'] ?? ''),
                    ':e' => (string) ($row['expected_intent'] ?? 'general'),
                    ':l' => (string) ($row['language_tag'] ?? 'unknown'),
                    ':c' => (float) ($row['confidence_hint'] ?? 0.7),
                    ':s' => $source,
                ]);
                $counts['training_data']++;
            }

            foreach (($data['patterns'] ?? []) as $row) {
                $stmt = $pdo->prepare(
                    'INSERT INTO patterns (intent, token, token_language, weight, hit_count, created_at, updated_at)
                     VALUES (:i, :t, :l, :w, 1, NOW(), NOW())
                     ON DUPLICATE KEY UPDATE weight = VALUES(weight), updated_at = NOW()'
                );
                $stmt->execute([
                    ':i' => (string) ($row['intent'] ?? 'general'),
                    ':t' => (string) ($row['token'] ?? ''),
                    ':l' => (string) ($row['token_language'] ?? 'unknown'),
                    ':w' => (int) ($row['weight'] ?? 1),
                ]);
                $counts['patterns']++;
            }

            foreach (($data['neural_weights'] ?? []) as $row) {
                $stmt = $pdo->prepare(
                    'INSERT INTO neural_weights (layer_name, from_node, to_node, weight_value, bias_value, activation, version_no, created_at, updated_at)
                     VALUES (:ln, :f, :t, :w, :b, :a, :v, NOW(), NOW())
                     ON DUPLICATE KEY UPDATE weight_value = VALUES(weight_value), bias_value = VALUES(bias_value), updated_at = NOW()'
                );
                $stmt->execute([
                    ':ln' => (string) ($row['layer_name'] ?? 'input_hidden'),
                    ':f' => (int) ($row['from_node'] ?? 0),
                    ':t' => (int) ($row['to_node'] ?? 0),
                    ':w' => (float) ($row['weight_value'] ?? 0.0),
                    ':b' => (float) ($row['bias_value'] ?? 0.0),
                    ':a' => (string) ($row['activation'] ?? 'relu'),
                    ':v' => (int) ($row['version_no'] ?? 1),
                ]);
                $counts['neural_weights']++;
            }

            foreach (($data['knowledge_entities'] ?? []) as $row) {
                $meta = isset($row['metadata']) && is_array($row['metadata']) ? json_encode($row['metadata'], JSON_UNESCAPED_UNICODE) : null;
                $stmt = $pdo->prepare(
                    'INSERT INTO knowledge_entities (name, type, language_tag, metadata, created_at, updated_at)
                     VALUES (:n, :t, :l, :m, NOW(), NOW())
                     ON DUPLICATE KEY UPDATE language_tag = VALUES(language_tag), metadata = VALUES(metadata), updated_at = NOW()'
                );
                $stmt->execute([
                    ':n' => (string) ($row['name'] ?? ''),
                    ':t' => (string) ($row['type'] ?? 'general'),
                    ':l' => (string) ($row['language_tag'] ?? 'unknown'),
                    ':m' => $meta,
                ]);
                $counts['knowledge_entities']++;
            }

            foreach (($data['knowledge_relations'] ?? []) as $row) {
                $srcName = (string) ($row['source_name'] ?? '');
                $tgtName = (string) ($row['target_name'] ?? '');
                if ($srcName === '' || $tgtName === '') {
                    continue;
                }
                $srcId = (int) $pdo->query("SELECT id FROM knowledge_entities WHERE name = " . $pdo->quote($srcName) . " LIMIT 1")->fetchColumn();
                $tgtId = (int) $pdo->query("SELECT id FROM knowledge_entities WHERE name = " . $pdo->quote($tgtName) . " LIMIT 1")->fetchColumn();
                if ($srcId <= 0 || $tgtId <= 0) {
                    continue;
                }
                $stmt = $pdo->prepare(
                    'INSERT INTO knowledge_relations (source_entity_id, relation, target_entity_id, relation_weight, created_at, updated_at)
                     VALUES (:s, :r, :t, :w, NOW(), NOW())
                     ON DUPLICATE KEY UPDATE relation_weight = VALUES(relation_weight), updated_at = NOW()'
                );
                $stmt->execute([
                    ':s' => $srcId,
                    ':r' => (string) ($row['relation'] ?? 'related_to'),
                    ':t' => $tgtId,
                    ':w' => (float) ($row['relation_weight'] ?? 1.0),
                ]);
                $counts['knowledge_relations']++;
            }

            $pdo->commit();
            return ['ok' => true, 'message' => 'Bundle imported', 'counts' => $counts];
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => $e->getMessage(), 'counts' => $counts];
        }
    }
}
