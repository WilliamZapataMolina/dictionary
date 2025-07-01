<?php

require_once __DIR__ . '/../daos/db.php';

// Asegúrate de que esta función esté disponible si no se usa Controller.php directamente
if (!function_exists('sendJson')) {
    function sendJson($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

class ActionAddWord
{
    public function execute($data)
    {
        // Validar campos requeridos
        $requiredFields = ['english', 'spanish', 'category', 'image_url'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                sendJson([
                    'success' => false,
                    'message' => "El campo '$field' es obligatorio."
                ], 400);
            }
        }

        // Conexión a la base de datos
        $database = new DatabaseController();
        $db = $database->getConnection();

        // Preparar consulta
        $stmt = $db->prepare("INSERT INTO words (english, spanish, category, image_url) VALUES (?, ?, ?, ?)");

        try {
            $ok = $stmt->execute([
                trim($data['english']),
                trim($data['spanish']),
                trim($data['category']),
                trim($data['image_url'])
            ]);

            if ($ok) {
                sendJson([
                    'success' => true,
                    'message' => 'Palabra agregada correctamente.'
                ]);
            } else {
                sendJson([
                    'success' => false,
                    'message' => 'Error al agregar palabra.'
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
