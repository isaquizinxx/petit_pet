<?php

// Exige autenticação antes de processar qualquer solicitação de exclusão.
include("verificar_sessao.php");

// A exclusão é uma alteração destrutiva e só pode ser solicitada por POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: animais_cadastrados.php?erro=metodo");
    exit();
}

// Impede que outro site acione a exclusão usando a sessão autenticada do usuário.
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

// Aceita somente um identificador inteiro positivo.
$idRecebido = $_POST['id'] ?? null;
if (!is_string($idRecebido) || !ctype_digit($idRecebido) || (int) $idRecebido < 1) {
    header("Location: animais_cadastrados.php?erro=id");
    exit();
}
$id = (int) $idRecebido;

// Remove o registro por consulta preparada, limitando a operação ao pet solicitado.
include("conexao.php");
try {
    $comando = $conexao->prepare("DELETE FROM livros WHERE id = ?");
    $comando->bind_param("i", $id);
    $comando->execute();
    $comando->close();
} catch (mysqli_sql_exception $erroBanco) {
    error_log("Falha ao excluir pet: " . $erroBanco->getMessage());
    header("Location: animais_cadastrados.php?erro=excluir");
    exit();
}

// Invalida o token anterior para que o formulário precise ser carregado novamente.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
header("Location: animais_cadastrados.php?sucesso=exclusao");
exit();
