<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_admin();

$busca = valor_get('busca');
$status = valor_get('status');

if (!in_array($status, pet_status_validos(), true)) {
    $status = '';
}

$pets = [];
$erroConsulta = false;

try {
    $pets = listar_pets($busca, $status);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao listar pets no painel: ' . $erro->getMessage());
    $erroConsulta = true;
}

$total = count($pets);

$titulo = 'Pets cadastrados';
$subtitulo = 'Consulte, edite ou remova os pets da Petit Pet.';
$eyebrow = 'Administração';
$paginaAtiva = 'admin-pets';
$area = 'admin';
$descricaoPagina = 'Gerenciamento de pets na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<div class="barra-lista">
    <form class="filtros" action="admin_pets.php" method="get" role="search">
        <div class="campo-busca">
            <label class="rotulo-oculto" for="busca">Buscar pet</label>
            <input type="search" id="busca" name="busca" placeholder="Nome, espécie, tutor ou conta..."
                value="<?= e($busca) ?>">
        </div>

        <div class="campo-status">
            <label class="rotulo-oculto" for="status">Situação</label>
            <select id="status" name="status" data-auto-enviar>
                <option value="">Todas as situações</option>
                <option value="disponivel" <?= $status === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                <option value="em_atendimento" <?= $status === 'em_atendimento' ? 'selected' : '' ?>>Em atendimento</option>
            </select>
        </div>

        <button type="submit" class="btn">Buscar</button>
        <?php if ($busca !== '' || $status !== ''): ?>
            <a href="admin_pets.php" class="btn btn-contorno">Limpar</a>
        <?php endif; ?>
    </form>

    <a class="btn" href="cadastrar_pet.php"><?= icone_svg('plus') ?> Novo pet</a>
</div>

<p class="resultado-info">
    <?= $total ?> <?= $total === 1 ? 'pet encontrado' : 'pets encontrados' ?>
</p>

<?php if ($erroConsulta): ?>
    <div class="mensagem mensagem-erro" role="alert">
        <span class="mensagem-icone"><?= icone_svg('circle-alert') ?></span>
        <p>Não foi possível carregar a lista agora. Tente atualizar a página.</p>
    </div>
<?php elseif ($pets): ?>
    <div class="tabela-wrapper">
        <table class="tabela">
            <thead>
                <tr>
                    <th scope="col">Pet</th>
                    <th scope="col">Espécie / Raça</th>
                    <th scope="col">Tutor</th>
                    <th scope="col">Conta</th>
                    <th scope="col">Situação</th>
                    <th scope="col"><span class="rotulo-oculto">Ações</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pets as $pet): ?>
                    <tr>
                        <td data-rotulo="Pet">
                            <strong><?= e($pet['nome']) ?></strong>
                            <span class="tabela-extra"><?= e(rotulo_sexo((string) $pet['sexo'])) ?> · <?= e(idade_pet($pet['data_nascimento'] ?? null)) ?></span>
                        </td>
                        <td data-rotulo="Espécie / Raça">
                            <?= e($pet['especie']) ?>
                            <?php if (!empty($pet['raca'])): ?>
                                <span class="tabela-extra"><?= e($pet['raca']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td data-rotulo="Tutor">
                            <?= e($pet['tutor_nome'] ?: '—') ?>
                            <?php if (!empty($pet['tutor_telefone'])): ?>
                                <span class="tabela-extra"><?= e($pet['tutor_telefone']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td data-rotulo="Conta">
                            <?php if (!empty($pet['cliente_email'])): ?>
                                <?= e($pet['cliente_email']) ?>
                            <?php else: ?>
                                <span class="tabela-vazio">Sem conta vinculada</span>
                            <?php endif; ?>
                        </td>
                        <td data-rotulo="Situação">
                            <span class="selo <?= e(classe_status((string) $pet['status'])) ?>">
                                <?= e(rotulo_status((string) $pet['status'])) ?>
                            </span>
                        </td>
                        <td class="tabela-acoes">
                            <a class="btn btn-pequeno btn-contorno" href="editar_pet.php?id=<?= (int) $pet['id'] ?>">Editar</a>
                            <form action="excluir_pet.php" method="post" data-confirmar="Remover o pet <?= e($pet['nome']) ?> definitivamente?">
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
<?php else: ?>
    <div class="vazio">
        <span class="vazio-icone"><?= icone_svg('paw') ?></span>
        <h2>Nenhum pet encontrado</h2>
        <p><?= $busca !== '' || $status !== '' ? 'Tente outra busca ou remova os filtros.' : 'Cadastre o primeiro pet da Petit Pet.' ?></p>
        <a class="btn" href="cadastrar_pet.php">Cadastrar pet</a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
