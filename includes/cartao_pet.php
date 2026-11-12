<?php
/**
 * Cartão de pet reutilizado na home, na listagem e nos painéis.
 *
 * Variáveis esperadas:
 * - $petCard (array com os dados do pet)
 * - $mostrarTutor (bool, opcional)
 */
$petCard = is_array($petCard ?? null) ? $petCard : [];
$mostrarTutor = (bool) ($mostrarTutor ?? false);
$petCardId = (int) ($petCard['id'] ?? 0);
$petCardNome = (string) ($petCard['nome'] ?? 'Pet');
$petCardEspecie = (string) ($petCard['especie'] ?? '');
$petCardSexo = (string) ($petCard['sexo'] ?? 'nao_informado');
$petCardStatus = (string) ($petCard['status'] ?? 'disponivel');
$petCardTutor = (string) ($petCard['tutor_nome'] ?? '');

$meta = array_filter([
    $petCardEspecie,
    $petCardSexo !== 'nao_informado' ? rotulo_sexo($petCardSexo) : '',
    idade_pet($petCard['data_nascimento'] ?? null),
], static fn (string $item): bool => $item !== '');
?>
<article class="pet-card">
    <a class="pet-card-link" href="pet.php?id=<?= $petCardId ?>">
        <span class="pet-foto" aria-hidden="true"><?= emoji_especie($petCardEspecie) ?></span>
        <span class="pet-conteudo">
            <span class="pet-linha">
                <span class="pet-nome"><?= e($petCardNome) ?></span>
                <span class="selo <?= e(classe_status($petCardStatus)) ?>"><?= e(rotulo_status($petCardStatus)) ?></span>
            </span>
            <span class="pet-meta"><?= e(implode(' · ', $meta)) ?></span>
            <?php if ($mostrarTutor && $petCardTutor !== ''): ?>
                <span class="pet-tutor">Tutor: <?= e($petCardTutor) ?></span>
            <?php endif; ?>
            <span class="pet-ver">Ver detalhes <span aria-hidden="true">→</span></span>
        </span>
    </a>
</article>
