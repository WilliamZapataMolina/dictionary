<?php

require_once __DIR__ . '/../daos/db.php';

class ActionDeleteWord
{
    public function execute()
    {
        if (!isset($_POST['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID no proporcionado.'
            ]);
            return;
        }

        $id = $_POST['id'];

        $database = new DatabaseController();
        $db = $database->getConnection();

        $stmt = $db->prepare("DELETE FROM words WHERE id = ?");
        $ok = $stmt->execute([$id]);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Palabra eliminada correctamente.' : 'Error al eliminar la palabra.'
        ]);
    }
}
