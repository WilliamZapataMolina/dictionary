<?php

require_once __DIR__ . '/../daos/db.php';

class ActionAddWord
{
    public function execute()
    {
        $data = $_POST;

        $database = new DatabaseController();
        $db = $database->getConnection();

        // Preparar la consulta
        $stmt = $db->prepare("INSERT INTO words (english, spanish, category, image_url) VALUES (?, ?, ?, ?)");

        // Ejecutar la consulta con los datos
        $ok = $stmt->execute([
            $data['english'],
            $data['spanish'],
            $data['category'],
            $data['image_url']
        ]);

        // Devolver respuesta en JSON
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Palabra agregada correctamente.' : 'Error al agregar palabra.'
        ]);
    }
}
