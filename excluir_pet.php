<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$modoAdmin = eh_admin();
$urlLista = $modoAdmin ? 'admin_pets.php' : 'painel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('erro', 'A exclusão precisa ser solicitada pelo formulário.');
    redirecionar($urlLista);
}

exigir_csrf($urlLista);

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('erro', 'O identificador do pet não é válido.');
    redirecionar($urlLista);
}

try {
    $pet = buscar_pet((int) $id);

    if (!$pet) {
        flash('erro', 'O pet solicitado não foi encontrado.');
        redirecionar($urlLista);
    }

    if (!$modoAdmin && !pet_pertence_ao_usuario($pet, (int) usuario_id())) {
        flash('erro', 'Você só pode excluir os pets da sua conta.');
        redirecionar('pets.php');
    }

    $removeu = excluir_pet((int) $id);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao excluir pet: ' . $erro->getMessage());
    flash('erro', 'Não foi possível remover o pet agora. Tente novamente.');
    redirecionar($urlLista);
}

if (!$removeu) {
    flash('erro', 'O pet não foi encontrado ou já havia sido removido.');
} else {
    flash('sucesso', 'Pet "' . $pet['nome'] . '" removido com sucesso.');
}

redirecionar($urlLista);
