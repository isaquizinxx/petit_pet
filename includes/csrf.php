<?php
/**
 * Proteção CSRF.
 *
 * O token é único por sessão e não é rotacionado a cada envio.
 * Isso evita que duas abas abertas invalidem o formulário uma da outra.
 */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_valido(mixed $token): bool
{
    return is_string($token)
        && $token !== ''
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function exigir_csrf(string $destino = 'login.php'): void
{
    if (!csrf_valido($_POST['csrf_token'] ?? null)) {
        flash('erro', 'Sua sessão expirou ou a solicitação é inválida. Tente novamente.');
        redirecionar($destino);
    }
}
