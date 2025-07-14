<?php
// Requiere acceso a la base de datos
require_once __DIR__ . '/../daos/db.php';

/**
 * Clase que cuenta cuántas palabras tienen una imagen asociada (es decir, file_id NO NULL).
 */
class ActionCountWordsWithImages
{
    public function execute()
    {
        // Crear conexión a la base de datos
        $database = new DatabaseController();
        $db = $database->getConnection();

        try {
            // Contar palabras que tienen file_id (es decir, tienen imagen asociada)
            $stmt = $db->prepare("
                SELECT COUNT(*) AS count FROM words WHERE file_id IS NOT NULL
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Enviar respuesta JSON con el conteo
            echo json_encode([
                'success' => true,
                'count' => (int)$result['count']
            ]);
        } catch (PDOException $e) {
            // Enviar error si la consulta falla
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error en BD: ' . $e->getMessage()
            ]);
        }
    }
}
