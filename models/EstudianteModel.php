<?php
class EstudianteModel {
    private $db;

    public function __construct() {
        $conexion = Conexion::getInstancia();
        $this->db = $conexion->conectar();
    }

    
    public function obtenerTodosLosEstudiantes() {
        $sql = "SELECT * FROM estudiantes ORDER BY nomb_est ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    
    public function obtenerEstudiantePorCodigo($cod_est) {
        $sql = "SELECT * FROM estudiantes WHERE cod_est = :cod_est";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_est' => $cod_est]);
        return $stmt->fetch();
    }

    
    public function registrarEstudianteSimple($cod_est, $nomb_est) {
        $sql = "INSERT INTO estudiantes (cod_est, nomb_est) VALUES (:cod_est, :nomb_est)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cod_est'  => $cod_est,
            ':nomb_est' => $nomb_est
        ]);
    }

    
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

    
    public function inscribirEstudiante($cod_cur, $cod_est, $year, $periodo) {
        
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
            return false; 
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