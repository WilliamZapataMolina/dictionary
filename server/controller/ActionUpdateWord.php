<?php

require_once __DIR__ . '/../daos/db.php';

class ActionUpdateWord
{
    public function execute()
    {
        $data = $_POST;

        if (!isset($data['id'], $data['english'], $data['spanish'], $data['category'], $data['image_url'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Faltan datos requeridos para actualizar.'
            ]);
            return;
        }

        $database = new DatabaseController();
        $db = $database->getConnection();

        $stmt = $db->prepare("UPDATE words SET english = ?, spanish = ?, category = ?, image_url = ? WHERE id = ?");

        $ok = $stmt->execute([
            $data['english'],
            $data['spanish'],
            $data['category'],
            $data['image_url'],
            $data['id']
        ]);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Palabra actualizada correctamente.' : 'Error al actualizar la palabra.'
        ]);
    }
}
