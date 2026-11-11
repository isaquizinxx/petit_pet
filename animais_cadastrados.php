<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$busca = is_string($_GET['busca'] ?? null) ? trim($_GET['busca']) : '';
$status = is_string($_GET['status'] ?? null) ? $_GET['status'] : '';

if (!in_array($status, pet_status_validos(), true)) {
    $status = '';
}

$pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT);
$pagina = ($pagina && $pagina > 0) ? (int) $pagina : 1;
$porPagina = 8;

$pets = [];
$total = 0;
$totalPaginas = 1;
$erroConsulta = false;

try {
    $total = contar_pets($busca, $status);
    $totalPaginas = max(1, (int) ceil($total / $porPagina));

    if ($pagina > $totalPaginas) {
        $pagina = $totalPaginas;
    }

    $pets = listar_pets($busca, $status, $pagina, $porPagina);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao listar pets: ' . $erro->getMessage());
    $erroConsulta = true;
}

$montarUrl = static function (int $numeroPagina) use ($busca, $status): string {
    $parametros = [];
    if ($busca !== '') {
        $parametros['busca'] = $busca;
    }
    if ($status !== '') {
        $parametros['status'] = $status;
    }
    $parametros['pagina'] = $numeroPagina;

    return 'animais_cadastrados.php?' . http_build_query($parametros);
};

$temFiltro = $busca !== '' || $status !== '';
$inicioPaginas = max(1, $pagina - 2);
$fimPaginas = min($totalPaginas, $pagina + 2);

$titulo = 'Pets cadastrados';
$subtitulo = 'Consulte, busque e acompanhe a situação de cada atendimento.';
$eyebrow = 'Pets';
$containerClasse = 'container container-largo';
$paginaAtiva = 'pets';
$descricaoPagina = 'Lista de pets cadastrados na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<?php if ($erroConsulta): ?>
    <div class="mensagem mensagem-erro" role="alert">
        <span class="mensagem-icone" aria-hidden="true">!</span>
        <p>Não foi possível consultar os pets agora. Tente atualizar a página.</p>
    </div>
<?php endif; ?>

<form class="barra-ferramentas" method="get" action="animais_cadastrados.php" role="search">
    <div class="grupo-form campo-busca">
        <label for="busca">Buscar</label>
        <input type="search" id="busca" name="busca" value="<?= e($busca) ?>"
            placeholder="Nome, espécie, raça ou tutor" maxlength="100">
    </div>

    <div class="grupo-form filtro-status">
        <label for="status">Situação</label>
        <select id="status" name="status" data-auto-enviar>
            <option value="">Todas as situações</option>
            <option value="disponivel" <?= $status === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
            <option value="em_atendimento" <?= $status === 'em_atendimento' ? 'selected' : '' ?>>Em atendimento</option>
        </select>
    </div>

    <button type="submit" class="btn">Buscar</button>

    <?php if ($temFiltro): ?>
        <a class="btn btn-fantasma" href="animais_cadastrados.php">Limpar</a>
    <?php endif; ?>

    <a class="btn btn-secundario empurrar" href="cadastrar_animal.php">+ Novo pet</a>
</form>

<p class="contador-resultados">
    <?= (int) $total ?> <?= $total === 1 ? 'pet encontrado' : 'pets encontrados' ?>
    <?php if ($totalPaginas > 1): ?>
        · página <?= (int) $pagina ?> de <?= (int) $totalPaginas ?>
    <?php endif; ?>
</p>

<?php if ($pets): ?>
    <div class="tabela-responsiva">
        <table class="tabela tabela-pets">
            <thead>
                <tr>
                    <th scope="col">Pet</th>
                    <th scope="col">Sexo / idade</th>
                    <th scope="col">Tutor</th>
                    <th scope="col">Situação</th>
                    <th scope="col" class="col-acoes">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pets as $pet): ?>
                    <tr>
                        <td data-label="Pet">
                            <div class="pet-nome"><?= e($pet['nome']) ?></div>
                            <div class="pet-detalhe">
                                <?= e($pet['especie']) ?><?= !empty($pet['raca']) ? ' · ' . e($pet['raca']) : '' ?>
                            </div>
                        </td>
                        <td data-label="Sexo / idade">
                            <?= e(rotulo_sexo((string) $pet['sexo'])) ?> · <?= e(idade_pet($pet['data_nascimento'])) ?>
                        </td>
                        <td data-label="Tutor">
                            <?php if (!empty($pet['tutor_nome'])): ?>
                                <div><?= e($pet['tutor_nome']) ?></div>
                                <?php if (!empty($pet['tutor_telefone'])): ?>
                                    <div class="pet-detalhe"><?= e($pet['tutor_telefone']) ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="texto-suave">Não informado</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Situação">
                            <span class="badge <?= e(classe_status((string) $pet['status'])) ?>">
                                <?= e(rotulo_status((string) $pet['status'])) ?>
                            </span>
                        </td>
                        <td data-label="Ações" class="acoes">
                            <a class="btn btn-pequeno btn-contorno" href="editar_animal.php?id=<?= (int) $pet['id'] ?>">Editar</a>
                            <form action="excluir_animal.php" method="post"
                                data-confirmar="Remover o pet <?= e($pet['nome']) ?>? Esta ação não pode ser desfeita.">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="id" value="<?= (int) $pet['id'] ?>">
                                <button type="submit" class="btn btn-pequeno btn-perigo">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPaginas > 1): ?>
        <nav class="paginacao" aria-label="Paginação dos pets">
            <a class="pagina-link <?= $pagina <= 1 ? 'desativada' : '' ?>"
                href="<?= e($montarUrl(max(1, $pagina - 1))) ?>"
                <?= $pagina <= 1 ? 'aria-disabled="true" tabindex="-1"' : '' ?>>←</a>

            <?php for ($numero = $inicioPaginas; $numero <= $fimPaginas; $numero++): ?>
                <a class="pagina-link <?= $numero === $pagina ? 'ativa' : '' ?>"
                    href="<?= e($montarUrl($numero)) ?>"
                    <?= $numero === $pagina ? 'aria-current="page"' : '' ?>><?= $numero ?></a>
            <?php endfor; ?>

            <a class="pagina-link <?= $pagina >= $totalPaginas ? 'desativada' : '' ?>"
                href="<?= e($montarUrl(min($totalPaginas, $pagina + 1))) ?>"
                <?= $pagina >= $totalPaginas ? 'aria-disabled="true" tabindex="-1"' : '' ?>>→</a>
        </nav>
    <?php endif; ?>
<?php else: ?>
    <div class="estado-vazio">
        <div class="estado-vazio-icone" aria-hidden="true">🐾</div>
        <h2><?= $temFiltro ? 'Nenhum pet encontrado' : 'Nenhum pet cadastrado ainda' ?></h2>
        <p>
            <?= $temFiltro
                ? 'Tente ajustar a busca ou os filtros para encontrar o pet desejado.'
                : 'Cadastre o primeiro amigo para começar a acompanhar os atendimentos.' ?>
        </p>
        <?php if ($temFiltro): ?>
            <a class="btn btn-contorno" href="animais_cadastrados.php">Limpar filtros</a>
        <?php else: ?>
            <a class="btn" href="cadastrar_animal.php">Cadastrar primeiro pet</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<a href="painel.php" class="btn btn-voltar btn-contorno">← Voltar ao painel</a>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
