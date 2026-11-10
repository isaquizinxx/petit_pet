<?php

// Exige uma sessão válida antes de permitir consultar ou alterar um cadastro.
include("verificar_sessao.php");

// Aceita apenas um identificador inteiro positivo vindo da URL.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header("Location: animais_cadastrados.php?erro=id");
    exit();
}

// Garante que o formulário de edição tenha um token de segurança na sessão.
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Busca o cadastro com uma consulta preparada para evitar injeção SQL.
include("conexao.php");
try {
    $comando = $conexao->prepare(
        "SELECT id, titulo, autor, status FROM livros WHERE id = ?"
    );
    $comando->bind_param("i", $id);
    $comando->execute();
    $comando->bind_result($idPet, $nomePet, $especiePet, $statusPet);
    $encontrado = $comando->fetch();
    $comando->close();
} catch (mysqli_sql_exception $erroBanco) {
    error_log("Falha ao consultar pet para edição: " . $erroBanco->getMessage());
    header("Location: animais_cadastrados.php?erro=consulta");
    exit();
}

// Não exibe um formulário sem que exista um pet correspondente ao identificador.
if (!$encontrado) {
    header("Location: animais_cadastrados.php?erro=nao_encontrado");
    exit();
}

// Exibe somente mensagens previamente definidas para erros de edição.
$mensagensErro = [
    'campos' => 'Confira o nome, a espécie ou raça e a situação do pet.',
    'salvar' => 'Não foi possível salvar as alterações. Tente novamente.',
];
$codigoErro = $_GET['erro'] ?? '';
$mensagemErro = is_string($codigoErro) ? ($mensagensErro[$codigoErro] ?? '') : '';

// Converte os códigos legados do banco em opções compreensíveis na tela.
$situacaoPet = $statusPet === 'Emprestado' ? 'Em atendimento' : 'Disponível';

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar pet | Petit Pet</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Editar pet</h1>
        <p class="subtitulo">Atualize as informações do pet abaixo.</p>

        <!-- Mostra erros de validação sem descartar os dados existentes no cadastro. -->
        <?php if ($mensagemErro !== '') { ?>
            <div class="mensagem-erro" role="alert"><?php echo htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php } ?>

        <!-- O token associa a alteração à sessão que carregou este formulário. -->
        <form action="atualizar_animal.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="id" value="<?php echo (int) $idPet; ?>">

            <!-- Os nomes dos campos mantêm compatibilidade com as colunas existentes no banco. -->
            <div class="form-group">
                <label for="titulo">Nome do pet</label>
                <input type="text" id="titulo" name="titulo" maxlength="100" value="<?php echo htmlspecialchars($nomePet, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="autor">Espécie ou raça</label>
                <input type="text" id="autor" name="autor" maxlength="100" value="<?php echo htmlspecialchars($especiePet, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Situação</label>
                <select id="status" name="status" required>
                    <option value="Disponivel" <?php echo $situacaoPet === 'Disponível' ? 'selected' : ''; ?>>Disponível</option>
                    <option value="Emprestado" <?php echo $situacaoPet === 'Em atendimento' ? 'selected' : ''; ?>>Em atendimento</option>
                </select>
            </div>

            <!-- Envia os novos dados ao handler que valida e grava a alteração. -->
            <button type="submit" class="btn btn-block">Salvar alterações</button>
        </form>

        <!-- Retorna à listagem sem salvar nenhuma alteração. -->
        <a href="animais_cadastrados.php" class="btn btn-voltar">Voltar aos pets</a>
    </div>
</body>

</html>
