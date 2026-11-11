<?php
/**
 * Inicialização comum a todas as páginas.
 */

require_once __DIR__ . '/../config/config.php';

date_default_timezone_set('America/Sao_Paulo');
error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/sessao.php';
require_once __DIR__ . '/flash.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/validacao.php';
require_once __DIR__ . '/pets.php';

// Cabeçalhos de segurança aplicados a todas as páginas.
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

iniciar_sessao();
