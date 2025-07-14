<?php
// Inicia la sesión solo si aún no está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../daos/db.php';

/**
 * Clase que gestiona el cierre de sesión del usuario.
 */
class ActionLogout
{

    public function execute()
    {
        // Verificar si hay una sesión activa antes de destruirla
        if (isset($_SESSION["logged_user"])) {

            // Elimina todas las variables de sesión
            $_SESSION = array();

            // Destruye la sesión
            session_destroy();
        }

        // Redirige a la página de inicio después de cerrar sesión
        header("Location: /dictionary/public/index.html");
        exit();
    }
}
