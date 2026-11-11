<?php
/**
 * Configuração central da Petit Pet.
 *
 * Precedência: config.local.php < variáveis de ambiente.
 * As variáveis de ambiente permitem rodar o projeto em outro computador
 * sem alterar este arquivo.
 */

$padroes = [
    'db_host' => 'localhost',
    'db_usuario' => 'root',
    'db_senha' => 'root',
    'db_nome' => 'biblioteca',
    'debug' => false,
];

$config = $padroes;

$configLocal = __DIR__ . '/config.local.php';
if (is_file($configLocal)) {
    $local = require $configLocal;
    if (is_array($local)) {
        $config = array_merge($config, $local);
    }
}

$variaveisAmbiente = [
    'db_host' => 'PETIT_DB_HOST',
    'db_usuario' => 'PETIT_DB_USUARIO',
    'db_senha' => 'PETIT_DB_SENHA',
    'db_nome' => 'PETIT_DB_NOME',
    'debug' => 'PETIT_DEBUG',
];

foreach ($variaveisAmbiente as $chave => $variavel) {
    $valor = getenv($variavel);
    if ($valor === false) {
        continue;
    }
    $config[$chave] = $chave === 'debug'
        ? filter_var($valor, FILTER_VALIDATE_BOOL)
        : $valor;
}

define('APP_NOME', 'Petit Pet');
define('APP_DESCRICAO', 'Cuidado, carinho e organização para os seus melhores amigos.');
define('APP_ANO', date('Y'));
define('DB_HOST', (string) $config['db_host']);
define('DB_USUARIO', (string) $config['db_usuario']);
define('DB_SENHA', (string) $config['db_senha']);
define('DB_NOME', (string) $config['db_nome']);
define('DB_CHARSET', 'utf8mb4');
define('APP_DEBUG', (bool) $config['debug']);
