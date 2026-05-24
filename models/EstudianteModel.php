<?php
class EstudianteModel {
    private $db;

    public function __construct() {
        $conexion = Conexion::getInstancia();
        $this->db = $conexion->conectar();
    }

    /**
     * Obtiene todos los estudiantes registrados en la universidad
     */
    public function obtenerTodosLosEstudiantes() {
        $sql = "SELECT * FROM estudiantes ORDER BY nomb_est ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un estudiante específico por su código
     */
    public function obtenerEstudiantePorCodigo($cod_est) {
        $sql = "SELECT * FROM estudiantes WHERE cod_est = :cod_est";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_est' => $cod_est]);
        return $stmt->fetch();
    }

    /**
     * Registra un estudiante simple en la universidad (útil para la carga de CSV)
     */
    public function registrarEstudianteSimple($cod_est, $nomb_est) {
        $sql = "INSERT INTO estudiantes (cod_est, nomb_est) VALUES (:cod_est, :nomb_est)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cod_est'  => $cod_est,
            ':nomb_est' => $nomb_est
        ]);
    }

    /**
     * Obtiene los estudiantes inscritos en un curso, año y periodo específico
     */
    public function obtenerEstudiantesInscritos($cod_cur, $year, $periodo) {
        $sql = "SELECT e.cod_est, e.nomb_est 
                FROM estudiantes e 
                INNER JOIN inscripciones i ON e.cod_est = i.cod_est 
                WHERE i.cod_cur = :cod_cur AND i.year = :year AND i.periodo = :periodo 
                ORDER BY e.nomb_est ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cod_cur' => $cod_cur,
            ':year'    => $year,
            ':periodo' => $periodo
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Inscribe a un estudiante en un curso para un año y periodo específicos
     */
    public function inscribirEstudiante($cod_cur, $cod_est, $year, $periodo) {
        // Primero verificamos si ya existe la inscripción para evitar duplicados
        $sqlCheck = "SELECT COUNT(*) FROM inscripciones 
                     WHERE cod_cur = :cod_cur AND cod_est = :cod_est AND year = :year AND periodo = :periodo";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([
            ':cod_cur' => $cod_cur,
            ':cod_est' => $cod_est,
            ':year'    => $year,
            ':periodo' => $periodo
        ]);
        if ($stmtCheck->fetchColumn() > 0) {
            return false; // Ya está inscrito
        }

        $sql = "INSERT INTO inscripciones (cod_cur, cod_est, year, periodo) 
                VALUES (:cod_cur, :cod_est, :year, :periodo)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cod_cur' => $cod_cur,
            ':cod_est' => $cod_est,
            ':year'    => $year,
            ':periodo' => $periodo
        ]);
    }

    /**
     * Elimina la inscripción de un estudiante de un curso, año y periodo específicos
     */
    public function eliminarInscripcion($cod_cur, $cod_est, $year, $periodo) {
        $sql = "DELETE FROM inscripciones 
                WHERE cod_cur = :cod_cur AND cod_est = :cod_est AND year = :year AND periodo = :periodo";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cod_cur' => $cod_cur,
            ':cod_est' => $cod_est,
            ':year'    => $year,
            ':periodo' => $periodo
        ]);
    }
}
?>