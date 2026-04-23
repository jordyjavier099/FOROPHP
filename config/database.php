<?php
define('DB_HOST', '127.0.0.1'); 
define('DB_PORT', '3307');      
define('DB_USER', 'root');
define('DB_PASS', '');          
define('DB_NAME', 'auth_system');

function getConnection() {
    try {
        // Agregar el puerto al string de conexión
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        $conn = new PDO($dsn, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
?>