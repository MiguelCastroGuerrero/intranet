<?php
setlocale(LC_CTYPE, 'es_ES');
define('CONFIG_FILE', '../config.php');
if (file_exists(CONFIG_FILE) && !(isset($_POST['instalar']))) header('Location:'.'../index.php');

include('version.php');


// TITULO DE LA PAGINA
$page_header = 'Instalación de la Intranet';


// FUNCIONES Y VARIABLES NECESARIAS PARA LA INSTALACIÓN
$config_existe = 0;
$doc_dir = substr(__DIR__, 0, -6).'varios/publico/';

$provincias = array('Almería', 'Cádiz', 'Córdoba', 'Granada', 'Huelva', 'Jaén', 'Málaga', 'Sevilla');

function crear_archivo($filename)
{
	if($file = fopen($filename, 'w+'))
	{
		fclose($file);
		unlink($filename);
		return 1;
	}
	else
	{
		return 0;
	}
}

function crear_directorio($dirname)
{
	mkdir($dirname);
	if(! file_exists($dirname))
	{
		return 0;
	}
	else
	{
		rmdir($dirname);
		return 1;
	}
}

function generateRandomPassword($long)
{
	$alfabeto = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array();
    $long_alfabeto = strlen($alfabeto) - 1;
    for ($i = 0; $i < $long; $i++) {
        $p = rand(0, $long_alfabeto);
        $pass[] = $alfabeto[$p];
    }
    return implode($pass);
}

function forzar_ssl() {
	$ssl = ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) ? 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/intranet/config/ssl.json' : 'https://'.$_SERVER['SERVER_NAME'].'/intranet/config/ssl.json';

	$context = array(
	  'http' => array(
	  	'header' => "User-Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\n"
	  )
	);

	$file = @json_decode(@file_get_contents($ssl, false, stream_context_create($context)));
	return sprintf("%s", $file ? reset($file)->ssl : 0);
}

function limpiar_string($string)
{
	return trim(htmlspecialchars($string, ENT_QUOTES,'UTF-8'));
}

// PROCESAMOS EL FORMULARIO
if (isset($_POST['instalar']))
{

	// LIMPIAMOS CARACTERES
	$intranet_secret = generateRandomPassword(25);
	$dominio_centro = (isset($_POST['dominio_centro'])) ? limpiar_string($_POST['dominio_centro']) : limpiar_string($_SERVER['SERVER_NAME']);
	$forzar_ssl = (isset($_POST['forzar_ssl'])) ? 1 : 0;

	$nombre_centro = (isset($_POST['nombre_centro'])) ? limpiar_string($_POST['nombre_centro']) : '';
	$codigo_centro = (isset($_POST['codigo_centro'])) ? limpiar_string($_POST['codigo_centro']) : '';
	$email_centro = (isset($_POST['email_centro'])) ? limpiar_string($_POST['email_centro']) : '';
	$direccion_centro = (isset($_POST['direccion_centro'])) ? limpiar_string($_POST['direccion_centro']) : '';
	$codpostal_centro = (isset($_POST['codpostal_centro'])) ? limpiar_string($_POST['codpostal_centro']) : '';
	$localidad_centro = (isset($_POST['localidad_centro'])) ? limpiar_string($_POST['localidad_centro']) : '';
	$provincia_centro = (isset($_POST['provincia_centro'])) ? limpiar_string($_POST['provincia_centro']) : '';
	$telefono_centro = (isset($_POST['telefono_centro'])) ? limpiar_string($_POST['telefono_centro']) : '';
	$fax_centro = (isset($_POST['fax_centro'])) ? limpiar_string($_POST['fax_centro']) : '';

	$direccion_director = (isset($_POST['direccion_director'])) ? limpiar_string($_POST['direccion_director']) : '';
	$direccion_jefe_estudios = (isset($_POST['direccion_jefe_estudios'])) ? limpiar_string($_POST['direccion_jefe_estudios']) : '';
	$direccion_secretaria = (isset($_POST['direccion_secretaria'])) ? limpiar_string($_POST['direccion_secretaria']) : '';

	$db_host = (isset($_POST['db_host'])) ? limpiar_string($_POST['db_host']) : '';
	$db_name = (isset($_POST['db_name'])) ? limpiar_string($_POST['db_name']) : '';
	$db_user = (isset($_POST['db_user'])) ? limpiar_string($_POST['db_user']) : '';
	$db_pass = (isset($_POST['db_pass'])) ? limpiar_string($_POST['db_pass']) : '';

	$curso_escolar = (isset($_POST['curso_escolar'])) ? limpiar_string($_POST['curso_escolar']) : '';
	$fecha_inicio = (isset($_POST['fecha_inicio'])) ? limpiar_string($_POST['fecha_inicio']) : '';
	$fecha_final = (isset($_POST['fecha_final'])) ? limpiar_string($_POST['fecha_final']) : '';

	$modulo_biblioteca = (isset($_POST['mod_biblioteca'])) ? 1 : 0;
	$modulo_biblioteca_web = (isset($_POST['mod_biblioteca_web'])) ? limpiar_string($_POST['mod_biblioteca_web']) : '';

	$modulo_bilingue = (isset($_POST['mod_bilingue'])) ? 1 : 0;

	$modulo_centrotic = (isset($_POST['mod_centrotic'])) ? 1 : 0;
	$modulo_centrotic_office365 = (isset($_POST['mod_centrotic_office365'])) ? 1 : 0;
	$modulo_centrotic_gsuite = (isset($_POST['mod_centrotic_gsuite'])) ? 1 : 0;
	$modulo_centrotic_moodle = (isset($_POST['mod_centrotic_moodle'])) ? 1 : 0;

	$modulo_documentos = (isset($_POST['mod_documentos'])) ? 1 : 0;
	$modulo_documentos_dir = limpiar_string($_POST['mod_documentos_dir']);
	$mod_documentos_biblioteca = (isset($_POST['mod_documentos_biblioteca'])) ? 1 : 0;
	$mod_documentos_recursos = (isset($_POST['mod_documentos_recursos'])) ? 1 : 0;
	$mod_documentos_departamentos = (isset($_POST['mod_documentos_departamentos'])) ? 1 : 0;

	$modulo_sms = (isset($_POST['mod_sms'])) ? 1 : 0;
	$modulo_sms_id = (isset($_POST['mod_sms_id'])) ? limpiar_string($_POST['mod_sms_id']) : '';
	$modulo_sms_user = (isset($_POST['mod_sms_user'])) ? limpiar_string($_POST['mod_sms_user']) : '';
	$modulo_sms_pass = (isset($_POST['mod_sms_pass'])) ? limpiar_string($_POST['mod_sms_pass']) : '';

	$modulo_notificaciones = (isset($_POST['mod_notificaciones'])) ? 1 : 0;

	$modulo_notificaciones_dominios = '';
	if (isset($_POST['mod_notificaciones_dominios'])) {
		$_modulo_notificaciones_dominios = explode(',', limpiar_string($_POST['mod_notificaciones_dominios']));
		foreach ($_modulo_notificaciones_dominios as $_dominios_permitidos) {
			$modulo_notificaciones_dominios .= trim($_dominios_permitidos).', ';
		}
		$modulo_notificaciones_dominios = rtrim($modulo_notificaciones_dominios, ', ');
	}

	$modulo_notificaciones_asistencia = (isset($_POST['mod_notificaciones_asistencia'])) ? 1 : 0;

	$modulo_asistencia = (isset($_POST['mod_asistencia'])) ? 1 : 0;

	$modulo_horarios = (isset($_POST['mod_horarios'])) ? 1 : 0;

	$modulo_matriculacion = (isset($_POST['mod_matriculacion'])) ? 1 : 0;
	$modulo_transporte_escolar = (isset($_POST['mod_transporte_escolar'])) ? 1 : 0;


	$api_tinymce_key = (isset($_POST['api_tinymce_key'])) ? limpiar_string($_POST['api_tinymce_key']) : '';

	$api_google_analytics_tracking_id = (isset($_POST['api_google_analytics_tracking_id'])) ? limpiar_string($_POST['api_google_analytics_tracking_id']) : '';

	$api_google_maps_key = (isset($_POST['api_google_maps_key'])) ? limpiar_string($_POST['api_google_maps_key']) : '';
	$api_google_maps_latitude = (isset($_POST['api_google_maps_latitude'])) ? limpiar_string($_POST['api_google_maps_latitude']) : '';
	$api_google_maps_longitude = (isset($_POST['api_google_maps_longitude'])) ? limpiar_string($_POST['api_google_maps_longitude']) : '';
	$api_google_maps_zoom = (isset($_POST['api_google_maps_zoom'])) ? limpiar_string($_POST['api_google_maps_zoom']) : '';

	$api_google_recaptcha_key = (isset($_POST['api_google_recaptcha_key'])) ? limpiar_string($_POST['api_google_recaptcha_key']) : '';
	$api_google_recaptcha_secret = (isset($_POST['api_google_recaptcha_secret'])) ? limpiar_string($_POST['api_google_recaptcha_secret']) : '';

	$api_facebook_chat_page_id = (isset($_POST['api_facebook_chat_page_id'])) ? limpiar_string($_POST['api_facebook_chat_page_id']) : '';
	$api_facebook_chat_theme_color = (isset($_POST['api_facebook_chat_theme_color'])) ? limpiar_string($_POST['api_facebook_chat_theme_color']) : '';
	$api_facebook_chat_welcome = (isset($_POST['api_facebook_chat_welcome'])) ? limpiar_string($_POST['api_facebook_chat_welcome']) : '';

	// CREACIÓN DEL ARCHIVO DE CONFIGURACIÓN
	if($file = fopen(CONFIG_FILE, 'w+'))
	{
		fwrite($file, "<?php \r\n");

		fwrite($file, "\r\n// CONFIGURACIÓN INTRANET\r\n");
		fwrite($file, "\$config['intranet_secret']\t\t\t= '$intranet_secret';\r\n");
		fwrite($file, "\$config['dominio']\t\t\t= '$dominio_centro';\r\n");
		fwrite($file, "\$config['forzar_ssl']\t\t= $forzar_ssl;\r\n");
		fwrite($file, "\$config['mantenimiento']\t= 0;\r\n");

		fwrite($file, "\r\n// INFORMACIÓN DEL CENTRO\r\n");
		fwrite($file, "\$config['centro_denominacion']\t= '$nombre_centro';\r\n");
		fwrite($file, "\$config['centro_codigo']\t\t= '$codigo_centro';\r\n");
		fwrite($file, "\$config['centro_email']\t\t\t= '$email_centro';\r\n");
		fwrite($file, "\$config['centro_direccion']\t\t= '$direccion_centro';\r\n");
		fwrite($file, "\$config['centro_codpostal']\t\t= '$codpostal_centro';\r\n");
		fwrite($file, "\$config['centro_localidad']\t\t= '$localidad_centro';\r\n");
		fwrite($file, "\$config['centro_provincia']\t\t= '$provincia_centro';\r\n");
		fwrite($file, "\$config['centro_telefono']\t\t= '$telefono_centro';\r\n");
		fwrite($file, "\$config['centro_fax']\t\t\t= '$fax_centro';\r\n");

		fwrite($file, "\r\n// EQUIPO DIRECTIVO\r\n");
		fwrite($file, "\$config['directivo_direccion']\t= '$direccion_director';\r\n");
		fwrite($file, "\$config['directivo_jefatura']\t= '$direccion_jefe_estudios';\r\n");
		fwrite($file, "\$config['directivo_secretaria']\t= '$direccion_secretaria';\r\n");

		fwrite($file, "\r\n// BASE DE DATOS\r\n");
		fwrite($file, "\$config['db_host']\t= '$db_host';\r\n");
		fwrite($file, "\$config['db_name']\t= '$db_name';\r\n");
		fwrite($file, "\$config['db_user']\t= '$db_user';\r\n");
		fwrite($file, "\$config['db_pass']\t= '$db_pass';\r\n");

		fwrite($file, "\r\n// CURSO ESCOLAR\r\n");
		fwrite($file, "\$config['curso_actual']\t= '$curso_escolar';\r\n");
		fwrite($file, "\$config['curso_inicio']\t= '$fecha_inicio';\r\n");
		fwrite($file, "\$config['curso_fin']\t= '$fecha_final';\r\n");

		fwrite($file, "\r\n// MÓDULO: BIBLIOTECA\r\n");
		fwrite($file, "\$config['mod_biblioteca']\t\t= $modulo_biblioteca;\r\n");
		fwrite($file, "\$config['mod_biblioteca_web']\t= 'http://$modulo_biblioteca_web';\r\n");

		fwrite($file, "\r\n// MÓDULO: BILINGÜE\r\n");
		fwrite($file, "\$config['mod_bilingue']\t\t\t= $modulo_bilingue;\r\n");

		fwrite($file, "\r\n// MÓDULO: CENTRO TIC\r\n");
		fwrite($file, "\$config['mod_centrotic']\t\t= $modulo_centrotic;\r\n");
		fwrite($file, "\$config['mod_centrotic_office365']\t\t= $modulo_centrotic_office365;\r\n");
		fwrite($file, "\$config['mod_centrotic_gsuite']\t\t= $modulo_centrotic_gsuite;\r\n");
		fwrite($file, "\$config['mod_centrotic_moodle']\t\t= $modulo_centrotic_moodle;\r\n");

		fwrite($file, "\r\n// MÓDULO: DOCUMENTOS\r\n");
		fwrite($file, "\$config['mod_documentos']\t\t= $modulo_documentos;\r\n");
		fwrite($file, "\$config['mod_documentos_dir']\t= '$modulo_documentos_dir';\r\n");
		fwrite($file, "\$config['mod_documentos_biblioteca']\t= '$mod_documentos_biblioteca';\r\n");
		fwrite($file, "\$config['mod_documentos_recursos']\t= '$mod_documentos_recursos';\r\n");
		fwrite($file, "\$config['mod_documentos_departamentos']\t= '$mod_documentos_departamentos';\r\n");

		fwrite($file, "\r\n// MÓDULO: SMS\r\n");
		fwrite($file, "\$config['mod_sms']\t\t\t\t= $modulo_sms;\r\n");
		fwrite($file, "\$config['mod_sms_id']\t\t\t= '$modulo_sms_id';\r\n");
		fwrite($file, "\$config['mod_sms_user']\t\t\t= '$modulo_sms_user';\r\n");
		fwrite($file, "\$config['mod_sms_pass']\t\t\t= '$modulo_sms_pass';\r\n");

		fwrite($file, "\r\n// MÓDULO: NOTIFICACIONES\r\n");
		fwrite($file, "\$config['mod_notificaciones']\t= $modulo_notificaciones;\r\n");
		fwrite($file, "\$config['mod_notificaciones_dominios']\t= '$modulo_notificaciones_dominios';\r\n");
		fwrite($file, "\$config['mod_notificaciones_asistencia']\t= $modulo_notificaciones_asistencia;\r\n");

		fwrite($file, "\r\n// MÓDULO: FALTAS DE ASISTENCIA\r\n");
		fwrite($file, "\$config['mod_asistencia']\t\t= $modulo_asistencia;\r\n");

		fwrite($file, "\r\n// MÓDULO: HORARIOS\r\n");
		fwrite($file, "\$config['mod_horarios']\t\t\t= $modulo_horarios;\r\n");

		fwrite($file, "\r\n// MÓDULO: MATRICULACIÓN\r\n");
		fwrite($file, "\$config['mod_matriculacion']\t\t= $modulo_matriculacion;\r\n");
		fwrite($file, "\$config['mod_transporte_escolar']\t= $modulo_transporte_escolar;\r\n");

		fwrite($file, "\r\n// APIS: TINYMCE\r\n");
		fwrite($file, "\$config['api_tinymce_key']\t\t= '$api_tinymce_key';\r\n");

		fwrite($file, "\r\n// APIS: GOOGLE ANALYTICS\r\n");
		fwrite($file, "\$config['api_google_analytics_tracking_id']\t\t= '$api_google_analytics_tracking_id';\r\n");

		fwrite($file, "\r\n// APIS: GOOGLE MAPS\r\n");
		fwrite($file, "\$config['api_google_maps_key']\t\t= '$api_google_maps_key';\r\n");
		fwrite($file, "\$config['api_google_maps_latitude']\t\t= '$api_google_maps_latitude';\r\n");
		fwrite($file, "\$config['api_google_maps_longitude']\t\t= '$api_google_maps_longitude';\r\n");
		fwrite($file, "\$config['api_google_maps_zoom']\t\t= '$api_google_maps_zoom';\r\n");

		fwrite($file, "\r\n// APIS: GOOGLE RECAPTCHA\r\n");
		fwrite($file, "\$config['api_google_recaptcha_key']\t\t= '$api_google_recaptcha_key';\r\n");
		fwrite($file, "\$config['api_google_recaptcha_secret']\t\t= '$api_google_recaptcha_secret';\r\n");

		fwrite($file, "\r\n// APIS: FACEBOOK CHAT\r\n");
		fwrite($file, "\$config['api_facebook_chat_page_id']\t\t= '$api_facebook_chat_page_id';\r\n");
		fwrite($file, "\$config['api_facebook_chat_theme_color']\t\t= '$api_facebook_chat_theme_color';\r\n");
		fwrite($file, "\$config['api_facebook_chat_welcome']\t\t= '$api_facebook_chat_welcome';\r\n");

		fwrite($file, "\r\n\r\n// Fin del archivo de configuración");

		$config_existe = 1;
		fclose($file);
	}

	// FORZAR USO DE HTTPS
	if($forzar_ssl)
	{
		if($file = fopen('../.htaccess', 'w+'))
		{
			fwrite($file, "Options +FollowSymLinks\r\n");
			fwrite($file, "RewriteEngine On\r\n");
			fwrite($file, "RewriteCond %{SERVER_PORT} 80\r\n");
			fwrite($file, "RewriteCond %{REQUEST_URI} intranet\r\n");
			fwrite($file, "RewriteRule ^(.*)$ https://".$dominio_centro."/intranet/$1 [R,L]\r\n");
		}
		fclose($file);
	}


	if ($config_existe)
	{
		include(CONFIG_FILE);

		$db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass']);
		mysqli_query($db_con,"SET NAMES 'utf8'");

		if (mysqli_connect_errno())
		{
			$esError = 1;
			$details_error = mysqli_connect_error();

			unlink(CONFIG_FILE);
		}
		else
		{
			$pass_admin = generateRandomPassword(9);
			$pass_sha1	= sha1($pass_admin);

			// CREACIÓN DE LA BASE DE DATOS
			mysqli_query($db_con, "CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
			mysqli_select_db($db_con, $db_name) or die (mysqli_error($db_con));

			// IMPORTACIÓN DE TABLAS
			$sql = file_get_contents('tablas.sql');
			mysqli_multi_query($db_con, $sql) or die (mysqli_error($db_con));
			while (mysqli_next_result($db_con));

			// AÑADIENDO USUARIO ADMINISTRADOR
			mysqli_query($db_con, "INSERT INTO `c_profes` (`id`, `pass`, `PROFESOR`, `dni`, `idea`, `correo`, `estado`) VALUES
			(1, '$pass_sha1', 'Administrador', '$pass_admin', 'admin', NULL, 0)") or die ("No se ha podido añadir el usuario Administrador a la tabla 'c_profes'. Error: ".mysqli_error($db_con));

			mysqli_query($db_con, "INSERT INTO `departamentos` (`NOMBRE`, `DNI`, `DEPARTAMENTO`, `CARGO`, `idea`) VALUES
			('Administrador', '$pass_admin', 'Admin', '01', 'admin')") or die ("No se ha podido añadir el usuario Administrador a la tabla 'departamentos'. Error: ".mysqli_error($db_con));

			mysqli_query($db_con, "INSERT INTO `calendario_categorias` (`id`, `nombre`, `fecha`, `profesor`, `color`, `espublico`) VALUES
			(1, 'Calendario del centro', '".date('Y-m-d')."', 'admin', '#f29b12', 1),
			(2, 'Actividades extraescolares', '".date('Y-m-d')."', 'admin', '#18bc9c', 1),
			(3, 'Administrador', '".date('Y-m-d')."', 'admin', '#3498db', 0)");

			// Enviamos analíticas de uso al IES Monterroso
			$analitica = array(
				'centro_denominacion' => $config['centro_denominacion'],
				'centro_codigo' => $config['centro_codigo'],
				'centro_direccion' => $config['centro_direccion'],
				'centro_localidad' => $config['centro_localidad'],
				'centro_codpostal' => $config['centro_codpostal'],
				'centro_provincia' => $config['centro_provincia'],
				'centro_telefono' => $config['centro_telefono'],
				'centro_email' => $config['centro_email'],
				'centro_telefono' => $config['centro_telefono'],
				'dominio' => $config['dominio'],
				'https' => ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0),
				'request' => $_SERVER['REQUEST_URI'],
				'ip' => $_SERVER['SERVER_ADDR'],
				'osname' => php_uname('s'),
				'server' => $_SERVER['SERVER_SOFTWARE'],
				'php_version' => phpversion(),
				'mysql_version' => mysqli_get_server_info($db_con),
				'intranet_version' => INTRANET_VERSION
			);

			mysqli_close($db_con);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://iesmonterroso.org/intranet/analitica/baliza.php");
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $analitica);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_exec($ch);
			curl_close($ch);

			$esError = 0;
		}

	}
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Instalación de la Intranet">
	<meta name="author" content="IESMonterroso (https://github.com/IESMonterroso/intranet/)">
	<meta name="robots" content="noindex, nofollow">

	<title>Intranet - Instalación de la Intranet</title>

	<link href="../css/bootstrap.min.css" rel="stylesheet">
	<link href="../vendor/fontawesome-free-5.13.0-web/css/all.css" rel="stylesheet">
	<link href="../css/animate.css" rel="stylesheet">
	<link href="../css/otros.css" rel="stylesheet">

	<style type="text/css">
	.dl-horizontal dt
	{
		width: 230px;
	}

	.dl-horizontal dd
	{
		margin-left: 250px;
	}
	</style>
</head>

<body style="padding-top: 0;">

	<!--[if lte IE 9 ]>
	<div id="old-ie" class="modal">
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-body">
	      	<br>
	        <p class="lead text-center">Estás utilizando una versión de Internet Explorer demasiado antigua. <br>Actualiza tu navegador o cámbiate a <a href="http://www.google.com/chrome/">Chrome</a> o <a href="https://www.mozilla.org/es-ES/firefox/new/">Firefox</a>.</p>
	        <br>
	      </div>
	    </div>
	  </div>
	</div>
	<![endif]-->

	<div class="container">

		<div class="page-header">
			<h1 class="text-center">
				<i class="fas fa-tachometer-alt fa-2x"></i><br>
				<?php echo $page_header; ?>
			</h1>
		</div>

		<form id="form-instalacion" class="form-horizontal" data-toggle="validator" action="" method="post" role="form" autocomplete="off">

		<div class="row">

			<div class="col-sm-offset-2 col-sm-8">

				<div id="tabs-instalacion" class="tab-content">
					<?php if(! $config_existe): ?>
					<!-- TERMINOS Y CONDICIONES DE USO -->
				    <div role="tabpanel" class="tab-pane active" id="terminos">

				    	<div class="well">
				    		<h3>Términos y condiciones de uso</h3>
				    		<br>
				    		<object type="text/html" data="../LICENSE.md" style="width: 100%; min-height: 300px; border: 1px solid #dedede; background-color: #fff;"></object>

				    		<div class="checkbox">
				    			<label for="terms-accept">
				    				<input type="checkbox" name="terms-accept" id="terms-accept" value="YES">
				    				Acepto los términos y condiciones de uso de esta aplicación.
				    			</label>
				    		</div>

				    		<br>

				    		<div class="pull-right">
				    			<a href="#php-config" aria-controls="php-config" data-toggle="tab" class="btn btn-primary disabled">Continuar <i class="fas fa-chevron-right fa-fw"></i></a>
				    		</div>
				    		<div class="clearfix"></div>
				    	</div>

				    </div>

				    <!-- COMPROBACIÓN CONFIGURACIÓN PHP -->
				    <div role="tabpanel" class="tab-pane" id="php-config">

				    	<div class="well">
				    		<h3>Configuración de PHP</h3>
				    		<br>

				    		<p class="text-center text-info">Para una mejor experiencia en el uso de la Intranet, es recomendable que las variables de configuración de PHP estén marcadas en verde. En el caso de que aparezca marcada en rojo, modifique la configuración en <em>php.ini</em> o póngase en contacto con su proveedor de alojamiento Web y vuelva a iniciar esta instalación.</p>
				    		<br>

				    		<dl class="dl-horizontal">
				    		  <dt>Versión de PHP</dt>
				    		  <dd><?php echo (phpversion() < '7.4' || phpversion() > '8.1') ? '<span class="text-danger">Versión actual: '.phpversion().'. La aplicación requiere PHP 7.4 hasta 8.0</span>' : '<span class="text-success">'.phpversion().'</span>'; ?></dd>
				    		</dl>

				    		<dl class="dl-horizontal">
				    		  <dt>Display errors </dt>
				    		  <dd><?php echo (ini_get('display_errors') == false) ? ini_get('display_errors').'<span class="text-success">Deshabilitado</span>' : '<span class="text-danger">Valor actual: Habilitado. Por seguridad, deshabilite la variable <em>display_errors</em></span>'; ?></dd>
				    		</dl>

				    		<dl class="dl-horizontal">
				    		  <dt>Upload max filesize</dt>
				    		  <dd><?php echo (substr(ini_get('upload_max_filesize'),0,-1) < '32') ? '<span class="text-danger">Valor actual: '.ini_get('upload_max_filesize').'. Aumente el tamaño máximo de archivos a 32M o superior.</span>' : '<span class="text-success">'.ini_get('upload_max_filesize').'</span>'; ?></dd>
				    		</dl>

				    		<dl class="dl-horizontal">
				    		  <dt>Memory limit</dt>
				    		  <dd><?php echo (ini_get('memory_limit') != '-1' && substr(ini_get('memory_limit'),0,-1) < '256') ? '<span class="text-danger">Valor actual: '.ini_get('memory_limit').'. Aumente el tamaño de memoria a 256M o superior.</span>' : '<span class="text-success">'.((ini_get('memory_limit') == '-1') ? 'Ilimitado' : ini_get('memory_limit')).'</span>'; ?></dd>
				    		</dl>

				    		<dl class="dl-horizontal">
				    		  <dt>Escritura/lectura de archivos</dt>
				    		  <dd><?php echo (crear_archivo('install_tmp.txt')) ? '<span class="text-success">Habilitado</span>' : '<span class="text-danger">Valor actual: Deshabilitado. Debe crear el archivo <em>configuracion.php</em> en el directorio principal de la Intranet con permisos de escritura y lectura.</span>'; ?></dd>
				    		</dl>

				    		<dl class="dl-horizontal">
				    		  <dt>Escritura/lectura de directorios</dt>
				    		  <dd><?php echo (crear_directorio('install_tmp')) ? '<span class="text-success">Habilitado</span>' : '<span class="text-danger">Valor actual: Deshabilitado. Debe dar permisos de escritura y lectura a todos los directorios de la Intranet.</span>'; ?></dd>
				    		</dl>

				    		<br>

				    		<div class="pull-left">
				    			<a href="#terminos" aria-controls="terminos" data-toggle="tab" class="btn btn-default"><i class="fas fa-chevron-left fa-fw"></i> Anterior</a>
				    		</div>
				    		<div class="pull-right">
				    			<a href="#informacion" aria-controls="informacion" data-toggle="tab" class="btn btn-primary disabled">Continuar <i class="fas fa-chevron-right fa-fw"></i></a>
				    		</div>
				    		<div class="clearfix"></div>
				    	</div>

				    </div>


				    <!-- INFORMACIÓN CENTRO EDUCATIVO -->
				    <div role="tabpanel" class="tab-pane" id="informacion">

				    	<div class="well">
				    		<h3>Información de su centro educativo</h3>
				    		<br>

				    		<?php $tam_label = 3; ?>
				    		<?php $tam_control = 8; ?>

				    		  <input type="hidden" name="dominio_centro" value="<?php echo ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) ? $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'] : $_SERVER['SERVER_NAME']; ?>">

				    		  <?php if(forzar_ssl()): ?>
				    		  <input type="hidden" name="forzar_ssl" value="1">
				    		  <?php endif; ?>

				    		  <div class="form-group">
				    		    <label for="nombre_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Denominación <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="nombre_centro" name="nombre_centro" placeholder="IES Monterroso" data-error="La denominación del centro no es válida" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="codigo_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Centro código <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="codigo_centro" name="codigo_centro" placeholder="29002885" maxlength="8" data-minlength="8" data-error="El código del centro no es válido" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="email_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Correo electrónico <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="email" class="form-control" id="email_centro" name="email_centro" placeholder="29002885.edu@juntadeandalucia.es" data-error="La dirección de correo electrónico no es válida" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="direccion_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Dirección postal <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="direccion_centro" name="direccion_centro" placeholder="Calle Santo Tomás de Aquino, s/n" data-error="La dirección postal no es válida" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="codpostal_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Código postal <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="codpostal_centro" name="codpostal_centro" placeholder="29680" maxlength="5" data-minlength="5" data-error="El código postal no es válido" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="localidad_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Localidad <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="localidad_centro" name="localidad_centro" placeholder="Estepona" data-error="La localidad no es válida" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="provincia_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Provincia <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <select class="form-control" id="provincia_centro" name="provincia_centro" data-error="La provincia no es válida" required>
				    		      	<option value=""></option>
				    		      	<?php foreach($provincias as $provincia): ?>
				    		      	<option value="<?php echo $provincia; ?>"><?php echo $provincia; ?></option>
				    		      	<?php endforeach; ?>
				    		      </select>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="telefono_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Teléfono <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="tel" class="form-control" id="telefono_centro" name="telefono_centro" placeholder="952795802" maxlength="9" data-minlength="9" data-error="El télefono no es válido" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="fax_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Fax</label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="tel" class="form-control" id="fax_centro" name="fax_centro" placeholder="952795802" maxlength="9" data-minlength="9" data-error="El fax no es válido">
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="direccion_director" class="col-sm-<?php echo $tam_label; ?> control-label">Director/a <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="direccion_director" name="direccion_director" placeholder="Nombre y apellidos" maxlength="60" data-error="Este campo es obligatorio" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="direccion_jefe_estudios" class="col-sm-<?php echo $tam_label; ?> control-label">Jefe/a de Estudios <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="direccion_jefe_estudios" name="direccion_jefe_estudios" placeholder="Nombre y apellidos" maxlength="60" data-error="Este campo es obligatorio" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="direccion_secretaria" class="col-sm-<?php echo $tam_label; ?> control-label">Secretario/a <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="direccion_secretaria" name="direccion_secretaria" placeholder="Nombre y apellidos" maxlength="60" data-error="Este campo es obligatorio" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		<br>

				    		<div class="pull-left">
				    			<a href="#php-config" aria-controls="php-config" data-toggle="tab" class="btn btn-default"><i class="fas fa-chevron-left fa-fw"></i> Anterior</a>
				    		</div>
				    		<div class="pull-right">
				    			<a href="#base-datos" aria-controls="base-datos" data-toggle="tab" class="btn btn-primary disabled">Continuar <i class="fas fa-chevron-right fa-fw"></i></a>
				    		</div>
				    		<div class="clearfix"></div>
				    	</div>

				    </div>


				    <!-- CONFIGURACIÓN DE LA BASE DE DATOS -->
				    <div role="tabpanel" class="tab-pane" id="base-datos">

				    	<div class="well">
				    		<h3>Configuración de la base de datos</h3>
				    		<br>

				    		  <?php $tam_label = 3; ?>
				    		  <?php $tam_control = 8; ?>

				    		  <div class="form-group">
				    		    <label for="db_host" class="col-sm-<?php echo $tam_label; ?> control-label">Servidor <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="db_host" name="db_host" placeholder="localhost" data-error="La dirección servidor de base de datos no es válida" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="db_name" class="col-sm-<?php echo $tam_label; ?> control-label">Base de datos <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="db_name" name="db_name" placeholder="intranet" data-error="El nombre de la base de datos no es válido" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="db_user" class="col-sm-<?php echo $tam_label; ?> control-label">Usuario <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="db_user" name="db_user" data-error="El nombre de usuario de la base de datos no es válido" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="db_pass" class="col-sm-<?php echo $tam_label; ?> control-label">Contraseña <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="password" class="form-control" id="db_pass" name="db_pass" data-error="La contraseña de la base de datos no es válido" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		<br>

				    		<div class="pull-left">
				    			<a href="#informacion" aria-controls="informacion" data-toggle="tab" class="btn btn-default"><i class="fas fa-chevron-left fa-fw"></i> Anterior</a>
				    		</div>
				    		<div class="pull-right">
				    			<a href="#curso-escolar" aria-controls="curso-escolar" data-toggle="tab" class="btn btn-primary disabled">Continuar <i class="fas fa-chevron-right fa-fw"></i></a>
				    		</div>
				    		<div class="clearfix"></div>
				    	</div>

				    </div>


				    <!-- INFORMACIÓN CURSO ESCOLAR -->
				    <div role="tabpanel" class="tab-pane" id="curso-escolar">

				    	<div class="well">
				    		<h3>Información del curso escolar</h3>
				    		<br>

				    		  <?php $tam_label = 3; ?>
				    		  <?php $tam_control = 8; ?>

				    		  <div class="form-group">
				    		    <label for="curso_escolar" class="col-sm-<?php echo $tam_label; ?> control-label">Curso escolar <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="curso_escolar" name="curso_escolar" value="<?php echo (date('n') > 6) ?  date('Y').'/'.(date('y')+1) : (date('Y')-1).'/'.date('y'); ?>" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="fecha_inicio" class="col-sm-<?php echo $tam_label; ?> control-label">Fecha de inicio <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo (date('n') > 6) ?  date('Y').'-09-15' : (date('Y')-1).'-09-15'; ?>" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		  <div class="form-group">
				    		    <label for="fecha_final" class="col-sm-<?php echo $tam_label; ?> control-label">Fecha final <span class="text-danger">*</span></label>
				    		    <div class="col-sm-<?php echo $tam_control; ?>">
				    		      <input type="text" class="form-control" id="fecha_final" name="fecha_final" value="<?php echo (date('n') > 6) ?  (date('Y')+1).'-06-23' : date('Y').'-06-23'; ?>" required>
				    		      <div class="help-block with-errors"></div>
				    		    </div>
				    		  </div>

				    		<br>

				    		<div class="pull-left">
				    			<a href="#base-datos" aria-controls="base-datos" data-toggle="tab" class="btn btn-default"><i class="fas fa-chevron-left fa-fw"></i> Anterior</a>
				    		</div>
				    		<div class="pull-right">
				    			<a href="#apis" aria-controls="apis" data-toggle="tab" class="btn btn-primary disabled">Continuar <i class="fas fa-chevron-right fa-fw"></i></a>
				    		</div>
				    		<div class="clearfix"></div>
				    	</div>

				    </div>

				    <!-- APIS -->
					<div role="tabpanel" class="tab-pane" id="apis">
						<div class="row">

							<div class="col-sm-12">

								<div class="well">
									<h3><i class="fas fa-cubes"></i> APIs</h3>
									<br>

									<div class="row">
										<div class="col-sm-4" style="border-right: 3px solid #dce4ec; margin-right: -3px;">
											<ul class="nav nav-pills nav-stacked" role="tablist">
												<li class="active"><a href="#api_tinymce" aria-controls="api_tinymce" role="tab" data-toggle="tab">Editor TinyMCE</a></li>
											</ul>
										</div>

										<div class="tab-content col-sm-7" style="border-left: 3px solid #dce4ec; padding-left: 45px;">

											<!-- API: TinyMCE -->
										    <div role="tabpanel" class="tab-pane active" id="api_tinymce">

										    	<div class="form-group">
										    		<label for="cmp_api_tinymce_key">API Key</label>
							    			    	<input type="text" class="form-control" id="cmp_api_tinymce_key" name="api_tinymce_key" placeholder="no-api-key" value="">
						    			    	</div>
						    			    </div><!-- /.tab-panel -->

						    			    <?php if (isset($_SESSION['pagina_centro']) && $_SESSION['pagina_centro']): ?>
						    			    <!-- API: Google Analytics -->
										    <div role="tabpanel" class="tab-pane" id="api_google_analytics">

										    	<div class="form-group">
										    		<label for="cmp_api_google_analytics_tracking_id">GA Tracking ID</label>
							    			    	<input type="text" class="form-control" id="cmp_api_google_analytics_tracking_id" name="api_google_analytics_tracking_id" placeholder="YOUR_GA_TRACKING_ID" value="">
						    			    	</div>

						    			    	<p class="help-block">Consigue el ID de seguimiento para usar la API de Google Analytics en <a href="https://analytics.google.com/analytics/" target="_blank">https://analytics.google.com/analytics/</a></p>
						    			    </div><!-- /.tab-panel -->

						    			     <!-- API: Google Maps -->
										    <div role="tabpanel" class="tab-pane" id="api_google_maps">

										    	<div class="form-group">
										    		<label for="cmp_api_google_maps_key">API key</label>
							    			    	<input type="text" class="form-control" id="cmp_api_google_maps_key" name="api_google_maps_key" placeholder="YOUR_API_KEY" value="">
						    			    	</div>

						    			    	<p class="help-block">Consigue la clave para usar la API de Google Maps Javascript en <a href="https://console.cloud.google.com/" target="_blank">https://console.cloud.google.com/</a></p>

						    			    	<br>

						    			    	<div class="form-group">
										    		<label for="cmp_api_google_maps_latitude">Latitud</label>
							    			    	<input type="text" class="form-control" id="cmp_api_google_maps_latitude" name="api_google_maps_latitude" placeholder="36.4295948" value="">
						    			    	</div>

						    			    	<div class="form-group">
										    		<label for="cmp_api_google_maps_langitude">Longitud</label>
							    			    	<input type="text" class="form-control" id="cmp_api_google_maps_langitude" name="api_google_maps_longitude" placeholder="-5.1544486" value="">
						    			    	</div>

						    			    	<div class="form-group">
										    		<label for="cmp_api_google_maps_zoom">Zoom</label>
							    			    	<select class="form-control" id="cmp_api_google_maps_zoom" name="api_google_maps_zoom">
							    			    		<?php for ($zoom=0; $zoom < 19; $zoom++): ?>
							    			    		<option value="<?php echo $zoom; ?>" <?php echo ($zoom == 15) ? ' selected' : ''; ?>><?php echo $zoom; ?></option>
							    			    		<?php endfor; ?>
							    			    	</select>
						    			    	</div>

						    			    	<p class="help-block">Puedes obtener las coordenadas de tu centro educativo en <a href="https://www.coordenadas-gps.com" target="_blank">https://www.coordenadas-gps.com</a></p>
						    			    	
						    			    </div><!-- /.tab-panel -->

						    			    <!-- API: Google reCaptcha -->
										    <div role="tabpanel" class="tab-pane" id="api_google_recaptcha">

										    	<div class="form-group">
										    		<label for="cmp_api_google_recaptcha_key">Site Key</label>
							    			    	<input type="text" class="form-control" id="cmp_api_google_recaptcha_key" name="api_google_recaptcha_key" placeholder="YOUR_SITE_KEY" value="">
						    			    	</div>

						    			    	<div class="form-group">
										    		<label for="cmp_api_google_recaptcha_secret">Secret Key</label>
							    			    	<input type="text" class="form-control" id="cmp_api_google_recaptcha_secret" name="api_google_recaptcha_secret" placeholder="YOUR_SITE_KEY" value="">
						    			    	</div>

						    			    	<p class="help-block">Consigue la clave para usar la API de Google reCAPTCHA v2 en <a href="https://www.google.com/recaptcha/admin/create" target="_blank">https://www.google.com/recaptcha/admin/create</a></p>
						    			    </div><!-- /.tab-panel -->

						    			    <!-- API: Facebook Chat -->
										    <div role="tabpanel" class="tab-pane" id="api_facebook_chat">

										    	<div class="form-group">
										    		<label for="cmp_api_facebook_chat_page_id">Page ID</label>
							    			    	<input type="text" class="form-control" id="cmp_api_facebook_chat_page_id" name="api_facebook_chat_page_id" placeholder="YOUR_PAGE_ID" value="">
						    			    	</div>

						    			    	<p class="help-block">Lea la documentación <a href="https://developers.facebook.com/docs/messenger-platform/discovery/facebook-chat-plugin" target="_blank">https://developers.facebook.com/docs/messenger-platform/discovery/facebook-chat-plugin</a></p>

						    			    	<br>

						    			    	<div class="form-group">
										    		<label for="cmp_api_facebook_chat_theme_color">Color del chat</label>
							    			    	<input type="text" class="form-control" id="cmp_api_facebook_chat_theme_color" name="api_facebook_chat_theme_color" placeholder="#0084ff" value="#0084ff">
						    			    	</div>

						    			    	<div class="form-group">
										    		<label for="cmp_api_facebook_chat_welcome">Mensaje de bienvenida</label>
							    			    	<input type="text" class="form-control" id="cmp_api_facebook_chat_welcome" name="api_facebook_chat_welcome" placeholder="¡Hola! ¿En qué te podemos ayudar?" value="¡Hola! ¿En qué te podemos ayudar?">
						    			    	</div>
						    			    	
						    			    </div><!-- /.tab-panel -->
						    				<?php endif; ?>

						    			</div><!-- /.tab-content -->
						    		</div><!-- /.row -->

						    		<br>

						    		<div class="pull-left">
						    			<a href="#curso-escolar" aria-controls="curso-escolar" data-toggle="tab" class="btn btn-default"><i class="fas fa-chevron-left fa-fw"></i> Anterior</a>
						    		</div>
						    		<div class="pull-right">
						    			<a href="#modulos" aria-controls="modulos" data-toggle="tab" class="btn btn-primary disabled">Continuar <i class="fas fa-chevron-right fa-fw"></i></a>
						    		</div>
						    		<div class="clearfix"></div>

						    	</div><!-- /.well -->

							</div><!-- /.col-sm-6 -->

						</div><!-- /.row -->
					</div><!-- /.tab-panel -->


				    <!-- SELECCIÓN DE MÓDULOS -->
				    <div role="tabpanel" class="tab-pane" id="modulos">

				    	<div id="wrap_modulos" class="well">
				    		<h3>Configuración de módulos</h3>
								<br>

				    		<div class="row">
				    			<div class="col-sm-4" style="border-right: 3px solid #dce4ec; margin-right: -3px;">
										<ul class="nav nav-pills nav-stacked" role="tablist">
											<li class="active"><a href="#mod_biblioteca" aria-controls="mod_biblioteca" role="tab" data-toggle="tab">Biblioteca</a></li>
											<li><a href="#mod_bilingue" aria-controls="mod_bilingue" role="tab" data-toggle="tab">Centro Bilingüe</a></li>
											<li><a href="#mod_centrotic" aria-controls="mod_centrotic" role="tab" data-toggle="tab">Centro TIC</a></li>
											<li><a href="#mod_documentos" aria-controls="mod_documentos" role="tab" data-toggle="tab">Documentos</a></li>
											<li><a href="#mod_sms" aria-controls="mod_sms" role="tab" data-toggle="tab">Envío SMS</a></li>
											<li><a href="#mod_notificaciones" aria-controls="mod_notificaciones" role="tab" data-toggle="tab">Notificaciones</a></li>
											<li><a href="#mod_asistencia" aria-controls="mod_asistencia" role="tab" data-toggle="tab">Faltas de Asistencia</a></li>
											<li><a href="#mod_horarios" aria-controls="mod_horarios" role="tab" data-toggle="tab">Horarios</a></li>
											<li><a href="#mod_matriculacion" aria-controls="mod_matriculacion" role="tab" data-toggle="tab">Matriculación</a></li>
										</ul>
									</div>

				    			<div class="tab-content col-sm-7" style="border-left: 3px solid #dce4ec; padding-left: 45px;">

				    				<!-- MÓDULO: BIBLIOTECA -->
				    			    <div role="tabpanel" class="tab-pane active" id="mod_biblioteca">

				    			    	<div class="form-group">
					    			    	<div class="checkbox">
					    			    		<label>
						    			    		<input type="checkbox" name="mod_biblioteca" value="1">
						    			    		<strong>Biblioteca</strong>
						    			    		<p class="help-block">Si el Centro dispone de Biblioteca que funciona con Abies, y cuenta con un equipo de profesores dedicados a su mantenimiento, este módulo permite consultar e importar los fondos, lectores y préstamos, así como hacer un seguimiento de los alumnos morosos. También incorpora el código de barras generado por Abies al Carnet del Alumno para facilitar la lectura por parte del scanner de la Biblioteca.</p>
						    			    	</label>
					    			    	</div>
					    			    </div>

				    			    	<br>

				    			    	<div class="form-group">
				    			    		<label for="mod_biblioteca_web">Página web de la Biblioteca</label>
				    			    		<div class="input-group">
			    			    		      <div class="input-group-addon">http://</div>
			    			    		      <input type="text" class="form-control" id="mod_biblioteca_web" name="mod_biblioteca_web" placeholder="http://www.juntadeandalucia.es/averroes/centros-tic/29002885/biblioweb/">
			    			    		    </div>
				    			    	</div>

				    			    </div>


				    			    <!-- MÓDULO: CENTRO BILINGÜE -->
				    			    <div role="tabpanel" class="tab-pane" id="mod_bilingue">

				    			    	<div class="form-group">
					    			    	<div class="checkbox">
					    			    		<label>
						    			    		<input type="checkbox" name="mod_bilingue" value="1">
						    			    		<strong>Centro Bilingüe</strong>
						    			    		<p class="help-block">Activa características para los Centros Bilingües, como el envío de mensajes a los profesores que pertenecen al programa bilingüe.</p>
						    			    	</label>
					    			    	</div>
					    			    </div>

				    			    </div>


				    			    <!-- MÓDULO: CENTRO TIC -->
				    			    <div role="tabpanel" class="tab-pane" id="mod_centrotic">

				    			    	<div class="form-group">
					    			    	<div class="checkbox">
					    			    		<label>
						    			    		<input type="checkbox" name="mod_centrotic" value="1">
						    			    		<strong>Centro TIC</strong>
						    			    		<p class="help-block">Aplicaciones propias de un Centro TIC: Incidencias, usuarios, etc.</p>
						    			    	</label>
					    			    	</div>
					    			    </div>

					    			    <h4 class="text-info">Exportación de usuarios para plataformas online</h4>

									    <div class="form-group">
									    	<div class="checkbox">
									    		<label>
						    			    		<input type="checkbox" name="mod_centrotic_moodle" value="1" <?php echo (isset($config['mod_centrotic_moodle']) && $config['mod_centrotic_moodle']) ? 'checked' : ''; ?>>
						    			    		<strong>Plataforma Moodle</strong>
						    			    		<p class="help-block">Genera los archivos de exportación de alumnado y profesorado del centro para la plataforma educativa Moodle.</p>
						    			    	</label>
									    	</div>
									    </div>

									    <div class="form-group">
									    	<div class="checkbox">
									    		<label>
						    			    		<input type="checkbox" name="mod_centrotic_gsuite" value="1" <?php echo (isset($config['mod_centrotic_gsuite']) && $config['mod_centrotic_gsuite']) ? 'checked' : ''; ?>>
						    			    		<strong>Google Suite para centros educativos</strong>
						    			    		<p class="help-block">Genera los archivos de exportación de alumnado y profesorado del centro para la Google Suite.</p>
						    			    	</label>
									    	</div>
									    </div>

									    <div class="form-group">
									    	<div class="checkbox">
									    		<label>
						    			    		<input type="checkbox" name="mod_centrotic_office365" value="1" <?php echo (isset($config['mod_centrotic_office365']) && $config['mod_centrotic_office365']) ? 'checked' : ''; ?>>
						    			    		<strong>Microsoft 365</strong>
						    			    		<p class="help-block">Genera los archivos de exportación de alumnado y profesorado del centro para Microsoft 365.</p>
						    			    	</label>
									    	</div>
									    </div>

				    			    </div>


				    			    <!-- MÓDULO: DOCUMENTOS -->
				    			    <div role="tabpanel" class="tab-pane" id="mod_documentos">

				    			    	<div class="form-group">
					    			    	<div class="checkbox">
					    			    		<label>
					    			    			<input type="checkbox" name="mod_documentos" value="1" checked>
					    			    			<strong>Documentos</strong>
					    			    			<p class="help-block">Directorio en el Servidor local donde tenemos documentos públicos que queremos administrar (visualizar, eliminar, subir, compartir, etc.) con la Intranet.</p>
					    			    		</label>
					    			    	</div>
					    			    </div>

				    			    	<div class="form-group">
				    			    		<label for="mod_documentos_dir">Directorio público</label>
				    			    	    <input type="text" class="form-control" id="mod_documentos_dir" name="mod_documentos_dir" value="<?php echo $doc_dir; ?>">
				    			    	</div>

				    			    	<div class="checkbox">
				    			    		<label>
				    			    			<input type="checkbox" name="mod_documentos_recursos" value="1">
				    			    			<strong>Recursos</strong>
				    			    			<p class="help-block">Creará una carpeta por cada unidad del Centro para que el profesorado pueda almacenar recursos educativos, visibles desde la <em>Página del Centro</em>.</p>
				    			    		</label>
				    			    	</div>

				    			    	<div class="checkbox">
				    			    		<label>
				    			    			<input type="checkbox" name="mod_documentos_biblioteca" value="1">
				    			    			<strong>Biblioteca</strong>
				    			    			<p class="help-block">Si hemos activado el módulo de <strong>Biblioteca</strong>, crea una carpeta donde el personal de la Biblioteca puede subir y compartir documentos de interés.</p>
				    			    		</label>
				    			    	</div>

				    			    	<div class="checkbox">
				    			    		<label>
				    			    			<input type="checkbox" name="mod_documentos_departamentos" value="1">
				    			    			<strong>Departamentos</strong>
				    			    			<p class="help-block">Creará una carpeta para los Departamentos del Centro donde estos pueden colocar documentos importantes y públicos (Programaciones, etc.) visibles desde la <em>Página del Centro</em>.</p>
				    			    		</label>
				    			    	</div>

				    			    </div>


				    			    <!-- MÓDULO: ENVÍO DE SMS -->
				    			    <div role="tabpanel" class="tab-pane" id="mod_sms">

				    			    	<div class="form-group">
					    			    	<div class="checkbox">
					    			    		<label>
					    			    			<input type="checkbox" name="mod_sms" value="1">
					    			    			<strong>Envío de SMS</strong>
					    			    			<p class="help-block">Pone en funcionamiento el envío de SMS en distintos lugares de la Intranet (Problemas de convivencia, faltas de asistencia, etc.). La aplicación está preparada para trabajar con la API de <a href="http://www.trendoo.es/" target="_blank">Trendoo</a>.</p>
					    			    		</label>
					    			    	</div>
					    			    </div>

				    			    	<div class="form-group">
				    			    		<label for="mod_sms_id">Nombre de identificación (<abbr title="Transmission Path Originating Address">TPOA</abbr>)</label>
				    			    	    <input type="text" class="form-control" id="mod_sms_id" name="mod_sms_id" placeholder="P. ej. IMonterroso" maxlength="11">
				    			    	    <p class="help-block">11 caracteres como máximo.</p>
				    			    	</div>

				    			    	<div class="form-group">
				    			    		<label for="mod_sms_user">Usuario</label>
				    			    	    <input type="text" class="form-control" id="mod_sms_user" name="mod_sms_user">
				    			    	</div>

				    			    	<div class="form-group">
				    			    		<label for="mod_sms_pass">Contraseña</label>
				    			    	    <input type="password" class="form-control" id="mod_sms_pass" name="mod_sms_pass">
				    			    	</div>

				    			    </div>

				    			    <!-- MÓDULO: NOTIFICACIONES -->
				    			    <div role="tabpanel" class="tab-pane" id="mod_notificaciones">

				    			    	<div class="form-group">
				    			        	<div class="checkbox">
				    			        		<label>
				    			        			<input type="checkbox" name="mod_notificaciones" value="1">
				    			        			<strong>Notificar a los profesores con tareas pendientes</strong>
				    			        			<p class="help-block">Pone en funcionamiento el envío de SMS o correo electrónico a los profesores que no hayan accedido a la aplicación hace más de 4 días o tengan tareas pendientes: más de 25 mensajes sin leer, informes de tareas o tutoría sin rellenar.</p>
				    			        		</label>
				    			        	</div>
				    			        </div>

				    			         <div class="form-group">
								        	<label for="mod_notificaciones_dominios">Lista de dominios permitidos (separados por coma)</label>
								    	    <input type="text" class="form-control" id="mod_notificaciones_dominios" name="mod_notificaciones_dominios" value="">
								        </div>

								        <div class="form-group">
				    			        	<div class="checkbox">
				    			        		<label>
				    			        			<input type="checkbox" name="mod_notificaciones_asistencia" value="1">
				    			        			<strong>Notificar a los profesores si no registran faltas de asistencia en más de 2 días</strong>
				    			        		</label>
				    			        	</div>
				    			        </div>

				    			    </div>


				    			    <!-- MÓDULO: FALTAS DE ASISTENCIA -->
				    			    <div role="tabpanel" class="tab-pane" id="mod_asistencia">

				    			    	<div class="form-group">
					    			    	<div class="checkbox">
					    			    		<label>
					    			    			<input type="checkbox" id="check_asistencia" name="mod_asistencia" value="1">
					    			    			<strong>Faltas de Asistencia</strong>
					    			    			<p class="help-block">El módulo de Faltas gestiona las asistencia de los alumnos. Permite registrar las ausencias diarias, al modo de <em>iSeneca</em>), que luego podremos gestionar (Consultar, Justificar, crear Informes, enviar SMS, etc.) y subir finalmente a Séneca. <br>O bien podemos descargar las faltas desde Séneca para utilizar los módulos de la aplicación basados en faltas de asistencia (Informes de alumnos, Tutoría, Absentismo, etc.).</p>
					    			    		</label>
					    			    	</div>
					    			    </div>

				    			    	<div class="alert alert-warning">Este módulo depende del módulo de Horarios. Si decide utilizarlo se activará el módulo de Horarios automáticamente.</div>

				    			    </div>


				    			    <!-- MÓDULO: HORARIOS -->
				    			    <div role="tabpanel" class="tab-pane" id="mod_horarios">

				    			    	<div class="form-group">
					    			    	<div class="checkbox">
					    			    		<label>
					    			    			<input type="checkbox" id="check_horarios" name="mod_horarios" value="1">
					    			    			<strong>Horarios</strong>
					    			    			<p class="help-block">Si disponemos de un archivo de Horario en formato XML (como el que se utiliza para subir a Séneca) o DEL (como el que genera el programa Horw) para importar sus datos a la Intranet. Aunque no obligatoria, esta opción es necesaria si queremos hacernos una idea de todo lo que la aplicación puede ofrecer.</p>
					    			    		</label>
					    			    	</div>
					    			    </div>

				    			    </div>


				    			    <!-- MÓDULO: MATRICULACIÓN -->
				    			    <div role="tabpanel" class="tab-pane" id="mod_matriculacion">

				    			    	<div class="form-group">
					    			    	<div class="checkbox">
					    			    		<label>
					    			    			<input type="checkbox" name="mod_matriculacion" value="1">
					    			    			<strong>Matriculación</strong>
					    			    			<p class="help-block">Este módulo permite matricular a los alumnos desde la propia aplicación o bien desde la página pública del Centro. Requiere que cada Centro personalice las materias y optativas que va a ofrecer a sus Alumnos.</p>
					    			    		</label>
					    			    	</div>
					    			    </div>

				    			    	<div class="form-group">
					    			    	<div class="checkbox">
					    			    		<label>
					    			    			<input type="checkbox" name="mod_transporte_escolar" value="1">
					    			    			<strong>Transporte escolar</strong>
					    			    			<p class="help-block">Activa la selección de transporte escolar</p>
					    			    		</label>
					    			    	</div>
					    			    </div>

				    			    </div>

				    			  </div>
				    		</div>

				    		<br><br>

				    		<div class="pull-left">
				    			<a href="#apis" aria-controls="api" data-toggle="tab" class="btn btn-default"><i class="fas fa-chevron-left fa-fw"></i> Anterior</a>
				    		</div>
				    		<div class="pull-right">
				    			<button type="button" class="btn btn-success" id="instalar">Instalar</button>
				    		</div>
				    		<div class="clearfix"></div>
				    	</div><!-- /.well -->

				    	<div id="wrap_instalacion" class="well" style="display: none; padding: 50px 0; min-height: 300px;">

				    		<div class="text-center text-success">
				    			<i class="fas fa-cog fa-spin fa-5x"></i>
				    		</div>
				    		<h3 class="text-center text-success">Instalando la Intranet&hellip;</h3>

				    		<br>
				    		<p class="text-center" style="font-size: 1.12em;">Este proceso puede tardar unos segundos.<br>No cierre esta página mientras se está instalando.</p>

				    	</div>

				    </div>

				    <?php else: ?>

				    <!-- INSTALACION -->
				    <div role="tabpanel" class="tab-pane active" id="instalacion">


				    	<div class="well well-lg">

				    		<?php if ($esError): ?>
				    		<div class="text-center text-error">
				    			<i class="far fa-mehfa-5x"></i>
				    		</div>
				    		<h3 class="text-center text-error">Error al conectar con la base de datos</h3>
				    		<br>
				    		<code style="display: block;"><?php echo $details_error; ?></code>
				    		<br>
				    		<div class="text-center">
				    			<a href="index.php" class="btn btn-default">Volver</a>
				    		</div>
				    		<?php else: ?>
				    		<div class="text-center text-success">
				    			<i class="fas fa-check fa-5x"></i>
				    		</div>
				    		<h3 class="text-center text-success">La Intranet ha sido instalada correctamente</h3>

				    		<br>
				    		<p class="text-center" style="font-size: 1.12em;">Inicie sesión como Administrador para continuar con la importación de los datos de Séneca.<br>En el menú de la página principal encontrará el enlace <em>Administración de la Intranet</em>, lea atentamente las instrucciones que aparecen para la puesta en marcha de la aplicación. Haga clic en el botón Iniciar sesión y utilice el usuario <strong>admin</strong> y la siguiente contraseña:</p>

				    		<div class="text-center">
				    			<code class="text-center lead"><?php echo $pass_admin; ?></code>
				    		</div>

				    		<br><br>

				    		<div class="text-center">
				    			<a href="../index.php" class="btn btn-primary">Iniciar sesión</a>
				    		</div>
				    		<?php endif; ?>

				    	</div>

				    	<br><br>

				    </div>
				    <?php endif; ?>

				  </div><!-- /.tab-content -->

			</div><!-- /.col-sm-offset-2 .col-sm-8 -->

		</div><!-- /.row -->

		</form>


	</div><!-- /.container -->


	<footer class="hidden-print">
		<div class="container-fluid">
			<p class="pull-left text-muted">&copy; <?php echo date('Y'); ?>, IES Monterroso</p>

			<ul class="pull-right list-inline">
				<li>Versión <?php echo INTRANET_VERSION; ?></li>
				<li><a href="../aviso-legal/">Aviso legal</a></li>
				<li><a href="../LICENSE.md" target="_blank">Licencia</a></li>
				<li><a href="https://github.com/IESMonterroso/intranet" target="_blank">Github</a></li>
			</ul>
		</div>
	</footer>


	<script src="../js/jquery-2.1.1.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
	<script src="../js/validator/validator.min.js"></script>


	<script>
	$(document).ready(function() {

		$('#old-ie').modal({
			backdrop: true,
			keyboard: false,
			show: true
		});


		$("#terms-accept").click(function() {
		    if($("#terms-accept").is(':checked')) {
		        $("a").removeClass("disabled");
		    }
		    else {
		        $("a").addClass("disabled");
		    }
		});

		$("#check_asistencia").click(function() {
		    if($("#check_asistencia").is(':checked')) {
		        $("#check_horarios").prop('checked', true);
		    }
		    else {
		        $("#check_horarios").prop('checked', false);
		    }
		});

		$("#check_horarios").click(function() {
		    if(! $("#check_horarios").is(':checked')) {
		        $("#check_asistencia").prop('checked', false);
		    }
		});

		$('#instalar').click(function(e) {
			if(! $(this).hasClass('disabled')) {
				$('#wrap_modulos').hide();
				$('#wrap_instalacion').show();

				setTimeout(function(){
					$('#wrap_instalacion').append($("<input type=\"hidden\" name=\"instalar\" value=\"instalar\">"));
					$("form").submit();
				}, 1500);
			}
			else {
				$("form").submit();
			}

		});

		$('#form-instalacion').validator();

	});
	</script>

</body>
</html>
