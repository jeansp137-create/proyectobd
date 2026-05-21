<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Notas - PDF</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        .header-print {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
        }

        .header-print h1 {
            font-size: 20px;
            margin: 0 0 5px 0;
            color: #0f172a;
            text-transform: uppercase;
            font-weight: 700;
        }

        .header-print h2 {
            font-size: 14px;
            margin: 0 0 15px 0;
            color: #475569;
            font-weight: 500;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            text-align: left;
            max-width: 600px;
            margin: 0 auto;
            font-size: 11px;
            background: #f8fafc;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .meta-item strong {
            color: #0f172a;
        }

        .table-print {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            margin-bottom: 30px;
        }

        .table-print th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 10px;
            border: 1px solid #0f172a;
            letter-spacing: 0.5px;
        }

        .table-print td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }

        .table-print tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .definitiva-col {
            font-weight: 700;
            background-color: #f1f5f9;
        }

        .badge-print {
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 10px;
        }

        .badge-pass {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .badge-fail {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .no-print-btn {
            background-color: #3b82f6;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
            transition: all 0.2s ease;
        }

        .no-print-btn:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }

        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #475569;
            margin-top: 40px;
            padding-top: 5px;
            color: #475569;
            font-size: 10px;
        }

        /* Ocultar botones e interfaces durante la impresión real */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <!-- Botón para forzar impresión manual si el disparador automático no corre -->
    <div class="no-print" style="text-align: center;">
        <button onclick="window.print()" class="no-print-btn">🖨️ Imprimir Planilla Oficial</button>
        <button onclick="window.close()" class="no-print-btn" style="background-color: #64748b; box-shadow: none;">❌ Cerrar Ventana</button>
    </div>

    <div class="header-print">
        <h1>Universidad de los Llanos</h1>
        <h2>Reporte Consolidado de Calificaciones - Planilla Oficial</h2>

        <div class="meta-grid">
            <div class="meta-item">
                <strong>Curso:</strong> <?= htmlspecialchars($curso['cod_cur']) ?> - <?= htmlspecialchars($curso['nomb_cur']) ?>
            </div>
            <div class="meta-item">
                <strong>Docente:</strong> <?= htmlspecialchars($_SESSION['docente_nombre']) ?>
            </div>
            <div class="meta-item">
                <strong>Año Académico:</strong> <?= htmlspecialchars($_SESSION['year_activo']) ?>
            </div>
            <div class="meta-item">
                <strong>Periodo Semestral:</strong> <?= htmlspecialchars($_SESSION['periodo_activo']) ?>
            </div>
        </div>
    </div>

    <table class="table-print">
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No.</th>
                <th style="width: 70px;">Código</th>
                <th>Estudiante</th>
                
                <!-- Columnas de Cohortes -->
                <?php foreach ($cohortes as $coh): ?>
                    <th class="text-center" style="width: 90px;">
                        <?= htmlspecialchars($coh['desc_nota']) ?>
                        <div style="font-size: 8px; font-weight: normal; margin-top: 2px;">
                            (<?= htmlspecialchars($coh['porcentaje']) ?>%)
                        </div>
                    </th>
                <?php endforeach; ?>

                <!-- Columna Definitiva 100% -->
                <th class="text-center" style="width: 90px;">Definitiva</th>
                <th class="text-center" style="width: 80px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $contador = 1;
            foreach ($estudiantes as $est): 
                $definitiva = 0.0;
                $sumaPorcentajes = 0.0;
            ?>
                <tr>
                    <td class="text-center"><?= $contador++ ?></td>
                    <td style="font-family: monospace; font-weight: 600;"><?= htmlspecialchars($est['cod_est']) ?></td>
                    <td style="font-weight: 500;"><?= htmlspecialchars($est['nomb_est']) ?></td>

                    <!-- Calificaciones por cohorte -->
                    <?php foreach ($cohortes as $coh): 
                        $nota_id = $coh['nota'];
                        $val_nota = isset($calificaciones[$est['cod_est']][$nota_id]) ? $calificaciones[$est['cod_est']][$nota_id] : '';
                        
                        if ($val_nota !== '') {
                            $definitiva += (float) $val_nota * ((float) $coh['porcentaje'] / 100.0);
                            $sumaPorcentajes += (float) $coh['porcentaje'];
                        }
                    ?>
                        <td class="text-center">
                            <?= $val_nota !== '' ? number_format($val_nota, 1) : '-' ?>
                        </td>
                    <?php endforeach; ?>

                    <!-- Definitiva Calculada -->
                    <td class="text-center definitiva-col">
                        <?php 
                        if ($sumaPorcentajes > 0) {
                            echo number_format($definitiva, 2);
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>

                    <!-- Estado Aprobado / Reprobado -->
                    <td class="text-center" style="font-weight: bold;">
                        <?php if ($sumaPorcentajes > 0): ?>
                            <?php if ($definitiva >= 3.0): ?>
                                <span class="badge-print badge-pass">APROBADO</span>
                            <?php else: ?>
                                <span class="badge-print badge-fail">REPROBADO</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color: #64748b; font-weight: normal;">S/N</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="signature-area">
        <div>
            <div class="signature-line">
                Prof. <?= htmlspecialchars($_SESSION['docente_nombre']) ?><br>
                Firma del Docente de la Asignatura
            </div>
        </div>
        <div>
            <div class="signature-line">
                Director de Departamento / Registro y Control<br>
                Validación de Calificaciones Oficial
            </div>
        </div>
    </div>
</body>
</html>
