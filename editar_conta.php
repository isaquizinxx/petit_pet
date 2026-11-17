<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_cliente();

$usuario = buscar_usuario((int) usuario_id());
if (!$usuario) {
    flash('erro', 'Não foi possível carregar sua conta. Entre novamente.');
    redirecionar('logout.php');
}

$dados = [
    'nome' => (string) $usuario['nome'],
    'email' => (string) $usuario['email'],
    'telefone' => (string) ($usuario['telefone'] ?? ''),
];
$erros = [];
$erroAtualizacao = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_csrf('editar_conta.php');

    $dadosPost = dados_cliente_do_post();
    $senhaAtual = valor_post_bruto('senha_atual');
    $erros = validar_edicao_conta_cliente($dadosPost);

    if ($dadosPost['senha'] !== '') {
        if ($senhaAtual === '') {
            $erros['senha_atual'] = 'Informe sua senha atual para definir uma nova senha.';
        } elseif (!senha_atual_confere((int) usuario_id(), $senhaAtual)) {
            $erros['senha_atual'] = 'A senha atual está incorreta.';
        }
    }

    if (!isset($erros['email']) && email_em_uso($dadosPost['email'], (int) usuario_id())) {
        $erros['email'] = 'Este e-mail já pertence a outra conta.';
    }

    if (!$erros) {
        try {
            $senhaHash = $dadosPost['senha'] !== ''
                ? password_hash($dadosPost['senha'], PASSWORD_DEFAULT)
                : null;

            if (!atualizar_cliente((int) usuario_id(), $dadosPost, $senhaHash)) {
                throw new RuntimeException('A conta do cliente não foi atualizada.');
            }

            $_SESSION['nome'] = $dadosPost['nome'];
            flash('sucesso', 'Seus dados foram atualizados com sucesso.');
            redirecionar('editar_conta.php');
        } catch (Throwable $erro) {
            error_log('[Petit Pet] Falha ao atualizar os dados da conta: ' . $erro->getMessage());
            $erroAtualizacao = true;
        }
    }

    $dados = [
        'nome' => $dadosPost['nome'],
        'email' => $dadosPost['email'],
        'telefone' => $dadosPost['telefone'],
    ];
}

$temErro = static fn (string $campo): bool => isset($erros[$campo]);
$attrErro = static function (string $campo) use ($temErro): string {
    return $temErro($campo)
        ? 'aria-invalid="true" aria-describedby="erro-conta-' . e($campo) . '" class="campo-invalido"'
        : '';
};
$mensagemErro = static function (string $campo) use ($erros, $temErro): string {
    return $temErro($campo)
        ? '<p class="erro-campo" id="erro-conta-' . e($campo) . '">' . e($erros[$campo]) . '</p>'
        : '';
};

$titulo = 'Meu cadastro';
$subtitulo = 'Mantenha seus dados de contato atualizados.';
$eyebrow = 'Área do cliente';
$containerClasse = 'container container-form';
$paginaAtiva = 'painel';
$descricaoPagina = 'Atualize seus dados de cadastro na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<?php if ($erros): ?>
    <div class="mensagem mensagem-erro" role="alert">
        <span class="mensagem-icone"><?= icone_svg('circle-alert') ?></span>
        <p>Confira os campos destacados e tente novamente.</p>
    </div>
<?php elseif ($erroAtualizacao): ?>
    <div class="mensagem mensagem-erro" role="alert">
        <span class="mensagem-icone"><?= icone_svg('circle-alert') ?></span>
        <p>Não foi possível atualizar seus dados agora. Tente novamente.</p>
    </div>
<?php endif; ?>

<form action="editar_conta.php" method="post" class="formulario">
    <?= csrf_campo() ?>

    <div class="linha-campos">
        <div class="grupo-form">
            <label for="nome">Nome completo</label>
            <input type="text" id="nome" name="nome" maxlength="100" required autocomplete="name"
                value="<?= e($dados['nome']) ?>" <?= $attrErro('nome') ?>>
            <?= $mensagemErro('nome') ?>
        </div>

        <div class="grupo-form">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" maxlength="100" required autocomplete="email"
                value="<?= e($dados['email']) ?>" <?= $attrErro('email') ?>>
            <?= $mensagemErro('email') ?>
        </div>
    </div>

    <div class="grupo-form">
        <label for="telefone">Telefone <span class="opcional">(opcional)</span></label>
        <input type="tel" id="telefone" name="telefone" maxlength="30" autocomplete="tel"
            value="<?= e($dados['telefone']) ?>" <?= $attrErro('telefone') ?>>
        <?= $mensagemErro('telefone') ?>
    </div>

    <fieldset class="secao-form">
        <legend>Alterar senha <span class="opcional">(opcional)</span></legend>
        <p class="ajuda-bloco">Para trocar a senha, informe a senha atual e preencha os dois campos abaixo.</p>

        <div class="linha-campos">
            <div class="grupo-form">
                <label for="senha_atual">Senha atual</label>
                <input type="password" id="senha_atual" name="senha_atual" maxlength="72"
                    autocomplete="current-password" <?= $attrErro('senha_atual') ?>>
                <?= $mensagemErro('senha_atual') ?>
            </div>

            <div class="grupo-form">
                <label for="senha">Nova senha</label>
                <input type="password" id="senha" name="senha" minlength="8" maxlength="72"
                    autocomplete="new-password" <?= $attrErro('senha') ?>>
                <?= $mensagemErro('senha') ?>
            </div>

            <div class="grupo-form">
                <label for="senha_confirmacao">Confirmar nova senha</label>
                <input type="password" id="senha_confirmacao" name="senha_confirmacao" minlength="8" maxlength="72"
                    autocomplete="new-password" <?= $attrErro('senha_confirmacao') ?>>
                <?= $mensagemErro('senha_confirmacao') ?>
            </div>
        </div>
    </fieldset>

    <div class="form-acoes">
        <button type="submit" class="btn">Salvar alterações</button>
        <a class="btn btn-contorno" href="painel.php">Voltar ao painel</a>
    </div>
</form>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
