<?php
require('../../bootstrap.php');
require("../../pdf/mc_table.php");

$MiPDF = new PDF_MC_Table('L', 'mm', 'A4');
$MiPDF->AddFont('Noto Sans HK Bold','','NotoSansHK-Bold.php');
$MiPDF->AddFont('Noto Sans HK Bold','B','NotoSansHK-Bold.php');
$MiPDF->AddFont('Noto Sans HK','','NotoSansHK-Regular.php');
$MiPDF->AddFont('Noto Sans HK','B','NotoSansHK-Bold.php');

$MiPDF->AddFont('NewsGotT','','NewsGotT.php');
$MiPDF->AddFont('NewsGotT','B','NewsGotTb.php');
$MiPDF->AddFont('ErasDemiBT','','ErasDemiBT.php');
$MiPDF->AddFont('ErasDemiBT','B','ErasDemiBT.php');
$MiPDF->AddFont('ErasMDBT','','ErasMDBT.php');
$MiPDF->AddFont('ErasMDBT','I','ErasMDBT.php');

$MiPDF->SetMargins(5, 5, 5);
$MiPDF->SetAutoPageBreak(true, 10);
$MiPDF->SetDisplayMode('fullpage');

$todasUnidades = (isset($_POST['todasUnidades']) && $_POST['todasUnidades'] == 1) ? 1 : 0;

$unidades = array();

if (isset($_GET['unidad']) || isset($_POST['unidad'])) {
	if (isset($_GET['unidad'])) $unidades[] = $_GET['unidad'];
	else $unidades = $_POST['unidad'];
}
else {
	if (acl_permiso($carg, array('1','7')) || $todasUnidades == 1) {
		$result_unidades = mysqli_query($db_con, "SELECT DISTINCT nomunidad FROM unidades ORDER BY nomunidad ASC");
		while ($row_unidades = mysqli_fetch_array($result_unidades)) $unidades[] = $row_unidades['nomunidad'];
		mysqli_free_result($result_unidades);
	}
	else {
		$result_unidades = mysqli_query($db_con, "SELECT DISTINCT grupo AS nomunidad FROM profesores WHERE profesor='".mb_strtoupper($_SESSION['profi'], 'UTF-8')."' ORDER BY grupo ASC");
		$result_unidades_pmar = mysqli_query($db_con, "SELECT DISTINCT CONCAT(u.nomunidad, ' (PMAR)') AS nomunidad FROM unidades AS u JOIN materias AS m ON u.nomunidad = m.grupo JOIN profesores AS p ON u.nomunidad = p.grupo WHERE m.abrev LIKE '%**%' AND p.profesor='".mb_strtoupper($_SESSION['profi'], 'UTF-8')."' ORDER BY u.nomunidad ASC");
		while ($row_unidades = mysqli_fetch_array($result_unidades)) $unidades[] = $row_unidades['nomunidad'];
		mysqli_free_result($result_unidades);
		while ($row_unidades = mysqli_fetch_array($result_unidades_pmar)) $unidades[] = $row_unidades['nomunidad'];
		mysqli_free_result($result_unidades_pmar);
		asort($unidades);
	}
}

foreach ($unidades as $unidad) {

	// COMPROBAMOS SI ES UN PMAR
	$esPMAR = (stristr($unidad, ' (PMAR)') == true) ? 1 : 0;
	if ($esPMAR) {
		$unidad = str_ireplace(' (PMAR)', '', $unidad);
	}

	// Control en la obtención del listado. Solo los profesores que imparten materia en la unidad pueden visualizar el listado.
	if (! acl_permiso($carg, array('1','7')) && $todasUnidades != 1) {
		$result_unidades = mysqli_query($db_con, "SELECT * FROM profesores WHERE profesor='".$_SESSION['profi']."' AND grupo = '".$unidad."'");
		if (! mysqli_num_rows($result_unidades)) die ('FORBIDDEN');
	}

	// Comprobamos y obtenemos los alumnos del profesor en su asignatura
	$result_alumnos_profesor = mysqli_query($db_con, "SELECT alumnos FROM grupos WHERE profesor = '".$_SESSION['profi']."' AND curso = '".$unidad."' LIMIT 1");
	if (mysqli_num_rows($result_alumnos_profesor)) {
		$row_alumnos_profesor = mysqli_fetch_array($result_alumnos_profesor);

		$row_alumnos_profesor['alumnos'] = rtrim($row_alumnos_profesor['alumnos'], ',');
		$alumnos_profesor = explode(',', $row_alumnos_profesor['alumnos']);

		// Sustituimos el NC de la tabla FALUMNO por el NIE del alumno
		$alumnos_profesor_por_claveal = array();
		foreach ($alumnos_profesor as $alumno_profesor_nc) {
			array_push($alumnos_profesor_por_claveal, $alumno_profesor_nc);
		}
	}

	$cursos = array();
	$result_cursos = mysqli_query($db_con, "SELECT cursos.nomcurso FROM unidades JOIN cursos ON unidades.idcurso = cursos.idcurso WHERE unidades.nomunidad = '".$unidad."' ORDER BY nomcurso ASC");
	while ($row_cursos = mysqli_fetch_array($result_cursos)) $cursos[] = $row_cursos['nomcurso'];
	mysqli_free_result($result_cursos);

	foreach ($cursos as $curso) {
		$MiPDF->Addpage();

		$MiPDF->SetFont('Noto Sans HK', 'B', 10);
		$MiPDF->Multicell(0, 5, mb_strtoupper("Listado de clase y asignaturas matriculadas", 'UTF-8'), 0, 'C', 0 );
		$MiPDF->Ln(2);

		$MiPDF->SetFont('Noto Sans HK', 'B', 8);
		$MiPDF->Cell(13, 5, 'Unidad: ', 0, 0, 'L', 0);
		$MiPDF->SetFont('Noto Sans HK', '', 8);

		if ($esPMAR) {
			$MiPDF->Cell(120, 5, $unidad.' (PMAR) ('.$curso.')', 0, 0, 'L', 0 );
		}
		else {
			$MiPDF->Cell(120, 5, $unidad.' ('.$curso.')', 0, 0, 'L', 0 );
		}

		$MiPDF->SetFont('Noto Sans HK', 'B', 8);
		$MiPDF->Cell(27, 5, 'Curso académico: ', 0, 0, 'L', 0);
		$MiPDF->SetFont('Noto Sans HK', '', 8);
		$MiPDF->Cell(92, 5, $config['curso_actual'], 0, 1, 'L', 0 );

		// Obtenemos el tutor/a de la unidad
		$result = mysqli_query($db_con, "SELECT tutor FROM FTUTORES WHERE unidad='$unidad'");
		$row = mysqli_fetch_array($result);
		$tutor = $row['tutor'];
		mysqli_free_result($result);

		$MiPDF->SetFont('Noto Sans HK', 'B', 8);
		$MiPDF->Cell(13, 5, 'Tutor/a: ', 0, 0, 'L', 0);
		$MiPDF->SetFont('Noto Sans HK', '', 8);
		$MiPDF->Cell(120, 5, nomprofesor($tutor), 0, 0, 'L', 0 );

		$MiPDF->SetFont('Noto Sans HK', 'B', 8);
		$MiPDF->Cell(11, 5, 'Fecha: ', 0, 0, 'L', 0);
		$MiPDF->SetFont('Noto Sans HK', '', 8);
		$MiPDF->Cell(108, 5, date('d/m/Y'), 0, 1, 'L', 0 );

		$MiPDF->Ln(2);

		// ENCABEZADO DE LA TABLA

		$width_columns = array(6, 50);
		$columns_names = array('NC', 'Alumno/a');
		$columns_aligns = array('L', 'L');


		$result_asignaturas = mysqli_query($db_con, "SELECT codigo, abrev FROM asignaturas WHERE abrev NOT LIKE '%\_%' AND abrev <> 'TCA' AND curso = '".$curso."' ORDER BY abrev ASC");
		while ($row_asignaturas = mysqli_fetch_array($result_asignaturas)) {
			if ($esPMAR && (stristr($row_asignaturas['abrev'], '**') == true OR $row_asignaturas['abrev'] == 'ACT'  OR $row_asignaturas['abrev'] == 'ALS')) {
				$codasig_pmar = $row_asignaturas['codigo'];
			}

			array_push($width_columns, 7.5);
			if (! $esPMAR && $row_asignaturas['abrev'] == 'AMBCM') {
				array_push($columns_names, 'PMAR');
			}
			else {
				array_push($columns_names, $row_asignaturas['abrev']);
			}
			array_push($columns_aligns, 'C');
		}

		array_push($width_columns, 7.5);
		array_push($columns_names, 'Total');
		array_push($columns_aligns, 'C');

		// Imprime el encabezado
		$MiPDF->SetWidths($width_columns);
		$MiPDF->SetFont('Noto Sans HK', 'B', 5);
		$MiPDF->SetTextColor(255, 255, 255);
		$MiPDF->SetFillColor(61, 61, 61);

		$MiPDF->SetFont('Noto Sans HK', '', 5);
		$MiPDF->SetAligns($columns_aligns);
		$MiPDF->Row($columns_names, 'DF', 6);

		// FIN DE ENCABEZADO DE LA TABLA


		// CUERPO DE LA TABLA
		$result = mysqli_query($db_con, "SELECT claveal, apellidos, nombre, combasi, matriculas FROM alma WHERE unidad='".$unidad."' AND curso = '".$curso."' ORDER BY apellidos ASC, nombre ASC");
		$MiPDF->SetTextColor(0, 0, 0);
		$MiPDF->SetFont('Noto Sans HK', '', 7);

		$MiPDF->SetFillColor(239,240,239);

		$total_alumnos = 0; // Total alumnos en la unidad

		$nc = 0;
		$fila = 1;
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			if ($fila % 2 == 0) $fill = 'DF';
			else $fill = '';

			$nc++;

			$total_asigmat = 0; // Total asignaturas matriculadas

			$aux = '';

			if ($row['matriculas'] > 1) {
				$aux .= ' (R)';
			}

			// Comprobamos si el centro utiliza el módulo de matriculaciones y obtenemos si el alumno es bilingüe o está exento de alguna materia
			$result_datos_matricula = mysqli_query($db_con, "SELECT bilinguismo, exencion FROM matriculas WHERE claveal = '".$row['claveal']."'");
			$row_datos_matricula = mysqli_fetch_array($result_datos_matricula);
			$result_datos_matricula_bach = mysqli_query($db_con, "SELECT bilinguismo FROM matriculas_bach WHERE claveal = '".$row['claveal']."'");
			$row_datos_matricula_bach = mysqli_fetch_array($result_datos_matricula_bach);

			if ($row_datos_matricula['bilinguismo'] == 'Si') {
				$aux .= ' (B)';
			}
			if ($row_datos_matricula_bach['bilinguismo'] == 'Si') {
				$aux .= ' (B)';
			}

			if ($row_datos_matricula['exencion'] == 1) {
				$aux .= ' (E)';
			}

			$row_data = array($nc, $row['apellidos'].', '.$row['nombre'].$aux);

			mysqli_data_seek($result_asignaturas, 0);
			while ($row_asignaturas = mysqli_fetch_array($result_asignaturas)) {
				$row['combasi'] = rtrim($row['combasi'], ':');
				$combasi = explode(':', $row['combasi']);
				if(in_array($row_asignaturas['codigo'], $combasi)) {
					$matriculado = 'X';
				}
				else {
					$matriculado = '';
				}

				if ($matriculado == 'X') $total_asigmat++;

				array_push($row_data, $matriculado);
			}

			array_push($row_data, $total_asigmat); // Total asignaturas matriculadas

			if (! isset($alumnos_profesor_por_claveal) || (isset($alumnos_profesor_por_claveal) && in_array($row['claveal'], $alumnos_profesor_por_claveal))) {

				$MiPDF->Row($row_data, $fill, 5);

				$total_alumnos++;

				$fila++;
			}
		}

		// FIN CUERPO DE LA TABLA

		mysqli_free_result($result);

	}

}

$MiPDF->Output();
?>
