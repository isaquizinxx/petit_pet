<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('erro', 'O identificador do pet não é válido.');
    redirecionar('animais_cadastrados.php');
}

try {
    $petEncontrado = buscar_pet((int) $id);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao consultar pet para edição: ' . $erro->getMessage());
    flash('erro', 'Não foi possível consultar os dados do pet agora.');
    redirecionar('animais_cadastrados.php');
}

if (!$petEncontrado) {
    flash('erro', 'O pet solicitado não foi encontrado.');
    redirecionar('animais_cadastrados.php');
}

$pet = array_merge(pet_dados_iniciais(), $petEncontrado);

if (is_array($_SESSION['pet_formulario'] ?? null)) {
    $pet = array_merge($pet, $_SESSION['pet_formulario']);
}

$errosPet = is_array($_SESSION['pet_erros'] ?? null) ? $_SESSION['pet_erros'] : [];
unset($_SESSION['pet_formulario'], $_SESSION['pet_erros']);

$acaoFormulario = 'atualizar_animal.php';
$petId = (int) $petEncontrado['id'];
$textoBotao = 'Salvar alterações';

$titulo = 'Editar pet';
$subtitulo = 'Atualize as informações do pet abaixo.';
$eyebrow = 'Edição';
$paginaAtiva = 'pets';
$descricaoPagina = 'Edite o cadastro de um pet na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
require __DIR__ . '/includes/form_pet.php';
require __DIR__ . '/includes/layout/footer.php';
