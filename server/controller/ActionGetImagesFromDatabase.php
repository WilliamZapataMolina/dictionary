<?php
// Incluye las clases necesarias: tu conexión a la DB y el FileModel
require_once __DIR__ . '/../daos/db.php';
require_once __DIR__ . '/../daos/FileModel.php';

class ActionGetImagesFromDatabase
{
    private $baseUrl = '/dictionary/public/images/';

    public function execute()
    {
        $database = new DatabaseController();
        $db = $database->getConnection();
        if (!$db) {
            http_response_code(500);
            echo json_encode(['error' => 'Error de conexión a la base de datos']);
            exit;
        }
        $fileModel = new FileModel($db);

        $dbImages = $fileModel->getAllFiles();

        $imagesForFrontend = [];
        foreach ($dbImages as $img) {
            $fullUrlPath = $this->baseUrl . $img['path'];

            $imagesForFrontend[] = [
                'id' => $img['id'],
                'name' => $img['name'],
                'path' => $fullUrlPath
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($imagesForFrontend);
        exit;
    }
}
