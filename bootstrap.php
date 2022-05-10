<?php
if (ini_get('default_charset') != "UTF-8" && ini_get('default_charset') != "UTF-8") {
	ini_set("default_charset", "UTF-8");
}

// COMPROBAMOS LA VERSIÓN DE PHP DEL SERVIDOR
if (version_compare(phpversion(), '7.2', '<')) die ("<h1>Versión de PHP incompatible</h1><br><p>Necesita PHP 7.2, 7.3 o 7.4 para poder utilizar esta aplicación.</p>");
if (version_compare(phpversion(), '7.5', '>=')) die ("<h1>Versión de PHP incompatible</h1><br><p>Necesita PHP 7.2, 7.3 o 7.4 para poder utilizar esta aplicación.</p>");

// CONFIGURACIÓN DE LA SESIÓN
ini_set("session.use_cookies", 1);
ini_set("session.use_only_cookies", 1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")  {
	ini_set("session.cookie_secure", 1);
}
ini_set("session.cookie_httponly", 1);
session_set_cookie_params(1800); // Duración de la sesión: 1800 segundos (30 minutos)
ini_set("session.gc_maxlifetime", 1800);
session_name("is");
session_start();

// Regeneramos ID de sesión
if (!isset($_SESSION['SERVER_GENERATED_SID_TIME']) || $_SESSION['SERVER_GENERATED_SID_TIME'] < (time() - 30)) {
  session_regenerate_id();
  $_SESSION['SERVER_GENERATED_SID_TIME'] = time();
}

// CONFIGURACIÓN INICIAL
error_reporting(0);
date_default_timezone_set('Europe/Madrid');
setlocale(LC_TIME, 'es_ES.UTF-8');

$server_name = ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) ? $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'] : $_SERVER['SERVER_NAME'];

define('INTRANET_DIRECTORY', __DIR__);
define('CONFIG_FILE', INTRANET_DIRECTORY . '/config.php');
define('VERSION_FILE', INTRANET_DIRECTORY .'/config/version.php');

if (file_exists(CONFIG_FILE)) {

	include_once(CONFIG_FILE);
	if (file_exists(INTRANET_DIRECTORY . '/config_datos.php')) {
		include_once(INTRANET_DIRECTORY . '/config_datos.php');
	}
	include_once(VERSION_FILE);
	include_once(INTRANET_DIRECTORY . '/funciones.php');
	include_once(INTRANET_DIRECTORY . '/lib/cleanxss.php');
	include_once(INTRANET_DIRECTORY . '/lib/csrf.php');
	include_once(INTRANET_DIRECTORY . '/simplepie/autoloader.php');
	include_once(INTRANET_DIRECTORY . '/lib/trendoo/trendoo.php');

	add_security_header();

}
else {

	if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {
	  header('Location:'.'https://'.$server_name.'/intranet/config/index.php');
	  exit();
	}
	else {
		header('Location:'.'http://'.$server_name.'/intranet/config/index.php');
		exit();
	}

}

unset($server_name);

// CONEXIÓN A LA BASE DE DATOS
$db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']) or die("<h1>Error " . mysqli_connect_error() . "</h1>");
mysqli_query($db_con,"SET NAMES 'utf8'");

if ($_SERVER['SCRIPT_NAME'] == '/intranet/validarCorreo.php' ) {
	if (! isset($_GET['verificar'])) {
		if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {
		  header('Location:'.'https://'.$config['dominio'].'/intranet/index.php');
		  exit();
		}
		else {
			header('Location:'.'http://'.$config['dominio'].'/intranet/index.php');
			exit();
		}
	}
}
elseif($_SERVER['SCRIPT_NAME'] != '/intranet/login.php' && $_SERVER['SCRIPT_NAME'] != '/intranet/loginSeneca.php' && $_SERVER['SCRIPT_NAME'] != '/intranet/logout.php') {

	// COMPROBAMOS LA SESION
	if ($_SESSION['intranet_auth'] != 1) {
		$_SESSION = array();
		session_destroy();

		if(isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {
			header('Location:'.'https://'.$config['dominio'].'/intranet/logout.php');
			exit();
		}
		else {
			header('Location:'.'http://'.$config['dominio'].'/intranet/logout.php');
			exit();
		}
	}
	else {

		if ((! acl_permiso($_SESSION['cargo'], array('1')) && $_SESSION['user_admin'] != 0) && (isset($config['mantenimiento']) && $config['mantenimiento'])) {
			include(INTRANET_DIRECTORY.'/mantenimiento.php');
			$_SESSION['intranet_auth'] = 0;
			session_unset();
			session_destroy();
			exit();
		}

	}

	if($_SERVER['SCRIPT_NAME'] != '/intranet/usuario.php') {
		if(isset($_SESSION['cambiar_clave']) && $_SESSION['cambiar_clave']) {
			if(isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {
				header('Location:'.'https://'.$config['dominio'].'/intranet/usuario.php?tab=cuenta&pane=password');
				exit();
			}
			else {
				header('Location:'.'http://'.$config['dominio'].'/intranet/usuario.php?tab=cuenta&pane=password');
				exit();
			}
		}
	}

	// REGISTRAMOS EL ACCESO A LA PAGINA
	registraPagina($db_con, $_SERVER['REQUEST_URI']);

	// VER COMO USUARIO

	// Es el Administrador de la Aplicación.
	if (($_SESSION['ide'] == 'admin')) {
		$_SESSION['user_admin'] = 1;
	}
	else {
		// Variables de sesión del cargo del Profesor
		// Con esto evitamos que el profesor tenga que cerrar y abrir sesión si se cambia de departamento o permisos.
		$cargo0 = mysqli_query($db_con, "select cargo, departamento, idea from departamentos where nombre = '".$_SESSION['profi']."'" );
		$cargo1 = mysqli_fetch_array ( $cargo0 );
		$_SESSION['cargo'] = $cargo1 [0];
		$carg = $_SESSION['cargo'];
		$_SESSION['dpt'] = $cargo1[1];
		$_SESSION['ide'] = $cargo1[2];
	}

	if(isset($_SESSION['user_admin']) && isset($_POST['view_as_user'])) {
		$_SESSION['profi'] = limpiarInput($_POST['view_as_user'], 'alphanumericspecial');
		$profe = $_SESSION['profi'];

		// Variables de sesión del cargo del Profesor
		$cargo0 = mysqli_query($db_con, "select cargo, departamento, idea from departamentos where nombre = '$profe'" );
		$cargo1 = mysqli_fetch_array ( $cargo0 );
		$_SESSION['cargo'] = $cargo1 [0];
		$carg = $_SESSION['cargo'];
		$_SESSION['dpt'] = $cargo1 [1];
		if (! isset($_POST['idea'])) {
			$_SESSION['ide'] = $cargo1 [2];
		}

		// Si es tutor
		if (stristr($_SESSION['cargo'], '2') == TRUE) {
			$result = mysqli_query($db_con, "select distinct unidad from FTUTORES where tutor = '$profe'" );
			$row = mysqli_fetch_array ( $result );
			$_SESSION['mod_tutoria']['tutor'] = $profe;
			$_SESSION['mod_tutoria']['unidad'] = $row [0];
		}

		// Si tiene Horario
		$cur0 = mysqli_query($db_con, "SELECT distinct profesor FROM profesores where profesor = '$profe'" );
		$cur00 = mysqli_query($db_con, "SELECT distinct prof FROM horw where prof = '$profe'" );
		$cur1 = mysqli_num_rows ( $cur0 );
		$cur11 = mysqli_num_rows ( $cur00 );
		if ($cur1>'0' or $cur11>'0') {
			$_SESSION['n_cursos'] = 1;
		}

		// Si tiene tema personalizado
		$res = mysqli_query($db_con, "select distinct tema, fondo from temas where idea = '".$_SESSION['ide']."'" );
		if (mysqli_num_rows($res)>0) {
			$ro = mysqli_fetch_array ( $res );
			$_SESSION['tema'] = $ro[0];
			$_SESSION['fondo'] = $ro[1];
		}
		else{
			$_SESSION['tema']="bootstrap.min.css";
			$_SESSION['fondo'] = "navbar-default";
		}

	}

	// Variable del cargo del Profesor
	$pr = $_SESSION['profi']; // Nombre
	$carg = $_SESSION['cargo']; // Perfil
	$dpto = $_SESSION['dpt']; // Departamento
	$idea = $_SESSION['ide']; // Usuario iDea de Séneca
	$n_curso = $_SESSION['n_cursos']; // Tiene Horario

}

// ¡No escribir código aquí!
