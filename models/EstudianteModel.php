<?php
class EstudianteModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    /**
     * Obtiene todos los estudiantes registrados en la universidad
     * @return array Lista de todos los estudiantes
     */
    public function obtenerTodosLosEstudiantes() {
        $sql = "SELECT * FROM estudiantes ORDER BY nomb_est ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los estudiantes inscritos en un curso, año y periodo específico
     * @param string $cod_cur Código del curso
     * @param int $year Año académico
     * @param string $periodo Periodo académico
     * @return array Lista de estudiantes inscritos
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
     * @param string $cod_cur Código del curso
     * @param string $cod_est Código del estudiante
     * @param int $year Año académico
     * @param string $periodo Periodo académico
     * @return bool True si tuvo éxito, False en caso contrario
     */
    public function inscribirEstudiante($cod_cur, $cod_est, $year, $periodo) {
        // Primero verificamos si ya existe la inscripción para evitar errores de llave primaria duplicada
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
     * Nota: Gracias a la restricción ON DELETE CASCADE, esto eliminará automáticamente las calificaciones del estudiante.
     * @param string $cod_cur Código del curso
     * @param string $cod_est Código del estudiante
     * @param int $year Año académico
     * @param string $periodo Periodo académico
     * @return bool True si se eliminó, False en caso contrario
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
