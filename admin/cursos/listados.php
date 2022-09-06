<?php
require('../../bootstrap.php');
require("../../pdf/mc_table.php");

class GranPDF extends PDF_MC_Table {
	function SetFontSpacing($size) {
		$size = ($size / 100);
	    if($this->FontSpacingPt==$size)
	        return;
	    $this->FontSpacingPt = $size;
	    $this->FontSpacing = $size/$this->k;
	    if ($this->page>0)
	        $this->_out(sprintf('BT %.3f Tc ET', $size));
	}

	function Header() {
		global $config;

		$this->SetTextColor(48, 46, 43);
		$this->SetFontSpacing(-10);
		$this->SetY(14);
		$this->SetFont('Noto Sans HK Bold','',16);
		$this->Cell(80,5,'Junta de Andalucía',0,1);
		$this->SetY(15);
		$this->Cell(75);
		$this->SetFontSpacing(0);
		$this->SetFont('Noto Sans HK','',10);
		$this->Cell(80,5,'Consejería de Desarrollo Educativo y Formación Profesional',0,1);
		$this->Cell(75);
		$this->SetTextColor(53, 110, 59);
		$this->SetFont('Noto Sans HK','',7);
		$this->Cell(80,5,mb_strtoupper($config['centro_denominacion']),0,1);
		$this->SetTextColor(255, 255, 255);
	}
	function Footer() {
		global $config;

		$this->SetTextColor(53, 110, 59);
		$this->Image( '../../img/pie.jpg', 0, 250, 25, '', 'jpg' );
		$this->SetY(275);
		$this->SetFont('Noto Sans HK','',7);
		$this->Cell(75);
		$this->Cell(80,4,$config['centro_direccion'].'. '.$config['centro_codpostal'].', '.$config['centro_localidad'].' ('.$config['centro_provincia'] .')',0,1);
		$this->Cell(75);
		$this->Cell(80,4,'Telf: '.$config['centro_telefono'].' '.(($config['centro_fax']) ? '   Fax: '.$config['centro_fax'] : ''),0,1);
		$this->Cell(75);
		$this->Cell(80,4,'Correo-e: '.$config['centro_email'],0,1);
		$this->SetTextColor(255, 255, 255);
	}
}

$todasUnidades = ((isset($_POST['todasUnidades']) && $_POST['todasUnidades'] == 1) || (isset($_GET['todasUnidades']) && $_GET['todasUnidades'] == 1)) ? 1 : 0;

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

$MiPDF = new GranPDF('P', 'mm', 'A4');
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

$MiPDF->SetMargins(25, 20, 20);
$MiPDF->SetDisplayMode('fullpage');

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

	// Comprobamos si el profesor imparte varias asignaturas en la unidad
	$result_asignaturas_profesor = mysqli_query($db_con, "SELECT DISTINCT materia FROM profesores WHERE profesor = '".$_SESSION['profi']."' AND grupo = '".$unidad."'");
	$flag_profesor_sin_asignaturas = 0;
	if (! mysqli_num_rows($result_asignaturas_profesor)) $flag_profesor_sin_asignaturas = 1;

	// IMPRESIÓN DE UNIDADES POR ASIGNATURAS DEL PROFESOR
	if (! $todasUnidades && ! $flag_profesor_sin_asignaturas) {

		while ($row_asignaturas_profesor = mysqli_fetch_array($result_asignaturas_profesor)) {
			// Obtenemos el código de la asignatura
			$result_codigo_asignatura = mysqli_query($db_con, "SELECT codigo FROM materias WHERE nombre = '".$row_asignaturas_profesor['materia']."' AND grupo = '".$unidad."' and abrev not like '%\_%' ");
			
			$asig= " AND (";
			while($row_codigo_asignatura = mysqli_fetch_array($result_codigo_asignatura)){
				$asig.= " asignatura = '".$row_codigo_asignatura['codigo']."' OR";
			}
			$asig = substr($asig, 0, -3).")";

			// Comprobamos y obtenemos los alumnos del profesor en su asignatura
			$result_alumnos_profesor = mysqli_query($db_con, "SELECT alumnos FROM grupos WHERE profesor = '".$_SESSION['profi']."' AND curso = '".$unidad."' $asig");
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

			$MiPDF->Addpage();
			$MiPDF->SetY(30);

			$MiPDF->SetFont('Noto Sans HK', 'B', 10);
			$MiPDF->Multicell(0, 5, mb_strtoupper("Listado de clase", 'UTF-8'), 0, 'C', 0 );
			$MiPDF->Ln(2);

			$MiPDF->SetFont('Noto Sans HK', 'B', 10);
			$MiPDF->Cell(15, 5, 'Unidad: ', 0, 0, 'L', 0);
			$MiPDF->SetFont('Noto Sans HK', '', 10);

			if ($esPMAR) {
				$MiPDF->Cell(80, 5, $unidad.' (PMAR)', 0, 0, 'L', 0 );
			}
			else {
				$MiPDF->Cell(80, 5, $unidad, 0, 0, 'L', 0 );
			}

			$MiPDF->SetFont('Noto Sans HK', 'B', 10);
			$MiPDF->Cell(32, 5, 'Curso académico: ', 0, 0, 'L', 0);
			$MiPDF->SetFont('Noto Sans HK', '', 10);
			$MiPDF->Cell(36, 5, $config['curso_actual'], 0, 1, 'L', 0 );

			// Obtenemos el tutor/a de la unidad
			$result = mysqli_query($db_con, "SELECT tutor FROM FTUTORES WHERE unidad='$unidad'");
			$row = mysqli_fetch_array($result);
			$tutor = $row['tutor'];
			mysqli_free_result($result);

			$MiPDF->SetFont('Noto Sans HK', 'B', 10);
			$MiPDF->Cell(15, 5, 'Tutor/a: ', 0, 0, 'L', 0);
			$MiPDF->SetFont('Noto Sans HK', '', 10);
			$MiPDF->Cell(80, 5, nomprofesor($tutor), 0, 0, 'L', 0 );

			$MiPDF->SetFont('Noto Sans HK', 'B', 10);
			$MiPDF->Cell(14, 5, 'Fecha: ', 0, 0, 'L', 0);
			$MiPDF->SetFont('Noto Sans HK', '', 10);
			$MiPDF->Cell(54, 5, date('d/m/Y'), 0, 1, 'L', 0 );

			$MiPDF->SetFont('Noto Sans HK', 'B', 10);
			$MiPDF->Cell(20, 5, 'Asignatura: ', 0, 0, 'L', 0);
			$MiPDF->SetFont('Noto Sans HK', '', 10);
			$MiPDF->Cell(80, 5, $row_asignaturas_profesor['materia'], 0, 1, 'L', 0 );

			$MiPDF->Ln(2);

			$MiPDF->SetWidths(array(8, 65, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9));
			$MiPDF->SetFont('Noto Sans HK', 'B', 8);
			$MiPDF->SetTextColor(255, 255, 255);
			$MiPDF->SetFillColor(61, 61, 61);

			$MiPDF->Row(array('Nº', 'Alumno/a', '', '', '', '', '', '', '', '', '', ''), 'DF', 6);

			if ($esPMAR) {
				$result_codasig_pmar = mysqli_query($db_con, "SELECT codigo FROM materias WHERE grupo = '".$unidad."' AND abrev LIKE '%**%' and abrev not like '%\_%' LIMIT 1");
				$row_codasig_pmar = mysqli_fetch_array($result_codasig_pmar);
				$codasig_pmar = $row_codasig_pmar['codigo'];
				$result = mysqli_query($db_con, "SELECT claveal, apellidos, nombre, matriculas FROM alma WHERE unidad='$unidad' AND combasi LIKE '%$codasig_pmar%' ORDER BY apellidos ASC, nombre ASC");
			}
			else {
				$result = mysqli_query($db_con, "SELECT claveal, apellidos, nombre, matriculas FROM alma WHERE unidad='$unidad' ORDER BY apellidos ASC, nombre ASC");
			}

			$MiPDF->SetTextColor(48, 46, 43);
			$MiPDF->SetFont('Noto Sans HK', '', 8);

			$MiPDF->SetFillColor(239,240,239);

			$nc = 0;
			$fila = 1;
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				if ($fila % 2 == 0) $fill = 'DF';
				else $fill = '';

				$nc++;
				$aux = '';

				if ($row['matriculas'] > 1) {
					$aux .= ' (Rep.)';
				}

				// Comprobamos si el centro utiliza el módulo de matriculaciones y obtenemos si el alumno es bilingüe o está exento de alguna materia
				$result_datos_matricula = mysqli_query($db_con, "SELECT bilinguismo, exencion FROM matriculas WHERE claveal = '".$row['claveal']."'");
				$row_datos_matricula = mysqli_fetch_array($result_datos_matricula);
				$result_datos_matricula_bach = mysqli_query($db_con, "SELECT bilinguismo FROM matriculas_bach WHERE claveal = '".$row['claveal']."'");
				//echo "SELECT bilinguismo FROM matriculas_bach WHERE claveal = '".$row['claveal']."'<br>";
				$row_datos_matricula_bach = mysqli_fetch_array($result_datos_matricula_bach);

				if ($row_datos_matricula['bilinguismo'] == 'Si') {
					$aux .= ' (Bil.)';
				}
				if ($row_datos_matricula_bach['bilinguismo'] == 'Si') {
					$aux .= ' (Bil.)';
				}

				if ($row_datos_matricula['exencion'] == 1) {
					$aux .= ' (Exe.)';
				}

				$alumno = $row['apellidos'].', '.$row['nombre'].$aux;

				if (! isset($alumnos_profesor_por_claveal) || (isset($alumnos_profesor_por_claveal) && in_array($row['claveal'], $alumnos_profesor_por_claveal))) {
					$MiPDF->Row(array($nc, $alumno, '', '', '', '', '', '', '', '', '', ''), $fill, 6);

					$fila++;
				}

			}

			mysqli_free_result($result);

		}

	} // IMPRESIÓN DE TODAS LAS UNIDADES
	else {

		$MiPDF->Addpage();
		$MiPDF->SetY(30);

		$MiPDF->SetFont('Noto Sans HK', 'B', 10);
		$MiPDF->Multicell(0, 5, mb_strtoupper("Listado de clase", 'UTF-8'), 0, 'C', 0 );
		$MiPDF->Ln(2);

		$MiPDF->SetFont('Noto Sans HK', 'B', 10);
		$MiPDF->Cell(15, 5, 'Unidad: ', 0, 0, 'L', 0);
		$MiPDF->SetFont('Noto Sans HK', '', 10);

		if ($esPMAR) {
			$MiPDF->Cell(80, 5, $unidad.' (PMAR)', 0, 0, 'L', 0 );
		}
		else {
			$MiPDF->Cell(80, 5, $unidad, 0, 0, 'L', 0 );
		}

		$MiPDF->SetFont('Noto Sans HK', 'B', 10);
		$MiPDF->Cell(32, 5, 'Curso académico: ', 0, 0, 'L', 0);
		$MiPDF->SetFont('Noto Sans HK', '', 10);
		$MiPDF->Cell(36, 5, $config['curso_actual'], 0, 1, 'L', 0 );

		// Obtenemos el tutor/a de la unidad
		$result = mysqli_query($db_con, "SELECT tutor FROM FTUTORES WHERE unidad='$unidad'");
		$row = mysqli_fetch_array($result);
		$tutor = $row['tutor'];
		mysqli_free_result($result);

		$MiPDF->SetFont('Noto Sans HK', 'B', 10);
		$MiPDF->Cell(15, 5, 'Tutor/a: ', 0, 0, 'L', 0);
		$MiPDF->SetFont('Noto Sans HK', '', 10);
		$MiPDF->Cell(80, 5, nomprofesor($tutor), 0, 0, 'L', 0 );

		$MiPDF->SetFont('Noto Sans HK', 'B', 10);
		$MiPDF->Cell(14, 5, 'Fecha: ', 0, 0, 'L', 0);
		$MiPDF->SetFont('Noto Sans HK', '', 10);
		$MiPDF->Cell(54, 5, date('d/m/Y'), 0, 1, 'L', 0 );

		$MiPDF->Ln(2);

		$MiPDF->SetWidths(array(8, 65, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9));
		$MiPDF->SetFont('Noto Sans HK', 'B', 8);
		$MiPDF->SetTextColor(255, 255, 255);
		$MiPDF->SetFillColor(61, 61, 61);

		$MiPDF->Row(array('Nº', 'Alumno/a', '', '', '', '', '', '', '', '', '', ''), 'DF', 6);

		if ($esPMAR) {
			$result_codasig_pmar = mysqli_query($db_con, "SELECT codigo FROM materias WHERE grupo = '".$unidad."' AND abrev LIKE '%**%' and abrev not like '%\_%' LIMIT 1");
			$row_codasig_pmar = mysqli_fetch_array($result_codasig_pmar);
			$codasig_pmar = $row_codasig_pmar['codigo'];
			$result = mysqli_query($db_con, "SELECT claveal, apellidos, nombre, matriculas FROM alma WHERE unidad='$unidad' AND combasi LIKE '%$codasig_pmar%' ORDER BY apellidos ASC, nombre ASC");
		}
		else {
			$result = mysqli_query($db_con, "SELECT claveal, apellidos, nombre, matriculas FROM alma WHERE unidad='$unidad' ORDER BY apellidos ASC, nombre ASC");
		}

		$MiPDF->SetTextColor(48, 46, 43);
		$MiPDF->SetFont('Noto Sans HK', '', 8);

		$MiPDF->SetFillColor(239,240,239);

		$nc = 0;
		$fila = 1;
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			if ($fila % 2 == 0) $fill = 'DF';
			else $fill = '';

			$nc++;
			$aux = '';

			if ($row['matriculas'] > 1) {
				$aux .= ' (Rep.)';
			}

			// Comprobamos si el centro utiliza el módulo de matriculaciones y obtenemos si el alumno es bilingüe o está exento de alguna materia
			$result_datos_matricula = mysqli_query($db_con, "SELECT bilinguismo, exencion FROM matriculas WHERE claveal = '".$row['claveal']."'");
			$row_datos_matricula = mysqli_fetch_array($result_datos_matricula);
			$result_datos_matricula_bach = mysqli_query($db_con, "SELECT bilinguismo FROM matriculas_bach WHERE claveal = '".$row['claveal']."'");
			$row_datos_matricula_bach = mysqli_fetch_array($result_datos_matricula_bach);

			if ($row_datos_matricula['bilinguismo'] == 'Si') {
				$aux .= ' (Bil.)';
			}
			if ($row_datos_matricula_bach['bilinguismo'] == 'Si') {
				$aux .= ' (Bil.)';
			}

			if ($row_datos_matricula['exencion'] == 1) {
				$aux .= ' (Exe.)';
			}

			$alumno = $row['apellidos'].', '.$row['nombre'].$aux;
			$MiPDF->Row(array($nc, $alumno, '', '', '', '', '', '', '', '', '', ''), $fill, 6);

			$fila++;

		}

		mysqli_free_result($result);

	}

}

$MiPDF->Output();
