<?php
session_start();
include("../../config.php");
include_once('../../config/version.php');

$GLOBALS['db_con'] = $db_con;

// COMPROBAMOS LA SESION
if ($_SESSION['autentificado'] != 1) {
	$_SESSION = array();
	session_destroy();
	
	if(isset($_SERVER['HTTPS'])) {
	    if ($_SERVER["HTTPS"] == "on") {
	        header('Location:'.'https://'.$dominio.'/intranet/salir.php');
	        exit();
	    } 
	}
	else {
		header('Location:'.'http://'.$dominio.'/intranet/salir.php');
		exit();
	}
}

if($_SESSION['cambiar_clave']) {
	if(isset($_SERVER['HTTPS'])) {
	    if ($_SERVER["HTTPS"] == "on") {
	        header('Location:'.'https://'.$dominio.'/intranet/clave.php');
	        exit();
	    } 
	}
	else {
		header('Location:'.'http://'.$dominio.'/intranet/clave.php');
		exit();
	}
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
$descripcion_evento = mysqli_real_escape_string($db_con, $_POST['cmp_descripcion']);
$lugar_evento = mysqli_real_escape_string($db_con, $_POST['cmp_lugar']);
$calendario_evento = mysqli_real_escape_string($db_con, $_POST['cmp_calendario']);
$unidad_asignatura_evento = $_POST['cmp_unidad_asignatura'];
$cuaderno_evento = $_POST['cmp_cuaderno'];
$departamento_evento = mysqli_real_escape_string($db_con, $_POST['cmp_departamento']);
$profesores_evento = $_POST['cmp_profesores'];
$unidades_evento = $_POST['cmp_unidades'];
$profesorreg_evento = mysqli_real_escape_string($db_con, $_SESSION['ide']);
$fechareg_evento = date('Y-m-d');


// Limpiamos espacios innecesarios
$nombre_evento = trim($nombre_evento);
$fechaini_evento = trim($fechaini_evento);
$horaini_evento = trim($horaini_evento);
$fechafin_evento = trim($fechafin_evento);
$horafin_evento = trim($horafin_evento);
$descripcion_evento = trim($descripcion_evento);
$lugar_evento = trim($lugar_evento);


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


// Declaramos las variables para los tipos de calendario
$string_departamento = "";
$string_profesores = "";
$string_unidad = "";
$string_asignatura = "";

// Es una actividad extraescolar
if ($calendario_evento == 2) {
	
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
	header('Location:'.'http://'.$dominio.'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'&msg=ErrorEventoExiste');
	exit();
}
else {
	
	if ($fechaini_evento_sql > $fechafin_evento_sql) {
		header('Location:'.'http://'.$dominio.'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'&msg=ErrorEventoFecha');
		exit();
	}
	else {
		$crear = mysqli_query($db_con, "INSERT INTO calendario (categoria, nombre, descripcion, fechaini, horaini, fechafin, horafin, lugar, departamento, profesores, unidades, asignaturas, fechareg, profesorreg) VALUES ($calendario_evento, '$nombre_evento', '$descripcion_evento', '$fechaini_evento_sql', '$horaini_evento', '$fechafin_evento_sql', '$horafin_evento', '$lugar_evento', '$string_departamento', '$string_profesores', '$string_unidad', '$string_asignatura' , '$fechareg_evento', '$profesorreg_evento')");
		if (! $crear) {
			header('Location:'.'http://'.$dominio.'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'&msg=ErrorEventoInsertar');
			exit();
		}
		else {
			
			// Si se trata de una actividad extraescolar, lo registramos en la tabla de actividades extraescolares
			if ($calendario_evento == 2) {
				mysqli_query($db_con, "INSERT INTO actividades (grupos, actividad, descripcion, departamento, profesor, horario, fecha, hoy, confirmado, justificacion) VALUES ('".$string_unidad."','".$nombre_evento."','".$descripcion_evento."','".$string_departamento."','".$string_profesores."','".$horaini_evento." - ".$horafin_evento."','".$fechaini_evento_sql."','".$fechareg_evento."','0','')");
			}
			
			// Comprobamos si el profesor ha marcado la opción de crear columna en el cuaderno
			if ($calendario_evento != 1 && $calendario_evento != 2 && $cuaderno_evento == 1) {
			
				$string_unidades = "";
				
				foreach ($unidad_asignatura_evento as $unidad) {
					$exp_unidad = explode(' => ', $unidad);
					$string_unidades .= mysqli_real_escape_string($db_con, $exp_unidad[0]).', ';
					
					// Las siguiente variables sirven para obtener el código de la asignatura
					$unidad = mysqli_real_escape_string($db_con, $exp_unidad[0]);
					$nomasignatura = mysqli_real_escape_string($db_con, $exp_unidad[1]); 
				}
				
				$string_unidades = trim($string_unidades);
				
				$result_asignatura = mysqli_query($db_con, "SELECT DISTINCT c_asig FROM horw WHERE prof='".$_SESSION['profi']."' AND a_grupo='$unidad' AND asig='$nomasignatura'");
				$codasignatura = mysqli_fetch_array($result_asignatura);
				$codigo = $codasignatura[0];
				
				$result_columnas = mysqli_query($db_con, "SELECT MAX(orden) FROM notas_cuaderno WHERE profesor = '".$_SESSION['profi']."' AND curso='$string_unidades' AND asignatura='$codigo'");
				$numcolumna = mysqli_fetch_array($result_columnas);
				$orden = $numcolumna[0] + 1;
				
				mysqli_query($db_con, "INSERT INTO notas_cuaderno (profesor, fecha, nombre, texto , asignatura, curso, orden, visible_nota, Tipo, color) VALUES ('".$_SESSION['profi']."', '$fechareg_evento', '$nombre_evento', '$descripcion_evento', '$codigo', '$string_unidades', '$orden', '0', 'Números', '#FFFFFF')") or die (mysqli_error($db_con));
			}
			
			header('Location:'.'http://'.$dominio.'/intranet/calendario/index.php?mes='.$_GET['mes'].'&anio='.$_GET['anio'].'');
			exit();
		}
	}
}
?>