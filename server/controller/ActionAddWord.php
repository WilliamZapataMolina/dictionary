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

class ActionAddWord
{
    public function execute($data)
    {
        $requiredFields = ['word_in', 'meaning', 'category_id'];
        file_put_contents(__DIR__ . '/../debug_addword.log', print_r($data, true));

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

        $file_id = null;

        try {
            $db->beginTransaction();

            if (!empty($data['file_path']) && strtolower(trim($data['file_path'])) !== 'undefined') {
                $path = trim($data['file_path']);

                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $name = pathinfo($path, PATHINFO_FILENAME);

                // Buscar si ya existe la ruta en la tabla files
                $stmt = $db->prepare("SELECT id FROM files WHERE path = ?");
                $stmt->execute([$path]);
                $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingFile) {
                    $file_id = $existingFile['id'];
                } else {
                    // Insertar en la tabla files
                    $stmt = $db->prepare("INSERT INTO files (name, extension, path) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $extension, $path]);
                    $file_id = $db->lastInsertId();
                }
            }

            // Insertar en tabla words
            $stmt = $db->prepare("
                INSERT INTO words (word_in, meaning, date_register, category_id, file_id)
                VALUES (?, ?, NOW(), ?, ?)
            ");

            $ok = $stmt->execute([
                trim($data['word_in']),
                trim($data['meaning']),
                (int)$data['category_id'],
                $file_id
            ]);

            $db->commit();

            if ($ok) {
                sendJson([
                    'success' => true,
                    'message' => 'Palabra agregada correctamente.'
                ]);
            } else {
                sendJson([
                    'success' => false,
                    'message' => 'Error al insertar palabra.'
                ], 500);
            }
        } catch (PDOException $e) {
            $db->rollBack();
            sendJson([
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ], 500);
        }
    }
}
