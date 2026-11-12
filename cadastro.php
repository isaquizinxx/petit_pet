<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (usuario_logado()) {
    redirecionar(eh_admin() ? 'admin.php' : 'painel.php');
}

$dados = [
    'nome' => '',
    'email' => '',
    'telefone' => '',
];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_csrf('cadastro.php');

    $dadosPost = dados_cliente_do_post();
    $erros = validar_cadastro_cliente($dadosPost);

    if (!isset($erros['email']) && email_em_uso($dadosPost['email'])) {
        $erros['email'] = 'Este e-mail já está cadastrado. Tente entrar na sua conta.';
    }

    if (!$erros) {
        try {
            $id = criar_cliente($dadosPost, password_hash($dadosPost['senha'], PASSWORD_DEFAULT));

            iniciar_sessao_usuario([
                'id' => $id,
                'nome' => $dadosPost['nome'],
                'perfil' => 'cliente',
            ]);

            flash('sucesso', 'Sua conta foi criada. Agora você já pode cadastrar seu pet!');
            redirecionar('painel.php');
        } catch (Throwable $erro) {
            error_log('[Petit Pet] Falha ao criar conta: ' . $erro->getMessage());
            flash('erro', 'Não foi possível criar sua conta agora. Tente novamente.');
        }
    }

    $dados = [
        'nome' => $dadosPost['nome'],
        'email' => $dadosPost['email'],
        'telefone' => $dadosPost['telefone'],
    ];
}

$valor = static fn (string $campo): string => e($dados[$campo] ?? '');
$temErro = static fn (string $campo): bool => isset($erros[$campo]);
$attrErro = static function (string $campo) use ($temErro): string {
    return $temErro($campo)
        ? 'aria-invalid="true" aria-describedby="erro-' . e($campo) . '" class="campo-invalido"'
        : '';
};
$mensagemErro = static function (string $campo) use ($erros, $temErro): string {
    return $temErro($campo)
        ? '<p class="erro-campo" id="erro-' . e($campo) . '">' . e($erros[$campo]) . '</p>'
        : '';
};

$titulo = 'Faça parte da Petit Pet';
$subtitulo = 'Crie sua conta para cadastrar seus pets e acompanhar as informações.';
$eyebrow = 'Área dos clientes';
$containerClasse = 'container container-auth';
$paginaAtiva = 'cadastro';
$descricaoPagina = 'Crie sua conta na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<?php if ($erros): ?>
    <div class="mensagem mensagem-erro" role="alert">
        <span class="mensagem-icone" aria-hidden="true">!</span>
        <p>Confira os campos destacados e tente novamente.</p>
    </div>
<?php endif; ?>

<form action="cadastro.php" method="post" class="formulario formulario-auth">
    <?= csrf_campo() ?>

    <div class="grupo-form">
        <label for="nome">Nome completo</label>
        <input type="text" id="nome" name="nome" placeholder="Seu nome" maxlength="100"
            required autocomplete="name" value="<?= $valor('nome') ?>" <?= $attrErro('nome') ?>>
        <?= $mensagemErro('nome') ?>
    </div>

    <div class="grupo-form">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="voce@email.com" maxlength="100"
            required autocomplete="email" value="<?= $valor('email') ?>" <?= $attrErro('email') ?>>
        <?= $mensagemErro('email') ?>
    </div>

    <div class="grupo-form">
        <label for="telefone">Telefone <span class="opcional">(opcional)</span></label>
        <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" maxlength="30"
            autocomplete="tel" value="<?= $valor('telefone') ?>" <?= $attrErro('telefone') ?>>
        <p class="ajuda">Usado para identificar o contato do tutor dos seus pets.</p>
        <?= $mensagemErro('telefone') ?>
    </div>

    <div class="linha-campos">
        <div class="grupo-form">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" placeholder="Mínimo de 8 caracteres"
                minlength="8" maxlength="72" required autocomplete="new-password" <?= $attrErro('senha') ?>>
            <?= $mensagemErro('senha') ?>
        </div>

        <div class="grupo-form">
            <label for="senha_confirmacao">Confirmar senha</label>
            <input type="password" id="senha_confirmacao" name="senha_confirmacao" placeholder="Repita a senha"
                minlength="8" maxlength="72" required autocomplete="new-password" <?= $attrErro('senha_confirmacao') ?>>
            <?= $mensagemErro('senha_confirmacao') ?>
        </div>
    </div>

    <div class="form-acoes">
        <button type="submit" class="btn btn-bloco">Criar minha conta</button>
    </div>
</form>

<p class="form-rodape">Já tem conta? <a href="login.php">Entrar</a></p>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
