<?php
// Requiere la conexión a la base de datos
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
/**
 * Clase que maneja la acción de obtener las categorías con imágenes asociadas.
 * Esta acción devuelve un listado de categorías donde cada una contiene
 * hasta 5 imágenes de palabras relacionadas.
 */
class ActionGetCategoriesWithImages
{
    public function execute()
    {
        $database = new DatabaseController();
        $db = $database->getConnection();

        // Solo ejecuta si se pasó correctamente la acción por GET
        if (isset($_GET['action']) && $_GET['action'] === 'getCategoriesWithImages') {
            try {
                // Consulta: obtiene nombre de categoría y ruta de imagen,
                // uniendo las tablas categories, words y files.
                $stmt = $db->prepare("
                    SELECT c.name AS category, f.path AS image_url
                    FROM categories c
                    JOIN words w ON w.category_id = c.id
                    JOIN files f ON f.id = w.file_id
                    WHERE f.path IS NOT NULL
                    ORDER BY c.name
                ");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $result = [];

                // Agrupar resultados por categoría
                foreach ($rows as $row) {
                    $cat = $row['category'];
                    if (!isset($result[$cat])) {
                        $result[$cat] = [];
                    }

                    if (count($result[$cat]) < 5) { // máximo 5 imágenes por categoría
                        $result[$cat][] = $row['image_url'];
                    }
                }
                // Devolver el JSON con las categorías y sus imágenes
                echo json_encode($result);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al obtener categorías con imágenes: ' . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Acción inválida o no especificada'
            ]);
        }
    }
}
