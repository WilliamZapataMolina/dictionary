<?php
require_once __DIR__ . '/db.php';

class FileModel
{
    private $conn;
    public function __construct($db)
    {
        $this->conn = $db;
    }
    public function getAllFiles()
    {
        $query = "SELECT id, name, path FROM files ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
