<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (usuario_logado()) {
    redirecionar('painel.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('erro', 'Envie o formulário de cadastro para continuar.');
    redirecionar('cadastro.php');
}

exigir_csrf('cadastro.php');

$nome = valor_post('nome');
$email = normalizar_email(valor_post('email'));
$senha = valor_post_bruto('senha');
$confirmacao = valor_post_bruto('senha_confirmacao');

$dados = [
    'nome' => $nome,
    'email' => $email,
    'senha' => $senha,
    'senha_confirmacao' => $confirmacao,
];

$erros = validar_cadastro_usuario($dados);

if ($erros) {
    $_SESSION['cadastro_dados'] = ['nome' => $nome, 'email' => $email];
    $_SESSION['cadastro_erros'] = $erros;
    flash('erro', 'Confira os campos destacados e tente novamente.');
    redirecionar('cadastro.php');
}

try {
    $verificacao = db()->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
    $verificacao->bind_param('s', $email);
    $verificacao->execute();
    $emailJaExiste = $verificacao->get_result()->num_rows > 0;
    $verificacao->close();
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao verificar e-mail no cadastro: ' . $erro->getMessage());
    flash('erro', 'Não foi possível concluir o cadastro agora. Tente novamente.');
    redirecionar('cadastro.php');
}

if ($emailJaExiste) {
    $_SESSION['cadastro_dados'] = ['nome' => $nome, 'email' => $email];
    $_SESSION['cadastro_erros'] = ['email' => 'Este e-mail já está cadastrado. Faça login para continuar.'];
    flash('erro', 'Este e-mail já está cadastrado.');
    redirecionar('cadastro.php');
}

$senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);
if ($senhaCriptografada === false) {
    error_log('[Petit Pet] Falha ao gerar o hash da senha.');
    flash('erro', 'Não foi possível concluir o cadastro agora. Tente novamente.');
    redirecionar('cadastro.php');
}

try {
    $comando = db()->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
    $comando->bind_param('sss', $nome, $email, $senhaCriptografada);
    $comando->execute();
    $comando->close();
} catch (mysqli_sql_exception $erro) {
    error_log('[Petit Pet] Falha ao cadastrar usuário: ' . $erro->getMessage());

    if ((int) $erro->getCode() === 1062 || strpos($erro->getMessage(), 'Duplicate entry') !== false) {
        $_SESSION['cadastro_dados'] = ['nome' => $nome, 'email' => $email];
        $_SESSION['cadastro_erros'] = ['email' => 'Este e-mail já está cadastrado. Faça login para continuar.'];
        flash('erro', 'Este e-mail já está cadastrado.');
    } else {
        flash('erro', 'Não foi possível concluir o cadastro agora. Tente novamente.');
    }

    redirecionar('cadastro.php');
}

unset($_SESSION['cadastro_dados'], $_SESSION['cadastro_erros']);
flash('sucesso', 'Conta criada com sucesso! Faça login para continuar.');
redirecionar('login.php');
