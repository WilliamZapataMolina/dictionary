<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../daos/db.php';

class ActionLogin
{
    public function execute($post)
    {
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
            $stmt = $dbController->executeQuery($query, $params);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                if (isset($_SESSION['logged_user']) && $_SESSION['logged_user']['email'] !== $email) {
                    session_unset();
                    session_destroy();
                    session_start();
                }

                $_SESSION['logged_user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'rol' => $user['rol'],
                ];

                if ($remember) {
                    setcookie('email', $email, time() + (86400 * 30), "/");
                }

                $redirectUrl = $user['rol'] === 'Admin'
                    ? '/dictionary/public/adminPanel.html'
                    : '/dictionary/public/index.html';

                if ($this->isFetchRequest()) {
                    echo json_encode(['redirect' => $redirectUrl]);
                } else {
                    header("Location: $redirectUrl");
                }
                exit();
            } else {
                if ($this->isFetchRequest()) {
                    http_response_code(401);
                    echo json_encode(['error' => "Usuario o contraseña incorrectos."]);
                } else {
                    header('Location: ../../public/errorLogIn.php');
                }
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => "Error del servidor: " . $e->getMessage()]);
        } finally {
            $dbController->close();
        }
    }

    private function isFetchRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
