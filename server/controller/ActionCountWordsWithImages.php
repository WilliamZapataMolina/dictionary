<?php
require_once __DIR__ . '/../daos/db.php';

class ActionCountWordsWithImages
{
    public function execute()
    {
        $database = new DatabaseController();
        $db = $database->getConnection();

        try {
            $stmt = $db->prepare("
                SELECT COUNT(*) AS count FROM words WHERE file_id IS NOT NULL
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'count' => (int)$result['count']
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error en BD: ' . $e->getMessage()
            ]);
        }
    }
}
