<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (usuario_logado()) {
    redirecionar('painel.php');
}

$dados = $_SESSION['cadastro_dados'] ?? [];
$erros = $_SESSION['cadastro_erros'] ?? [];
unset($_SESSION['cadastro_dados'], $_SESSION['cadastro_erros']);

if (!is_array($dados)) {
    $dados = [];
}
if (!is_array($erros)) {
    $erros = [];
}

$valor = static fn (string $campo): string => e($dados[$campo] ?? '');
$temErro = static fn (string $campo): bool => isset($erros[$campo]);
$mensagemErro = static function (string $campo) use ($erros, $temErro): string {
    return $temErro($campo)
        ? '<p class="erro-campo" id="erro-' . e($campo) . '">' . e($erros[$campo]) . '</p>'
        : '';
};

$titulo = 'Faça parte da Petit Pet';
$subtitulo = 'Crie sua conta para acompanhar o cuidado com os seus pets.';
$containerClasse = 'container container-auth';
$paginaAtiva = 'cadastro';
$descricaoPagina = 'Crie sua conta na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<form action="salvar_usuario.php" method="post" class="formulario">
    <?= csrf_campo() ?>

    <div class="grupo-form">
        <label for="nome">Seu nome</label>
        <input type="text" id="nome" name="nome" placeholder="Como podemos chamar você?"
            autocomplete="name" maxlength="100" required
            value="<?= $valor('nome') ?>"
            <?= $temErro('nome') ? 'aria-invalid="true" aria-describedby="erro-nome" class="campo-invalido"' : '' ?>>
        <?= $mensagemErro('nome') ?>
    </div>

    <div class="grupo-form">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="voce@email.com"
            autocomplete="email" maxlength="100" required
            value="<?= $valor('email') ?>"
            <?= $temErro('email') ? 'aria-invalid="true" aria-describedby="erro-email" class="campo-invalido"' : '' ?>>
        <?= $mensagemErro('email') ?>
    </div>

    <div class="grupo-form">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="Crie uma senha segura"
            autocomplete="new-password" minlength="8" maxlength="72" required
            <?= $temErro('senha') ? 'aria-invalid="true" aria-describedby="erro-senha" class="campo-invalido"' : '' ?>>
        <p class="ajuda">Use entre 8 e 72 caracteres.</p>
        <?= $mensagemErro('senha') ?>
    </div>

    <div class="grupo-form">
        <label for="senha_confirmacao">Confirme a senha</label>
        <input type="password" id="senha_confirmacao" name="senha_confirmacao" placeholder="Repita a senha"
            autocomplete="new-password" minlength="8" maxlength="72" required
            <?= $temErro('senha_confirmacao') ? 'aria-invalid="true" aria-describedby="erro-senha_confirmacao" class="campo-invalido"' : '' ?>>
        <?= $mensagemErro('senha_confirmacao') ?>
    </div>

    <button type="submit" class="btn btn-bloco">Criar conta</button>
</form>

<div class="rodape-form">
    <p>Já tem conta? <a href="login.php">Fazer login</a></p>
</div>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
