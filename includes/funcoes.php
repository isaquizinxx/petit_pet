<?php
/**
 * Funções utilitárias, mensagens, CSRF e validações da Petit Pet.
 */

/* --------------------------------------------------------------------------
 * Texto e exibição
 * ----------------------------------------------------------------------- */

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

/** Valor de um campo de texto enviado por GET. */
function valor_get(string $campo): string
{
    $valor = $_GET[$campo] ?? '';

    return is_string($valor) ? trim($valor) : '';
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

/** Retorna um ícone SVG padronizado para uso na interface. */
function icone_svg(string $nome): string
{
    static $icones = [
        'paw' => '<ellipse cx="5.5" cy="8" rx="1.5" ry="2"/><ellipse cx="9.5" cy="5" rx="1.5" ry="2"/><ellipse cx="14.5" cy="5" rx="1.5" ry="2"/><ellipse cx="18.5" cy="8" rx="1.5" ry="2"/><path d="M12 12c-3 0-6 4-6 7a2 2 0 0 0 2 2c1 0 2.5-.8 4-.8s3 .8 4 .8a2 2 0 0 0 2-2c0-3-3-7-6-7z"/>',
        'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8z"/>',
        'clipboard-list' => '<rect x="8" y="3" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M8 12h8M8 16h8"/>',
        'stethoscope' => '<path d="M6 3v5a4 4 0 0 0 8 0V3"/><path d="M6 5H4m10 0h2"/><path d="M10 12v2a6 6 0 0 0 12 0v-1"/><circle cx="22" cy="11" r="2"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'search' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>',
        'log-out' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/>',
        'arrow-right' => '<path d="M5 12h14m-7-7 7 7-7 7"/>',
        'arrow-left' => '<path d="M19 12H5m7 7-7-7 7-7"/>',
        'circle-check' => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
        'circle-alert' => '<circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>',
        'circle-info' => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01"/>',
        'x' => '<path d="m18 6-12 12M6 6l12 12"/>',
    ];

    if (!isset($icones[$nome])) {
        return '';
    }

    return '<svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
        . $icones[$nome]
        . '</svg>';
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

/* --------------------------------------------------------------------------
 * Mensagens de feedback (flash)
 * ----------------------------------------------------------------------- */

function flash(string $tipo, string $mensagem): void
{
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }

    $_SESSION['flash'][] = [
        'tipo' => $tipo,
        'mensagem' => $mensagem,
    ];
}

function obter_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return is_array($flashes) ? $flashes : [];
}

/* --------------------------------------------------------------------------
 * CSRF
 * ----------------------------------------------------------------------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_valido(mixed $token): bool
{
    return is_string($token)
        && $token !== ''
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function exigir_csrf(string $destino = 'login.php'): void
{
    if (!csrf_valido($_POST['csrf_token'] ?? null)) {
        flash('erro', 'Sua sessão expirou ou a solicitação é inválida. Tente novamente.');
        redirecionar($destino);
    }
}

/* --------------------------------------------------------------------------
 * Validação de clientes
 * ----------------------------------------------------------------------- */

function dados_cliente_do_post(): array
{
    return [
        'nome' => valor_post('nome'),
        'email' => normalizar_email(valor_post('email')),
        'telefone' => valor_post('telefone'),
        'senha' => valor_post_bruto('senha'),
        'senha_confirmacao' => valor_post_bruto('senha_confirmacao'),
    ];
}

function dados_cliente_validos(array $dados): array
{
    $erros = [];

    if (!texto_valido($dados['nome'] ?? '', 2, 100)) {
        $erros['nome'] = 'Informe o nome completo (entre 2 e 100 caracteres).';
    }

    if (strlen($dados['email'] ?? '') > 100 || !filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'Informe um e-mail válido.';
    }

    $telefone = $dados['telefone'] ?? '';
    if ($telefone !== '') {
        $telefoneValido = texto_valido($telefone, 8, 30)
            && preg_match('/^[0-9()+\-\s.]+$/', $telefone) === 1;

        if (!$telefoneValido) {
            $erros['telefone'] = 'Informe um telefone válido, como (41) 99999-1234.';
        }
    }

    return $erros;
}

/** Validação do cadastro feito pelo próprio cliente (senha obrigatória). */
function validar_cadastro_cliente(array $dados): array
{
    $erros = dados_cliente_validos($dados);

    if (strlen($dados['senha'] ?? '') < 8 || strlen($dados['senha'] ?? '') > 72) {
        $erros['senha'] = 'A senha deve ter entre 8 e 72 caracteres.';
    } elseif (($dados['senha'] ?? '') !== ($dados['senha_confirmacao'] ?? '')) {
        $erros['senha_confirmacao'] = 'As senhas não coincidem.';
    }

    return $erros;
}

/** Validação do cliente no painel administrativo (senha é opcional na edição). */
function validar_cliente_admin(array $dados, bool $senhaObrigatoria): array
{
    $erros = dados_cliente_validos($dados);

    if ($senhaObrigatoria && ($dados['senha'] ?? '') === '') {
        $erros['senha'] = 'Defina uma senha para o cliente.';
    }

    if (($dados['senha'] ?? '') !== '' && (strlen($dados['senha']) < 8 || strlen($dados['senha']) > 72)) {
        $erros['senha'] = 'A senha deve ter entre 8 e 72 caracteres.';
    }

    return $erros;
}

function validar_edicao_conta_cliente(array $dados): array
{
    $erros = dados_cliente_validos($dados);
    $senha = (string) ($dados['senha'] ?? '');
    $confirmacao = (string) ($dados['senha_confirmacao'] ?? '');

    if ($senha !== '' && (strlen($senha) < 8 || strlen($senha) > 72)) {
        $erros['senha'] = 'A nova senha deve ter entre 8 e 72 caracteres.';
    }

    if ($senha !== $confirmacao) {
        $erros['senha_confirmacao'] = 'As senhas não coincidem.';
    }

    return $erros;
}

/* --------------------------------------------------------------------------
 * Validação de pets
 * ----------------------------------------------------------------------- */

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
