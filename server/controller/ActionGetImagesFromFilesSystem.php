<?php

class ActionGetImagesFromFilesSystem
{
    // Ruta absoluta en disco al directorio 'images'
    private $baseDir = __DIR__ . '/../../public/images/';

    // URL base para acceder a las imágenes desde el navegador
    private $baseUrl = '/dictionary/public/images/';

    public function execute()
    {
        $images = $this->listImages($this->baseDir, $this->baseUrl);

        header('Content-Type: application/json');
        echo json_encode($images);
        exit;
    }

    private function listImages($dir, $baseUrl)
    {
        $result = [];
        $folders = scandir($dir);

        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $fullFolderPath = $dir . DIRECTORY_SEPARATOR . $folder;
            if (is_dir($fullFolderPath)) {
                $files = scandir($fullFolderPath);

                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') continue;
                    $fullFilePath = $fullFolderPath . DIRECTORY_SEPARATOR . $file;

                    if (is_file($fullFilePath)) {
                        $result[] = [
                            'path' => $baseUrl . $folder . '/' . $file,
                            'name' => $file
                        ];
                    }
                }
            }
        }

        return $result;
    }
}
