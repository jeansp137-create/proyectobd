<?php
class Conexion {
    private $host = 'localhost';
    private $port = '5432';
    private $dbname = 'proyectobd'; 
    private $user = 'postgres';
    private $password = '0192';   

    public function conectar() {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ];
            $pdo = new PDO($dsn, $this->user, $this->password, $opciones);
            return $pdo;
        } catch (PDOException $e) {
            die("Error crítico conectando a la base de datos: " . $e->getMessage());
        }
    }
}
?>