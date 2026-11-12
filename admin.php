<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_admin();

$estatisticas = ['total' => 0, 'disponiveis' => 0, 'em_atendimento' => 0, 'tutores' => 0];
$totalClientes = 0;
$petsRecentes = [];
$clientesRecentes = [];
$erroConsulta = false;

try {
    $estatisticas = estatisticas_pets();
    $totalClientes = contar_clientes();
    $petsRecentes = pets_recentes(5);
    $clientesRecentes = clientes_recentes(5);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao carregar o painel administrativo: ' . $erro->getMessage());
    $erroConsulta = true;
}

$titulo = 'Painel administrativo';
$subtitulo = 'Um resumo simples da Petit Pet para acompanhar clientes e pets.';
$eyebrow = 'Administração';
$paginaAtiva = 'admin';
$area = 'admin';
$descricaoPagina = 'Painel administrativo da Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<?php if ($erroConsulta): ?>
    <div class="mensagem mensagem-erro" role="alert">
        <span class="mensagem-icone" aria-hidden="true">!</span>
        <p>Não foi possível carregar os números do painel agora. Tente atualizar a página.</p>
    </div>
<?php endif; ?>

<section class="stats-grid" aria-label="Resumo da Petit Pet">
    <article class="stat-card">
        <span class="stat-icone" aria-hidden="true">🐾</span>
        <span class="stat-numero"><?= (int) $estatisticas['total'] ?></span>
        <span class="stat-rotulo">Pets cadastrados</span>
    </article>
    <article class="stat-card">
        <span class="stat-icone" aria-hidden="true">💗</span>
        <span class="stat-numero"><?= (int) $estatisticas['disponiveis'] ?></span>
        <span class="stat-rotulo">Disponíveis</span>
    </article>
    <article class="stat-card">
        <span class="stat-icone" aria-hidden="true">🩺</span>
        <span class="stat-numero"><?= (int) $estatisticas['em_atendimento'] ?></span>
        <span class="stat-rotulo">Em atendimento</span>
    </article>
    <article class="stat-card">
        <span class="stat-icone" aria-hidden="true">👥</span>
        <span class="stat-numero"><?= $totalClientes ?></span>
        <span class="stat-rotulo">Clientes cadastrados</span>
    </article>
</section>

<section class="acoes-rapidas" aria-label="Atalhos administrativos">
    <a class="acao-card" href="admin_pets.php">
        <span class="acao-icone" aria-hidden="true">📋</span>
        <span class="acao-titulo">Gerenciar pets</span>
        <span class="acao-texto">Consultar, editar e remover pets cadastrados.</span>
    </a>
    <a class="acao-card" href="admin_clientes.php">
        <span class="acao-icone" aria-hidden="true">👤</span>
        <span class="acao-titulo">Gerenciar clientes</span>
        <span class="acao-texto">Cadastrar e atualizar contas de clientes.</span>
    </a>
    <a class="acao-card" href="cadastrar_pet.php">
        <span class="acao-icone" aria-hidden="true">➕</span>
        <span class="acao-titulo">Novo pet</span>
        <span class="acao-texto">Cadastrar um pet com os dados do tutor.</span>
    </a>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <div>
            <p class="eyebrow">Últimos cadastros</p>
            <h2>Pets recentes</h2>
        </div>
        <a class="link-seta" href="admin_pets.php">Ver todos <span aria-hidden="true">→</span></a>
    </div>

    <?php if ($petsRecentes): ?>
        <div class="tabela-wrapper">
            <table class="tabela">
                <thead>
                    <tr>
                        <th scope="col">Pet</th>
                        <th scope="col">Espécie</th>
                        <th scope="col">Tutor</th>
                        <th scope="col">Situação</th>
                        <th scope="col"><span class="rotulo-oculto">Ações</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($petsRecentes as $pet): ?>
                        <tr>
                            <td data-rotulo="Pet"><strong><?= e($pet['nome']) ?></strong></td>
                            <td data-rotulo="Espécie"><?= e($pet['especie']) ?></td>
                            <td data-rotulo="Tutor"><?= e($pet['tutor_nome'] ?: '—') ?></td>
                            <td data-rotulo="Situação">
                                <span class="selo <?= e(classe_status((string) $pet['status'])) ?>">
                                    <?= e(rotulo_status((string) $pet['status'])) ?>
                                </span>
                            </td>
                            <td class="tabela-acoes">
                                <a class="btn btn-pequeno btn-contorno" href="editar_pet.php?id=<?= (int) $pet['id'] ?>">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="vazio">
            <p>Nenhum pet cadastrado ainda.</p>
            <a class="btn" href="cadastrar_pet.php">Cadastrar primeiro pet</a>
        </div>
    <?php endif; ?>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <div>
            <p class="eyebrow">Contas recentes</p>
            <h2>Clientes recentes</h2>
        </div>
        <a class="link-seta" href="admin_clientes.php">Ver todos <span aria-hidden="true">→</span></a>
    </div>

    <?php if ($clientesRecentes): ?>
        <div class="tabela-wrapper">
            <table class="tabela">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">E-mail</th>
                        <th scope="col">Telefone</th>
                        <th scope="col"><span class="rotulo-oculto">Ações</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientesRecentes as $cliente): ?>
                        <tr>
                            <td data-rotulo="Nome"><strong><?= e($cliente['nome']) ?></strong></td>
                            <td data-rotulo="E-mail"><?= e($cliente['email']) ?></td>
                            <td data-rotulo="Telefone"><?= e($cliente['telefone'] ?: '—') ?></td>
                            <td class="tabela-acoes">
                                <a class="btn btn-pequeno btn-contorno" href="admin_clientes.php?editar=<?= (int) $cliente['id'] ?>">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="vazio">
            <p>Nenhum cliente cadastrado ainda.</p>
            <a class="btn" href="admin_clientes.php">Cadastrar cliente</a>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
