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
        error_log("Recibido ID: " . var_export($data['id'], true));
        error_log("word_in: " . var_export($data['word_in'], true));
        error_log("meaning: " . var_export($data['meaning'], true));
        error_log("category_id: " . var_export($data['category_id'], true));
        error_log("file_id: " . var_export($data['file_id'] ?? null, true));

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

            // Validar y convertir correctamente el file_id
            $fileId = isset($data['file_id']) && is_numeric($data['file_id']) && (int)$data['file_id'] > 0
                ? (int)$data['file_id']
                : null;

            // Verificar existencia del archivo si se proporcionó un file_id
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

            // Preparar sentencia de actualización (siempre incluye file_id)
            $sql = "UPDATE words 
                    SET word_in = :word_in, 
                        meaning = :meaning, 
                        category_id = :category_id, 
                        file_id = :file_id
                    WHERE id = :id";

            $stmt = $db->prepare($sql);

            $stmt->bindValue(':word_in', trim($data['word_in']), PDO::PARAM_STR);
            $stmt->bindValue(':meaning', trim($data['meaning']), PDO::PARAM_STR);
            $stmt->bindValue(':category_id', (int)$data['category_id'], PDO::PARAM_INT);
            $stmt->bindValue(':id', (int)$data['id'], PDO::PARAM_INT);

            // file_id puede ser NULL
            if ($fileId === null) {
                $stmt->bindValue(':file_id', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':file_id', $fileId, PDO::PARAM_INT);
            }

            $ok = $stmt->execute();
            $rowCount = $stmt->rowCount();
            error_log("Filas afectadas: $rowCount");

            if ($ok && $rowCount > 0) {
                sendJson([
                    'success' => true,
                    'message' => 'Palabra actualizada correctamente.'
                ]);
            } elseif ($ok && $rowCount === 0) {
                sendJson([
                    'success' => true,
                    'message' => 'La palabra no fue modificada (posiblemente los valores son iguales).'
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
