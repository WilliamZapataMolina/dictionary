<?php
// Incluir el archivo de conexión a la base de datos
require_once __DIR__ . '/../daos/db.php';

/**
 * Define la función sendJson si no ha sido definida previamente.
 * Esta función se usa para devolver respuestas JSON con el código HTTP apropiado.
 */
if (!function_exists('sendJson')) {
    function sendJson($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

/**
 * Clase que maneja la eliminación de una palabra del diccionario.
 */
class ActionDeleteWord
{
    public function execute($data)
    {
        // Validar si se proporcionó un ID válido
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            sendJson([
                'success' => false,
                'message' => 'ID no válido o no proporcionado.'
            ], 400);
        }

        // Convertir el ID a entero
        $id = (int)$data['id'];

        // Establecer conexión a la base de datos
        $database = new DatabaseController();
        $db = $database->getConnection();

        try {
            // Preparar y ejecutar la consulta para eliminar la palabra
            $stmt = $db->prepare("DELETE FROM words WHERE id = ?");
            $ok = $stmt->execute([$id]);

            // Comprobar si se eliminó alguna fila
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
