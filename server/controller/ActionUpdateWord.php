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
        error_log(print_r($data, true));

        $requiredFields = ['id', 'word_in', 'meaning', 'category_id'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === 'undefined') {
                sendJson([
                    'success' => false,
                    'message' => "El campo '$field' es obligatorio."
                ], 400);
            }
        }

        try {
            $database = new DatabaseController();
            $db = $database->getConnection();

            // Validar y convertir correctamente el file_id desde $data, no desde $_POST
            $fileId = (isset($data['file_id']) && is_numeric($data['file_id']) && (int)$data['file_id'] > 0)
                ? (int)$data['file_id']
                : null;

            // Validar existencia del archivo si file_id no es null
            if ($fileId !== null) {
                $checkFileStmt = $db->prepare("SELECT COUNT(*) FROM files WHERE id = :id");
                $checkFileStmt->bindValue(':id', $fileId, PDO::PARAM_INT);
                $checkFileStmt->execute();

                if ($checkFileStmt->fetchColumn() == 0) {
                    sendJson([
                        'success' => false,
                        'message' => 'El archivo seleccionado no existe en la base de datos.'
                    ], 400);
                }
            }

            // Preparar sentencia de actualización
            $stmt = $db->prepare("UPDATE words 
                SET word_in = :word_in, 
                    meaning = :meaning, 
                    category_id = :category_id, 
                    file_id = :file_id 
                WHERE id = :id");

            $stmt->bindValue(':word_in', trim($data['word_in']), PDO::PARAM_STR);
            $stmt->bindValue(':meaning', trim($data['meaning']), PDO::PARAM_STR);
            $stmt->bindValue(':category_id', (int)$data['category_id'], PDO::PARAM_INT);
            $stmt->bindValue(':id', (int)$data['id'], PDO::PARAM_INT);

            if ($fileId === null) {
                $stmt->bindValue(':file_id', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':file_id', $fileId, PDO::PARAM_INT);
            }

            $ok = $stmt->execute();

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
