<?php

class Conexion {
    // Definimos las credenciales como propiedades privadas por seguridad
    private $host = 'localhost';
    private $port = '5432';
    private $dbname = 'proyectobd'; 
    private $user = 'postgres';
    private $password = '0192';   

    // Método principal que será llamado por los Modelos
    public function conectar() {
        try {
            // Construimos el Data Source Name (DSN)
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            
            // Opciones de seguridad y optimización
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ];

            // Instanciamos la clase nativa de PHP para bases de datos
            $pdo = new PDO($dsn, $this->user, $this->password, $opciones);
            
            return $pdo;

        } catch (PDOException $e) {
            // Si el motor de PostgreSQL está apagado o la clave es incorrecta, capturamos el golpe
            die("Error crítico conectando a la base de datos: " . $e->getMessage());
        }
    }
}
<<<<<<< HEAD:config/conexion.php

?>  HOLAHOALHOLA