<?php
require_once __DIR__ . '/includes/bootstrap.php';

$destaques = [];
$estatisticas = null;

try {
    $destaques = listar_pets();
    $destaques = array_slice($destaques, 0, 3);
    $estatisticas = estatisticas_pets();
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao carregar a página inicial: ' . $erro->getMessage());
}

$titulo = '';
$containerClasse = 'container container-largo';
$paginaAtiva = 'inicio';
$descricaoPagina = 'Petit Pet: cadastro e cuidado dos seus pets com carinho e organização.';

require __DIR__ . '/includes/layout/header.php';
?>

<section class="hero">
    <div class="hero-texto">
        <p class="eyebrow">Cuidado de perto</p>
        <h1>Um cantinho organizado para quem você ama.</h1>
        <p class="hero-descricao">
            A Petit Pet reúne o cadastro dos seus pets em um só lugar: informações,
            contato do tutor e situação de cada atendimento, com o carinho que eles merecem.
        </p>

        <div class="hero-acoes">
            <a class="btn btn-grande" href="pets.php">Conhecer os pets</a>
            <?php if (usuario_logado()): ?>
                <a class="btn btn-grande btn-contorno" href="<?= eh_admin() ? 'admin.php' : 'painel.php' ?>">
                    <?= eh_admin() ? 'Ir para a administração' : 'Meu painel' ?>
                </a>
            <?php else: ?>
                <a class="btn btn-grande btn-contorno" href="cadastro.php">Criar minha conta</a>
            <?php endif; ?>
        </div>

        <ul class="hero-lista">
            <li><?= icone_svg('heart') ?> Cadastro simples e rápido</li>
            <li><?= icone_svg('paw') ?> Acompanhe cada pet de perto</li>
            <li><?= icone_svg('clipboard-list') ?> Informações sempre à mão</li>
        </ul>
    </div>

    <aside class="hero-card" aria-label="Resumo da Petit Pet">
        <div class="hero-card-topo">
            <span class="hero-card-icone"><?= icone_svg('paw') ?></span>
            <p class="hero-card-titulo">Petit Pet</p>
            <p class="hero-card-texto">Cuidado que vira carinho, todos os dias.</p>
        </div>

        <?php if ($estatisticas !== null): ?>
            <dl class="hero-numeros">
                <div>
                    <dt>Pets cadastrados</dt>
                    <dd><?= (int) $estatisticas['total'] ?></dd>
                </div>
                <div>
                    <dt>Disponíveis</dt>
                    <dd><?= (int) $estatisticas['disponiveis'] ?></dd>
                </div>
                <div>
                    <dt>Tutores</dt>
                    <dd><?= (int) $estatisticas['tutores'] ?></dd>
                </div>
            </dl>
        <?php endif; ?>

        <a class="hero-card-link" href="pets.php">Ver todos os pets <?= icone_svg('arrow-right') ?></a>
    </aside>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <div>
            <p class="eyebrow">Nossos amigos</p>
            <h2>Pets em destaque</h2>
        </div>
        <a class="link-seta" href="pets.php">Ver todos <?= icone_svg('arrow-right') ?></a>
    </div>

    <?php if ($destaques): ?>
        <div class="grade-pets">
            <?php foreach ($destaques as $pet): ?>
                <?php $petCard = $pet; require __DIR__ . '/includes/cartao_pet.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="vazio">
            <p>Nenhum pet cadastrado por enquanto.</p>
            <a class="btn btn-contorno" href="cadastro.php">Comece criando sua conta</a>
        </div>
    <?php endif; ?>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <div>
            <p class="eyebrow">Simples assim</p>
            <h2>Como funciona</h2>
        </div>
    </div>

    <div class="passos">
        <article class="passo">
            <span class="passo-numero" aria-hidden="true">1</span>
            <h3>Crie sua conta</h3>
            <p>Um cadastro rápido para você acompanhar os seus pets com segurança.</p>
        </article>
        <article class="passo">
            <span class="passo-numero" aria-hidden="true">2</span>
            <h3>Cadastre seu pet</h3>
            <p>Informe nome, espécie, idade e os cuidados especiais de cada amigo.</p>
        </article>
        <article class="passo">
            <span class="passo-numero" aria-hidden="true">3</span>
            <h3>Acompanhe tudo</h3>
            <p>Consulte e atualize as informações sempre que precisar, em poucos cliques.</p>
        </article>
    </div>
</section>

<section class="faixa-cta">
    <div>
        <h2>Pronto para começar?</h2>
        <p><?= usuario_logado() ? 'Acesse seu painel e cuide dos seus pets.' : 'Crie sua conta gratuita e cadastre seu primeiro pet.' ?></p>
    </div>
    <?php if (usuario_logado()): ?>
        <a class="btn btn-grande" href="<?= eh_admin() ? 'admin.php' : 'painel.php' ?>">Acessar meu painel</a>
    <?php else: ?>
        <a class="btn btn-grande" href="cadastro.php">Criar minha conta</a>
    <?php endif; ?>
</section>

<p class="rodape-admin">
    É da equipe Petit Pet?
    <a href="login.php?area=admin">Acessar a área administrativa</a>
</p>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
