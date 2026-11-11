<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$estatisticas = [
    'total' => 0,
    'disponiveis' => 0,
    'em_atendimento' => 0,
    'tutores' => 0,
];
$recentes = [];
$erroConsulta = false;

try {
    $estatisticas = estatisticas_pets();
    $recentes = pets_recentes(4);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao carregar o painel: ' . $erro->getMessage());
    $erroConsulta = true;
}

$titulo = 'Olá, ' . usuario_nome() . '!';
$subtitulo = 'Bem-vindo(a) à Petit Pet. Aqui está o resumo do cuidado com os pets.';
$eyebrow = 'Painel';
$containerClasse = 'container container-largo';
$paginaAtiva = 'painel';
$descricaoPagina = 'Painel de controle da Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<?php if ($erroConsulta): ?>
    <div class="mensagem mensagem-erro" role="alert">
        <span class="mensagem-icone" aria-hidden="true">!</span>
        <p>Não foi possível carregar os números do painel agora. Tente atualizar a página.</p>
    </div>
<?php endif; ?>

<section aria-labelledby="titulo-resumo">
    <div class="secao-cabecalho">
        <h2 class="secao-titulo" id="titulo-resumo">Resumo geral</h2>
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

<section class="secao" aria-labelledby="titulo-atalhos">
    <div class="secao-cabecalho">
        <h2 class="secao-titulo" id="titulo-atalhos">O que vamos fazer hoje?</h2>
    </div>
    <div class="acoes-rapidas">
        <a href="cadastrar_animal.php" class="acao-card">
            <span class="acao-icone" aria-hidden="true">➕</span>
            <span class="acao-titulo">Cadastrar pet</span>
            <span class="acao-descricao">Adicione um novo amigo e as informações do tutor.</span>
        </a>
        <a href="animais_cadastrados.php" class="acao-card acao-card--azul">
            <span class="acao-icone" aria-hidden="true">🔎</span>
            <span class="acao-titulo">Ver pets cadastrados</span>
            <span class="acao-descricao">Consulte, busque, edite ou remova registros.</span>
        </a>
        <a href="logout.php" class="acao-card acao-card--sair">
            <span class="acao-icone" aria-hidden="true">🚪</span>
            <span class="acao-titulo">Sair da conta</span>
            <span class="acao-descricao">Encerrar a sessão com segurança.</span>
        </a>
    </div>
</section>

<section class="secao" aria-labelledby="titulo-recentes">
    <div class="secao-cabecalho">
        <h2 class="secao-titulo" id="titulo-recentes">Últimos cadastros</h2>
        <a class="secao-link" href="animais_cadastrados.php">Ver todos →</a>
    </div>

    <?php if ($recentes): ?>
        <div class="lista-recentes">
            <?php foreach ($recentes as $pet): ?>
                <article class="recente-item">
                    <div class="recente-info">
                        <p class="recente-nome"><?= e($pet['nome']) ?></p>
                        <p class="recente-detalhe">
                            <?= e($pet['especie']) ?><?= $pet['raca'] ? ' · ' . e($pet['raca']) : '' ?>
                            <?php if (!empty($pet['tutor_nome'])): ?>
                                · Tutor(a): <?= e($pet['tutor_nome']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <span class="badge <?= e(classe_status((string) $pet['status'])) ?>">
                        <?= e(rotulo_status((string) $pet['status'])) ?>
                    </span>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="estado-vazio">
            <div class="estado-vazio-icone" aria-hidden="true">🐾</div>
            <h2>Nenhum pet cadastrado ainda</h2>
            <p>Comece cadastrando o primeiro amigo para acompanhar os atendimentos.</p>
            <a class="btn" href="cadastrar_animal.php">Cadastrar primeiro pet</a>
        </div>
    <?php endif; ?>
</section>

<div class="dica-navegacao">
    <strong>Cuidado de perto:</strong> mantenha o cadastro dos pets atualizado para facilitar
    o atendimento e a comunicação com os tutores.
</div>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
