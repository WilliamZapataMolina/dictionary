<?php

require_once __DIR__ . '/../daos/db.php';

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
        $requiredFields = ['id', 'word_in', 'meaning', 'category_id', 'file_id'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                sendJson([
                    'success' => false,
                    'message' => "El campo '$field' es obligatorio."
                ], 400);
            }
        }

        try {
            $database = new DatabaseController();
            $db = $database->getConnection();

            $stmt = $db->prepare("UPDATE words SET word_in = ?, meaning = ?, category_id = ?, file_id = ? WHERE id = ?");
            $ok = $stmt->execute([
                trim($data['word_in']),
                trim($data['meaning']),
                (int)$data['category_id'],
                (int)$data['file_id'],
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
