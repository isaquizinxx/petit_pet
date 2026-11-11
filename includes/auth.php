<?php
/**
 * Autenticação e proteção de páginas.
 */

function exigir_login(): void
{
    if (!usuario_logado()) {
        flash('aviso', 'Faça login para acessar o painel.');
        redirecionar('login.php');
    }

    enviar_cabecalhos_sem_cache();
}

/**
 * Busca o usuário pelo e-mail e confere a senha com password_verify.
 * Retorna os dados do usuário ou null.
 */
function autenticar_usuario(string $email, string $senha): ?array
{
    $stmt = db()->prepare('SELECT id, nome, email, senha FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$usuario) {
        // Mantém um custo de verificação parecido mesmo quando o e-mail não existe.
        password_verify($senha, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
        return null;
    }

    if (!password_verify($senha, (string) $usuario['senha'])) {
        return null;
    }

    return $usuario;
}

function login_bloqueado(): bool
{
    $tentativas = $_SESSION['login_tentativas'] ?? null;

    return is_array($tentativas) && (int) ($tentativas['bloqueado_ate'] ?? 0) > time();
}

function registrar_falha_login(): void
{
    $tentativas = $_SESSION['login_tentativas'] ?? ['contador' => 0, 'bloqueado_ate' => 0];
    $tentativas['contador'] = (int) ($tentativas['contador'] ?? 0) + 1;

    if ($tentativas['contador'] >= 5) {
        $tentativas['contador'] = 0;
        $tentativas['bloqueado_ate'] = time() + 300;
    }

    $_SESSION['login_tentativas'] = $tentativas;
}

function limpar_tentativas_login(): void
{
    unset($_SESSION['login_tentativas']);
}
