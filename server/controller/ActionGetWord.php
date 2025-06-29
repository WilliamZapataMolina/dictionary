<?php

require_once __DIR__ . '/../daos/db.php';

class ActionGetWord
{
    public function execute()
    {
        $database = new DatabaseController(); //Instancia del controlador 
        $db = $database->getConnection(); //Obtenemos la conexión a la base de datos

        $stmt = $db->prepare("SELECT * FROM words");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
