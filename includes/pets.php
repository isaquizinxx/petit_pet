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
function filtros_pets(string $busca, string $status): array
{
    $condicoes = [];
    $tipos = '';
    $parametros = [];

    if ($busca !== '') {
        $condicoes[] = '(nome LIKE ? OR especie LIKE ? OR raca LIKE ? OR tutor_nome LIKE ?)';
        // Trata % e _ como texto comum, e não como curingas do LIKE.
        $like = '%' . addcslashes($busca, '%_\\') . '%';
        $tipos .= 'ssss';
        array_push($parametros, $like, $like, $like, $like);
    }

    if (in_array($status, pet_status_validos(), true)) {
        $condicoes[] = 'status = ?';
        $tipos .= 's';
        $parametros[] = $status;
    }

    $where = $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '';

    return [$where, $tipos, $parametros];
}

function contar_pets(string $busca = '', string $status = ''): int
{
    [$where, $tipos, $parametros] = filtros_pets($busca, $status);

    $stmt = db()->prepare('SELECT COUNT(*) AS total FROM pets ' . $where);
    if ($tipos !== '') {
        $stmt->bind_param($tipos, ...$parametros);
    }
    $stmt->execute();
    $total = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
    $stmt->close();

    return $total;
}

function listar_pets(string $busca = '', string $status = '', int $pagina = 1, int $porPagina = 8): array
{
    [$where, $tipos, $parametros] = filtros_pets($busca, $status);

    $porPagina = max(1, min(50, $porPagina));
    $pagina = max(1, $pagina);
    $offset = ($pagina - 1) * $porPagina;

    $sql = 'SELECT * FROM pets ' . $where . ' ORDER BY nome ASC, id ASC LIMIT ? OFFSET ?';
    $tipos .= 'ii';
    $parametros[] = $porPagina;
    $parametros[] = $offset;

    $stmt = db()->prepare($sql);
    $stmt->bind_param($tipos, ...$parametros);
    $stmt->execute();
    $pets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $pets;
}

function buscar_pet(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM pets WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $pet = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $pet ?: null;
}

function criar_pet(array $dados): int
{
    $stmt = db()->prepare(
        'INSERT INTO pets
            (nome, especie, raca, sexo, data_nascimento, porte, tutor_nome, tutor_telefone, observacoes, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
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
        'ssssssssss',
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

/** Números exibidos no painel. */
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
