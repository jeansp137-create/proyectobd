<?php
class Conexion {
    private static $instancia = null;
    private $pdo;
    private static $conectarCount = 0; // Cuenta las conexiones físicas reales a la base de datos

    private $host = 'localhost';
    private $port = '5432';
    private $dbname = 'proyectobd'; 
    private $user = 'postgres';
    private $password = '0192';   

    // Constructor privado para evitar la creación directa de instancias mediante 'new'
    private function __construct() {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ];
            $this->pdo = new PDO($dsn, $this->user, $this->password, $opciones);
            self::$conectarCount++; // Solo se incrementa al instanciar físicamente
        } catch (PDOException $e) {
            die("Error crítico conectando a la base de datos: " . $e->getMessage());
        }
    }

    // Método estático para obtener la única instancia de la clase Conexion
    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    // Retorna la conexión PDO
    public function conectar() {
        return $this->pdo;
    }

    // Retorna el total de conexiones físicas realizadas durante esta petición
    public static function getConectarCount() {
        return self::$conectarCount;
    }
}
?>