<?php

require_once __DIR__ . '/../daos/db.php';

// Definir la función sendJson si aún no está definida
if (!function_exists('sendJson')) {
    function sendJson($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

class ActionDeleteWord
{
    public function execute($data)
    {
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            sendJson([
                'success' => false,
                'message' => 'ID no válido o no proporcionado.'
            ], 400);
        }

        $id = (int)$data['id'];

        $database = new DatabaseController();
        $db = $database->getConnection();

        try {
            $stmt = $db->prepare("DELETE FROM words WHERE id = ?");
            $ok = $stmt->execute([$id]);

            if ($ok && $stmt->rowCount() > 0) {
                sendJson([
                    'success' => true,
                    'message' => 'Palabra eliminada correctamente.'
                ]);
            } else {
                sendJson([
                    'success' => false,
                    'message' => 'No se encontró la palabra o no se pudo eliminar.'
                ], 404);
            }
        } catch (PDOException $e) {
            sendJson([
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ], 500);
        }
    }
}
