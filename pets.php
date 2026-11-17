<?php
require_once __DIR__ . '/includes/bootstrap.php';

$busca = valor_get('busca');
$status = valor_get('status');

if (!in_array($status, pet_status_validos(), true)) {
    $status = '';
}

$pets = [];
$total = 0;
$erroConsulta = false;

try {
    $pets = listar_pets($busca, $status);
    $total = count($pets);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao listar pets: ' . $erro->getMessage());
    $erroConsulta = true;
}

$titulo = 'Nossos pets';
$subtitulo = 'Conheça os amigos cadastrados na Petit Pet e veja as informações de cada um.';
$eyebrow = 'Área dos clientes';
$paginaAtiva = 'pets';
$descricaoPagina = 'Lista de pets cadastrados na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<div class="barra-lista">
    <form class="filtros" action="pets.php" method="get" role="search">
        <div class="campo-busca">
            <label class="rotulo-oculto" for="busca">Buscar pet</label>
            <input type="search" id="busca" name="busca" placeholder="Buscar por nome, espécie ou tutor..."
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
            <a href="pets.php" class="btn btn-contorno">Limpar</a>
        <?php endif; ?>
    </form>

    <p class="resultado-info">
        <?= $total ?> <?= $total === 1 ? 'pet encontrado' : 'pets encontrados' ?>
    </p>
</div>

<?php if ($erroConsulta): ?>
    <div class="mensagem mensagem-erro" role="alert">
        <span class="mensagem-icone"><?= icone_svg('circle-alert') ?></span>
        <p>Não foi possível carregar a lista agora. Tente atualizar a página.</p>
    </div>
<?php elseif ($pets): ?>
    <div class="grade-pets">
        <?php foreach ($pets as $pet): ?>
            <?php $petCard = $pet; require __DIR__ . '/includes/cartao_pet.php'; ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="vazio">
        <span class="vazio-icone"><?= icone_svg('paw') ?></span>
        <h2>Nenhum pet encontrado</h2>
        <p><?= $busca !== '' || $status !== '' ? 'Tente outra busca ou remova os filtros.' : 'Assim que houver pets cadastrados, eles aparecerão aqui.' ?></p>
        <?php if ($busca !== '' || $status !== ''): ?>
            <a class="btn btn-contorno" href="pets.php">Ver todos os pets</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!usuario_logado()): ?>
    <section class="faixa-cta faixa-cta--suave">
        <div>
            <h2>Tem um pet para cadastrar?</h2>
            <p>Crie sua conta e mantenha as informações do seu amigo sempre organizadas.</p>
        </div>
        <a class="btn btn-grande" href="cadastro.php">Criar conta</a>
    </section>
<?php elseif (!eh_admin()): ?>
    <section class="faixa-cta faixa-cta--suave">
        <div>
            <h2>Cadastre o seu pet</h2>
            <p>Leve as informações do seu amigo para o seu painel na Petit Pet.</p>
        </div>
        <a class="btn btn-grande" href="cadastrar_pet.php">Cadastrar pet</a>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
