-- ============================================================================
-- Petit Pet — migração da estrutura antiga (tabela livros) para pets
--
-- Use este arquivo se o banco já existe com as tabelas usuarios e livros.
-- A tabela livros NÃO é apagada: ela permanece como backup/legado.
-- O script pode ser executado mais de uma vez sem duplicar registros.
-- ============================================================================

USE biblioteca;

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

-- Copia os registros antigos (titulo -> nome, autor -> especie).
-- "Emprestado" era o código legado para "Em atendimento".
INSERT INTO pets (nome, especie, status)
SELECT
    COALESCE(NULLIF(TRIM(l.titulo), ''), 'Sem nome'),
    COALESCE(NULLIF(TRIM(l.autor), ''), 'Não informado'),
    CASE WHEN l.status = 'Emprestado' THEN 'em_atendimento' ELSE 'disponivel' END
FROM livros AS l
WHERE NOT EXISTS (
    SELECT 1
    FROM pets AS p
    WHERE p.nome = COALESCE(NULLIF(TRIM(l.titulo), ''), 'Sem nome')
      AND p.especie = COALESCE(NULLIF(TRIM(l.autor), ''), 'Não informado')
);

-- Confira o resultado:
-- SELECT id, nome, especie, status FROM pets ORDER BY id;
