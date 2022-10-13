<?php
require('../../bootstrap.php');
require('inc_evaluaciones.php');

if (file_exists('config.php')) {
	include('config.php');
}

if (isset($_POST['curso'])) $curso = $_POST['curso'];
if (isset($_POST['curso'])) $evaluacion = $_POST['evaluacion'];
if (isset($_GET['id'])) $id = $_GET['id'];
if (isset($_GET['msg_insert'])) $msg_insert = $_GET['msg_insert'];

// COMPROBAMOS SI EL ACTA HA SIDO RELLENADO Y REDIRIGIMOS AL USUARIO
if (isset($_POST['curso']) && isset($_POST['curso'])) {

	$result_acta = mysqli_query($db_con, "SELECT id, impresion FROM evaluaciones_actas WHERE unidad = '".$curso."' AND evaluacion = '".$evaluacion."' LIMIT 1");
	if (mysqli_num_rows($result_acta)) {
		$row_acta = mysqli_fetch_array($result_acta);

		if ($row_acta['impresion']) {
			header('Location:'.'imprimir.php?id='.$row_acta['id']);
			exit;
		}
		else {
			header('Location:'.'actas.php?id='.$row_acta['id'].'&action=edit');
			exit;
		}

	}
}

// COMPROBAMOS SI ES UN PMAR
$esPMAR = (stristr($curso, ' (PMAR)') == true) ? 1 : 0;
if ($esPMAR) {
	$curso = str_ireplace(' (PMAR)', '', $curso);
}

// Comprobamos el nivel educativo para cargar el modelo de acta predefinida por el centro
$result = mysqli_query($db_con, "SELECT cursos.nomcurso FROM unidades JOIN cursos ON unidades.idcurso = cursos.idcurso WHERE unidades.nomunidad = '".$curso."' LIMIT 1");
$row = mysqli_fetch_array($result);
$nivel = $row['nomcurso'];
if (stristr($nivel, 'E.S.O.') == true) {
	if (! isset($config['evaluaciones']['acta_eso'])) {
		$texto_acta = '<h4>1.- Acuerdos de carácter general sobre el grupo:</h4><table class="table table-bordered"><thead><tr><th width="30%"><br></th><th>Muy bueno</th><th>Bueno</th><th>Regular</th><th>Malo</th><th>Muy malo</th></tr></thead><tbody><tr><td class="active"><b>Rendimiento global</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Actitud general ante el estudio</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Actitud ante las normas y la convivencia</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Asistencia a clase</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr></tbody></table><p><b>Otros:</b></p><p><br></p><p><br></p><p><br></p><h4>2.- Acuerdos sobre el proceso individual</h4><h5>2.1.- Necesidades educativas</h5><table class="table table-bordered"><tbody><tr><td width="30%" class="active"><b>Refuerzo educativo</b></td><td width="70%"><br></td></tr><tr><td class="active"><b>A.C.I</b></td><td><br></td></tr><tr><td class="active"><b>Propuestas entrada en el PMAR 2º / PMAR 3º / FP Básica</b></td><td><br></td></tr><tr><td class="active"><b>Programa de enriquecimiento</b></td><td><br></td></tr><tr><td class="active"><b>A tener en cuenta por el Depto. de Orientación</b></td><td><br></td></tr></tbody></table><p><br></p><h4>3.- Alumnos con problemas graves de convivencia y/o absentismo: <small>(marcar con X según cada caso)</small></h4><table class="table table-bordered"><thead><tr><th width="50%">Nombre y apellidos</th><th style="text-align: center; ">Faltas de asistencia</th><th style="text-align: center; ">Problemas disciplinarios</th></tr></thead><tbody><tr><td>{NOMBRE_Y_APELLIDOS}</td><td style="text-align: center; ">{FALTAS_DE_ASISTENCIA}</td><td style="text-align: center; ">{PROBLEMAS_DISCIPLINARIOS}</td></tr><tr><td><br></td><td style="text-align: center; "><br></td><td style="text-align: center; "><br></td></tr><tr><td><br></td><td style="text-align: center; "><br></td><td style="text-align: center; "><br></td></tr></tbody></table><p><br></p><h4>4.- Otros alumnos con algún otro tipo de problema manifiesto (integración escolar, problemas con los compañeros, autoestima, ambiente familiar,...):</h4><table class="table table-bordered"><thead><tr><th width="50%">Nombre y apellidos</th><th>Tipo de problema</th></tr></thead><tbody><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr></tbody></table><p><br></p><h4>5.- Acuerdos tomados por el equipo docente:</h4><p><br></p><p><br></p>';
	}
	else {
		$texto_acta = $config['evaluaciones']['acta_eso'];
	}
}
else if (stristr($nivel, 'Esa') == true) {
	if (! isset($config['evaluaciones']['acta_eso_adultos'])) {
		$texto_acta = '<h4>1.- Acuerdos de carácter general sobre el grupo:</h4><table class="table table-bordered"><thead><tr><th width="30%"><br></th><th>Muy bueno</th><th>Bueno</th><th>Regular</th><th>Malo</th><th>Muy malo</th></tr></thead><tbody><tr><td class="active"><b>Rendimiento global</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Actitud general ante el estudio</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Actitud ante las normas y la convivencia</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Asistencia a clase</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr></tbody></table><p><b>Otros:</b></p><p><br></p><p><br></p><p><br></p><h4>2.- Acuerdos o consideraciones sobre el proceso de evaluación final individual:</h4><p><br></p><p><br></p><p><br></p><h4>3.- Alumnos con problemas graves de convivencia y/o absentismo: <small>(marcar con X según cada caso)</small></h4><table class="table table-bordered"><thead><tr><th width="50%">Nombre y apellidos</th><th style="text-align: center; ">Faltas de asistencia</th><th style="text-align: center; ">Problemas disciplinarios</th></tr></thead><tbody><tr><td>{NOMBRE_Y_APELLIDOS}</td><td style="text-align: center; ">{FALTAS_DE_ASISTENCIA}</td><td style="text-align: center; ">{PROBLEMAS_DISCIPLINARIOS}</td></tr><tr><td><br></td><td style="text-align: center; "><br></td><td style="text-align: center; "><br></td></tr><tr><td><br></td><td style="text-align: center; "><br></td><td style="text-align: center; "><br></td></tr></tbody></table><p><br></p><h4>4.- Otros alumnos con algún otro tipo de problema manifiesto (integración escolar, problemas con los compañeros, autoestima, ambiente familiar,...):</h4><table class="table table-bordered"><thead><tr><th width="50%">Nombre y apellidos</th><th>Tipo de problema</th></tr></thead><tbody><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr></tbody></table><p><br></p><h4>5.- Alumnado que se considera no reúne perfil de hacer estudios de ESO para Adultos</h4><p><br></p><p><br></p><p><br></p><h4>6.- Alumnado excelente a felicitar</h4><p><br></p><p><br></p>';
	}
	else {
		$texto_acta = $config['evaluaciones']['acta_eso_adultos'];
	}
}
else if (stristr($nivel, 'Bachillerato') == true) {
	if (! isset($config['evaluaciones']['acta_bach'])) {
		$texto_acta = '<h4>1.- Acuerdos de carácter general sobre el grupo:</h4><table class="table table-bordered"><thead><tr><th width="30%"><br></th><th>Muy bueno</th><th>Bueno</th><th>Regular</th><th>Malo</th><th>Muy malo</th></tr></thead><tbody><tr><td class="active"><b>Rendimiento global</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Actitud general ante el estudio</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Actitud ante las normas y la convivencia</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Asistencia a clase</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr></tbody></table><p><b>Otros:</b></p><p><br></p><p><br></p><p><br></p><h4>2.- Acuerdos o consideraciones sobre el proceso de evaluación final individual:</h4><p><br></p><p><br></p><p><br></p><h4>3.- Alumnos con problemas graves de convivencia y/o absentismo: <small>(marcar con X según cada caso)</small></h4><table class="table table-bordered"><thead><tr><th width="50%">Nombre y apellidos</th><th style="text-align: center; ">Faltas de asistencia</th><th style="text-align: center; ">Problemas disciplinarios</th></tr></thead><tbody><tr><td>{NOMBRE_Y_APELLIDOS}</td><td style="text-align: center; ">{FALTAS_DE_ASISTENCIA}</td><td style="text-align: center; ">{PROBLEMAS_DISCIPLINARIOS}</td></tr><tr><td><br></td><td style="text-align: center; "><br></td><td style="text-align: center; "><br></td></tr><tr><td><br></td><td style="text-align: center; "><br></td><td style="text-align: center; "><br></td></tr></tbody></table><p><br></p><h4>4.- Otros alumnos con algún otro tipo de problema manifiesto (integración escolar, problemas con los compañeros, autoestima, ambiente familiar,...):</h4><table class="table table-bordered"><thead><tr><th width="50%">Nombre y apellidos</th><th>Tipo de problema</th></tr></thead><tbody><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr></tbody></table><p><br></p><h4>5.- Alumnado que se considera no reúne perfil de hacer estudios de Bachillerato</h4><p><br></p><p><br></p><p><br></p><h4>6.- Alumnado excelente a felicitar</h4><p><br></p><p><br></p>';
	}
	else {
		$texto_acta = $config['evaluaciones']['acta_bach'];
	}
}
else if (stristr($nivel, 'Bach.Pers.Adul.') == true) {
	if (! isset($config['evaluaciones']['acta_bach_adultos'])) {
		$texto_acta = '<h4>1.- Acuerdos de carácter general sobre el grupo:</h4><table class="table table-bordered"><thead><tr><th width="30%"><br></th><th>Muy bueno</th><th>Bueno</th><th>Regular</th><th>Malo</th><th>Muy malo</th></tr></thead><tbody><tr><td class="active"><b>Rendimiento global</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Actitud general ante el estudio</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Actitud ante las normas y la convivencia</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Asistencia a clase</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr></tbody></table><p><b>Otros:</b></p><p><br></p><p><br></p><p><br></p><h4>2.- Acuerdos o consideraciones sobre el proceso de evaluación final individual:</h4><p><br></p><p><br></p><p><br></p><h4>3.- Alumnos con problemas graves de convivencia y/o absentismo: <small>(marcar con X según cada caso)</small></h4><table class="table table-bordered"><thead><tr><th width="50%">Nombre y apellidos</th><th style="text-align: center; ">Faltas de asistencia</th><th style="text-align: center; ">Problemas disciplinarios</th></tr></thead><tbody><tr><td>{NOMBRE_Y_APELLIDOS}</td><td style="text-align: center; ">{FALTAS_DE_ASISTENCIA}</td><td style="text-align: center; ">{PROBLEMAS_DISCIPLINARIOS}</td></tr><tr><td><br></td><td style="text-align: center; "><br></td><td style="text-align: center; "><br></td></tr><tr><td><br></td><td style="text-align: center; "><br></td><td style="text-align: center; "><br></td></tr></tbody></table><p><br></p><h4>4.- Otros alumnos con algún otro tipo de problema manifiesto (integración escolar, problemas con los compañeros, autoestima, ambiente familiar,...):</h4><table class="table table-bordered"><thead><tr><th width="50%">Nombre y apellidos</th><th>Tipo de problema</th></tr></thead><tbody><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr></tbody></table><p><br></p><h4>5.- Alumnado que se considera no reúne perfil de hacer estudios de Bachillerato para Adultos</h4><p><br></p><p><br></p><p><br></p><h4>6.- Alumnado excelente a felicitar</h4><p><br></p><p><br></p>';
	}
	else {
		$texto_acta = $config['evaluaciones']['acta_bach_adultos'];
	}
}
else if (stristr($nivel, 'F.P.') == true OR stristr($nivel, 'C.F.G.') == true) {
	if (! isset($config['evaluaciones']['acta_fp'])) {
		$texto_acta = '<h4>1.- Acuerdos de carácter general sobre el grupo:</h4><table class="table table-bordered"><thead><tr><th width="30%"><br></th><th>Muy bueno</th><th>Bueno</th><th>Regular</th><th>Malo</th><th>Muy malo</th></tr></thead><tbody><tr><td class="active"><b>Rendimiento global</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Actitud general ante el estudio</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Actitud ante las normas y la convivencia</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td class="active"><b>Asistencia a clase</b></td><td><br></td><td><br></td><td><br></td><td><br></td><td><br></td></tr></tbody></table><p><b>Otros:</b></p><p><br></p><p><br></p><p><br></p><h4>2.- Acuerdos o consideraciones sobre el proceso de evaluación individual</h4><p><br></p><p><br></p><p><br></p><h4>3.- Alumnos con problemas graves de convivencia y/o absentismo: <small>(marcar con X según cada caso)</small></h4><table class="table table-bordered"><thead><tr><th width="50%">Nombre y apellidos</th><th style="text-align: center; ">Faltas de asistencia</th><th style="text-align: center; ">Problemas disciplinarios</th></tr></thead><tbody><tr><td>{NOMBRE_Y_APELLIDOS}</td><td style="text-align: center; ">{FALTAS_DE_ASISTENCIA}</td><td style="text-align: center; ">{PROBLEMAS_DISCIPLINARIOS}</td></tr><tr><td><br></td><td style="text-align: center; "><br></td><td style="text-align: center; "><br></td></tr><tr><td><br></td><td style="text-align: center; "><br></td><td style="text-align: center; "><br></td></tr></tbody></table><p><br></p><h4>4.- Otros alumnos con algún otro tipo de problema manifiesto (integración escolar, problemas con los compañeros, autoestima, ambiente familiar,...):</h4><table class="table table-bordered"><thead><tr><th width="50%">Nombre y apellidos</th><th>Tipo de problema</th></tr></thead><tbody><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td></tr></tbody></table><p><br></p>';
	}
	else {
		$texto_acta = $config['evaluaciones']['acta_fp'];
	}
}


// Rellenamos los campos automáticos
if ($esPMAR) {
	$result_codasig_pmar = mysqli_query($db_con, "SELECT codigo FROM materias WHERE grupo = '".$curso."' AND abrev LIKE '%*%' LIMIT 1");
	$row_cosasig_pmar = mysqli_fetch_array($result_codasig_pmar);
	$cosasig_pmar = $row_cosasig_pmar['codigo'];

	$result = mysqli_query($db_con, "SELECT CONCAT(apellidos, ', ', nombre) AS alumno, claveal FROM alma WHERE unidad = '".$curso."' AND combasi LIKE '%".$cosasig_pmar."%' ORDER BY apellidos ASC, nombre ASC");
}
else {
	$result = mysqli_query($db_con, "SELECT CONCAT(apellidos, ', ', nombre) AS alumno, claveal FROM alma WHERE unidad = '".$curso."' ORDER BY apellidos ASC, nombre ASC");
}

if (mysqli_num_rows($result)) {

	$datos_recopilados = "";
	while ($row = mysqli_fetch_array($result)) {


		// Rango de fechas según evaluación
		$anio_escolar = substr($config['curso_actual'],0,4);

		$fecha_evi = $anio_escolar.'-09-30';

		$result_festivos = mysqli_query($db_con, "SELECT fecha, MONTH(fecha) AS mes FROM festivos WHERE nombre LIKE '%Navidad' ORDER BY fecha ASC LIMIT 1");
		$row_festivo = mysqli_fetch_array($result_festivos);
		$fecha_1ev = $row_festivo['fecha'];
		$fecha_mes_1ev = $row_festivo['mes'];

		$result_festivos = mysqli_query($db_con, "SELECT fecha, MONTH(fecha) AS mes FROM festivos WHERE nombre like '%Semana Santa%' ORDER BY fecha ASC LIMIT 1");
		$row_festivo = mysqli_fetch_array($result_festivos);
		$fecha_2ev = $row_festivo['fecha'];
		$fecha_mes_2ev = $row_festivo['mes'];

		switch ($evaluacion) {
			case 'EVI' :
				$rango_fechas = "AND fecha BETWEEN '".$config['curso_inicio']."' AND '".$fecha_evi."'";
				$rango_meses = "AND mes = 9";
				break;
			case '1EV' :
				$rango_fechas = "AND fecha BETWEEN '".$config['curso_inicio']."' AND '".$fecha_1ev."'";
				$rango_meses = "AND mes BETWEEN 9 AND $fecha_mes_1ev";
				break;
			case '2EV' :
				$rango_fechas = "AND fecha BETWEEN '".$fecha_1ev."' AND '".$fecha_2ev."'";
				$rango_meses = "AND mes BETWEEN $fecha_mes_1ev AND $fecha_mes_2ev";
				break;
			case '3EV' :
			case 'ORD' :
			case 'EXT' :
				$rango_fechas = "AND fecha BETWEEN '".$fecha_2ev."' AND '".$config['curso_fin']."'";
				$rango_meses = "AND mes BETWEEN 6";
				break;
			default :
				$rango_fechas = "";
				$rango_meses = "";
		}


		// Comprobamos si es absentista o tiene más de 25 faltas de asistencia sin justificar
		$result_absentimo = mysqli_query($db_con, "SELECT id FROM absentismo WHERE claveal = '".$row['claveal']."' $rango_meses");
		if (mysqli_num_rows($result_absentimo)) {
			$tieneFaltas = 1;
			$texto_asistencia = 'X';
		}
		else {
			$minimo_asistencia = 25 * 3; // Ya que es 25 faltas por mes
			$result_asistencia = mysqli_query($db_con, "SELECT id FROM FALTAS WHERE claveal = '".$row['claveal']."' AND falta = 'F' $rango_fechas") or die (mysqli_error($db_con));
			if (mysqli_num_rows($result_asistencia) >= $minimo_asistencia) {
				$tieneFaltas = 1;
				$texto_asistencia = 'X';
			}
			else {
				$tieneFaltas = 0;
				$texto_asistencia = '';
			}
		}

		// Comprobamos los problemas disciplinarios del alumno
		$minimo_problemas = 1;
		$result_problemas = mysqli_query($db_con, "SELECT id FROM Fechoria WHERE claveal = '".$row['claveal']."' $rango_fechas");
		if (mysqli_num_rows($result_problemas) >= $minimo_problemas) {
			$tieneProblemas = 1;
			$texto_problemas = 'X';
		}
		else {
			$tieneProblemas = 0;
			$texto_problemas = '';
		}

		if ($tieneFaltas || $tieneProblemas) {
			$datos_recopilados .= '<tr><td>'.$row['alumno'].'</td><td style="text-align: center; ">'.$texto_asistencia.'</td><td style="text-align: center; ">'.$texto_problemas.'</td></tr>';
		}
	}

	// Sustituimos la fila de campos automáticos por los datos recopilados
	$texto_acta = str_replace('<tr><td>{NOMBRE_Y_APELLIDOS}</td><td style="text-align: center; ">{FALTAS_DE_ASISTENCIA}</td><td style="text-align: center; ">{PROBLEMAS_DISCIPLINARIOS}</td></tr>', $datos_recopilados, $texto_acta);
}
$row = mysqli_fetch_array($result);


// ENVIO DEL FORMULARIO
if (isset($_POST['submit'])) {

	$evaluacion = $_POST['evaluacion'];
	$curso = $_POST['unidad'];
	$fecha = $_POST['fecha'];
	$exp_fecha = explode('-', $fecha);
	$fecha_sql = $exp_fecha[2].'-'.$exp_fecha[1].'-'.$exp_fecha[0];
	$_texto_acta = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $_POST['texto_acta']);
	$texto_acta = trim($_texto_acta);
	$asistencia = $_POST['asistencia'];

	if (!empty($evaluacion) && !empty($curso) && !empty($fecha) && !empty($texto_acta) && !empty($asistencia)) {

		$asistencia_serialize = serialize($asistencia);

		if (isset($id)) {
			$msg_insert = 0;
			$result = mysqli_query($db_con, "UPDATE evaluaciones_actas SET fecha = '$fecha_sql', texto_acta = '$texto_acta', asistentes = '$asistencia_serialize' WHERE id = $id LIMIT 1");

			if (!$result) $msg_error = "El acta no ha podido ser actualizado. Error: ".mysqli_error($db_con);
			else $msg_success = "El acta ha sido actualizado.";
		}
		else {

			$result = mysqli_query($db_con, "INSERT INTO evaluaciones_actas (unidad, evaluacion, fecha, texto_acta, asistentes) VALUES ('$curso', '$evaluacion', '$fecha_sql', '$texto_acta', '$asistencia_serialize')");

			if (!$result) {
				$msg_error = "El acta no ha podido ser registrado. Error: ".mysqli_error($db_con);
			}
			else {
				$id = mysqli_insert_id($db_con);
				header('Location:'.'actas.php?id='.$id.'&action=edit&msg_insert=1');
				exit();
			}
		}

	}

	// COMPROBAMOS SI ES UN PMAR
	$esPMAR = (stristr($curso, ' (PMAR)') == true) ? 1 : 0;
	if ($esPMAR) {
		$curso = str_ireplace(' (PMAR)', '', $curso);
	}

}

// RECOGEMOS LOS DATOS SI SE TRATA DE UNA ACTUALIZACION
if (isset($id) && (isset($_GET['action']) && $_GET['action'] == 'edit')) {

	$result = mysqli_query($db_con, "SELECT unidad, evaluacion, texto_acta, asistentes FROM evaluaciones_actas WHERE id = ".$id." LIMIT 1");

	if (!$result) {
		$msg_error = "El acta al que intenta acceder no existe.";
		unset($id);
	}
	else {
		$row = mysqli_fetch_array($result);

		$curso = $row['unidad'];
		$evaluacion = $row['evaluacion'];
		$_texto_acta = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $row['texto_acta']);
		$texto_acta = $_texto_acta;
		$asistencia = unserialize($row['asistentes']);

		// COMPROBAMOS SI ES UN PMAR
		$esPMAR = (stristr($curso, ' (PMAR)') == true) ? 1 : 0;
		if ($esPMAR) {
			$curso = str_ireplace(' (PMAR)', '', $curso);
		}
	}
}


// ELIMINAR UN ACTA
if (isset($id) && (isset($_GET['action']) && $_GET['action'] == 'delete')) {
	$result = mysqli_query($db_con, "DELETE FROM evaluaciones_actas WHERE id = ".$id." LIMIT 1");

	if (!$result) $msg_error = "El acta no ha podido ser eliminado. Error: ".mysqli_error($db_con);
	else $msg_success = "El acta ha sido eliminado.";
}


$PLUGIN_DATATABLES = 1;

include("../../menu.php");
include("menu.php");
?>

	<div class="container">

		<!-- TITULO DE LA PAGINA -->
		<div class="page-header">
			<h2>Actas de evaluación <small>Actas de sesiones de evaluación</small></h2>
		</div>

		<!-- MENSAJES -->
		<?php if (isset($msg_error)): ?>
		<div class="alert alert-danger">
			<?php echo $msg_error; ?>
		</div>
		<?php endif; ?>

		<?php if (isset($msg_insert) && $msg_insert): ?>
		<div class="alert alert-success">
			El acta ha sido registrado.
		</div>
		<?php endif; ?>

		<?php if (isset($msg_success)): ?>
		<div class="alert alert-success">
			<?php echo $msg_success; ?>
		</div>
		<?php endif; ?>

		<!-- SCAFFOLDING -->
		<div class="row">

			<?php if (!empty($curso) && !empty($evaluacion)): ?>

			<!-- COLUMNA CENTRAL -->
			<div class="col-sm-12">

				<h3>Redactar acta</h3>

				<form method="post" action="">

					<div class="well">

						<fieldset>

							<div class="row">

								<div class="col-sm-3">

									<div class="form-group">
										<label for="evaluacion">Evaluación</label>
										<input type="hidden" name="evaluacion" value="<?php echo $evaluacion ?>">
										<input type="text" class="form-control" id="texto_evaluacion" name="texto_evaluacion" value="<?php echo $evaluaciones[$evaluacion]; ?>" readonly>
									</div>

								</div>

								<div class="col-sm-2">

									<div class="form-group">
										<label for="unidad">Unidad</label>
										<input type="text" class="form-control" id="unidad" name="unidad" value="<?php echo ($esPMAR) ? $curso.' (PMAR)' : $curso; ?>" readonly>
									</div>

								</div>

								<div class="col-sm-4">

									<div class="form-group">
										<label for="tutor">Tutor/a</label>
										<?php $result = mysqli_query($db_con, "SELECT tutor FROM FTUTORES WHERE unidad='$curso'"); ?>
										<?php $row = mysqli_fetch_array($result); ?>
										<?php $tutor = mb_convert_case($row['tutor'], MB_CASE_TITLE, "UTF-8"); ?>
										<input type="text" class="form-control" id="tutor" name="tutor" value="<?php echo $tutor; ?>" readonly>
									</div>

								</div>

								<div class="col-sm-3">

									<div class="form-group" id="datetimepicker1">
										<label for="fecha">Fecha</label>
										<div class="input-group">
											<input type="text" class="form-control" id="fecha" name="fecha" value="<?php echo (isset($fecha)) ? $fecha : date('d-m-Y'); ?>" data-date-format="DD-MM-YYYY">
											<span class="input-group-addon"><span class="far fa-calendar"></span></span>
										</div>
									</div>

								</div>

							</div>

							<div class="form-group">
								<textarea class="form-control" id="texto_acta" name="texto_acta">
								<?php if (isset($texto_acta)): ?>
								<?php echo $texto_acta; ?>
								<?php else: ?>
								<p><br></p>
								<?php endif; ?>
								</textarea>
							</div>

							<h5>Profesores asistentes <small>Desmarcar los que <strong>no</strong> han asistido</small></h5>
							<?php $result = mysqli_query($db_con, "SELECT DISTINCT profesores.profesor, c_profes.idea FROM profesores JOIN c_profes ON profesores.profesor = c_profes.profesor WHERE grupo = '".$curso."' ORDER BY profesor ASC"); ?>

							<?php if (isset($id) && ! empty($asistencia)): ?>
							<?php while ($row = mysqli_fetch_array($result)): ?>
							<div class="checkbox">
								<label for="asistencia_<?php echo $row['profesor']; ?>">
									<input type="checkbox" id="asistencia_<?php echo $row['profesor']; ?>" name="asistencia[]" value="<?php echo $row['idea']; ?>" <?php if (in_array($row['idea'], $asistencia)) echo 'checked'; ?>> <?php echo $row['profesor']; ?>
								</label>
							</div>
							<?php endwhile; ?>

							<?php else: ?>

							<?php while ($row = mysqli_fetch_array($result)): ?>
							<div class="checkbox">
								<label for="asistencia_<?php echo $row['profesor']; ?>">
									<input type="checkbox" id="asistencia_<?php echo $row['profesor']; ?>" name="asistencia[]" value="<?php echo $row['idea']; ?>" checked> <?php echo $row['profesor']; ?>
								</label>
							</div>
							<?php endwhile; ?>
							<?php endif; ?>

							<br>

							<button type="submit" class="btn btn-primary" name="submit">Guardar</button>
							<button type="reset" class="btn btn-default">Cancelar</button>
						</fieldset>

					</div>

				</form>


			</div><!-- /.col-sm-12 -->

			<?php else: ?>

			<div class="col-sm-12">
				<?php $result = mysqli_query($db_con, "SELECT DISTINCT ea.id, ea.unidad, ea.evaluacion, ea.fecha, ea.impresion, tut.tutor FROM evaluaciones_actas AS ea, FTUTORES AS tut WHERE REPLACE(ea.unidad, ' (PMAR)', '') = tut.unidad ORDER BY ea.id DESC"); ?>

				<?php if (mysqli_num_rows($result)): ?>
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover datatable">
						<thead>
							<tr>
								<th>#</th>
								<th>Unidad</th>
								<th>Tutor/a</th>
								<th>Evaluación</th>
								<th>Fecha</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?php while ($row = mysqli_fetch_array($result)): ?>
							<tr>
								<td><?php echo $row['id']; ?></td>
								<td<?php echo (nomprofesor($row['tutor']) == nomprofesor($pr)) ? ' class="warning"' : ''; ?>><?php echo $row['unidad']; ?></td>
								<td><?php echo nomprofesor($row['tutor']); ?></td>
								<td><?php echo $evaluaciones[$row['evaluacion']]; ?></td>
								<td><?php echo $row['fecha']; ?></td>
								<td>
									<?php if ((nomprofesor($row['tutor']) == nomprofesor($pr) || acl_permiso($_SESSION['cargo'], array('1'))) && !$row['impresion']): ?>
									<a href="actas.php?id=<?php echo $row['id']; ?>&amp;action=edit" data-bs="tooltip" title="Editar"><span class="far fa-edit fa-fw fa-lg"></span></a>
									<a href="imprimir.php?id=<?php echo $row['id']; ?>" data-bs="tooltip" title="Imprimir"><span class="fas fa-print fa-fw fa-lg"></span></a>
									<?php else: ?>
									<a href="imprimir.php?id=<?php echo $row['id']; ?>" data-bs="tooltip" title="Ver acta"><span class="far fa-file fa-fw fa-lg"></span></a>
									<?php endif; ?>
									<?php if ((nomprofesor($row['tutor']) == nomprofesor($pr) && !$row['impresion']) || acl_permiso($_SESSION['cargo'], array('1'))): ?>
									<a href="actas.php?id=<?php echo $row['id']; ?>&amp;action=delete" data-bs="tooltip" title="Eliminar" data-bb="confirm-delete"><span class="far fa-trash-alt fa-fw fa-lg"></span></a>
									<?php endif; ?>
								</td>
							</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
					<?php else: ?>

					<h3>No se ha redactado ningún acta de sesión de evaluación.</h3>
					<br>
					<br>

					<?php endif; ?>
				</div>

			</div><!-- /.col-sm-12 -->

			<?php endif; ?>


		</div><!-- /.row -->

	</div><!-- /.container -->

<?php include("../../pie.php"); ?>

 <script>
 $(document).ready(function() {

 	// DATATABLES
	var table = $('.datatable').DataTable({
	"paging":   true,
    "ordering": true,
    "info":     false,

		"lengthMenu": [[15, 35, 50, -1], [15, 35, 50, "Todos"]],

		"order": [[ 0, "desc" ]],

		"language": {
		            "lengthMenu": "_MENU_",
		            "zeroRecords": "No se ha encontrado ningún resultado con ese criterio.",
		            "info": "Página _PAGE_ de _PAGES_",
		            "infoEmpty": "No hay resultados disponibles.",
		            "infoFiltered": "(filtrado de _MAX_ resultados)",
		            "search": "Buscar: ",
		            "paginate": {
		                  "first": "Primera",
		                  "next": "Última",
		                  "next": "",
		                  "previous": ""
		                }
		        }
	});

 	// EDITOR DE TEXTO
 	tinymce.init({
		selector: 'textarea#texto_acta',
		language: 'es',
		height: 600,
		plugins: 'print preview fullpage paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars',
		imagetools_cors_hosts: ['picsum.photos'],
		menubar: 'file edit view insert format tools table help',
		toolbar: 'undo redo | bold italic underline strikethrough | fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap | fullscreen  preview save print | insertfile image media template link anchor | ltr rtl',
		toolbar_sticky: true,
		autosave_ask_before_unload: true,
		autosave_interval: "30s",
		autosave_prefix: "{path}{query}-{id}-",
		autosave_restore_when_empty: false,
		autosave_retention: "2m",
		image_advtab: true,
		
		/* enable title field in the Image dialog*/
		image_title: true,
		/* enable automatic uploads of images represented by blob or data URIs*/
		automatic_uploads: true,
		/*
		URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
		images_upload_url: 'postAcceptor.php',
		here we add custom filepicker only to Image dialog
		*/
		file_picker_types: 'image',
		/* and here's our custom image picker*/
		file_picker_callback: function (cb, value, meta) {
		var input = document.createElement('input');
		input.setAttribute('type', 'file');
		input.setAttribute('accept', 'image/*');

		/*
		  Note: In modern browsers input[type="file"] is functional without
		  even adding it to the DOM, but that might not be the case in some older
		  or quirky browsers like IE, so you might want to add it to the DOM
		  just in case, and visually hide it. And do not forget do remove it
		  once you do not need it anymore.
		*/

		input.onchange = function () {
		  var file = this.files[0];

		  var reader = new FileReader();
		  reader.onload = function () {
		    /*
		      Note: Now we need to register the blob in TinyMCEs image blob
		      registry. In the next release this part hopefully won't be
		      necessary, as we are looking to handle it internally.
		    */
		    var id = 'blobid' + (new Date()).getTime();
		    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
		    var base64 = reader.result.split(',')[1];
		    var blobInfo = blobCache.create(id, file, base64);
		    blobCache.add(blobInfo);

		    /* call the callback and populate the Title field with the file name */
		    cb(blobInfo.blobUri(), { title: file.name });
		  };
		  reader.readAsDataURL(file);
		};

		input.click();
		}
	});

 	// DATETIMEPICKER
 	$(function () {
 	    $('#datetimepicker1').datetimepicker({
 	    	language: 'es',
 	    	pickTime: false,
 	    });
 	});

 });
 </script>

</body>
</html>
