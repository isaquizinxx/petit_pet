<?php
/**
 * Sessão segura e helpers de autenticação básica.
 */

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

function usuario_logado(): bool
{
    return isset($_SESSION['usuario_id']) || isset($_SESSION['nome']);
}

function usuario_nome(): string
{
    return (string) ($_SESSION['nome'] ?? '');
}

function usuario_id(): ?int
{
    return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
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
