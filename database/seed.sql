-- Petit Pet - dados de demonstração
-- Opcional. Use apenas no ambiente local da apresentação.
--
-- Contas:
--   admin@petitpet.local   / Admin@123
--   cliente@petitpet.local / Cliente@123
--
-- Troque as senhas antes de publicar o projeto.

USE petit_pet;

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

SELECT 'usuarios' AS tabela, COUNT(*) AS total FROM usuarios
UNION ALL
SELECT 'pets', COUNT(*) FROM pets;
