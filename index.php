<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (usuario_logado()) {
    redirecionar('painel.php');
}

$estatisticas = null;
try {
    $estatisticas = estatisticas_pets();
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao carregar estatísticas da página inicial: ' . $erro->getMessage());
}

$titulo = '';
$containerClasse = 'container container-largo';
$paginaAtiva = 'inicio';
$descricaoPagina = 'Petit Pet: cadastro e acompanhamento de pets com cuidado, carinho e organização.';

require __DIR__ . '/includes/layout/header.php';
?>

<section class="hero">
    <div class="hero-texto">
        <p class="eyebrow">Cuidado de perto</p>
        <h1>Cuidado que vira carinho, todos os dias.</h1>
        <p class="hero-descricao">
            Organize o cadastro dos pets, acompanhe a situação de cada atendimento e mantenha
            todas as informações importantes em um só lugar.
        </p>
        <div class="hero-acoes">
            <a class="btn btn-grande btn-secundario" href="cadastro.php">Criar minha conta</a>
            <a class="btn btn-grande btn-contorno" href="login.php">Já tenho conta</a>
        </div>
        <ul class="hero-lista">
            <li>Cadastro completo de cada pet e do seu tutor</li>
            <li>Busca rápida e filtros por situação</li>
            <li>Painel com o resumo dos atendimentos</li>
        </ul>
    </div>
    <div class="hero-visual" aria-hidden="true">
        <div class="hero-circulo">🐾</div>
        <div class="hero-selo">Carinho em cada visita</div>
    </div>
</section>

<?php if ($estatisticas !== null): ?>
    <section class="secao" aria-labelledby="titulo-numeros">
        <div class="secao-cabecalho">
            <h2 class="secao-titulo" id="titulo-numeros">A Petit Pet em números</h2>
        </div>
        <div class="grade-estatisticas">
            <article class="cartao-estatistica">
                <p class="rotulo"><span aria-hidden="true">🐾</span> Pets cadastrados</p>
                <p class="numero"><?= (int) $estatisticas['total'] ?></p>
            </article>
            <article class="cartao-estatistica cartao-estatistica--verde">
                <p class="rotulo"><span aria-hidden="true">💚</span> Disponíveis</p>
                <p class="numero"><?= (int) $estatisticas['disponiveis'] ?></p>
            </article>
            <article class="cartao-estatistica cartao-estatistica--amarelo">
                <p class="rotulo"><span aria-hidden="true">🩺</span> Em atendimento</p>
                <p class="numero"><?= (int) $estatisticas['em_atendimento'] ?></p>
            </article>
            <article class="cartao-estatistica cartao-estatistica--azul">
                <p class="rotulo"><span aria-hidden="true">👤</span> Tutores</p>
                <p class="numero"><?= (int) $estatisticas['tutores'] ?></p>
            </article>
        </div>
    </section>
<?php endif; ?>

<section class="secao" aria-labelledby="titulo-recursos">
    <div class="secao-cabecalho">
        <h2 class="secao-titulo" id="titulo-recursos">Tudo o que você precisa em um só lugar</h2>
    </div>
    <div class="recursos">
        <article class="recurso">
            <div class="recurso-icone" aria-hidden="true">📋</div>
            <h2>Cadastro organizado</h2>
            <p>Registre nome, espécie, raça, idade, porte, tutor e observações importantes de cada pet.</p>
        </article>
        <article class="recurso">
            <div class="recurso-icone" aria-hidden="true">🔎</div>
            <h2>Encontre rapidinho</h2>
            <p>Busque por nome, espécie, raça ou tutor e filtre os pets por situação em poucos cliques.</p>
        </article>
        <article class="recurso">
            <div class="recurso-icone" aria-hidden="true">💗</div>
            <h2>Acompanhamento próximo</h2>
            <p>Veja o resumo dos atendimentos e mantenha a rotina de cuidado sempre em dia.</p>
        </article>
    </div>
</section>

<section class="cta-final">
    <div>
        <h2>Pronto para começar?</h2>
        <p>Crie sua conta gratuitamente e cadastre o primeiro pet.</p>
    </div>
    <a class="btn btn-grande" href="cadastro.php">Quero participar</a>
</section>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
