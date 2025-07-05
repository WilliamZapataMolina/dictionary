<?php

require_once __DIR__ . '/../daos/db.php';

class ActionGetCategories
{
    public function execute()
    {
        $database = new DatabaseController();
        $db = $database->getConnection();

        try {
            $stmt = $db->prepare("SELECT id, name FROM categories ORDER BY name ASC");
            $stmt->execute();

            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($categories);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener las categorías: ' . $e->getMessage()
            ]);
        }
    }
}
