<?php

// Exibe os pets cadastrados e permite editar ou remover seus registros.
// Protege a página: só usuários logados podem consultar os pets.
include("verificar_sessao.php");

// inclui a conexão do banco de dados
include("conexao.php");

// Cria um token reutilizável pelos formulários de edição e exclusão desta sessão.
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Consulta os registros cadastrados.
$sql = "SELECT * FROM livros";

// Executa a consulta e guarda o resultado 
$resultado = mysqli_query($conexao, $sql);

// Converte códigos de retorno em mensagens fixas para não exibir conteúdo da URL.
$mensagensSucesso = [
    'cadastro' => 'Pet cadastrado com sucesso!',
    'atualizacao' => 'Cadastro do pet atualizado com sucesso!',
    'exclusao' => 'Pet removido com sucesso!',
];
$mensagensErro = [
    'metodo' => 'A operação solicitada não é válida.',
    'sessao' => 'Sua sessão expirou. Atualize a página e tente novamente.',
    'id' => 'O identificador do pet não é válido.',
    'consulta' => 'Não foi possível consultar os dados do pet.',
    'nao_encontrado' => 'O pet solicitado não foi encontrado.',
    'campos' => 'Os dados enviados não são válidos.',
    'excluir' => 'Não foi possível remover o pet. Tente novamente.',
];
$codigoSucesso = $_GET['sucesso'] ?? '';
$codigoErro = $_GET['erro'] ?? '';
$mensagemSucesso = is_string($codigoSucesso) ? ($mensagensSucesso[$codigoSucesso] ?? '') : '';
$mensagemErro = is_string($codigoErro) ? ($mensagensErro[$codigoErro] ?? '') : '';

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pets cadastrados | Petit Pet</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Container mais largo para acomodar a tabela -->
    <div class="container container-largo">
        <h1>Pets cadastrados</h1>
        <p class="subtitulo">Acompanhe os amigos e a situação de cada atendimento.</p>

        <!-- Exibe o resultado da última operação sem confiar no texto da URL. -->
        <?php if ($mensagemSucesso !== '') { ?>
            <div class="mensagem-sucesso" role="status"><?php echo htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php } ?>
        <?php if ($mensagemErro !== '') { ?>
            <div class="mensagem-erro" role="alert"><?php echo htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php } ?>

        <!-- Tabela com os pets cadastrados -->
        <table class="tabela-livros">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Espécie ou raça</th>
                <th>Situação</th>
                <th>Ações</th>
            </tr>
            <?php
            while ($pet = mysqli_fetch_assoc($resultado)) {
                ?>
                <tr>
                    <td><?php echo (int) $pet['id']; ?></td>
                    <td><?php echo htmlspecialchars($pet['titulo'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($pet['autor'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($pet['status'] === 'Emprestado' ? 'Em atendimento' : 'Disponível', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="acoes">
                        <a href="editar_animal.php?id=<?php echo (int) $pet['id']; ?>" class="link-editar">
                            Editar</a>

                        <!-- Exclusão por POST e token CSRF evita remover registros por simples visita a um link. -->
                        <form action="excluir_animal.php" method="post" onsubmit="return confirm('Tem certeza que deseja remover este pet?')">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="id" value="<?php echo (int) $pet['id']; ?>">
                            <button type="submit" class="link-excluir">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php if (mysqli_num_rows($resultado) === 0) { ?>
            <p class="subtitulo">Ainda não há pets cadastrados. Comece adicionando um novo amigo.</p>
        <?php } ?>
        <a href="painel.php" class="btn btn-voltar">Voltar ao painel</a>
    </div>

</body>

</html>