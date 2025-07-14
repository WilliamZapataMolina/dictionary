<?php
// Inicia sesión si aún no está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Requiere el controlador de conexión a base de datos
require_once __DIR__ . '/../daos/db.php';

class ActionLogin
{
    /**
     * Ejecuta el inicio de sesión.
     * Verifica email y contraseña, y establece la sesión del usuario si es válido.
     * 
     * @param array $post Datos del formulario (email, password, remember).
     */
    public function execute($post)
    {
        // Verifica campos obligatorios
        if (!isset($post['email']) || !isset($post['password'])) {
            http_response_code(400);
            echo json_encode(['error' => "Por favor, complete ambos campos."]);
            return;
        }

        $email = trim($post['email']);
        $password = trim($post['password']);
        $remember = isset($post['remember']);

        $dbController = new DatabaseController();

        $query = "SELECT * FROM users WHERE email = :email";
        $params = [':email' => $email];

        try {
            // Ejecuta consulta para obtener el usuario por email
            $stmt = $dbController->executeQuery($query, $params);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica si el usuario existe y la contraseña es válida
            if ($user && password_verify($password, $user['password'])) {
                // Si ya hay un usuario logueado diferente, limpia la sesión anterior
                if (isset($_SESSION['logged_user']) && $_SESSION['logged_user']['email'] !== $email) {
                    session_unset();
                    session_destroy();
                    session_start();
                }
                // Guarda el usuario en la sesión
                $_SESSION['logged_user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'rol' => $user['rol'],
                ];

                // Si el usuario marcó "recordarme", guarda el email en una cookie
                if ($remember) {
                    setcookie('email', $email, time() + (86400 * 30), "/"); //30 días
                }

                // Redirecciona según rol
                $redirectUrl = $user['rol'] === 'Admin'
                    ? '/dictionary/public/adminPanel.php'
                    : '/dictionary/public/index.html';

                // Si la petición viene vía Fetch/AJAX
                if ($this->isFetchRequest()) {
                    echo json_encode(['redirect' => $redirectUrl]);
                } else {
                    header("Location: $redirectUrl");
                }
                exit();
            } else {
                // Si las credenciales no son válidas
                if ($this->isFetchRequest()) {
                    http_response_code(401);
                    echo json_encode(['error' => "Usuario o contraseña incorrectos."]);
                } else {
                    header('Location: ../../public/errorLogIn.php');
                }
            }
        } catch (PDOException $e) {
            // Error de base de datos
            http_response_code(500);
            echo json_encode(['error' => "Error del servidor: " . $e->getMessage()]);
        } finally {
            // Cierra conexión
            $dbController->close();
        }
    }

    /**
     * Verifica si la petición fue enviada usando Fetch/AJAX.
     * 
     * @return bool True si es una petición AJAX, false en caso contrario.
     */
    private function isFetchRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
