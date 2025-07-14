<?php
// Incluir el archivo de conexión a la base de datos
require_once __DIR__ . '/../daos/db.php';

// Función utilitaria para enviar respuestas JSON estandarizadas
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
        // Campos obligatorios
        $requiredFields = ['word_in', 'meaning', 'category_id'];
        //file_put_contents(__DIR__ . '/../debug_addword.log', print_r($data, true));

        // Validación de campos requeridos
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

        $file_id = null; // Inicializamos la variable de la clave foránea de imagen


        try {
            $db->beginTransaction();

            // Si se envió un path válido para la imagen
            if (!empty($data['file_path']) && strtolower(trim($data['file_path'])) !== 'undefined') {
                $path = trim($data['file_path']);

                // Obtener extensión y nombre del archivo desde la ruta
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $name = pathinfo($path, PATHINFO_FILENAME);

                // Verificar si el archivo ya existe en la tabla 'files'
                $stmt = $db->prepare("SELECT id FROM files WHERE path = ?");
                $stmt->execute([$path]);
                $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingFile) {
                    // Si ya existe, usamos su ID
                    $file_id = $existingFile['id'];
                } else {
                    // Si no existe, lo insertamos y obtenemos su ID
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

            $db->commit(); //Confirmar cambios

            // Enviar respuesta de éxito o fallo
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
            // Revertir en caso de error
            $db->rollBack();
            sendJson([
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ], 500);
        }
    }
}
