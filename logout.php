<?php
session_start();

/* ============================================================
   ENCERRAMENTO SEGURO DA SESSÃO
   ============================================================ */

// Remove todas as variáveis de sessão
session_unset();

// Destroi completamente a sessão no servidor
session_destroy();

// Impede que o navegador mantenha páginas antigas em cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* ============================================================
   REDIRECIONAMENTO PÓS-LOGOUT
   ============================================================ */
header("Location: index.php");
exit;
?>
