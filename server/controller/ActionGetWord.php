<?php

require_once __DIR__ . '/../daos/db.php';

// Definir la función sendJson si aún no está definida
if (!function_exists('sendJson')) {
    function sendJson($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

class ActionGetWord
{
    public function execute()
    {
        $database = new DatabaseController();
        $db = $database->getConnection();

        try {
            $stmt = $db->prepare("
                SELECT 
                    w.id, 
                    w.english, 
                    w.spanish, 
                    w.category, 
                    f.path AS image_url
                FROM words w
                LEFT JOIN files f ON w.file_id = f.id
            ");
            $stmt->execute();

            $words = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJson([
                'success' => true,
                'data' => $words
            ]);
        } catch (PDOException $e) {
            sendJson([
                'success' => false,
                'message' => 'Error al obtener las palabras: ' . $e->getMessage()
            ], 500);
        }
    }
}
