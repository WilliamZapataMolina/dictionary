<?php

require_once __DIR__ . '/../daos/db.php';

class User
{
    protected $id;
    protected $email;
    protected $password;
    protected $db;

    public function __construct($email, DatabaseController $db = null)
    {
        $this->db = $db ?? new DatabaseController();
        $this->loadUserData($email);
    }

    // Cargar los datos del usuario desde la base de datos para login
    private function loadUserData($email)
    {
        $query = "SELECT id, email, password FROM Users WHERE email = :email";
        $params = [':email' => $email];
        $userData = $this->db->executeQuery($query, $params)->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            $this->id = $userData['id'];
            $this->email = $userData['email'];
            $this->password = $userData['password'];
        } else {
            throw new Exception("Usuario no encontrado.");
        }
    }

    // Validar las credenciales para login
    public function validatePassword($password)
    {
        return password_verify($password, $this->password);
    }

    // Métodos getter
    public function getId()
    {
        return $this->id;
    }
    public function getEmail()
    {
        return $this->email;
    }
}
