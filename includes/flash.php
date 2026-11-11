<?php
/**
 * Mensagens de feedback que sobrevivem a um redirecionamento.
 */

function flash(string $tipo, string $mensagem): void
{
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }

    $_SESSION['flash'][] = [
        'tipo' => $tipo,
        'mensagem' => $mensagem,
    ];
}

function obter_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return is_array($flashes) ? $flashes : [];
}
