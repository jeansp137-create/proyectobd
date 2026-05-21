<?php
class EstudianteModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    /**
     * Obtiene los estudiantes matriculados en un curso específico en un año y periodo
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
            ':year' => $year, 
            ':periodo' => $periodo
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene absolutamente todos los estudiantes de la universidad (para el select de matrícula)
     */
    public function obtenerTodosLosEstudiantes() {
        $sql = "SELECT * FROM estudiantes ORDER BY nomb_est ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Inscribe a un alumno en el curso
     */
    public function inscribirEstudiante($cod_cur, $cod_est, $year, $periodo) {
        try {
            $sql = "INSERT INTO inscripciones (cod_cur, cod_est, year, periodo) 
                    VALUES (:cod_cur, :cod_est, :year, :periodo)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':cod_cur' => $cod_cur, 
                ':cod_est' => $cod_est, 
                ':year' => $year, 
                ':periodo' => $periodo
            ]);
        } catch (PDOException $e) {
            // Si el motor lanza error (ej. llave primaria duplicada porque ya está inscrito), devolvemos falso
            return false; 
        }
    }

    /**
     * Elimina la inscripción de un alumno
     */
    public function eliminarInscripcion($cod_cur, $cod_est, $year, $periodo) {
        $sql = "DELETE FROM inscripciones 
                WHERE cod_cur = :cod_cur AND cod_est = :cod_est AND year = :year AND periodo = :periodo";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cod_cur' => $cod_cur, 
            ':cod_est' => $cod_est, 
            ':year' => $year, 
            ':periodo' => $periodo
        ]);
    }
}
?>