<?php
/**
 * Acesso à tabela pets (consultas preparadas).
 */

function pet_status_validos(): array
{
    return ['disponivel', 'em_atendimento'];
}

function pet_sexos_validos(): array
{
    return ['macho', 'femea', 'nao_informado'];
}

function pet_portes_validos(): array
{
    return ['pequeno', 'medio', 'grande'];
}

/** Estrutura inicial usada pelos formulários de cadastro e edição. */
function pet_dados_iniciais(): array
{
    return [
        'nome' => '',
        'especie' => '',
        'raca' => '',
        'sexo' => 'nao_informado',
        'data_nascimento' => '',
        'porte' => '',
        'tutor_nome' => '',
        'tutor_telefone' => '',
        'observacoes' => '',
        'status' => 'disponivel',
    ];
}

/**
 * Monta WHERE, tipos e parâmetros dos filtros de listagem.
 *
 * @return array{0: string, 1: string, 2: array<int, mixed>}
 */
function filtros_pets(string $busca, string $status, ?int $usuarioId = null): array
{
    $condicoes = [];
    $tipos = '';
    $parametros = [];

    if ($busca !== '') {
        $condicoes[] = '(p.nome LIKE ? OR p.especie LIKE ? OR p.raca LIKE ? OR p.tutor_nome LIKE ?'
            . ' OR u.nome LIKE ? OR u.email LIKE ?)';
        // Trata % e _ como texto comum, e não como curingas do LIKE.
        $like = '%' . addcslashes($busca, '%_\\') . '%';
        $tipos .= 'ssssss';
        array_push($parametros, $like, $like, $like, $like, $like, $like);
    }

    if (in_array($status, pet_status_validos(), true)) {
        $condicoes[] = 'p.status = ?';
        $tipos .= 's';
        $parametros[] = $status;
    }

    if ($usuarioId !== null) {
        $condicoes[] = 'p.usuario_id = ?';
        $tipos .= 'i';
        $parametros[] = $usuarioId;
    }

    $where = $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '';

    return [$where, $tipos, $parametros];
}

function contar_pets(string $busca = '', string $status = '', ?int $usuarioId = null): int
{
    [$where, $tipos, $parametros] = filtros_pets($busca, $status, $usuarioId);

    $stmt = db()->prepare(
        'SELECT COUNT(*) AS total FROM pets p LEFT JOIN usuarios u ON u.id = p.usuario_id ' . $where
    );
    if ($tipos !== '') {
        $stmt->bind_param($tipos, ...$parametros);
    }
    $stmt->execute();
    $total = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
    $stmt->close();

    return $total;
}

function listar_pets(string $busca = '', string $status = '', ?int $usuarioId = null): array
{
    [$where, $tipos, $parametros] = filtros_pets($busca, $status, $usuarioId);

    $sql = 'SELECT p.*, u.nome AS cliente_nome, u.email AS cliente_email
            FROM pets p
            LEFT JOIN usuarios u ON u.id = p.usuario_id
            ' . $where . '
            ORDER BY p.nome ASC, p.id ASC';

    $stmt = db()->prepare($sql);
    if ($tipos !== '') {
        $stmt->bind_param($tipos, ...$parametros);
    }
    $stmt->execute();
    $pets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $pets;
}

/** Pets vinculados a uma conta de cliente. */
function pets_do_cliente(int $usuarioId): array
{
    return listar_pets('', '', $usuarioId);
}

function contar_pets_do_cliente(int $usuarioId): int
{
    return contar_pets('', '', $usuarioId);
}

function buscar_pet(int $id): ?array
{
    $stmt = db()->prepare(
        'SELECT p.*, u.nome AS cliente_nome, u.email AS cliente_email
         FROM pets p
         LEFT JOIN usuarios u ON u.id = p.usuario_id
         WHERE p.id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $pet = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $pet ?: null;
}

/** Confere se o pet pertence à conta informada. */
function pet_pertence_ao_usuario(array $pet, int $usuarioId): bool
{
    return isset($pet['usuario_id']) && (int) $pet['usuario_id'] === $usuarioId;
}

function criar_pet(array $dados, ?int $usuarioId = null): int
{
    $stmt = db()->prepare(
        'INSERT INTO pets
            (usuario_id, nome, especie, raca, sexo, data_nascimento, porte,
             tutor_nome, tutor_telefone, observacoes, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    $nome = $dados['nome'];
    $especie = $dados['especie'];
    $raca = $dados['raca'] !== '' ? $dados['raca'] : null;
    $sexo = $dados['sexo'];
    $dataNascimento = $dados['data_nascimento'] !== '' ? $dados['data_nascimento'] : null;
    $porte = $dados['porte'] !== '' ? $dados['porte'] : null;
    $tutorNome = $dados['tutor_nome'] !== '' ? $dados['tutor_nome'] : null;
    $tutorTelefone = $dados['tutor_telefone'] !== '' ? $dados['tutor_telefone'] : null;
    $observacoes = $dados['observacoes'] !== '' ? $dados['observacoes'] : null;
    $status = $dados['status'];

    $stmt->bind_param(
        'issssssssss',
        $usuarioId,
        $nome,
        $especie,
        $raca,
        $sexo,
        $dataNascimento,
        $porte,
        $tutorNome,
        $tutorTelefone,
        $observacoes,
        $status
    );
    $stmt->execute();
    $id = (int) $stmt->insert_id;
    $stmt->close();

    return $id;
}

function atualizar_pet(int $id, array $dados): bool
{
    $stmt = db()->prepare(
        'UPDATE pets SET
            nome = ?, especie = ?, raca = ?, sexo = ?, data_nascimento = ?,
            porte = ?, tutor_nome = ?, tutor_telefone = ?, observacoes = ?, status = ?
         WHERE id = ?'
    );

    $nome = $dados['nome'];
    $especie = $dados['especie'];
    $raca = $dados['raca'] !== '' ? $dados['raca'] : null;
    $sexo = $dados['sexo'];
    $dataNascimento = $dados['data_nascimento'] !== '' ? $dados['data_nascimento'] : null;
    $porte = $dados['porte'] !== '' ? $dados['porte'] : null;
    $tutorNome = $dados['tutor_nome'] !== '' ? $dados['tutor_nome'] : null;
    $tutorTelefone = $dados['tutor_telefone'] !== '' ? $dados['tutor_telefone'] : null;
    $observacoes = $dados['observacoes'] !== '' ? $dados['observacoes'] : null;
    $status = $dados['status'];

    $stmt->bind_param(
        'ssssssssssi',
        $nome,
        $especie,
        $raca,
        $sexo,
        $dataNascimento,
        $porte,
        $tutorNome,
        $tutorTelefone,
        $observacoes,
        $status,
        $id
    );
    $stmt->execute();
    $atualizou = $stmt->affected_rows >= 0;
    $stmt->close();

    return $atualizou;
}

function excluir_pet(int $id): bool
{
    $stmt = db()->prepare('DELETE FROM pets WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $removeu = $stmt->affected_rows > 0;
    $stmt->close();

    return $removeu;
}

/** Números exibidos nos painéis. */
function estatisticas_pets(): array
{
    $sql = "SELECT
                COUNT(*) AS total,
                COALESCE(SUM(status = 'disponivel'), 0) AS disponiveis,
                COALESCE(SUM(status = 'em_atendimento'), 0) AS em_atendimento,
                COUNT(DISTINCT NULLIF(tutor_nome, '')) AS tutores
            FROM pets";

    $resultado = db()->query($sql)->fetch_assoc();

    return [
        'total' => (int) ($resultado['total'] ?? 0),
        'disponiveis' => (int) ($resultado['disponiveis'] ?? 0),
        'em_atendimento' => (int) ($resultado['em_atendimento'] ?? 0),
        'tutores' => (int) ($resultado['tutores'] ?? 0),
    ];
}

function pets_recentes(int $limite = 4): array
{
    $limite = max(1, min(20, $limite));

    $stmt = db()->prepare('SELECT * FROM pets ORDER BY criado_em DESC, id DESC LIMIT ?');
    $stmt->bind_param('i', $limite);
    $stmt->execute();
    $pets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $pets;
}
