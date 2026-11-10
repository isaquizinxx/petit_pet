<?php
 
// Protege o formulário e permite gerar um token CSRF associado à sessão autenticada.
include("verificar_sessao.php");

// Cria o token uma vez por sessão; o handler valida esse mesmo valor no envio.
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Recupera os dados anteriores depois de uma validação com erro.
$dadosPet = $_SESSION['pet_formulario'] ?? [];
unset($_SESSION['pet_formulario']);

// Exibe apenas mensagens conhecidas, sem imprimir valores vindos diretamente da URL.
$mensagensErro = [
    'metodo' => 'Envie o cadastro pelo formulário.',
    'sessao' => 'Sua sessão do formulário expirou. Confira os dados e tente novamente.',
    'campos' => 'Preencha o nome, a espécie ou raça e uma situação válida.',
    'salvar' => 'Não foi possível cadastrar o pet agora. Tente novamente.',
];
$codigoErro = $_GET['erro'] ?? '';
$mensagemErro = is_string($codigoErro) ? ($mensagensErro[$codigoErro] ?? '') : '';
?>
 
<!DOCTYPE html>
<html lang="pt-BR">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar pet | Petit Pet</title>
    <link rel="stylesheet" href="style.css">
</head>
 
<body>
    <div class="container">
        <h1>Cadastrar pet</h1>
        <p class="subtitulo">Conte um pouquinho sobre o novo amigo da Petit Pet.</p>

        <!-- Mensagens de validação e falha do cadastro são exibidas junto ao formulário. -->
        <?php if ($mensagemErro !== '') { ?>
            <div class="mensagem-erro" role="alert"><?php echo htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php } ?>

        <!-- Envia o token CSRF com os dados para validar a origem do cadastro. -->
        <form action="salvar_animal.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <!-- O nome do pet é salvo no campo legado "titulo" da tabela atual. -->
            <div class="form-group">
                <label for="titulo">Nome do pet</label>
                <input type="text" id="titulo" name="titulo"
                    placeholder="Ex.: Mel" maxlength="100" value="<?php echo htmlspecialchars($dadosPet['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <!-- A espécie ou raça ocupa o campo legado "autor", sem exigir migração do banco. -->
            <div class="form-group">
                <label for="autor">Espécie ou raça</label>
                <input type="text" id="autor" name="autor"
                    placeholder="Ex.: cachorro, golden retriever" maxlength="100" value="<?php echo htmlspecialchars($dadosPet['autor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <!-- Os valores enviados permanecem compatíveis com os registros existentes. -->
            <div class="form-group">
                <label for="status">Situação</label>
                <select name="status" id="status" required>
                    <option value="Disponivel" <?php echo ($dadosPet['status'] ?? '') === 'Disponivel' ? 'selected' : ''; ?>>Disponível</option>
                    <option value="Emprestado" <?php echo ($dadosPet['status'] ?? '') === 'Emprestado' ? 'selected' : ''; ?>>Em atendimento</option>
                </select>
            </div>

            <!-- O botão envia o cadastro ao handler seguro do sistema. -->
            <button type="submit" class="btn btn-block btn-sucesso">
                Cadastrar pet
            </button>
        </form>

        <!-- Permite retornar ao painel sem enviar o formulário. -->
        <a href="painel.php" class="btn btn-voltar">
            Voltar ao painel
        </a>
    </div>
</body>
 
</html>
 