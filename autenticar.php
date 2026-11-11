<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (usuario_logado()) {
    redirecionar('painel.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('erro', 'Envie o formulário de login para continuar.');
    redirecionar('login.php');
}

exigir_csrf('login.php');

if (login_bloqueado()) {
    flash('aviso', 'Muitas tentativas de login. Aguarde alguns minutos e tente novamente.');
    redirecionar('login.php');
}

$email = normalizar_email(valor_post('email'));
$senha = valor_post_bruto('senha');

if ($email === '' || $senha === '') {
    flash('erro', 'Informe o e-mail e a senha para entrar.');
    redirecionar('login.php');
}

try {
    $usuario = autenticar_usuario($email, $senha);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao autenticar: ' . $erro->getMessage());
    flash('erro', 'Não foi possível concluir o login agora. Tente novamente.');
    redirecionar('login.php');
}

if (!$usuario) {
    registrar_falha_login();
    flash('erro', 'E-mail ou senha inválidos. Confira seus dados e tente novamente.');
    redirecionar('login.php');
}

session_regenerate_id(true);
unset($_SESSION['csrf_token']);

$_SESSION['usuario_id'] = (int) $usuario['id'];
$_SESSION['nome'] = (string) $usuario['nome'];
$_SESSION['email'] = (string) $usuario['email'];

limpar_tentativas_login();
flash('sucesso', 'Bem-vindo(a), ' . $usuario['nome'] . '!');
redirecionar('painel.php');
