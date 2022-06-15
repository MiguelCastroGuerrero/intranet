<?php
require('../../bootstrap.php');

$GLOBALS['db_con'] = $db_con;

if (file_exists('../config.php')) {
	include('../config.php');
}


if (! isset($_POST['cmp_nombre'])) {
	die("<h1>FORBIDDEN</h1>");
	exit();
}

// Limpiamos variables
$nombre_evento = mysqli_real_escape_string($db_con, $_POST['cmp_nombre']);
$fechadiacomp_evento = mysqli_real_escape_string($db_con, $_POST['cmp_fecha_diacomp']);
$fechaini_evento = mysqli_real_escape_string($db_con, $_POST['cmp_fecha_ini']);
$horaini_evento = mysqli_real_escape_string($db_con, $_POST['cmp_hora_ini']);
$fechafin_evento = mysqli_real_escape_string($db_con, $_POST['cmp_fecha_fin']);
$horafin_evento = mysqli_real_escape_string($db_con, $_POST['cmp_hora_fin']);
$todo_dia = mysqli_real_escape_string($db_con, $_POST['cmp_fecha_diacomp']);
$descripcion_evento = mysqli_real_escape_string($db_con, $_POST['cmp_descripcion']);
$lugar_evento = mysqli_real_escape_string($db_con, $_POST['cmp_lugar']);
$calendario_evento = mysqli_real_escape_string($db_con, $_POST['cmp_calendario']);
$unidad_asignatura_evento = $_POST['cmp_unidad_asignatura'];
$cuaderno_evento = $_POST['cmp_cuaderno'];
$departamento_evento = mysqli_real_escape_string($db_con, $_POST['cmp_departamento']);
$profesores_evento = $_POST['cmp_profesores'];
$observaciones_evento = mysqli_real_escape_string($db_con, $_POST['cmp_observaciones']);
$unidades_evento = $_POST['cmp_unidades'];
$profesorreg_evento = mysqli_real_escape_string($db_con, $_SESSION['ide']);
$fechareg_evento = date('Y-m-d');
$doble = count($unidad_asignatura_evento);


// Limpiamos espacios innecesarios
$nombre_evento = trim($nombre_evento);
$fechaini_evento = trim($fechaini_evento);
$horaini_evento = trim($horaini_evento);
$fechafin_evento = trim($fechafin_evento);
$horafin_evento = trim($horafin_evento);
$descripcion_evento = trim($descripcion_evento);
$lugar_evento = trim($lugar_evento);

$fecha_extra_ini = cambia_fecha($fechaini_evento);
$fecha_extra_fin = cambia_fecha($fechafin_evento);

foreach ($unidad_asignatura_evento as $grupo_cal) {
	$tr_gr = explode(" => ", $grupo_cal);
	$gr_cal = trim($tr_gr[0]);

	// Comprobamos si hay exámenes o actividades para ese grupo el mismo día
	$chk_exam = mysqli_query($db_con,"SELECT id FROM calendario WHERE categoria > '2' AND fechaini <= '$fecha_extra_ini' AND fechafin >= '$fecha_extra_fin' AND unidades LIKE '%$gr_cal%'");
	if (mysqli_num_rows($chk_exam) && (isset($config['calendario']['prefExamenes']) && $config['calendario']['prefExamenes'] == 0) && strstr($_SESSION['cargo'], "1")==FALSE) {
		header('Location:'.'http://'.$config['dominio'].'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'&msg_cal=11');
		exit();
	}

	$chk_actividad = mysqli_query($db_con,"SELECT id FROM calendario WHERE categoria = '2' AND fechaini <= '$fecha_extra_ini' AND fechafin >= '$fecha_extra_fin' AND unidades LIKE '%$gr_cal%'");
	if (mysqli_num_rows($chk_actividad) && (isset($config['calendario']['prefActividades']) && $config['calendario']['prefActividades'] == 0) && strstr($_SESSION['cargo'], "1")==FALSE) {
		header('Location:'.'http://'.$config['dominio'].'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'&msg_cal=11');
		exit();
	}
}

// Comprobamos si hay actividades para ese grupo el mismo día
foreach ($unidades_evento as $grupo_cal1) {
	$grupo_cal1 = trim($grupo_cal1);
	$chk = mysqli_query($db_con,"SELECT id FROM calendario WHERE categoria = '2' AND fechaini <= '$fecha_extra_ini' AND fechafin >= '$fecha_extra_fin' AND unidades LIKE '%$grupo_cal1%'");
		if (mysqli_num_rows($chk) && (isset($config['calendario']['prefActividades']) && $config['calendario']['prefActividades'] == 0) && strstr($_SESSION['cargo'], "1")==FALSE) {
			header('Location:'.'http://'.$config['dominio'].'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'&msg_cal=1');
			exit();
		}
	}

if ($fechadiacomp_evento == '') $fechadiacomp_evento = 0;
else $fechadiacomp_evento = 1;

if ($fechadiacomp_evento) {
	$exp_fechaini_evento = explode('/', $fechaini_evento);
	$fechaini_evento_sql = $exp_fechaini_evento[2].'-'.$exp_fechaini_evento[1].'-'.$exp_fechaini_evento[0];

	$fechafin_evento_sql = $fechaini_evento_sql;
	$horaini_evento = '00:00:00';
	$horafin_evento = '00:00:00';
}
else {
	$exp_fechaini_evento = explode('/', $fechaini_evento);
	$fechaini_evento_sql = $exp_fechaini_evento[2].'-'.$exp_fechaini_evento[1].'-'.$exp_fechaini_evento[0];

	$exp_fechafin_evento = explode('/', $fechafin_evento);
	$fechafin_evento_sql = $exp_fechafin_evento[2].'-'.$exp_fechafin_evento[1].'-'.$exp_fechafin_evento[0];
}

if (($todo_dia == 1) && ($fechaini_evento_sql > $fechafin_evento_sql)) {
	header('Location:'.'http://'.$config['dominio'].'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'&msg_cal=12');
	exit();
}


// Declaramos las variables para los tipos de calendario
$string_departamento = "";
$string_profesores = "";
$string_unidad = "";
$string_asignatura = "";

// Es una actividad extraescolar
if ($calendario_evento == 2) {

	// Sólo se registran actividades con un minimo de 7 días de antelación.
	$_fecha_hoy = date('Y-m-d');
	$_fecha_actividad = $fechaini_evento_sql;
	$_date_fecha_hoy = date_create($_fecha_actividad);
	$_date_fecha_actividad = date_create($_fecha_hoy);
	$_diff = date_diff($_date_fecha_hoy, $_date_fecha_actividad);
	$dias_diferencia = $_diff->format("%a");

	if (! isset($config['calendario']['limiteDiasEvento']) || (isset($config['calendario']['limiteDiasEvento']) && $config['calendario']['limiteDiasEvento'] == 0)) {
		if ($dias_diferencia < 7 && ! acl_permiso($_SESSION['cargo'], array('1','5'))) {
			header('Location:'.'http://'.$config['dominio'].'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'&msg_cal=13');
			exit();
		}
	}

	$string_departamento = $departamento_evento;

	if (is_array($profesores_evento)) {

		foreach ($profesores_evento as $profesor) {
			$string_profesores .= mysqli_real_escape_string($db_con, $profesor).'; ';
		}

		$string_profesores = trim($string_profesores);
	}

	if (is_array($unidades_evento)) {

		foreach ($unidades_evento as $unidad) {
			$string_unidad .= mysqli_real_escape_string($db_con, $unidad).'; ';
		}

		$string_unidad = trim($string_unidad);
	}
}
// Pertenece al diario del profesor
elseif ($calendario_evento != 2 && $calendario_evento != 1) {

	if (is_array($unidad_asignatura_evento)) {

		foreach ($unidad_asignatura_evento as $unidad) {
			$exp_unidad = explode(' => ', $unidad);
			$string_unidad .= mysqli_real_escape_string($db_con, $exp_unidad[0]).'; ';
			$string_asignatura .= mysqli_real_escape_string($db_con, $exp_unidad[1]).'; ';
		}

		$string_unidad = trim($string_unidad);
		$string_asignatura = trim($string_asignatura);
	}

	if ($cuaderno_evento == '') $cuaderno_evento = 0;
	else $cuaderno_evento = 1;
}

// Comprobamos si existe el evento
$result = mysqli_query($db_con, "SELECT nombre FROM calendario WHERE nombre='$nombre_evento' AND fechaini='$fechaini_evento_sql' AND horaini='$horaini_evento' AND fechafin='$fechafin_evento_sql' AND horafin='$horafin_evento' AND categoria='$calendario_evento' LIMIT 1");

if (mysqli_num_rows($result)) {
	header('Location:'.'http://'.$config['dominio'].'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'&msg=ErrorEventoExiste');
	exit();
}
else {

	if ($fechaini_evento_sql > $fechafin_evento_sql) {
		header('Location:'.'http://'.$config['dominio'].'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'&msg=ErrorEventoFecha');
		exit();
	}
	else {
			$crear = mysqli_query($db_con, "INSERT INTO calendario (categoria, nombre, descripcion, fechaini, horaini, fechafin, horafin, lugar, departamento, profesores, unidades, asignaturas, fechareg, profesorreg, observaciones, confirmado) VALUES ($calendario_evento, '$nombre_evento', '$descripcion_evento', '$fechaini_evento_sql', '$horaini_evento', '$fechafin_evento_sql', '$horafin_evento', '$lugar_evento', '$string_departamento', '$string_profesores', '$string_unidad', '$string_asignatura' , NOW(), '$profesorreg_evento', '$observaciones_evento','0')");
			if (! $crear) {
				header('Location:'.'http://'.$config['dominio'].'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'&msg=ErrorEventoInsertar');
				exit();
			}
			else {

				// Comprobamos si el profesor ha marcado la opción de crear columna en el cuaderno
				if ($calendario_evento != 1 && $calendario_evento != 2 && $cuaderno_evento == 1) {

					$string_unidades = "";

					foreach ($unidad_asignatura_evento as $unidad) {
						$exp_unidad = explode(' => ', $unidad);
						$string_unidades .= mysqli_real_escape_string($db_con, $exp_unidad[0]).', ';

						// Las siguientes variables sirven para obtener el código de la asignatura
						$unidad = mysqli_real_escape_string($db_con, $exp_unidad[0]);
						$nomasignatura = mysqli_real_escape_string($db_con, $exp_unidad[1]);

						// Códigos diferentes en Bachillerato
						$extra_unidad.="a_grupo='$unidad' or ";
					}

					$extra_unidades=substr($extra_unidad, 0, strlen($extra_unidad)-3);
					$string_unidades = trim($string_unidades);

					$result_asignatura = mysqli_query($db_con, "SELECT DISTINCT c_asig FROM horw WHERE prof='".$_SESSION['profi']."' AND ($extra_unidades) AND asig='$nomasignatura'");
					while($codasignatura = mysqli_fetch_array($result_asignatura)){
						$num_codigos++;
						${asignatura.$num_codigos}=$codasignatura[0];
					}

					// Códigos diferentes en Bachillerato 2ª parte
					if ($num_codigos>1) {
						$extra_asig = " or asignatura = '$asignatura2'";
					}
					else{
						$extra_asig = "";
					}

					if ($doble==1) {
						$result_columnas = mysqli_query($db_con, "SELECT MAX(orden) FROM notas_cuaderno WHERE profesor = '".$_SESSION['profi']."' AND curso='$string_unidades' AND (asignatura='$asignatura1' $extra_asig)");
						$numcolumna = mysqli_fetch_array($result_columnas);
						$orden = $numcolumna[0] + 1;

						$tipo="Números";
						mysqli_query($db_con, "INSERT INTO notas_cuaderno (profesor, fecha, nombre, texto , asignatura, curso, orden, visible_nota, Tipo, color) VALUES ('".$_SESSION['profi']."', '$fechareg_evento', '$nombre_evento', '$descripcion_evento', '$asignatura1', '$string_unidades', '$orden', '0', '$tipo', '#FFFFFF')") or die (mysqli_error($db_con));
						header('Location:'.'http://'.$config['dominio'].'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'');
						exit();
					}
					else{
						header('Location:'.'http://'.$config['dominio'].'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'');
						exit();
					}
					
				}

				header('Location:'.'http://'.$config['dominio'].'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'');
				exit();
			}

	}
}
?>
