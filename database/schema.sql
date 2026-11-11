-- ============================================================================
-- Petit Pet — estrutura para uma instalação nova
-- Execute este arquivo apenas em um banco vazio (ou use migrate_pets.sql
-- se já existir a estrutura antiga com a tabela livros).
-- ============================================================================

CREATE DATABASE IF NOT EXISTS biblioteca
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_0900_ai_ci;

USE biblioteca;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS pets (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    especie VARCHAR(50) NOT NULL,
    raca VARCHAR(80) DEFAULT NULL,
    sexo ENUM('macho', 'femea', 'nao_informado') NOT NULL DEFAULT 'nao_informado',
    data_nascimento DATE DEFAULT NULL,
    porte ENUM('pequeno', 'medio', 'grande') DEFAULT NULL,
    tutor_nome VARCHAR(100) DEFAULT NULL,
    tutor_telefone VARCHAR(30) DEFAULT NULL,
    observacoes TEXT DEFAULT NULL,
    status ENUM('disponivel', 'em_atendimento') NOT NULL DEFAULT 'disponivel',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_pets_nome (nome),
    KEY idx_pets_status (status),
    KEY idx_pets_especie (especie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
