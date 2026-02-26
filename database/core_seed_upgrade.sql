SET NAMES utf8mb4;

INSERT INTO training_data (input_text, expected_intent, language_tag, confidence_hint, source, created_at, updated_at) VALUES
('kya haal h', 'normal_chat', 'hi', 0.90, 'core_seed_upgrade', NOW(), NOW()),
('whats your name', 'normal_chat', 'en', 0.90, 'core_seed_upgrade', NOW(), NOW()),
('index.html code write', 'code_generate', 'en', 0.92, 'core_seed_upgrade', NOW(), NOW()),
('write php code', 'code_generate', 'en', 0.92, 'core_seed_upgrade', NOW(), NOW()),
('5 ka square kya h', 'math_query', 'bilingual', 0.96, 'core_seed_upgrade', NOW(), NOW()),
('what is 5 square', 'math_query', 'en', 0.96, 'core_seed_upgrade', NOW(), NOW()),
('my friend name is hritik', 'memory_store', 'bilingual', 0.95, 'core_seed_upgrade', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

INSERT INTO patterns (intent, token, token_language, weight, hit_count, created_at, updated_at) VALUES
('normal_chat', 'kya', 'hi', 7, 20, NOW(), NOW()),
('normal_chat', 'haal', 'hi', 7, 20, NOW(), NOW()),
('code_generate', 'index.html', 'en', 9, 30, NOW(), NOW()),
('code_generate', 'php', 'en', 9, 40, NOW(), NOW()),
('math_query', 'square', 'en', 10, 50, NOW(), NOW()),
('math_query', 'squre', 'en', 8, 20, NOW(), NOW())
ON DUPLICATE KEY UPDATE weight = VALUES(weight), hit_count = hit_count + VALUES(hit_count), updated_at = NOW();

INSERT INTO neural_weights (layer_name, from_node, to_node, weight_value, bias_value, activation, version_no, created_at, updated_at) VALUES
('input_hidden', 0, 0, 0.4500000000, 0.1000000000, 'relu', 1, NOW(), NOW()),
('input_hidden', 1, 0, 0.5200000000, 0.1000000000, 'relu', 1, NOW(), NOW()),
('input_hidden', 2, 1, 0.4800000000, 0.1000000000, 'relu', 1, NOW(), NOW()),
('input_hidden', 3, 1, 0.5500000000, 0.1000000000, 'relu', 1, NOW(), NOW()),
('hidden_output', 0, 0, 0.6200000000, 0.0500000000, 'sigmoid', 1, NOW(), NOW()),
('hidden_output', 1, 0, 0.5800000000, 0.0500000000, 'sigmoid', 1, NOW(), NOW()),
('hidden_output', 1, 1, 0.5700000000, 0.0500000000, 'sigmoid', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE weight_value = VALUES(weight_value), bias_value = VALUES(bias_value), updated_at = NOW();

INSERT INTO knowledge_entities (name, type, language_tag, metadata, created_at, updated_at) VALUES
('entity::php', 'general', 'en', JSON_OBJECT('label','PHP','domain','programming'), NOW(), NOW()),
('entity::html', 'general', 'en', JSON_OBJECT('label','HTML','domain','web'), NOW(), NOW()),
('entity::css', 'general', 'en', JSON_OBJECT('label','CSS','domain','web'), NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

INSERT INTO knowledge_entities (name, type, language_tag, metadata, created_at, updated_at) VALUES ('burj khalifa', 'structure', 'en', JSON_OBJECT('label','Burj Khalifa','location','Dubai', 'height_m', 829.8), NOW(), NOW()), ('eiffel tower', 'structure', 'en', JSON_OBJECT('label','Eiffel Tower','location','Paris', 'height_m', 330), NOW(), NOW()) ON DUPLICATE KEY UPDATE updated_at = NOW();

INSERT INTO knowledge_relations (source_entity_id, relation, target_entity_id, relation_weight, created_at, updated_at)
SELECT e1.id, 'works_with', e2.id, 1.00000, NOW(), NOW()
FROM knowledge_entities e1
JOIN knowledge_entities e2 ON e1.name = 'entity::php' AND e2.name = 'entity::html'
ON DUPLICATE KEY UPDATE updated_at = NOW();

INSERT INTO knowledge_relations (source_entity_id, relation, target_entity_id, relation_weight, created_at, updated_at)
SELECT e1.id, 'styled_by', e2.id, 1.00000, NOW(), NOW()
FROM knowledge_entities e1
JOIN knowledge_entities e2 ON e1.name = 'entity::html' AND e2.name = 'entity::css'
ON DUPLICATE KEY UPDATE updated_at = NOW();

INSERT INTO knowledge_relations (source_entity_id, relation, target_entity_id, relation_weight, created_at, updated_at)
SELECT e1.id, 'taller_than', e2.id, 1.00000, NOW(), NOW()
FROM knowledge_entities e1 JOIN knowledge_entities e2 ON e1.name = 'burj khalifa' AND e2.name = 'eiffel tower'
ON DUPLICATE KEY UPDATE updated_at = NOW();
