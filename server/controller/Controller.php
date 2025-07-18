<?php
// Iniciar la sesión si aún no ha sido iniciada
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
// Detectar la acción solicitada (por GET o POST)
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Determinar si la petición es AJAX (desde JS)
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Incluir dependencias y clases de acciones
require_once __DIR__ . '/../daos/db.php';
require_once __DIR__ . '/../daos/FileModel.php';
require_once __DIR__ . '/ActionLogin.php';
require_once __DIR__ . '/ActionLogout.php';
require_once __DIR__ . '/ActionGetWord.php';
require_once __DIR__ . '/ActionGetCategories.php';
require_once __DIR__ . '/ActionGetCategoriesWithImages.php';
require_once __DIR__ . '/ActionGetImagesFromFilesSystem.php';
require_once __DIR__ . '/ActionGetImagesFromDatabase.php';
require_once __DIR__ . '/ActionAddWord.php';
require_once __DIR__ . '/ActionUpdateWord.php';
require_once __DIR__ . '/ActionSearchWord.php';
require_once __DIR__ . '/ActionDeleteWord.php';
require_once __DIR__ . '/ActionCountWordsWithImages.php';

// Enrutador: ejecuta la acción correspondiente según el valor de `action`
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

    case 'getCategoriesWithImages':
        try {
            (new ActionGetCategoriesWithImages())->execute();
        } catch (Exception $e) {
            sendJson(['error' => 'Error al obtener categorías con imágenes: ' . $e->getMessage()], 500);
        }
        break;

    case 'getImagesFromFilesSystem':
        try {
            (new ActionGetImagesFromFilesSystem())->execute();
        } catch (Exception $e) {
            sendJson(['error' => 'Error al obtener imágenes: ' . $e->getMessage()], 500);
        }
        break;

    case 'getImagesFromDatabase':
        try {
            (new ActionGetImagesFromDatabase())->execute();
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

    case 'searchWord':
        try {
            (new ActionSearchWord())->execute();
        } catch (Exception $e) {
            $message = $isAjax
                ? ['error' => $e->getMessage()]
                : 'Error al buscar palabra.';
            sendJson($message, 500);
        }
        break;

    case 'deleteWord':
        (new ActionDeleteWord())->execute($_POST);
        break;

    case 'countWordsWithImages':
        (new ActionCountWordsWithImages())->execute();
        break;

    default:
        // Acción desconocida
        $message = ['error' => 'Acción no válida'];
        sendJson($message, 400);
        break;
}
