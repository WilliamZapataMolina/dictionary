<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función reutilizable para enviar respuestas JSON
function sendJson($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? null;

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

require_once __DIR__ . '/../daos/db.php';
require_once __DIR__ . '/ActionLogin.php';
require_once __DIR__ . '/ActionLogout.php';
require_once __DIR__ . '/ActionGetWord.php';
require_once __DIR__ . '/ActionGetCategories.php';
require_once __DIR__ . '/ActionGetImages.php';
require_once __DIR__ . '/ActionAddWord.php';
require_once __DIR__ . '/ActionUpdateWord.php';
require_once __DIR__ . '/ActionDeleteWord.php';

switch ($action) {
    case 'login':
        try {
            (new ActionLogin())->execute($_POST);
        } catch (Exception $e) {
            $message = $isAjax
                ? ['error' => $e->getMessage()]
                : 'Error al iniciar sesión.';
            sendJson($message, 500);
        }
        break;

    case 'logout':
        (new ActionLogout())->execute();
        break;

    case 'getWord':
        try {
            (new ActionGetWord())->execute();
        } catch (Exception $e) {
            $message = $isAjax
                ? ['error' => $e->getMessage()]
                : 'Error en getWord.';
            sendJson($message, 500);
        }
        break;
    case 'getCategories':
        try {
            (new ActionGetCategories())->execute();
        } catch (Exception $e) {
            sendJson(['error' => 'Error al obtener categorías: ' . $e->getMessage()], 500);
        }
        break;

    case 'getImages':
        try {
            (new ActionGetImages())->execute();
        } catch (Exception $e) {
            sendJson(['error' => 'Error al obtener imágenes: ' . $e->getMessage()], 500);
        }
        break;


    case 'addWord':
        try {
            (new ActionAddWord())->execute($_POST);
        } catch (Exception $e) {
            $message = $isAjax
                ? ['error' => $e->getMessage()]
                : 'Error en getWord.';
            sendJson($message, 500);
        }
        break;

    case 'updateWord':
        try {
            (new ActionUpdateWord())->execute($_POST);
        } catch (Exception $e) {
            $message = $isAjax
                ? ['error' => $e->getMessage()]
                : 'Error en getWord.';
            sendJson($message, 500);
        }
        break;

    case 'deleteWord':
        (new ActionDeleteWord())->execute($_POST);
        break;

    default:
        $message = ['error' => 'Acción no válida'];
        sendJson($message, 400);
        break;
}
