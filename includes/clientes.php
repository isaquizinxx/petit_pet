<?php
/**
 * Acesso à tabela usuarios (clientes e administradores).
 */

/** Lista os clientes com a quantidade de pets vinculados. */
function listar_clientes(string $busca = ''): array
{
    $sql = "SELECT u.id, u.nome, u.email, u.telefone, u.criado_em,
                   COUNT(p.id) AS total_pets
            FROM usuarios u
            LEFT JOIN pets p ON p.usuario_id = u.id
            WHERE u.perfil = 'cliente'";

    $tipos = '';
    $parametros = [];

    if ($busca !== '') {
        $sql .= ' AND (u.nome LIKE ? OR u.email LIKE ?)';
        $like = '%' . addcslashes($busca, '%_\\') . '%';
        $tipos = 'ss';
        $parametros = [$like, $like];
    }

    $sql .= ' GROUP BY u.id ORDER BY u.nome ASC';

    $stmt = db()->prepare($sql);
    if ($tipos !== '') {
        $stmt->bind_param($tipos, ...$parametros);
    }
    $stmt->execute();
    $clientes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $clientes;
}

function contar_clientes(): int
{
    $resultado = db()->query("SELECT COUNT(*) AS total FROM usuarios WHERE perfil = 'cliente'")->fetch_assoc();

    return (int) ($resultado['total'] ?? 0);
}

function buscar_usuario(int $id): ?array
{
    $stmt = db()->prepare(
        'SELECT id, nome, email, telefone, perfil, criado_em FROM usuarios WHERE id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $usuario ?: null;
}

/** Confere se o e-mail já pertence a outro usuário. */
function email_em_uso(string $email, ?int $ignorarId = null): bool
{
    $sql = 'SELECT id FROM usuarios WHERE email = ?';
    $tipos = 's';
    $parametros = [$email];

    if ($ignorarId !== null) {
        $sql .= ' AND id <> ?';
        $tipos .= 'i';
        $parametros[] = $ignorarId;
    }

    $sql .= ' LIMIT 1';

    $stmt = db()->prepare($sql);
    $stmt->bind_param($tipos, ...$parametros);
    $stmt->execute();
    $existe = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    return $existe;
}

function criar_cliente(array $dados, string $senhaHash): int
{
    $stmt = db()->prepare(
        "INSERT INTO usuarios (nome, email, telefone, senha, perfil)
         VALUES (?, ?, ?, ?, 'cliente')"
    );

    $nome = $dados['nome'];
    $email = $dados['email'];
    $telefone = $dados['telefone'] !== '' ? $dados['telefone'] : null;
    $stmt->bind_param('ssss', $nome, $email, $telefone, $senhaHash);
    $stmt->execute();
    $id = (int) $stmt->insert_id;
    $stmt->close();

    return $id;
}

function atualizar_cliente(int $id, array $dados, ?string $senhaHash = null): bool
{
    $telefone = $dados['telefone'] !== '' ? $dados['telefone'] : null;

    if ($senhaHash !== null) {
        $stmt = db()->prepare(
            "UPDATE usuarios SET nome = ?, email = ?, telefone = ?, senha = ?
             WHERE id = ? AND perfil = 'cliente'"
        );
        $stmt->bind_param('ssssi', $dados['nome'], $dados['email'], $telefone, $senhaHash, $id);
    } else {
        $stmt = db()->prepare(
            "UPDATE usuarios SET nome = ?, email = ?, telefone = ?
             WHERE id = ? AND perfil = 'cliente'"
        );
        $stmt->bind_param('sssi', $dados['nome'], $dados['email'], $telefone, $id);
    }

    $stmt->execute();
    $atualizou = $stmt->affected_rows >= 0;
    $stmt->close();

    return $atualizou;
}

/** Remove somente contas de cliente; administradores são protegidos. */
function excluir_cliente(int $id): bool
{
    $stmt = db()->prepare("DELETE FROM usuarios WHERE id = ? AND perfil = 'cliente'");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $removeu = $stmt->affected_rows > 0;
    $stmt->close();

    return $removeu;
}

function clientes_recentes(int $limite = 3): array
{
    $limite = max(1, min(10, $limite));

    $stmt = db()->prepare(
        "SELECT id, nome, email, telefone, criado_em FROM usuarios
         WHERE perfil = 'cliente'
         ORDER BY id DESC LIMIT ?"
    );
    $stmt->bind_param('i', $limite);
    $stmt->execute();
    $clientes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $clientes;
}
