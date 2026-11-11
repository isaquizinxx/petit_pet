<?php
/**
 * Arquivo mantido por compatibilidade.
 * As páginas novas incluem includes/bootstrap.php e chamam exigir_login().
 */

require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();
