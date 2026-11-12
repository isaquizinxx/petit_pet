<?php
require_once __DIR__ . '/includes/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('erro', 'O pet solicitado não foi encontrado.');
    redirecionar('pets.php');
}

try {
    $pet = buscar_pet((int) $id);
} catch (Throwable $erro) {
    error_log('[Petit Pet] Falha ao consultar pet: ' . $erro->getMessage());
    flash('erro', 'Não foi possível consultar o pet agora.');
    redirecionar('pets.php');
}

if (!$pet) {
    flash('erro', 'O pet solicitado não foi encontrado.');
    redirecionar('pets.php');
}

$souTutor = usuario_logado() && !eh_admin() && pet_pertence_ao_usuario($pet, (int) usuario_id());
$podeEditar = eh_admin() || $souTutor;
$podeVerContato = eh_admin() || $souTutor;

$tutorExibicao = (string) ($pet['tutor_nome'] ?? '');
if ($tutorExibicao === '') {
    $tutorExibicao = (string) ($pet['cliente_nome'] ?? '');
}

$titulo = (string) $pet['nome'];
$subtitulo = 'Informações e cuidados de ' . $pet['nome'] . '.';
$eyebrow = 'Detalhes do pet';
$paginaAtiva = 'pets';
$descricaoPagina = 'Detalhes do pet ' . $pet['nome'] . ' na Petit Pet.';

require __DIR__ . '/includes/layout/header.php';
?>

<article class="detalhe-pet">
    <div class="detalhe-foto">
        <span class="detalhe-emoji" aria-hidden="true"><?= emoji_especie($pet['especie']) ?></span>
        <span class="selo <?= e(classe_status((string) $pet['status'])) ?>">
            <?= e(rotulo_status((string) $pet['status'])) ?>
        </span>
    </div>

    <div class="detalhe-conteudo">
        <dl class="detalhe-lista">
            <div>
                <dt>Espécie</dt>
                <dd><?= e($pet['especie']) ?></dd>
            </div>
            <div>
                <dt>Raça</dt>
                <dd><?= e($pet['raca'] ?: 'Não informada') ?></dd>
            </div>
            <div>
                <dt>Sexo</dt>
                <dd><?= e(rotulo_sexo((string) $pet['sexo'])) ?></dd>
            </div>
            <div>
                <dt>Idade</dt>
                <dd><?= e(idade_pet($pet['data_nascimento'] ?? null)) ?></dd>
            </div>
            <div>
                <dt>Nascimento</dt>
                <dd><?= e(formatar_data($pet['data_nascimento'] ?? null)) ?></dd>
            </div>
            <div>
                <dt>Porte</dt>
                <dd><?= e(rotulo_porte($pet['porte'] ?? null)) ?></dd>
            </div>
            <div>
                <dt>Tutor</dt>
                <dd><?= e($tutorExibicao !== '' ? $tutorExibicao : 'Não informado') ?></dd>
            </div>
            <?php if ($podeVerContato): ?>
                <div>
                    <dt>Contato</dt>
                    <dd>
                        <?= e($pet['tutor_telefone'] ?: 'Não informado') ?>
                        <?php if (!empty($pet['cliente_email'])): ?>
                            <span class="detalhe-extra"><?= e($pet['cliente_email']) ?></span>
                        <?php endif; ?>
                    </dd>
                </div>
            <?php endif; ?>
        </dl>

        <div class="detalhe-observacoes">
            <h2>Observações</h2>
            <p><?= nl2br(e($pet['observacoes'] ?: 'Nenhuma observação registrada.')) ?></p>
        </div>

        <div class="detalhe-acoes">
            <a class="btn btn-contorno" href="pets.php">← Voltar para os pets</a>
            <?php if ($podeEditar): ?>
                <a class="btn" href="editar_pet.php?id=<?= (int) $pet['id'] ?>">Editar pet</a>
            <?php endif; ?>
            <?php if (eh_admin()): ?>
                <a class="btn btn-contorno" href="admin_pets.php">Painel de pets</a>
            <?php endif; ?>
        </div>
    </div>
</article>

<?php if (!$podeVerContato): ?>
    <p class="nota-privacidade">
        Os dados de contato do tutor ficam visíveis apenas para a administração e para o próprio tutor.
    </p>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
