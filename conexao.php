<?php
// conexao.php é responsável por estabelecer a conexão com o banco de dados MySQL. Ele utiliza as funções do PHP para conectar-se ao servidor de banco de dados, especificando o host, usuário, senha e nome do banco de dados. Se a conexão falhar, ele exibe uma mensagem de erro e encerra a execução do script.
$conexao = mysqli_connect(
    "localhost",
    "root",
    "root",
    "biblioteca"
);
if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_errno());
}
