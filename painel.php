<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_cliente();

$usuario = buscar_usuario((int) usuario_id());
if (!$usuario) {
    flash('erro', 'Não foi possível carregar sua conta. Entre novamente.');
    redirecionar('logout.php');
}

$pets = [];
$erroConsulta = false;

try {
    $pets = pets_do_cliente((int) usuario_id());
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao carregar pets do cliente: ' . $erro->getMessage());
    $erroConsulta = true;
}

$totalPets = count($pets);
$disponiveis = count(array_filter($pets, static fn (array $pet): bool => $pet['status'] === 'disponivel'));
$emAtendimento = $totalPets - $disponiveis;

$titulo = 'Olá, ' . $usuario['nome'] . '!';
$subtitulo = 'Este é o seu espaço na Petit Pet. Cuide das informações dos seus pets em poucos cliques.';
$eyebrow = 'Meu painel';
$paginaAtiva = 'painel';
$descricaoPagina = 'Painel do cliente na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<section class="resumo-cliente" aria-label="Resumo dos seus pets">
    <div class="resumo-item">
        <span class="resumo-numero"><?= $totalPets ?></span>
        <span class="resumo-rotulo"><?= $totalPets === 1 ? 'pet cadastrado' : 'pets cadastrados' ?></span>
    </div>
    <div class="resumo-item">
        <span class="resumo-numero"><?= $disponiveis ?></span>
        <span class="resumo-rotulo">disponíveis</span>
    </div>
    <div class="resumo-item">
        <span class="resumo-numero"><?= $emAtendimento ?></span>
        <span class="resumo-rotulo">em atendimento</span>
    </div>
</section>

<section class="acoes-rapidas" aria-label="Ações rápidas">
    <a class="acao-card" href="cadastrar_pet.php">
        <span class="acao-icone"><?= icone_svg('plus') ?></span>
        <span class="acao-titulo">Cadastrar pet</span>
        <span class="acao-texto">Adicione um novo amigo ao seu painel.</span>
    </a>
    <a class="acao-card" href="pets.php">
        <span class="acao-icone"><?= icone_svg('search') ?></span>
        <span class="acao-titulo">Explorar pets</span>
        <span class="acao-texto">Veja todos os pets cadastrados na Petit Pet.</span>
    </a>
    <a class="acao-card" href="editar_conta.php">
        <span class="acao-icone"><?= icone_svg('user') ?></span>
        <span class="acao-titulo">Meu cadastro</span>
        <span class="acao-texto">Atualize seus dados pessoais e sua senha.</span>
    </a>
    <a class="acao-card acao-card--sair" href="logout.php">
        <span class="acao-icone"><?= icone_svg('log-out') ?></span>
        <span class="acao-titulo">Sair da conta</span>
        <span class="acao-texto">Encerrar a sessão com segurança.</span>
    </a>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <div>
            <p class="eyebrow">Meus amigos</p>
            <h2>Meus pets</h2>
        </div>
        <a class="link-seta" href="cadastrar_pet.php">Cadastrar novo <?= icone_svg('arrow-right') ?></a>
    </div>

    <?php if ($erroConsulta): ?>
        <div class="mensagem mensagem-erro" role="alert">
            <span class="mensagem-icone"><?= icone_svg('circle-alert') ?></span>
            <p>Não foi possível carregar seus pets agora. Tente atualizar a página.</p>
        </div>
    <?php elseif ($pets): ?>
        <div class="grade-pets">
            <?php foreach ($pets as $pet): ?>
                <div class="pet-card-wrapper">
                    <?php $petCard = $pet; require __DIR__ . '/includes/cartao_pet.php'; ?>
                    <div class="pet-card-acoes">
                        <a class="btn btn-pequeno btn-contorno" href="editar_pet.php?id=<?= (int) $pet['id'] ?>">Editar</a>
                        <form action="excluir_pet.php" method="post" data-confirmar="Remover <?= e($pet['nome']) ?> do seu cadastro?">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= (int) $pet['id'] ?>">
                            <button type="submit" class="btn btn-pequeno btn-perigo">Excluir</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="vazio">
            <span class="vazio-icone"><?= icone_svg('paw') ?></span>
            <h2>Você ainda não cadastrou nenhum pet</h2>
            <p>Comece agora e mantenha as informações do seu amigo sempre organizadas.</p>
            <a class="btn" href="cadastrar_pet.php">Cadastrar meu primeiro pet</a>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
