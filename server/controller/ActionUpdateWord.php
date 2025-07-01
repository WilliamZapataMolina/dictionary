<?php

require_once __DIR__ . '/../daos/db.php';

// Definir la función si no está disponible (por si no se usa desde Controller.php directamente)
if (!function_exists('sendJson')) {
    function sendJson($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

class ActionUpdateWord
{
    public function execute($data)
    {
        // Validar campos requeridos
        $requiredFields = ['id', 'english', 'spanish', 'category', 'image_url'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                sendJson([
                    'success' => false,
                    'message' => "El campo '$field' es obligatorio."
                ], 400);
            }
        }

        $database = new DatabaseController();
        $db = $database->getConnection();

        $stmt = $db->prepare("UPDATE words SET english = ?, spanish = ?, category = ?, image_url = ? WHERE id = ?");

        try {
            $ok = $stmt->execute([
                trim($data['english']),
                trim($data['spanish']),
                trim($data['category']),
                trim($data['image_url']),
                (int)$data['id']
            ]);

            if ($ok) {
                sendJson([
                    'success' => true,
                    'message' => 'Palabra actualizada correctamente.'
                ]);
            } else {
                sendJson([
                    'success' => false,
                    'message' => 'Error al actualizar la palabra.'
                ], 500);
            }
        } catch (PDOException $e) {
            sendJson([
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ], 500);
        }
    }
}
