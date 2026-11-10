<?php

// Aceita somente envios do formulário e protege a operação contra acessos indevidos.
include("verificar_sessao.php");

// O cadastro altera dados; por isso, a operação só pode ser chamada por POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cadastrar_animal.php?erro=metodo");
    exit();
}

// Confere o token criado pelo formulário para evitar envios forjados.
$tokenEnviado = $_POST['csrf_token'] ?? '';
$tokenSessao = $_SESSION['csrf_token'] ?? '';

if (
    !is_string($tokenEnviado)
    || !is_string($tokenSessao)
    || $tokenEnviado === ''
    || !hash_equals($tokenSessao, $tokenEnviado)
) {
    header("Location: cadastrar_animal.php?erro=sessao");
    exit();
}

// Garante que os campos recebidos sejam texto antes de limpá-los.
$tituloRecebido = $_POST['titulo'] ?? null;
$autorRecebido = $_POST['autor'] ?? null;
$statusRecebido = $_POST['status'] ?? null;

if (
    !is_string($tituloRecebido)
    || !is_string($autorRecebido)
    || !is_string($statusRecebido)
) {
    header("Location: cadastrar_animal.php?erro=campos");
    exit();
}

// Remove espaços acidentais no começo e no fim dos dados do pet.
$titulo = trim($tituloRecebido);
$autor = trim($autorRecebido);
$status = $statusRecebido;

// Mantém os campos preenchidos ao retornar para corrigir o formulário.
$_SESSION['pet_formulario'] = [
    'titulo' => $titulo,
    'autor' => $autor,
    'status' => $status,
];

// Os valores do status seguem os códigos já utilizados pela tabela existente.
$statusPermitidos = ['Disponivel', 'Emprestado'];

if ($titulo === '' || $autor === '' || !in_array($status, $statusPermitidos, true)) {
    header("Location: cadastrar_animal.php?erro=campos");
    exit();
}

// Usa parâmetros preparados para que os dados digitados não sejam interpretados como SQL.
include("conexao.php");

try {
    $comando = $conexao->prepare(
        "INSERT INTO livros (titulo, autor, status) VALUES (?, ?, ?)"
    );
    $comando->bind_param("sss", $titulo, $autor, $status);
    $comando->execute();
    $comando->close();
} catch (mysqli_sql_exception $erroBanco) {
    // Registra detalhes técnicos no servidor sem expor informações do banco ao usuário.
    error_log("Falha ao cadastrar pet: " . $erroBanco->getMessage());
    header("Location: cadastrar_animal.php?erro=salvar");
    exit();
}

// Limpa os dados temporários e renova o token após salvar com sucesso.
unset($_SESSION['pet_formulario']);
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// O redirecionamento evita que atualizar a página envie o formulário novamente.
header("Location: animais_cadastrados.php?sucesso=cadastro");
exit();
