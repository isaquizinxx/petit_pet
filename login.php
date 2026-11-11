<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (usuario_logado()) {
    redirecionar('painel.php');
}

$mensagemSucesso = '';
if (($_GET['sucesso'] ?? '') === 'logout') {
    $mensagemSucesso = 'Você saiu da sua conta com segurança.';
}

$titulo = 'Entrar na sua conta';
$subtitulo = 'Acesse o painel para cuidar dos pets e acompanhar os atendimentos.';
$containerClasse = 'container container-auth';
$paginaAtiva = 'login';
$descricaoPagina = 'Entre na sua conta da Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<?php if ($mensagemSucesso !== ''): ?>
    <div class="mensagem mensagem-sucesso" role="status">
        <span class="mensagem-icone" aria-hidden="true">✓</span>
        <p><?= e($mensagemSucesso) ?></p>
    </div>
<?php endif; ?>

<form action="autenticar.php" method="post" class="formulario">
    <?= csrf_campo() ?>

    <div class="grupo-form">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="voce@email.com"
            autocomplete="email" maxlength="100" required>
    </div>

    <div class="grupo-form">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="Digite sua senha"
            autocomplete="current-password" required>
    </div>

    <button type="submit" class="btn btn-bloco">Entrar</button>
</form>

<div class="rodape-form">
    <p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
</div>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
