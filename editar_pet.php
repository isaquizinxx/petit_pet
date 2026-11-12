<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$modoFormulario = eh_admin() ? 'admin' : 'cliente';
$id = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)
    : filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$urlLista = $modoFormulario === 'admin' ? 'admin_pets.php' : 'painel.php';

if (!$id || $id < 1) {
    flash('erro', 'O identificador do pet não é válido.');
    redirecionar($urlLista);
}

try {
    $petEncontrado = buscar_pet((int) $id);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao consultar pet para edição: ' . $erro->getMessage());
    flash('erro', 'Não foi possível consultar os dados do pet agora.');
    redirecionar($urlLista);
}

if (!$petEncontrado) {
    flash('erro', 'O pet solicitado não foi encontrado.');
    redirecionar($urlLista);
}

if ($modoFormulario === 'cliente' && !pet_pertence_ao_usuario($petEncontrado, (int) usuario_id())) {
    flash('erro', 'Você só pode editar os pets da sua conta.');
    redirecionar('pets.php');
}

$errosPet = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_csrf('editar_pet.php?id=' . (int) $id);

    $pet = array_merge(pet_dados_iniciais(), $petEncontrado, dados_pet_do_post($_POST));

    if ($modoFormulario === 'cliente') {
        $usuario = buscar_usuario((int) usuario_id());
        $pet['tutor_nome'] = (string) ($usuario['nome'] ?? usuario_nome());
        $pet['tutor_telefone'] = (string) ($usuario['telefone'] ?? '');
        $pet['status'] = (string) $petEncontrado['status'];
    }

    $errosPet = validar_dados_pet($pet);

    if (!$errosPet) {
        try {
            atualizar_pet((int) $id, $pet);
            flash('sucesso', 'Dados de "' . $pet['nome'] . '" atualizados com sucesso!');
            redirecionar($urlLista);
        } catch (Throwable $erro) {
            error_log('[Petit Pet] Falha ao atualizar pet: ' . $erro->getMessage());
            flash('erro', 'Não foi possível salvar as alterações agora. Tente novamente.');
        }
    } else {
        flash('erro', 'Confira os campos destacados e tente novamente.');
    }
} else {
    $pet = array_merge(pet_dados_iniciais(), $petEncontrado);
}

$acaoFormulario = 'editar_pet.php?id=' . (int) $id;
$petId = (int) $id;
$textoBotao = 'Salvar alterações';
$urlCancelar = $urlLista;

$titulo = 'Editar ' . $pet['nome'];
$subtitulo = 'Atualize as informações do pet sempre que precisar.';
$eyebrow = $modoFormulario === 'admin' ? 'Administração' : 'Área dos clientes';
$containerClasse = 'container container-form';
$paginaAtiva = $modoFormulario === 'admin' ? 'admin-pets' : 'painel';
$area = $modoFormulario === 'admin' ? 'admin' : 'site';
$descricaoPagina = 'Edite as informações do pet na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
require __DIR__ . '/includes/form_pet.php';
require __DIR__ . '/includes/layout/footer.php';
