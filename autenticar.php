<?php

// autenticar.php é responsável por verificar se o e-mail e a senha informados no formulário de login estão corretos. Ele inicia uma sessão, conecta-se ao banco de dados, busca o usuário pelo e-mail fornecido e verifica se a senha corresponde à senha armazenada no banco de dados. Se a autenticação for bem-sucedida, o usuário é redirecionado para o painel; caso contrário, ele é redirecionado de volta para a página de login com uma mensagem de erro.
// autenticar.php verifica se o e-mail e senha informados estão corretos!
session_start();

include("conexao.php"); //recebe o email e a senha digitados no formulário de login
$email = $_POST['email'];
$senha = $_POST['senha'];

// ==============================================
// CONSULTA NO BANCO ( READ do CRUD )
// Busca o usúario pelo email informado
// ==============================================

$sql = "SELECT * FROM usuarios WHERE email = '$email'";
$resultado = mysqli_query($conexao, $sql);
$usuario = mysqli_fetch_assoc($resultado);

// ==============================================
// VERIFICAÇÃO DA SENHA
// ==============================================

if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['nome'] = $usuario['nome'];
    header("Location: painel.php");
    exit();
} else {
    header("Location: login.php?erro=login");
    exit();
}