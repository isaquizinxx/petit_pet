<?php
require_once __DIR__ . '/includes/bootstrap.php';

enviar_cabecalhos_sem_cache();
encerrar_sessao();

header('Location: login.php?sucesso=logout');
exit;
