SET NAMES utf8mb4;

INSERT INTO training_data (input_text, expected_intent, language_tag, confidence_hint, source, created_at, updated_at) VALUES
('hello', 'greeting', 'en', 0.90, 'sql_seed', NOW(), NOW()),
('namaste', 'greeting', 'hi', 0.90, 'sql_seed', NOW(), NOW()),
('5+5', 'math_query', 'bilingual', 0.95, 'sql_seed', NOW(), NOW()),
('what is ai', 'general', 'en', 0.85, 'sql_seed', NOW(), NOW()),
('ai kya hai', 'general', 'hi', 0.85, 'sql_seed', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

INSERT INTO patterns (intent, token, token_language, weight, hit_count, created_at, updated_at) VALUES
('greeting', 'hello', 'en', 6, 12, NOW(), NOW()),
('greeting', 'namaste', 'hi', 6, 11, NOW(), NOW()),
('math_query', 'plus', 'en', 4, 8, NOW(), NOW()),
('help', 'madad', 'hi', 4, 7, NOW(), NOW())
ON DUPLICATE KEY UPDATE weight = VALUES(weight), hit_count = VALUES(hit_count), updated_at = NOW();

INSERT INTO knowledge_entities (name, type, language_tag, metadata, created_at, updated_at) VALUES
('what is ai', 'qa', 'en', JSON_OBJECT('question','what is ai','answer','AI stands for Artificial Intelligence.','language','en'), NOW(), NOW()),
('ai kya hai', 'qa', 'hi', JSON_OBJECT('question','ai kya hai','answer','AI ka matlab Artificial Intelligence hai.','language','hi'), NOW(), NOW()),
('can you speak hindi and english', 'qa', 'bilingual', JSON_OBJECT('question','can you speak hindi and english','answer','Yes, I can chat in Hindi and English.','language','bilingual'), NOW(), NOW()),
('user_fact::name', 'user_fact', 'bilingual', JSON_OBJECT('key','name','value','Sachin','language','bilingual'), NOW(), NOW())
ON DUPLICATE KEY UPDATE metadata = VALUES(metadata), updated_at = NOW();
