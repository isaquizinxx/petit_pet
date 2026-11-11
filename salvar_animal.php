<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('erro', 'Envie o cadastro pelo formulário.');
    redirecionar('cadastrar_animal.php');
}

exigir_csrf('cadastrar_animal.php');

$dados = dados_pet_do_post($_POST);
$erros = validar_dados_pet($dados);

if ($erros) {
    $_SESSION['pet_formulario'] = $dados;
    $_SESSION['pet_erros'] = $erros;
    flash('erro', 'Confira os campos destacados e tente novamente.');
    redirecionar('cadastrar_animal.php');
}

try {
    criar_pet($dados);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao cadastrar pet: ' . $erro->getMessage());
    $_SESSION['pet_formulario'] = $dados;
    flash('erro', 'Não foi possível cadastrar o pet agora. Tente novamente.');
    redirecionar('cadastrar_animal.php');
}

unset($_SESSION['pet_formulario'], $_SESSION['pet_erros']);
flash('sucesso', 'Pet "' . $dados['nome'] . '" cadastrado com sucesso!');
redirecionar('animais_cadastrados.php');
