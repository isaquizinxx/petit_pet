<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$pet = array_merge(pet_dados_iniciais(), is_array($_SESSION['pet_formulario'] ?? null) ? $_SESSION['pet_formulario'] : []);
$errosPet = is_array($_SESSION['pet_erros'] ?? null) ? $_SESSION['pet_erros'] : [];
unset($_SESSION['pet_formulario'], $_SESSION['pet_erros']);

$acaoFormulario = 'salvar_animal.php';
$petId = null;
$textoBotao = 'Cadastrar pet';

$titulo = 'Cadastrar pet';
$subtitulo = 'Conte um pouquinho sobre o novo amigo da Petit Pet.';
$eyebrow = 'Novo cadastro';
$paginaAtiva = 'cadastrar';
$descricaoPagina = 'Cadastre um novo pet na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
require __DIR__ . '/includes/form_pet.php';
require __DIR__ . '/includes/layout/footer.php';
