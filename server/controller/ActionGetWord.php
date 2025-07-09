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

class ActionGetWord
{
    public function execute()
    {
        $db = (new DatabaseController())->getConnection();

        $sql = "
            SELECT
                w.id,
                w.word_in,
                w.meaning,
                w.category_id,
                w.file_id,            
                c.name AS category,
                f.path AS file_path
            FROM words w
            JOIN categories c ON w.category_id = c.id
            LEFT JOIN files f   ON w.file_id = f.id
            ORDER BY w.id DESC
        ";

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendJson($result);
    }
}
