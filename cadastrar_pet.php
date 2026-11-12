<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$modoFormulario = eh_admin() ? 'admin' : 'cliente';
$pet = pet_dados_iniciais();
$errosPet = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_csrf('cadastrar_pet.php');

    $pet = array_merge(pet_dados_iniciais(), dados_pet_do_post($_POST));

    if ($modoFormulario === 'cliente') {
        $usuario = buscar_usuario((int) usuario_id());
        $pet['tutor_nome'] = (string) ($usuario['nome'] ?? usuario_nome());
        $pet['tutor_telefone'] = (string) ($usuario['telefone'] ?? '');
        $pet['status'] = 'disponivel';
    }

    $errosPet = validar_dados_pet($pet);

    if (!$errosPet) {
        try {
            $usuarioId = $modoFormulario === 'cliente' ? (int) usuario_id() : null;
            criar_pet($pet, $usuarioId);

            flash('sucesso', 'Pet "' . $pet['nome'] . '" cadastrado com sucesso!');
            redirecionar($modoFormulario === 'admin' ? 'admin_pets.php' : 'painel.php');
        } catch (Throwable $erro) {
            error_log('[Petit Pet] Falha ao cadastrar pet: ' . $erro->getMessage());
            flash('erro', 'Não foi possível cadastrar o pet agora. Tente novamente.');
        }
    } else {
        flash('erro', 'Confira os campos destacados e tente novamente.');
    }
}

$acaoFormulario = 'cadastrar_pet.php';
$petId = null;
$textoBotao = 'Cadastrar pet';
$urlCancelar = $modoFormulario === 'admin' ? 'admin_pets.php' : 'painel.php';

$titulo = 'Cadastrar pet';
$subtitulo = 'Conte um pouquinho sobre o novo amigo da Petit Pet.';
$eyebrow = $modoFormulario === 'admin' ? 'Administração' : 'Área dos clientes';
$containerClasse = 'container container-form';
$paginaAtiva = $modoFormulario === 'admin' ? 'admin-novo-pet' : 'cadastrar';
$area = $modoFormulario === 'admin' ? 'admin' : 'site';
$descricaoPagina = 'Cadastre um novo pet na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
require __DIR__ . '/includes/form_pet.php';
require __DIR__ . '/includes/layout/footer.php';
