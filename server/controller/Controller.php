<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? $_POST['action'] ?? null;

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

require_once __DIR__ . '/../daos/db.php';
require_once __DIR__ . '/ActionLogin.php';
require_once __DIR__ . '/ActionLogout.php';
require_once __DIR__ . '/ActionGetWord.php';
require_once __DIR__ . '/ActionAddWord.php';
require_once __DIR__ . '/ActionUpdateWord.php';
require_once __DIR__ . '/ActionDeleteWord.php';

switch ($action) {
    case 'login':
        try {
            $loginAction = new ActionLogin();
            $loginAction->execute($_POST);
        } catch (Exception $e) {
            http_response_code(500);
            echo $isAjax ? json_encode(['error' => $e->getMessage()]) : 'Error al iniciar sesión.';
        }
        break;
    case 'logout':

        break;
    case 'get_word':
        (new ActionGetWord())->execute();
        break;
    case 'add_word':
        (new ActionAddWord())->execute($_POST);
        break;
    case 'update_word':
        (new ActionUpdateWord())->execute($_POST);
        break;
    case 'delete_word':
        (new ActionDeleteWord())->execute($_POST);
        break;
    default:
        http_response_code(400);
        $message = ['error' => 'Acción no válida'];
        echo $isAjax ? json_encode($message) : 'Acción no válida';
        break;
}
