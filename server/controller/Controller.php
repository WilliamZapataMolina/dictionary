<?php
$action = $_GET['action'] ?? $_POST['action'] ?? null;

switch ($action) {
    case 'login':
        require_once 'ActionLogin.php';
        break;
    case 'register':
        require_once 'ActionRegister.php';
        break;
    case 'logout':
        require_once 'ActionLogout.php';
        break;
    case 'get_word':
        require_once 'ActionAddWord.php';
        break;
    case 'add_word':
        require_once 'ActionAddWord.php';
        break;
    case 'update_word':
        require_once 'ActionUpdateWord.php';
        break;
    case 'delete_word':
        require_once 'ActionDeleteWord.php';
        break;
    default:
        echo json_encode(['error' => 'Acción no válida']);
        http_response_code(400);
}
