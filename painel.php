<?php

// Painel principal da Petit Pet: atalhos para cadastrar e consultar pets.
// Inclui a verificação de sessão(protege a página de acesso não
// autorizado)
include("verificar_sessao.php");

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel | Petit Pet</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Olá, <?php echo htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <p class="subtitulo">Bem-vindo à Petit Pet. O que vamos fazer hoje?</p>
        <div class="painel-cards">
            <a href="cadastrar_animal.php" class="card-link">🐾 Cadastrar pet</a>
            <a href="animais_cadastrados.php" class="card-link">♡ Ver pets cadastrados</a>
            <a href="logout.php" class="card-link card-sair">Sair da conta</a>
        </div>
        <div class="dica-navegacao">
            <strong>Cuidado de perto:</strong>
            cadastre um pet para manter as informações e o atendimento organizados.
        </div>
    </div>
</body>

</html>