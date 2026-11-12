<?php
/**
 * Inicialização comum a todas as páginas: configuração, sessão,
 * conexão com o banco e proteção de acesso.
 */

require_once __DIR__ . '/../config/config.php';

date_default_timezone_set('America/Sao_Paulo');
error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/funcoes.php';
require_once __DIR__ . '/clientes.php';
require_once __DIR__ . '/pets.php';

// Cabeçalhos de segurança aplicados a todas as páginas.
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

/* --------------------------------------------------------------------------
 * Sessão
 * ----------------------------------------------------------------------- */

function iniciar_sessao(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_name('petitpetsessao');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function encerrar_sessao(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $parametros = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 4200,
            'path' => $parametros['path'] ?: '/',
            'domain' => $parametros['domain'] ?: '',
            'secure' => (bool) $parametros['secure'],
            'httponly' => (bool) $parametros['httponly'],
            'samesite' => $parametros['samesite'] ?: 'Lax',
        ]);
    }

    session_destroy();
}

/* --------------------------------------------------------------------------
 * Usuário logado
 * ----------------------------------------------------------------------- */

function usuario_logado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function usuario_id(): ?int
{
    return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
}

function usuario_nome(): string
{
    return (string) ($_SESSION['nome'] ?? '');
}

function usuario_perfil(): string
{
    $perfil = (string) ($_SESSION['perfil'] ?? 'cliente');

    return $perfil === 'admin' ? 'admin' : 'cliente';
}

function eh_admin(): bool
{
    return usuario_logado() && usuario_perfil() === 'admin';
}

/* --------------------------------------------------------------------------
 * Entrada e saída
 * ----------------------------------------------------------------------- */

/**
 * Busca o usuário pelo e-mail e confere a senha com password_verify.
 * Retorna os dados do usuário (sem a senha) ou null.
 */
function autenticar_usuario(string $email, string $senha): ?array
{
    $stmt = db()->prepare(
        'SELECT id, nome, email, telefone, perfil, senha FROM usuarios WHERE email = ? LIMIT 1'
    );
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

    unset($usuario['senha']);

    return $usuario;
}

function iniciar_sessao_usuario(array $usuario): void
{
    session_regenerate_id(true);

    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['nome'] = (string) $usuario['nome'];
    $_SESSION['perfil'] = ($usuario['perfil'] ?? 'cliente') === 'admin' ? 'admin' : 'cliente';

    limpar_tentativas_login();
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

/* --------------------------------------------------------------------------
 * Proteção de páginas
 * ----------------------------------------------------------------------- */

function exigir_login(): void
{
    if (!usuario_logado()) {
        flash('aviso', 'Faça login para continuar.');
        redirecionar('login.php');
    }

    enviar_cabecalhos_sem_cache();
}

/** Área do cliente: administradores são levados ao painel administrativo. */
function exigir_cliente(): void
{
    exigir_login();

    if (eh_admin()) {
        redirecionar('admin.php');
    }
}

function exigir_admin(): void
{
    if (!usuario_logado()) {
        flash('aviso', 'Entre com uma conta administrativa para continuar.');
        redirecionar('login.php?area=admin');
    }

    if (!eh_admin()) {
        flash('erro', 'Sua conta não tem acesso à área administrativa.');
        redirecionar('painel.php');
    }

    enviar_cabecalhos_sem_cache();
}

iniciar_sessao();
