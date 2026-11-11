-- ============================================================================
-- Petit Pet — dados de demonstração
--
-- Opcional. Use apenas em ambiente local/escolar.
-- Conta criada: demo@petitpet.local / Petit@123
-- Remova ou troque essa senha antes de publicar o projeto.
-- ============================================================================

USE biblioteca;

INSERT INTO usuarios (nome, email, senha)
VALUES (
    'Demonstração',
    'demo@petitpet.local',
    '$2y$12$hEBh/16JmptUrfRZCDlWDubx/MbGtU03DvPnDk/Rka9VsalYB/WK6'
)
ON DUPLICATE KEY UPDATE
    nome = VALUES(nome),
    senha = VALUES(senha);

-- Completa o registro que veio da migração antiga, sem sobrescrever dados já preenchidos.
UPDATE pets
SET
    sexo = 'femea',
    data_nascimento = '2021-03-15',
    porte = 'pequeno',
    tutor_nome = 'Ana Souza',
    tutor_telefone = '(41) 99999-1234',
    observacoes = 'Muito dócil, adora brincar e passear no fim da tarde.'
WHERE nome = 'Mel'
  AND tutor_nome IS NULL;

INSERT INTO pets (nome, especie, raca, sexo, data_nascimento, porte, tutor_nome, tutor_telefone, observacoes, status)
SELECT 'Thor', 'Cachorro', 'Border Collie', 'macho', '2022-11-02', 'medio', 'Carlos Lima', '(41) 98888-4321', 'Muito energético; precisa de atividades durante o dia.', 'em_atendimento'
WHERE NOT EXISTS (SELECT 1 FROM pets WHERE nome = 'Thor' AND especie = 'Cachorro');

INSERT INTO pets (nome, especie, raca, sexo, data_nascimento, porte, tutor_nome, tutor_telefone, observacoes, status)
SELECT 'Luna', 'Gato', 'Siamês', 'femea', '2023-06-10', 'pequeno', 'Ana Souza', '(41) 99999-1234', 'Tímida no começo, mas muito carinhosa.', 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM pets WHERE nome = 'Luna' AND especie = 'Gato');

INSERT INTO pets (nome, especie, raca, sexo, data_nascimento, porte, tutor_nome, tutor_telefone, observacoes, status)
SELECT 'Bento', 'Cachorro', 'SRD', 'macho', '2020-01-20', 'grande', 'Marina Alves', '(41) 97777-2211', 'Calmo e sociável com outros animais.', 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM pets WHERE nome = 'Bento' AND especie = 'Cachorro');

INSERT INTO pets (nome, especie, raca, sexo, data_nascimento, porte, tutor_nome, tutor_telefone, observacoes, status)
SELECT 'Nina', 'Gato', 'SRD', 'femea', '2024-02-14', 'pequeno', 'Rafael Gomes', '(41) 96666-3344', 'Filhote resgatada; acompanhar vacinação.', 'em_atendimento'
WHERE NOT EXISTS (SELECT 1 FROM pets WHERE nome = 'Nina' AND especie = 'Gato');
