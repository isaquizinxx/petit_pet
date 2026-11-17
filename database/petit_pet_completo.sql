-- Petit Pet - banco de dados completo
-- Projeto escolar de PHP + MySQL
--
-- Como executar no MySQL Workbench:
-- 1. Abra este arquivo.
-- 2. Conecte na instância local.
-- 3. Rode o script inteiro com Ctrl+Shift+Enter.
--
-- Pode executar mais de uma vez: as tabelas usam IF NOT EXISTS
-- e os dados de exemplo não são duplicados.

CREATE DATABASE IF NOT EXISTS petit_pet
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_0900_ai_ci;

USE petit_pet;

-- Tabela de contas (clientes e administração)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(30) DEFAULT NULL,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY email (email),
    KEY idx_usuarios_perfil (perfil)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabela de pets
CREATE TABLE IF NOT EXISTS pets (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario_id INT DEFAULT NULL,
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
    KEY idx_pets_especie (especie),
    KEY idx_pets_usuario (usuario_id),
    CONSTRAINT fk_pets_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Conta da administração: admin@petitpet.local / Admin@123
INSERT INTO usuarios (nome, email, telefone, senha, perfil)
VALUES (
    'Administração Petit Pet',
    'admin@petitpet.local',
    NULL,
    '$2y$12$PD/AFTysddHthIE/l6QVK.qRyruCeqp8rn3q04wPMxdNcj84O53oy',
    'admin'
)
ON DUPLICATE KEY UPDATE
    nome = VALUES(nome),
    telefone = VALUES(telefone),
    senha = VALUES(senha),
    perfil = VALUES(perfil);

-- Conta de cliente: cliente@petitpet.local / Cliente@123
INSERT INTO usuarios (nome, email, telefone, senha, perfil)
VALUES (
    'Ana Souza',
    'cliente@petitpet.local',
    '(41) 99999-1234',
    '$2y$12$Mk1Uqne7912tVuaqbJhsTO7UsOzRZ.efyAUbAAOpDmaNXYdhnhpZi',
    'cliente'
)
ON DUPLICATE KEY UPDATE
    nome = VALUES(nome),
    telefone = VALUES(telefone),
    senha = VALUES(senha),
    perfil = VALUES(perfil);

-- Pets da Ana
INSERT INTO pets (
    usuario_id, nome, especie, raca, sexo, data_nascimento, porte,
    tutor_nome, tutor_telefone, observacoes, status
)
SELECT u.id, 'Mel', 'Cachorro', 'Shih-tzu', 'femea', '2021-03-15', 'pequeno',
       'Ana Souza', '(41) 99999-1234',
       'Muito dócil, adora brincar e passear no fim da tarde.', 'disponivel'
FROM usuarios u
WHERE u.email = 'cliente@petitpet.local'
  AND NOT EXISTS (SELECT 1 FROM pets WHERE nome = 'Mel');

INSERT INTO pets (
    usuario_id, nome, especie, raca, sexo, data_nascimento, porte,
    tutor_nome, tutor_telefone, observacoes, status
)
SELECT u.id, 'Luna', 'Gato', 'Siamês', 'femea', '2022-08-02', 'pequeno',
       'Ana Souza', '(41) 99999-1234',
       'Tímida no começo, mas muito carinhosa.', 'em_atendimento'
FROM usuarios u
WHERE u.email = 'cliente@petitpet.local'
  AND NOT EXISTS (SELECT 1 FROM pets WHERE nome = 'Luna');

-- Pets cadastrados pela administração
INSERT INTO pets (
    nome, especie, raca, sexo, data_nascimento, porte,
    tutor_nome, tutor_telefone, observacoes, status
)
SELECT 'Thor', 'Cachorro', 'Golden Retriever', 'macho', '2020-05-10', 'grande',
       'Carlos Lima', '(41) 98888-4567',
       'Muito ativo, precisa de passeios longos.', 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM pets WHERE nome = 'Thor');

INSERT INTO pets (
    nome, especie, raca, sexo, data_nascimento, porte,
    tutor_nome, tutor_telefone, observacoes, status
)
SELECT 'Bento', 'Cachorro', 'Poodle', 'macho', '2023-01-20', 'pequeno',
       'Marina Alves', '(41) 97777-1234',
       'Alergia a alguns petiscos.', 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM pets WHERE nome = 'Bento');

INSERT INTO pets (
    nome, especie, raca, sexo, data_nascimento, porte,
    tutor_nome, tutor_telefone, observacoes, status
)
SELECT 'Nina', 'Gato', 'Sem raça definida', 'femea', '2019-11-05', 'medio',
       'Rafael Costa', '(41) 96666-7890',
       'Calma e independente.', 'em_atendimento'
WHERE NOT EXISTS (SELECT 1 FROM pets WHERE nome = 'Nina');

-- Conferencia
SELECT 'usuarios' AS tabela, COUNT(*) AS total FROM usuarios
UNION ALL
SELECT 'pets', COUNT(*) FROM pets;
