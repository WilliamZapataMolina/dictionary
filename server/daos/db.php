<?php
class DatabaseController
{
    private  $host = 'localhost';
    private  $db_name = 'dictionary';
    private  $username = 'root';
    private  $password = '';
    private  $connection;

    public function __construct()
    {
        $this->connect();
    }
    private function connect()
    {
        try {
            $this->connection = new PDO("mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4", $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    public function getConnection()
    {
        return $this->connection;
    }
    //Método para ejecutar una consulta
    public function executeQuery($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            echo "Error al ejecutar la consulta: " . $e->getMessage();
            exit();
        }
    }
    //Método para verificar si un usuario existe en la tabla admins
    public function isAdmin($email)
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $params = [':email' => $email];
        $stmt = $this->executeQuery($query, $params);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user && isset($user['rol']) && $user['rol'] === 'Admin';
    }
    //Método para cerrar la conexión
    public function close()
    {
        $this->connection = null;
    }
}
