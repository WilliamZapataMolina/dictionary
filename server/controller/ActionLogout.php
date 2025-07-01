<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../daos/db.php';

class ActionLogout
{

    public function execute()
    {

        // Iniciar sesión si aún no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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
