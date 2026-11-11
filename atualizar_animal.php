<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('erro', 'Envie as alterações pelo formulário.');
    redirecionar('animais_cadastrados.php');
}

exigir_csrf('animais_cadastrados.php');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('erro', 'O identificador do pet não é válido.');
    redirecionar('animais_cadastrados.php');
}

$dados = dados_pet_do_post($_POST);
$erros = validar_dados_pet($dados);

if ($erros) {
    $_SESSION['pet_formulario'] = $dados;
    $_SESSION['pet_erros'] = $erros;
    flash('erro', 'Confira os campos destacados e tente novamente.');
    redirecionar('editar_animal.php?id=' . (int) $id);
}

try {
    if (!buscar_pet((int) $id)) {
        flash('erro', 'O pet solicitado não foi encontrado.');
        redirecionar('animais_cadastrados.php');
    }

    atualizar_pet((int) $id, $dados);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao atualizar pet: ' . $erro->getMessage());
    $_SESSION['pet_formulario'] = $dados;
    flash('erro', 'Não foi possível salvar as alterações agora. Tente novamente.');
    redirecionar('editar_animal.php?id=' . (int) $id);
}

unset($_SESSION['pet_formulario'], $_SESSION['pet_erros']);
flash('sucesso', 'Cadastro de "' . $dados['nome'] . '" atualizado com sucesso!');
redirecionar('animais_cadastrados.php');
