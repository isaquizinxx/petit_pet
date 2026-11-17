-- Petit Pet - migração para a versão com cliente e administração
--
-- Use uma unica vez em um banco que ja tenha as tabelas usuarios e pets:
--   mysql -u root -p petit_pet -e "source database/migrate_areas.sql"
--
-- O MySQL 8 nao aceita ADD COLUMN IF NOT EXISTS, entao nao repita a execução.

USE petit_pet;

ALTER TABLE usuarios
    ADD COLUMN telefone VARCHAR(30) DEFAULT NULL AFTER email,
    ADD COLUMN perfil ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente' AFTER senha,
    ADD COLUMN criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER perfil;

ALTER TABLE pets
    ADD COLUMN usuario_id INT DEFAULT NULL AFTER id,
    ADD KEY idx_pets_usuario (usuario_id),
    ADD CONSTRAINT fk_pets_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

-- Contas de demonstração
--   admin@petitpet.local   / Admin@123
--   cliente@petitpet.local / Cliente@123
-- Troque as senhas antes de publicar o projeto.

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

-- Liga os pets de exemplo a conta da Ana
UPDATE pets p
JOIN usuarios u ON u.email = 'cliente@petitpet.local'
SET p.usuario_id = u.id
WHERE p.nome IN ('Mel', 'Luna')
  AND p.usuario_id IS NULL;
