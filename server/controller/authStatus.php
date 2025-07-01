<?php
// Iniciar sesión si no está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

// Revisar si hay un usuario en la sesión
$user = $_SESSION['logged_user'] ?? null;

// Devolver estado de sesión
echo json_encode([
    'is_logged_in' => $user !== null,
    'is_admin' => $user && isset($user['rol']) && $user['rol'] === 'Admin',
    'user' => $user
]);
