<?php
require_once __DIR__ . '/includes/bootstrap.php';

encerrar_sessao();
iniciar_sessao();

flash('sucesso', 'Você saiu da sua conta com segurança.');
redirecionar('index.php');
