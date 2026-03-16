-- PHP Text AI Engine - MySQL Schema
-- Date: 2026-02-25
-- Engine: InnoDB
-- Charset/Collation for bilingual content: utf8mb4 / utf8mb4_unicode_ci

SET NAMES utf8mb4;
SET time_zone = '+00:00';
USE ai_db;

CREATE TABLE IF NOT EXISTS training_data (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    input_text LONGTEXT NOT NULL,
    expected_intent VARCHAR(120) NOT NULL,
    language_tag ENUM('en','hi','bilingual','unknown') NOT NULL DEFAULT 'unknown',
    confidence_hint DECIMAL(6,5) NULL,
    source VARCHAR(80) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_training_intent (expected_intent),
    INDEX idx_training_language (language_tag),
    INDEX idx_training_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS patterns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    intent VARCHAR(120) NOT NULL,
    token VARCHAR(255) NOT NULL,
    token_language ENUM('en','hi','bilingual','unknown') NOT NULL DEFAULT 'unknown',
    weight INT NOT NULL DEFAULT 1,
    hit_count BIGINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_patterns_intent_token (intent, token),
    INDEX idx_patterns_intent_weight (intent, weight),
    INDEX idx_patterns_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS neural_weights (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    layer_name VARCHAR(100) NOT NULL,
    from_node INT NOT NULL,
    to_node INT NOT NULL,
    weight_value DECIMAL(16,10) NOT NULL DEFAULT 0.0000000000,
    bias_value DECIMAL(16,10) NOT NULL DEFAULT 0.0000000000,
    activation VARCHAR(32) NOT NULL DEFAULT 'relu',
    version_no INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_neural_edge_version (layer_name, from_node, to_node, version_no),
    INDEX idx_neural_layer (layer_name),
    INDEX idx_neural_nodes (from_node, to_node)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS knowledge_entities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL DEFAULT 'general',
    language_tag ENUM('en','hi','bilingual','unknown') NOT NULL DEFAULT 'unknown',
    metadata JSON NULL,
    confidence_score DECIMAL(6,5) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_knowledge_entity (name, type),
    INDEX idx_entities_type (type),
    INDEX idx_entities_language (language_tag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS knowledge_relations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    source_entity_id BIGINT UNSIGNED NOT NULL,
    relation VARCHAR(100) NOT NULL,
    target_entity_id BIGINT UNSIGNED NOT NULL,
    relation_weight DECIMAL(8,5) NOT NULL DEFAULT 1.00000,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_relation_triplet (source_entity_id, relation, target_entity_id),
    INDEX idx_rel_source (source_entity_id),
    INDEX idx_rel_target (target_entity_id),
    INDEX idx_rel_name (relation),
    CONSTRAINT fk_rel_source_entity
        FOREIGN KEY (source_entity_id) REFERENCES knowledge_entities(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_rel_target_entity
        FOREIGN KEY (target_entity_id) REFERENCES knowledge_entities(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conversations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(128) NULL,
    user_input LONGTEXT NOT NULL,
    ai_response LONGTEXT NOT NULL,
    intent VARCHAR(120) NOT NULL DEFAULT 'general',
    confidence DECIMAL(6,5) NOT NULL DEFAULT 0.00000,
    input_language ENUM('en','hi','bilingual','unknown') NOT NULL DEFAULT 'unknown',
    response_language ENUM('en','hi','bilingual','unknown') NOT NULL DEFAULT 'unknown',
    context_snapshot JSON NULL,
    tokens JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_conv_session (session_id),
    INDEX idx_conv_intent (intent),
    INDEX idx_conv_created (created_at),
    INDEX idx_conv_lang (input_language, response_language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    level ENUM('debug','info','warning','error','critical') NOT NULL DEFAULT 'info',
    channel VARCHAR(64) NOT NULL DEFAULT 'system',
    message TEXT NOT NULL,
    context JSON NULL,
    trace_id VARCHAR(128) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_logs_level (level),
    INDEX idx_logs_channel (channel),
    INDEX idx_logs_created (created_at),
    INDEX idx_logs_trace (trace_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

