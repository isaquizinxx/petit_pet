<?php
/**
 * Conexão única com o MySQL.
 *
 * A função db() devolve a mesma conexão durante toda a requisição.
 * Credenciais ficam em config/config.php (ou variáveis de ambiente).
 */

require_once __DIR__ . '/config/config.php';

function db(): mysqli
{
    static $conexao = null;

    if ($conexao instanceof mysqli) {
        return $conexao;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conexao = new mysqli(DB_HOST, DB_USUARIO, DB_SENHA, DB_NOME);
        $conexao->set_charset(DB_CHARSET);
    } catch (mysqli_sql_exception $erro) {
        error_log('[Petit Pet] Falha ao conectar no banco: ' . $erro->getMessage());
        http_response_code(500);
        exit('Não foi possível conectar ao banco de dados. Confira a configuração e tente novamente.');
    }

    return $conexao;
}
