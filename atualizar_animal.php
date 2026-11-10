<?php

// Protege a operação contra usuários não autenticados.
include("verificar_sessao.php");

// Alterações de dados devem ocorrer por POST, nunca por acesso direto via URL.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: animais_cadastrados.php?erro=metodo");
    exit();
}

// Confere que a solicitação veio de um formulário aberto na sessão atual.
$tokenEnviado = $_POST['csrf_token'] ?? '';
$tokenSessao = $_SESSION['csrf_token'] ?? '';
if (
    !is_string($tokenEnviado)
    || !is_string($tokenSessao)
    || $tokenEnviado === ''
    || !hash_equals($tokenSessao, $tokenEnviado)
) {
    header("Location: animais_cadastrados.php?erro=sessao");
    exit();
}

// Rejeita identificadores e campos que não tenham o formato esperado.
$idRecebido = $_POST['id'] ?? null;
$tituloRecebido = $_POST['titulo'] ?? null;
$autorRecebido = $_POST['autor'] ?? null;
$statusRecebido = $_POST['status'] ?? null;

if (
    !is_string($idRecebido)
    || !ctype_digit($idRecebido)
    || (int) $idRecebido < 1
    || !is_string($tituloRecebido)
    || !is_string($autorRecebido)
    || !is_string($statusRecebido)
) {
    header("Location: animais_cadastrados.php?erro=campos");
    exit();
}

// Normaliza os textos e limita o status aos códigos legados aceitos pelo cadastro.
$id = (int) $idRecebido;
$titulo = trim($tituloRecebido);
$autor = trim($autorRecebido);
$status = $statusRecebido;

if (
    $titulo === ''
    || $autor === ''
    || !in_array($status, ['Disponivel', 'Emprestado'], true)
) {
    header("Location: editar_animal.php?id=" . $id . "&erro=campos");
    exit();
}

// Atualiza os dados usando parâmetros vinculados, sem concatenar entrada do usuário.
include("conexao.php");
try {
    $comando = $conexao->prepare(
        "UPDATE livros SET titulo = ?, autor = ?, status = ? WHERE id = ?"
    );
    $comando->bind_param("sssi", $titulo, $autor, $status, $id);
    $comando->execute();
    $comando->close();
} catch (mysqli_sql_exception $erroBanco) {
    error_log("Falha ao atualizar pet: " . $erroBanco->getMessage());
    header("Location: editar_animal.php?id=" . $id . "&erro=salvar");
    exit();
}

// Renova o token e informa a listagem sobre a operação concluída.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
header("Location: animais_cadastrados.php?sucesso=atualizacao");
exit();
