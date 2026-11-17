<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_admin();

$busca = valor_get('busca');
$editarId = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
$clienteEdicao = null;
$errosCliente = [];

$form = [
    'nome' => '',
    'email' => '',
    'telefone' => '',
];

// Carrega o cliente escolhido para edição (GET).
if ($editarId && $editarId > 0) {
    $clienteEdicao = buscar_usuario((int) $editarId);

    if (!$clienteEdicao || ($clienteEdicao['perfil'] ?? '') !== 'cliente') {
        flash('erro', 'Cliente não encontrado para edição.');
        redirecionar('admin_clientes.php');
    }

    $form = [
        'nome' => (string) $clienteEdicao['nome'],
        'email' => (string) $clienteEdicao['email'],
        'telefone' => (string) ($clienteEdicao['telefone'] ?? ''),
    ];
}

// Ações do formulário e da lista.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_csrf('admin_clientes.php');

    $acao = valor_post('acao');

    try {
        if ($acao === 'criar' || $acao === 'atualizar') {
            $dados = dados_cliente_do_post();
            $editando = $acao === 'atualizar';
            $idEdicao = $editando ? (int) filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) : 0;

            if ($editando && $idEdicao < 1) {
                flash('erro', 'Cliente inválido para atualização.');
                redirecionar('admin_clientes.php');
            }

            $errosCliente = validar_cliente_admin($dados, !$editando);

            if (!$editando
                && !isset($errosCliente['senha'])
                && $dados['senha'] !== $dados['senha_confirmacao']) {
                $errosCliente['senha_confirmacao'] = 'As senhas não coincidem.';
            }

            if (!isset($errosCliente['email']) && email_em_uso($dados['email'], $editando ? $idEdicao : null)) {
                $errosCliente['email'] = 'Este e-mail já pertence a outra conta.';
            }

            if (!$errosCliente) {
                if ($editando) {
                    $hash = $dados['senha'] !== '' ? password_hash($dados['senha'], PASSWORD_DEFAULT) : null;
                    atualizar_cliente($idEdicao, $dados, $hash);
                    flash('sucesso', 'Cliente atualizado com sucesso.');
                } else {
                    criar_cliente($dados, password_hash($dados['senha'], PASSWORD_DEFAULT));
                    flash('sucesso', 'Cliente cadastrado com sucesso.');
                }

                redirecionar('admin_clientes.php');
            }

            // Mantém o formulário preenchido quando há erros.
            $form = [
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'telefone' => $dados['telefone'],
            ];
            $clienteEdicao = $editando ? buscar_usuario($idEdicao) : null;
        } elseif ($acao === 'excluir') {
            $idExcluir = (int) filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

            if ($idExcluir === (int) usuario_id()) {
                flash('erro', 'Você não pode excluir a própria conta.');
            } elseif ($idExcluir > 0 && excluir_cliente($idExcluir)) {
                flash('sucesso', 'Cliente removido. Os pets continuam cadastrados com os dados do tutor.');
            } else {
                flash('erro', 'Cliente não encontrado ou não pode ser removido.');
            }

            redirecionar('admin_clientes.php');
        } else {
            flash('erro', 'Ação não reconhecida.');
            redirecionar('admin_clientes.php');
        }
    } catch (Throwable $erro) {
        error_log('[Petit Pet] Falha ao gerenciar cliente: ' . $erro->getMessage());
        flash('erro', 'Não foi possível concluir a operação agora. Tente novamente.');
    }
}

$clientes = [];
$erroConsulta = false;

try {
    $clientes = listar_clientes($busca);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao listar clientes: ' . $erro->getMessage());
    $erroConsulta = true;
}

$temErro = static fn (string $campo): bool => isset($errosCliente[$campo]);
$attrErro = static function (string $campo) use ($temErro): string {
    return $temErro($campo)
        ? 'aria-invalid="true" aria-describedby="erro-cliente-' . e($campo) . '" class="campo-invalido"'
        : '';
};
$mensagemErro = static function (string $campo) use ($errosCliente, $temErro): string {
    return $temErro($campo)
        ? '<p class="erro-campo" id="erro-cliente-' . e($campo) . '">' . e($errosCliente[$campo]) . '</p>'
        : '';
};

$editando = $clienteEdicao !== null;
$titulo = 'Clientes';
$subtitulo = 'Cadastre, atualize e remova as contas de clientes da Petit Pet.';
$eyebrow = 'Administração';
$paginaAtiva = 'admin-clientes';
$area = 'admin';
$descricaoPagina = 'Gerenciamento de clientes na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<?php if ($errosCliente): ?>
    <div class="mensagem mensagem-erro" role="alert">
        <span class="mensagem-icone"><?= icone_svg('circle-alert') ?></span>
        <p>Confira os campos destacados e tente novamente.</p>
    </div>
<?php endif; ?>

<section class="painel-bloco">
    <div class="painel-bloco-cabecalho">
        <h2><?= $editando ? 'Editar cliente' : 'Novo cliente' ?></h2>
        <?php if ($editando): ?>
            <a class="link-seta" href="admin_clientes.php">Cancelar edição</a>
        <?php endif; ?>
    </div>

    <form action="admin_clientes.php" method="post" class="formulario formulario-compacto">
        <?= csrf_campo() ?>
        <input type="hidden" name="acao" value="<?= $editando ? 'atualizar' : 'criar' ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= (int) $clienteEdicao['id'] ?>">
        <?php endif; ?>

        <div class="linha-campos">
            <div class="grupo-form">
                <label for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome" placeholder="Nome do cliente" maxlength="100"
                    required value="<?= e($form['nome']) ?>" <?= $attrErro('nome') ?>>
                <?= $mensagemErro('nome') ?>
            </div>

            <div class="grupo-form">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="cliente@email.com" maxlength="100"
                    required value="<?= e($form['email']) ?>" <?= $attrErro('email') ?>>
                <?= $mensagemErro('email') ?>
            </div>
        </div>

        <div class="linha-campos">
            <div class="grupo-form">
                <label for="telefone">Telefone <span class="opcional">(opcional)</span></label>
                <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" maxlength="30"
                    value="<?= e($form['telefone']) ?>" <?= $attrErro('telefone') ?>>
                <?= $mensagemErro('telefone') ?>
            </div>

            <div class="grupo-form">
                <label for="senha">Senha <?= $editando ? '<span class="opcional">(deixe em branco para manter)</span>' : '' ?></label>
                <input type="password" id="senha" name="senha" placeholder="Mínimo de 8 caracteres"
                    minlength="8" maxlength="72" <?= $editando ? '' : 'required' ?> <?= $attrErro('senha') ?>>
                <?= $mensagemErro('senha') ?>
            </div>
        </div>

        <?php if (!$editando): ?>
            <div class="grupo-form">
                <label for="senha_confirmacao">Confirmar senha</label>
                <input type="password" id="senha_confirmacao" name="senha_confirmacao" placeholder="Repita a senha"
                    minlength="8" maxlength="72" required <?= $attrErro('senha_confirmacao') ?>>
                <?= $mensagemErro('senha_confirmacao') ?>
            </div>
        <?php endif; ?>

        <div class="form-acoes">
            <button type="submit" class="btn"><?= $editando ? 'Salvar alterações' : 'Cadastrar cliente' ?></button>
            <?php if ($editando): ?>
                <a class="btn btn-contorno" href="admin_clientes.php">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <div>
            <p class="eyebrow">Contas de clientes</p>
            <h2>Clientes cadastrados</h2>
        </div>

        <form class="busca-simples" action="admin_clientes.php" method="get" role="search">
            <label class="rotulo-oculto" for="busca">Buscar cliente</label>
            <input type="search" id="busca" name="busca" placeholder="Buscar por nome ou e-mail..."
                value="<?= e($busca) ?>">
            <button type="submit" class="btn btn-pequeno">Buscar</button>
            <?php if ($busca !== ''): ?>
                <a class="btn btn-pequeno btn-contorno" href="admin_clientes.php">Limpar</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($erroConsulta): ?>
        <div class="mensagem mensagem-erro" role="alert">
            <span class="mensagem-icone"><?= icone_svg('circle-alert') ?></span>
            <p>Não foi possível carregar os clientes agora. Tente atualizar a página.</p>
        </div>
    <?php elseif ($clientes): ?>
        <div class="tabela-wrapper">
            <table class="tabela">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">E-mail</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">Pets</th>
                        <th scope="col"><span class="rotulo-oculto">Ações</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td data-rotulo="Nome"><strong><?= e($cliente['nome']) ?></strong></td>
                            <td data-rotulo="E-mail"><?= e($cliente['email']) ?></td>
                            <td data-rotulo="Telefone"><?= e($cliente['telefone'] ?: '—') ?></td>
                            <td data-rotulo="Pets"><?= (int) $cliente['total_pets'] ?></td>
                            <td class="tabela-acoes">
                                <a class="btn btn-pequeno btn-contorno" href="admin_clientes.php?editar=<?= (int) $cliente['id'] ?>">Editar</a>
                                <form action="admin_clientes.php" method="post"
                                    data-confirmar="Remover a conta de <?= e($cliente['nome']) ?>? Os pets continuam cadastrados com os dados do tutor.">
                                    <?= csrf_campo() ?>
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="<?= (int) $cliente['id'] ?>">
                                    <button type="submit" class="btn btn-pequeno btn-perigo">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="vazio">
            <span class="vazio-icone"><?= icone_svg('user') ?></span>
            <h2>Nenhum cliente encontrado</h2>
            <p><?= $busca !== '' ? 'Tente outra busca.' : 'Cadastre a primeira conta de cliente acima.' ?></p>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
