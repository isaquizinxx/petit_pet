<?php
require_once __DIR__ . '/includes/bootstrap.php';

$areaLogin = valor_get('area') === 'admin' ? 'admin' : 'site';
$urlLogin = $areaLogin === 'admin' ? 'login.php?area=admin' : 'login.php';

if (usuario_logado()) {
    redirecionar(eh_admin() ? 'admin.php' : 'painel.php');
}

$email = '';
$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_csrf($urlLogin);

    if (login_bloqueado()) {
        flash('aviso', 'Muitas tentativas de login. Aguarde alguns minutos e tente novamente.');
        redirecionar($urlLogin);
    }

    $email = normalizar_email(valor_post('email'));
    $senha = valor_post_bruto('senha');

    if ($email === '' || $senha === '') {
        $mensagemErro = 'Informe o e-mail e a senha para entrar.';
    } else {
        try {
            $usuario = autenticar_usuario($email, $senha);
        } catch (Throwable $erro) {
            error_log('[Petit Pet] Falha ao autenticar: ' . $erro->getMessage());
            $mensagemErro = 'Não foi possível concluir o login agora. Tente novamente.';
            $usuario = null;
        }

        if ($usuario === null) {
            registrar_falha_login();
            $mensagemErro = 'E-mail ou senha inválidos. Confira seus dados e tente novamente.';
        } elseif ($areaLogin === 'admin' && ($usuario['perfil'] ?? 'cliente') !== 'admin') {
            $mensagemErro = 'Esta conta não tem acesso à área administrativa.';
        } else {
            iniciar_sessao_usuario($usuario);
            flash('sucesso', 'Bem-vindo(a), ' . $usuario['nome'] . '!');
            redirecionar(($usuario['perfil'] ?? 'cliente') === 'admin' ? 'admin.php' : 'painel.php');
        }
    }
}

$modoAdmin = $areaLogin === 'admin';
$titulo = $modoAdmin ? 'Área administrativa' : 'Entrar na sua conta';
$subtitulo = $modoAdmin
    ? 'Acesso restrito à equipe da Petit Pet.'
    : 'Acesse seu painel para cuidar dos pets e acompanhar as informações.';
$eyebrow = $modoAdmin ? 'Administração' : 'Área dos clientes';
$containerClasse = 'container container-auth';
$paginaAtiva = 'login';
$descricaoPagina = 'Entre na sua conta da Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<?php if ($mensagemErro !== ''): ?>
    <div class="mensagem mensagem-erro" role="alert">
        <span class="mensagem-icone" aria-hidden="true">!</span>
        <p><?= e($mensagemErro) ?></p>
    </div>
<?php endif; ?>

<form action="<?= e($urlLogin) ?>" method="post" class="formulario formulario-auth">
    <?= csrf_campo() ?>

    <div class="grupo-form">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="voce@email.com"
            maxlength="100" required autocomplete="email" value="<?= e($email) ?>">
    </div>

    <div class="grupo-form">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="Sua senha"
            maxlength="72" required autocomplete="current-password">
    </div>

    <div class="form-acoes">
        <button type="submit" class="btn btn-bloco">Entrar</button>
    </div>
</form>

<?php if ($modoAdmin): ?>
    <div class="dica-demo">
        <p class="dica-demo-titulo">Conta de demonstração</p>
        <p><strong>E-mail:</strong> admin@petitpet.local<br><strong>Senha:</strong> Admin@123</p>
        <p class="dica-demo-nota">Troque a senha antes de publicar o projeto.</p>
    </div>
    <p class="form-rodape">
        <a href="login.php">Entrar como cliente</a> · <a href="index.php">Voltar ao site</a>
    </p>
<?php else: ?>
    <div class="dica-demo">
        <p class="dica-demo-titulo">Conta de demonstração</p>
        <p><strong>E-mail:</strong> cliente@petitpet.local<br><strong>Senha:</strong> Cliente@123</p>
    </div>
    <p class="form-rodape">
        Ainda não tem conta? <a href="cadastro.php">Criar conta</a>
    </p>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
