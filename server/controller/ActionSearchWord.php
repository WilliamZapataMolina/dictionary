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
class ActionSearchWord
{
    public function execute()
    {
        try {
            $db = (new DatabaseController())->getConnection();
            $searchTerm = $_POST['search'] ?? $_GET['search'] ?? '';

            if (empty($searchTerm)) {
                sendJson(['error' => 'Search term is required'], 400);
            }

            $sql = "
            SELECT w.word_in, w.meaning, c.name AS category, f.path AS image_url, w.id, w.category_id
            FROM words w
            JOIN categories c ON w.category_id = c.id
            LEFT JOIN files f ON w.file_id = f.id
            WHERE w.word_in LIKE :searchTerm
            ORDER BY w.id DESC
        ";

            $stmt = $db->prepare($sql);
            $stmt->execute([':searchTerm' => '%' . $searchTerm . '%']);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJson($result);
        } catch (Exception $e) {
            sendJson(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
