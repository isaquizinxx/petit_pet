<?php
/**
 * Cabeçalho compartilhado.
 *
 * Variáveis esperadas (todas opcionais):
 * - $titulo, $subtitulo, $eyebrow, $paginaAtiva, $containerClasse
 * - $descricaoPagina, $area ('site' ou 'admin')
 */
$titulo = $titulo ?? APP_NOME;
$subtitulo = $subtitulo ?? '';
$eyebrow = $eyebrow ?? '';
$paginaAtiva = $paginaAtiva ?? '';
$containerClasse = $containerClasse ?? 'container container-largo';
$descricaoPagina = $descricaoPagina ?? APP_DESCRICAO;
$area = ($area ?? 'site') === 'admin' ? 'admin' : 'site';
$flashes = obter_flashes();
$inicio = $area === 'admin' ? 'admin.php' : 'index.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($descricaoPagina) ?>">
    <meta name="theme-color" content="#ffceea">
    <title><?= e($titulo !== '' ? $titulo . ' | ' . APP_NOME : APP_NOME) ?></title>
    <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/fontes.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="<?= $area === 'admin' ? 'area-admin' : 'area-site' ?>">
    <a class="pular-link" href="#conteudo">Pular para o conteúdo</a>

    <header class="topo">
        <div class="topo-conteudo">
            <a class="marca" href="<?= e($inicio) ?>" aria-label="Petit Pet — início">
                <span class="marca-icone" aria-hidden="true">🐾</span>
                <span class="marca-texto">Petit Pet</span>
            </a>

            <?php if ($area === 'admin'): ?>
                <nav class="menu" aria-label="Navegação administrativa">
                    <a href="admin.php" class="<?= $paginaAtiva === 'admin' ? 'ativo' : '' ?>">Painel</a>
                    <a href="admin_pets.php" class="<?= $paginaAtiva === 'admin-pets' ? 'ativo' : '' ?>">Pets</a>
                    <a href="admin_clientes.php" class="<?= $paginaAtiva === 'admin-clientes' ? 'ativo' : '' ?>">Clientes</a>
                    <a href="cadastrar_pet.php" class="<?= $paginaAtiva === 'admin-novo-pet' ? 'ativo' : '' ?>">Novo pet</a>
                    <span class="menu-separador" aria-hidden="true"></span>
                    <a href="index.php" class="menu-suave">Ver site</a>
                    <span class="usuario-nome"><?= e(usuario_nome()) ?></span>
                    <a href="logout.php" class="btn btn-pequeno btn-contorno">Sair</a>
                </nav>
            <?php else: ?>
                <nav class="menu" aria-label="Navegação principal">
                    <a href="index.php" class="<?= $paginaAtiva === 'inicio' ? 'ativo' : '' ?>">Início</a>
                    <a href="pets.php" class="<?= $paginaAtiva === 'pets' ? 'ativo' : '' ?>">Pets</a>

                    <?php if (usuario_logado()): ?>
                        <?php if (eh_admin()): ?>
                            <a href="admin.php">Área administrativa</a>
                        <?php else: ?>
                            <a href="painel.php" class="<?= $paginaAtiva === 'painel' ? 'ativo' : '' ?>">Meu painel</a>
                            <a href="cadastrar_pet.php" class="<?= $paginaAtiva === 'cadastrar' ? 'ativo' : '' ?>">Cadastrar pet</a>
                        <?php endif; ?>
                        <span class="menu-separador" aria-hidden="true"></span>
                        <span class="usuario-nome">Olá, <?= e(usuario_nome()) ?></span>
                        <a href="logout.php" class="btn btn-pequeno btn-contorno">Sair</a>
                    <?php else: ?>
                        <span class="menu-separador" aria-hidden="true"></span>
                        <a href="login.php" class="<?= $paginaAtiva === 'login' ? 'ativo' : '' ?>">Entrar</a>
                        <a href="cadastro.php" class="btn btn-pequeno">Criar conta</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    </header>

    <main id="conteudo" class="pagina">
        <div class="<?= e($containerClasse) ?>">
            <?php if ($titulo !== ''): ?>
                <div class="cabecalho-pagina">
                    <?php if ($eyebrow !== ''): ?>
                        <p class="eyebrow"><?= e($eyebrow) ?></p>
                    <?php endif; ?>
                    <h1><?= e($titulo) ?></h1>
                    <?php if ($subtitulo !== ''): ?>
                        <p class="subtitulo"><?= e($subtitulo) ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php foreach ($flashes as $flash): ?>
                <?php
                $tipoFlash = in_array($flash['tipo'] ?? '', ['sucesso', 'erro', 'aviso', 'info'], true)
                    ? $flash['tipo']
                    : 'info';
                $iconeFlash = [
                    'sucesso' => '✓',
                    'erro' => '!',
                    'aviso' => '!',
                    'info' => 'i',
                ][$tipoFlash];
                ?>
                <div class="mensagem mensagem-<?= e($tipoFlash) ?>" role="<?= $tipoFlash === 'erro' ? 'alert' : 'status' ?>">
                    <span class="mensagem-icone" aria-hidden="true"><?= e($iconeFlash) ?></span>
                    <p><?= e($flash['mensagem'] ?? '') ?></p>
                    <button type="button" class="mensagem-fechar" data-fechar-mensagem aria-label="Fechar mensagem">×</button>
                </div>
            <?php endforeach; ?>
