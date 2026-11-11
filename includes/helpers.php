<?php
/**
 * Funções utilitárias usadas em todas as páginas.
 */

/** Escapa qualquer valor para exibição segura em HTML. */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/** Redireciona e encerra a requisição. */
function redirecionar(string $destino): void
{
    header('Location: ' . $destino);
    exit;
}

/** Impede que páginas autenticadas fiquem no cache do navegador. */
function enviar_cabecalhos_sem_cache(): void
{
    header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

/**
 * Valida tamanho de texto respeitando UTF-8, sem exigir a extensão mbstring.
 * Use $multilinha = true para campos que aceitam quebras de linha.
 */
function texto_valido(string $valor, int $minimo, int $maximo, bool $multilinha = false): bool
{
    $modificador = $multilinha ? 'us' : 'u';
    $padrao = '/^.{' . $minimo . ',' . $maximo . '}\z/' . $modificador;

    return preg_match($padrao, $valor) === 1;
}

/** Normaliza e-mails para comparação e armazenamento. */
function normalizar_email(string $email): string
{
    return strtolower(trim($email));
}

/** Valor de um campo de texto enviado por POST. */
function valor_post(string $campo, string $padrao = ''): string
{
    $valor = $_POST[$campo] ?? $padrao;

    return is_string($valor) ? trim($valor) : $padrao;
}

/** Valor bruto de POST, sem trim (usado em senhas). */
function valor_post_bruto(string $campo): string
{
    $valor = $_POST[$campo] ?? '';

    return is_string($valor) ? $valor : '';
}

/** Rótulo amigável para a situação do pet. */
function rotulo_status(string $status): string
{
    return match ($status) {
        'disponivel' => 'Disponível',
        'em_atendimento' => 'Em atendimento',
        default => 'Não informado',
    };
}

/** Classe CSS do selo de situação. */
function classe_status(string $status): string
{
    return $status === 'em_atendimento' ? 'badge-atendimento' : 'badge-disponivel';
}

/** Rótulo amigável para o sexo do pet. */
function rotulo_sexo(string $sexo): string
{
    return match ($sexo) {
        'macho' => 'Macho',
        'femea' => 'Fêmea',
        default => 'Não informado',
    };
}

/** Rótulo amigável para o porte do pet. */
function rotulo_porte(?string $porte): string
{
    return match ($porte) {
        'pequeno' => 'Pequeno',
        'medio' => 'Médio',
        'grande' => 'Grande',
        default => 'Não informado',
    };
}

/** Calcula a idade em anos, meses ou dias a partir da data de nascimento. */
function idade_pet(?string $dataNascimento): string
{
    if (!$dataNascimento) {
        return 'Idade não informada';
    }

    try {
        $nascimento = new DateTimeImmutable($dataNascimento);
        $hoje = new DateTimeImmutable('today');
    } catch (Exception) {
        return 'Idade não informada';
    }

    if ($nascimento > $hoje) {
        return 'Idade não informada';
    }

    $diferenca = $nascimento->diff($hoje);

    if ($diferenca->y > 0) {
        return $diferenca->y . ($diferenca->y === 1 ? ' ano' : ' anos');
    }
    if ($diferenca->m > 0) {
        return $diferenca->m . ($diferenca->m === 1 ? ' mês' : ' meses');
    }

    return $diferenca->d . ($diferenca->d === 1 ? ' dia' : ' dias');
}

/** Formata uma data ISO (Y-m-d) para o padrão brasileiro. */
function formatar_data(?string $data): string
{
    if (!$data) {
        return '—';
    }

    try {
        return (new DateTimeImmutable($data))->format('d/m/Y');
    } catch (Exception) {
        return '—';
    }
}
