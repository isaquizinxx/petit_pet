<?php
/**
 * Formulário compartilhado de cadastro e edição de pet.
 *
 * Variáveis esperadas:
 * - $pet (array com todos os campos)
 * - $errosPet (array campo => mensagem)
 * - $acaoFormulario (string)
 * - $petId (int|null)
 * - $textoBotao (string)
 * - $modoFormulario ('cliente' ou 'admin')
 * - $urlCancelar (string)
 */
$modoFormulario = ($modoFormulario ?? 'cliente') === 'admin' ? 'admin' : 'cliente';
$urlCancelar = $urlCancelar ?? ($modoFormulario === 'admin' ? 'admin_pets.php' : 'painel.php');

$temErroPet = static fn (string $campo): bool => isset($errosPet[$campo]);
$attrErroPet = static function (string $campo) use ($temErroPet): string {
    return $temErroPet($campo)
        ? 'aria-invalid="true" aria-describedby="erro-pet-' . e($campo) . '" class="campo-invalido"'
        : '';
};
$mensagemErroPet = static function (string $campo) use ($errosPet, $temErroPet): string {
    return $temErroPet($campo)
        ? '<p class="erro-campo" id="erro-pet-' . e($campo) . '">' . e($errosPet[$campo]) . '</p>'
        : '';
};
?>
<form action="<?= e($acaoFormulario) ?>" method="post" class="formulario">
    <?= csrf_campo() ?>
    <?php if (!empty($petId)): ?>
        <input type="hidden" name="id" value="<?= (int) $petId ?>">
    <?php endif; ?>

    <fieldset class="secao-form">
        <legend>Sobre o pet</legend>

        <div class="linha-campos">
            <div class="grupo-form">
                <label for="nome">Nome do pet</label>
                <input type="text" id="nome" name="nome" placeholder="Ex.: Mel"
                    maxlength="100" required value="<?= e($pet['nome'] ?? '') ?>" <?= $attrErroPet('nome') ?>>
                <?= $mensagemErroPet('nome') ?>
            </div>

            <div class="grupo-form">
                <label for="especie">Espécie</label>
                <input type="text" id="especie" name="especie" list="lista-especies"
                    placeholder="Ex.: Cachorro" maxlength="50" required
                    value="<?= e($pet['especie'] ?? '') ?>" <?= $attrErroPet('especie') ?>>
                <datalist id="lista-especies">
                    <option value="Cachorro"></option>
                    <option value="Gato"></option>
                    <option value="Coelho"></option>
                    <option value="Ave"></option>
                    <option value="Hamster"></option>
                    <option value="Outro"></option>
                </datalist>
                <?= $mensagemErroPet('especie') ?>
            </div>
        </div>

        <div class="linha-campos">
            <div class="grupo-form">
                <label for="raca">Raça</label>
                <input type="text" id="raca" name="raca" placeholder="Ex.: Shih-tzu"
                    maxlength="80" value="<?= e($pet['raca'] ?? '') ?>" <?= $attrErroPet('raca') ?>>
                <p class="ajuda">Opcional. Deixe em branco se não souber.</p>
                <?= $mensagemErroPet('raca') ?>
            </div>

            <div class="grupo-form">
                <label for="sexo">Sexo</label>
                <select id="sexo" name="sexo" required <?= $attrErroPet('sexo') ?>>
                    <option value="nao_informado" <?= ($pet['sexo'] ?? '') === 'nao_informado' ? 'selected' : '' ?>>Não informado</option>
                    <option value="macho" <?= ($pet['sexo'] ?? '') === 'macho' ? 'selected' : '' ?>>Macho</option>
                    <option value="femea" <?= ($pet['sexo'] ?? '') === 'femea' ? 'selected' : '' ?>>Fêmea</option>
                </select>
                <?= $mensagemErroPet('sexo') ?>
            </div>
        </div>

        <div class="linha-campos">
            <div class="grupo-form">
                <label for="data_nascimento">Data de nascimento</label>
                <input type="date" id="data_nascimento" name="data_nascimento"
                    max="<?= e(date('Y-m-d')) ?>" value="<?= e($pet['data_nascimento'] ?? '') ?>"
                    <?= $attrErroPet('data_nascimento') ?>>
                <p class="ajuda">Opcional. Ajuda a calcular a idade.</p>
                <?= $mensagemErroPet('data_nascimento') ?>
            </div>

            <div class="grupo-form">
                <label for="porte">Porte</label>
                <select id="porte" name="porte" <?= $attrErroPet('porte') ?>>
                    <option value="">Não informado</option>
                    <option value="pequeno" <?= ($pet['porte'] ?? '') === 'pequeno' ? 'selected' : '' ?>>Pequeno</option>
                    <option value="medio" <?= ($pet['porte'] ?? '') === 'medio' ? 'selected' : '' ?>>Médio</option>
                    <option value="grande" <?= ($pet['porte'] ?? '') === 'grande' ? 'selected' : '' ?>>Grande</option>
                </select>
                <?= $mensagemErroPet('porte') ?>
            </div>
        </div>

        <?php if ($modoFormulario === 'admin'): ?>
            <div class="grupo-form">
                <label for="status">Situação</label>
                <select id="status" name="status" required <?= $attrErroPet('status') ?>>
                    <option value="disponivel" <?= ($pet['status'] ?? '') === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                    <option value="em_atendimento" <?= ($pet['status'] ?? '') === 'em_atendimento' ? 'selected' : '' ?>>Em atendimento</option>
                </select>
                <?= $mensagemErroPet('status') ?>
            </div>
        <?php endif; ?>
    </fieldset>

    <fieldset class="secao-form">
        <legend><?= $modoFormulario === 'admin' ? 'Tutor e observações' : 'Observações' ?></legend>

        <?php if ($modoFormulario === 'admin'): ?>
            <div class="linha-campos">
                <div class="grupo-form">
                    <label for="tutor_nome">Nome do tutor</label>
                    <input type="text" id="tutor_nome" name="tutor_nome" placeholder="Quem cuida deste pet?"
                        maxlength="100" value="<?= e($pet['tutor_nome'] ?? '') ?>" <?= $attrErroPet('tutor_nome') ?>>
                    <?= $mensagemErroPet('tutor_nome') ?>
                </div>

                <div class="grupo-form">
                    <label for="tutor_telefone">Telefone do tutor</label>
                    <input type="tel" id="tutor_telefone" name="tutor_telefone" placeholder="(00) 00000-0000"
                        maxlength="30" value="<?= e($pet['tutor_telefone'] ?? '') ?>" <?= $attrErroPet('tutor_telefone') ?>>
                    <?= $mensagemErroPet('tutor_telefone') ?>
                </div>
            </div>
        <?php else: ?>
            <p class="ajuda ajuda-bloco">
                O tutor deste pet será você, <?= e(usuario_nome()) ?>.
                Para alterar o contato do tutor, atualize seus dados com a administração.
            </p>
        <?php endif; ?>

        <div class="grupo-form">
            <label for="observacoes">Observações</label>
            <textarea id="observacoes" name="observacoes" maxlength="2000"
                placeholder="Alergias, preferências, cuidados especiais..." <?= $attrErroPet('observacoes') ?>><?= e($pet['observacoes'] ?? '') ?></textarea>
            <p class="ajuda">Opcional, até 2000 caracteres.</p>
            <?= $mensagemErroPet('observacoes') ?>
        </div>
    </fieldset>

    <div class="form-acoes">
        <button type="submit" class="btn"><?= e($textoBotao) ?></button>
        <a href="<?= e($urlCancelar) ?>" class="btn btn-contorno">Cancelar</a>
    </div>
</form>
