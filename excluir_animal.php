<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('erro', 'A exclusão precisa ser solicitada pelo formulário.');
    redirecionar('animais_cadastrados.php');
}

exigir_csrf('animais_cadastrados.php');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('erro', 'O identificador do pet não é válido.');
    redirecionar('animais_cadastrados.php');
}

try {
    $pet = buscar_pet((int) $id);

    if (!$pet) {
        flash('erro', 'O pet solicitado não foi encontrado.');
        redirecionar('animais_cadastrados.php');
    }

    $removeu = excluir_pet((int) $id);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao excluir pet: ' . $erro->getMessage());
    flash('erro', 'Não foi possível remover o pet agora. Tente novamente.');
    redirecionar('animais_cadastrados.php');
}

if (!$removeu) {
    flash('erro', 'O pet não foi encontrado ou já havia sido removido.');
    redirecionar('animais_cadastrados.php');
}

flash('sucesso', 'Pet "' . $pet['nome'] . '" removido com sucesso.');
redirecionar('animais_cadastrados.php');
