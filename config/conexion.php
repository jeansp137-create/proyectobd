<?php
class Conexion {
    private static $instancia = null;
    private $pdo;
    private static $conectarCount = 0; 

    private $host = 'localhost';
    private $port = '5432';
    private $dbname = 'proyectobd'; 
    private $user = 'postgres';
    private $password = '0192';   

    
    private function __construct() {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ];
            $this->pdo = new PDO($dsn, $this->user, $this->password, $opciones);
            self::$conectarCount++; 
        } catch (PDOException $e) {
            die("Error crítico conectando a la base de datos: " . $e->getMessage());
        }
    }

    
    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    
    public function conectar() {
        return $this->pdo;
    }

    
    public static function getConectarCount() {
        return self::$conectarCount;
    }
}
?>