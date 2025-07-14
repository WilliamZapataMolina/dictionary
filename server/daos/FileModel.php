<?php
require_once __DIR__ . '/db.php';

/**
 * Clase FileModel
 * 
 * Representa el modelo para acceder a la tabla 'files' en la base de datos.
 * Permite obtener información de los archivos (imágenes, etc.) almacenados.
 */
class FileModel
{
    private $conn;
    /**
     * Constructor que recibe la conexión PDO.
     * 
     * @param PDO $db Objeto de conexión a la base de datos.
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Obtiene todos los archivos almacenados en la tabla 'files'.
     * 
     * @return array Arreglo asociativo con los campos 'id', 'name' y 'path' de cada archivo.
     */
    public function getAllFiles()
    {
        $query = "SELECT id, name, path FROM files ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
