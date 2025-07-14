<?php

/**
 * Clase que encapsula la lógica de conexión y operaciones básicas con la base de datos MySQL.
 * Utiliza PDO para la conexión y ejecución de consultas.
 */
class DatabaseController
{
    // Parámetros de conexión
    private  $host = 'localhost';
    private  $db_name = 'dictionary';
    private  $username = 'root';
    private  $password = '';
    private  $connection;

    /**
     * Constructor: automáticamente intenta conectar a la base de datos.
     */
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
    /**
     * Devuelve la conexión activa de PDO(PHP Data Objects u objetos de datos de PHP).
     *
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
    /**
     * Ejecuta una consulta preparada y devuelve el statement resultante.
     *
     * @param string $query Consulta SQL con parámetros.
     * @param array $params Parámetros para la consulta (opcional).
     * @return PDOStatement Resultado de la ejecución.
     */
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
    /**
     * Verifica si un usuario con el correo electrónico dado es administrador.
     *
     * @param string $email Correo electrónico del usuario.
     * @return bool True si el usuario es administrador, false si no lo es.
     */
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
