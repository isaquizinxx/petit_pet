<?php
/**
 * Validação dos dados de usuário e de pet.
 */

function validar_cadastro_usuario(array $dados): array
{
    $erros = [];

    if (!texto_valido($dados['nome'] ?? '', 2, 100)) {
        $erros['nome'] = 'Informe seu nome (entre 2 e 100 caracteres).';
    }

    if (strlen($dados['email'] ?? '') > 100 || !filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'Informe um e-mail válido.';
    }

    if (strlen($dados['senha'] ?? '') < 8 || strlen($dados['senha'] ?? '') > 72) {
        $erros['senha'] = 'A senha deve ter entre 8 e 72 caracteres.';
    } elseif (($dados['senha'] ?? '') !== ($dados['senha_confirmacao'] ?? '')) {
        $erros['senha_confirmacao'] = 'As senhas não coincidem.';
    }

    return $erros;
}

/** Monta o array de dados do pet a partir do POST. */
function dados_pet_do_post(array $post): array
{
    $texto = static function (string $campo) use ($post): string {
        $valor = $post[$campo] ?? '';

        return is_string($valor) ? trim($valor) : '';
    };

    return [
        'nome' => $texto('nome'),
        'especie' => $texto('especie'),
        'raca' => $texto('raca'),
        'sexo' => $texto('sexo'),
        'data_nascimento' => $texto('data_nascimento'),
        'porte' => $texto('porte'),
        'tutor_nome' => $texto('tutor_nome'),
        'tutor_telefone' => $texto('tutor_telefone'),
        'observacoes' => $texto('observacoes'),
        'status' => $texto('status'),
    ];
}

function validar_dados_pet(array $dados): array
{
    $erros = [];

    if (!texto_valido($dados['nome'], 1, 100)) {
        $erros['nome'] = 'Informe o nome do pet (até 100 caracteres).';
    }

    if (!texto_valido($dados['especie'], 1, 50)) {
        $erros['especie'] = 'Informe a espécie do pet (até 50 caracteres).';
    }

    if ($dados['raca'] !== '' && !texto_valido($dados['raca'], 1, 80)) {
        $erros['raca'] = 'A raça deve ter até 80 caracteres.';
    }

    if (!in_array($dados['sexo'], pet_sexos_validos(), true)) {
        $erros['sexo'] = 'Selecione uma opção de sexo válida.';
    }

    if ($dados['data_nascimento'] !== '') {
        $data = DateTimeImmutable::createFromFormat('!Y-m-d', $dados['data_nascimento']);
        $ultimosErros = DateTimeImmutable::getLastErrors();

        $dataInvalida = !$data
            || ($ultimosErros !== false && ($ultimosErros['warning_count'] > 0 || $ultimosErros['error_count'] > 0))
            || $data->format('Y-m-d') !== $dados['data_nascimento']
            || $data > new DateTimeImmutable('today');

        if ($dataInvalida) {
            $erros['data_nascimento'] = 'Informe uma data de nascimento válida (ela não pode ser futura).';
        }
    }

    if ($dados['porte'] !== '' && !in_array($dados['porte'], pet_portes_validos(), true)) {
        $erros['porte'] = 'Selecione um porte válido.';
    }

    if ($dados['tutor_nome'] !== '' && !texto_valido($dados['tutor_nome'], 2, 100)) {
        $erros['tutor_nome'] = 'O nome do tutor deve ter entre 2 e 100 caracteres.';
    }

    if ($dados['tutor_telefone'] !== '') {
        $telefoneValido = texto_valido($dados['tutor_telefone'], 8, 30)
            && preg_match('/^[0-9()+\-\s.]+$/', $dados['tutor_telefone']) === 1;

        if (!$telefoneValido) {
            $erros['tutor_telefone'] = 'Informe um telefone válido (somente números, espaços, parênteses, +, - e ponto).';
        }
    }

    if (!texto_valido($dados['observacoes'], 0, 2000, true)) {
        $erros['observacoes'] = 'As observações devem ter até 2000 caracteres.';
    }

    if (!in_array($dados['status'], pet_status_validos(), true)) {
        $erros['status'] = 'Selecione uma situação válida.';
    }

    return $erros;
}
