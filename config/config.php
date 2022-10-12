<?php
require('../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1));

// TITULO DE LA PAGINA
$page_header = 'Configuración de la Intranet';

$config_nuevo = 0;

$provincias = array('Almería', 'Cádiz', 'Córdoba', 'Granada', 'Huelva', 'Jaén', 'Málaga', 'Sevilla');

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
if (isset($_POST['config']))
{

	// LIMPIAMOS CARACTERES
	if (! isset($config['intranet_secret']) || empty($config['intranet_secret'])) {
		$intranet_secret = generateRandomPassword(25);
	}
	else {
		$intranet_secret = $config['intranet_secret'];
	}
	$dominio_centro	= limpiar_string($_POST['dominio_centro']);
	$forzar_ssl = (isset($_POST['forzar_ssl'])) ? 1 : 0;
	$mantenimiento = (isset($_POST['mantenimiento'])) ? 1 : 0;

	$nombre_centro		= limpiar_string($_POST['nombre_centro']);
	$codigo_centro		= limpiar_string($_POST['codigo_centro']);
	$email_centro		= limpiar_string($_POST['email_centro']);
	$direccion_centro	= limpiar_string($_POST['direccion_centro']);
	$codpostal_centro	= limpiar_string($_POST['codpostal_centro']);
	$localidad_centro	= limpiar_string($_POST['localidad_centro']);
	$provincia_centro	= limpiar_string($_POST['provincia_centro']);
	$telefono_centro	= limpiar_string($_POST['telefono_centro']);
	$fax_centro			= limpiar_string($_POST['fax_centro']);

	$direccion_director			= limpiar_string($_POST['direccion_director']);
	$direccion_jefe_estudios	= limpiar_string($_POST['direccion_jefe_estudios']);
	$direccion_secretaria		= limpiar_string($_POST['direccion_secretaria']);

	$db_host	= limpiar_string($_POST['db_host']);
	$db_name	= limpiar_string($_POST['db_name']);
	$db_user	= limpiar_string($_POST['db_user']);
	$db_pass	= limpiar_string($_POST['db_pass']);

	$ip_blacklist = (isset($_POST['ip_blacklist'])) ? limpiar_string($_POST['ip_blacklist']) : '';

	$email_smtp 			= (isset($_POST['email_smtp'])) ? 1 : 0;
	$email_smtp_hostname	= limpiar_string($_POST['email_smtp_hostname']);
	$email_smtp_port		= intval($_POST['email_smtp_port']);
	$email_smtp_username	= limpiar_string($_POST['email_smtp_username']);
	$email_smtp_password	= limpiar_string($_POST['email_smtp_password']);

	$curso_escolar	= limpiar_string($_POST['curso_escolar']);
	$fecha_inicio	= limpiar_string($_POST['fecha_inicio']);
	$fecha_final	= limpiar_string($_POST['fecha_final']);

	$modulo_biblioteca = (isset($_POST['mod_biblioteca'])) ? 1 : 0;
	$modulo_biblioteca_web	= limpiar_string($_POST['mod_biblioteca_web']);

	$modulo_bilingue = (isset($_POST['mod_bilingue'])) ? 1 : 0;

	$modulo_centrotic = (isset($_POST['mod_centrotic'])) ? 1 : 0;
	$modulo_centrotic_office365 = (isset($_POST['mod_centrotic_office365'])) ? 1 : 0;
	$modulo_centrotic_gsuite = (isset($_POST['mod_centrotic_gsuite'])) ? 1 : 0;
	$modulo_centrotic_moodle = (isset($_POST['mod_centrotic_moodle'])) ? 1 : 0;

	$modulo_documentos = (isset($_POST['mod_documentos'])) ? 1 : 0;
	$modulo_documentos_dir	= limpiar_string($_POST['mod_documentos_dir']);
	$mod_documentos_biblioteca = (isset($_POST['mod_documentos_biblioteca'])) ? 1 : 0;
	$mod_documentos_recursos = (isset($_POST['mod_documentos_recursos'])) ? 1 : 0;
	$mod_documentos_departamentos = (isset($_POST['mod_documentos_departamentos'])) ? 1 : 0;

	$modulo_sms = (isset($_POST['mod_sms'])) ? 1 : 0;
	$modulo_sms_id		= limpiar_string($_POST['mod_sms_id']);
	$modulo_sms_user	= limpiar_string($_POST['mod_sms_user']);
	$modulo_sms_pass	= limpiar_string($_POST['mod_sms_pass']);

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

	$modulo_convivencia = (isset($_POST['mod_convivencia'])) ? 1 : 0;

	$modulo_matriculacion = (isset($_POST['mod_matriculacion'])) ? 1 : 0;
	$modulo_transporte_escolar = (isset($_POST['mod_transporte_escolar'])) ? 1 : 0;


	$api_tinymce_key = limpiar_string($_POST['api_tinymce_key']);

	$api_google_analytics_tracking_id = limpiar_string($_POST['api_google_analytics_tracking_id']);

	$api_google_maps_key = limpiar_string($_POST['api_google_maps_key']);
	$api_google_maps_latitude = limpiar_string($_POST['api_google_maps_latitude']);
	$api_google_maps_longitude = limpiar_string($_POST['api_google_maps_longitude']);
	$api_google_maps_zoom = limpiar_string($_POST['api_google_maps_zoom']);

	$api_google_recaptcha_key = limpiar_string($_POST['api_google_recaptcha_key']);
	$api_google_recaptcha_secret = limpiar_string($_POST['api_google_recaptcha_secret']);

	$api_facebook_chat_page_id = limpiar_string($_POST['api_facebook_chat_page_id']);
	$api_facebook_chat_theme_color = limpiar_string($_POST['api_facebook_chat_theme_color']);
	$api_facebook_chat_welcome = limpiar_string($_POST['api_facebook_chat_welcome']);


	// CREACIÓN DEL ARCHIVO DE CONFIGURACIÓN
	if($file = fopen(CONFIG_FILE, 'w+'))
	{
		fwrite($file, "<?php \r\n");

		fwrite($file, "\r\n// CONFIGURACIÓN INTRANET\r\n");
		fwrite($file, "\$config['intranet_secret']\t\t\t= '$intranet_secret';\r\n");
		fwrite($file, "\$config['dominio']\t\t\t= '$dominio_centro';\r\n");
		fwrite($file, "\$config['forzar_ssl']\t\t= $forzar_ssl;\r\n");
		fwrite($file, "\$config['mantenimiento']\t= $mantenimiento;\r\n");

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

		fwrite($file, "\r\n// BLACKLIST INICIOS DE SESION\r\n");
		fwrite($file, "\$config['ip_blacklist']\t= '$ip_blacklist';\r\n");

		fwrite($file, "\r\n// SERVIDOR SMTP\r\n");
		fwrite($file, "\$config['email_smtp']['isSMTP']\t\t\t= $email_smtp;\r\n");
		fwrite($file, "\$config['email_smtp']['smtp_auth']\t\t= true;\r\n");
		fwrite($file, "\$config['email_smtp']['hostname']\t\t= '$email_smtp_hostname';\r\n");
		fwrite($file, "\$config['email_smtp']['port']\t\t\t= $email_smtp_port;\r\n");
		fwrite($file, "\$config['email_smtp']['smtp_secure']\t= 'tls';\r\n");
		fwrite($file, "\$config['email_smtp']['username']\t\t= '$email_smtp_username';\r\n");
		fwrite($file, "\$config['email_smtp']['password']\t\t= '$email_smtp_password';\r\n");

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

		fwrite($file, "\r\n// MÓDULO: AULA DE CONVIVENCIA\r\n");
		fwrite($file, "\$config['mod_convivencia']\t\t\t= $modulo_convivencia;\r\n");

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

		$config_nuevo = 1;
		fclose($file);

		include('../config.php');

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

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://iesmonterroso.org/intranet/analitica/baliza.php");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $analitica);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_exec($ch);
		curl_close($ch);
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

}


$PLUGIN_COLORPICKER = 1;

include('../menu.php');
?>

	<div class="container">

		<div class="page-header">
			<h2><?php echo $page_header; ?></h2>
		</div>

		<?php if($config_nuevo): ?>
		<div class="alert alert-success">
			Los cambios han sido guardados correctamente.
		</div>
		<?php endif; ?>


		<form id="form-configuracion" class="form-horizontal" data-toggle="validator" class="form-horizontal" method="post" action="" autocomplete="off">

			<ul class="nav nav-tabs" role="tablist">
				<li class="active"><a href="#configuracion" aria-controls="configuracion" role="tab" data-toggle="tab">Configuración general</a></li>
				<li><a href="#modulos" aria-controls="modulos" role="tab" data-toggle="tab">Módulos</a></li>
				<li><a href="#apis" aria-controls="apis" role="tab" data-toggle="tab">APIs</a></li>
			</ul>

			<br>

			<div id="tabs-configuracion" class="tab-content">

				<!-- CONFIGURACIÓN GENERAL -->
				<div role="tabpanel" class="tab-pane active" id="configuracion">
					<div class="row">

						<div class="col-sm-6">

							<div class="well">

								<h3><i class="fas fa-university fa-fw"></i> Información de su centro educativo</h3>
								<br>

								<input type="hidden" name="dominio_centro" value="<?php echo ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) ? $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'] : $_SERVER['SERVER_NAME']; ?>">

								<?php if(forzar_ssl()): ?>
								<input type="hidden" name="forzar_ssl" value="1">
								<?php endif; ?>

								<?php $tam_label = 4; ?>
								<?php $tam_control = 7; ?>

								<div class="form-group">
								  <label for="nombre_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Denominación <span class="text-danger">*</span></label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <input type="text" class="form-control" id="nombre_centro" name="nombre_centro" value="<?php echo $config['centro_denominacion']; ?>" data-error="La denominación del centro no es válida" required>
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

								<div class="form-group">
								  <label for="codigo_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Centro código <span class="text-danger">*</span></label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <input type="text" class="form-control" id="codigo_centro" name="codigo_centro" value="<?php echo $config['centro_codigo']; ?>" maxlength="8" data-minlength="8" data-error="El código del centro no es válido" required>
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

								<div class="form-group">
								  <label for="email_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Correo electrónico <span class="text-danger">*</span></label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <input type="email" class="form-control" id="email_centro" name="email_centro" value="<?php echo $config['centro_email']; ?>" data-error="La dirección de correo electrónico no es válida" required>
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

								<div class="form-group">
								  <label for="direccion_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Dirección postal <span class="text-danger">*</span></label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <input type="text" class="form-control" id="direccion_centro" name="direccion_centro" value="<?php echo $config['centro_direccion']; ?>" data-error="La dirección postal no es válida" required>
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

								<div class="form-group">
								  <label for="codpostal_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Código postal <span class="text-danger">*</span></label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <input type="text" class="form-control" id="codpostal_centro" name="codpostal_centro" value="<?php echo $config['centro_codpostal']; ?>" maxlength="5" data-minlength="5" data-error="El código postal no es válido" required>
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

								<div class="form-group">
								  <label for="localidad_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Localidad <span class="text-danger">*</span></label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <input type="text" class="form-control" id="localidad_centro" name="localidad_centro" value="<?php echo $config['centro_localidad']; ?>" data-error="La localidad no es válida" required>
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

								<div class="form-group">
								  <label for="provincia_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Provincia <span class="text-danger">*</span></label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <select class="form-control" id="provincia_centro" name="provincia_centro" data-error="La provincia no es válida" required>
								    	<option value=""></option>
								    	<?php foreach($provincias as $provincia): ?>
								    	<option value="<?php echo $provincia; ?>" <?php echo ($provincia == $config['centro_provincia']) ? 'selected' : ''; ?>><?php echo $provincia; ?></option>
								    	<?php endforeach; ?>
								    </select>
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

								<div class="form-group">
								  <label for="telefono_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Teléfono <span class="text-danger">*</span></label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <input type="tel" class="form-control" id="telefono_centro" name="telefono_centro" value="<?php echo $config['centro_telefono']; ?>" maxlength="9" data-minlength="9" data-error="El télefono no es válido" required>
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

								<div class="form-group">
								  <label for="fax_centro" class="col-sm-<?php echo $tam_label; ?> control-label">Fax</label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <input type="tel" class="form-control" id="fax_centro" name="fax_centro" value="<?php echo $config['centro_fax']; ?>" maxlength="9" data-minlength="9" data-error="El fax no es válido">
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

								<div class="form-group">
								  <label for="direccion_director" class="col-sm-<?php echo $tam_label; ?> control-label">Director/a <span class="text-danger">*</span></label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <input type="text" class="form-control" id="direccion_director" name="direccion_director" value="<?php echo $config['directivo_direccion']; ?>" maxlength="60" data-error="Este campo es obligatorio" required>
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

								<div class="form-group">
								  <label for="direccion_jefe_estudios" class="col-sm-<?php echo $tam_label; ?> control-label">Jefe/a de Estudios <span class="text-danger">*</span></label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <input type="text" class="form-control" id="direccion_jefe_estudios" name="direccion_jefe_estudios" value="<?php echo $config['directivo_jefatura']; ?>" maxlength="60" data-error="Este campo es obligatorio" required>
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

								<div class="form-group">
								  <label for="direccion_secretaria" class="col-sm-<?php echo $tam_label; ?> control-label">Secretario/a <span class="text-danger">*</span></label>
								  <div class="col-sm-<?php echo $tam_control; ?>">
								    <input type="text" class="form-control" id="direccion_secretaria" name="direccion_secretaria" value="<?php echo $config['directivo_secretaria']; ?>" maxlength="60" data-error="Este campo es obligatorio" required>
								    <div class="help-block with-errors"></div>
								  </div>
								</div>

							</div>

							<div class="well">

								<h3><i class="fas fa-sign-in-alt fa-fw"></i> Opciones de acceso</h3>
								<br>

								<div class="checkbox">
									<label>
										<input type="checkbox" name="forzar_ssl" value="1" <?php echo (isset($config['forzar_ssl']) && $config['forzar_ssl']) ? 'checked' : ''; ?>>
										<strong>Forzar acceso a la Intranet mediante HTTPS</strong>
									</label>
								</div>

								<div class="checkbox">
									<label>
										<input type="checkbox" name="mantenimiento" value="1" <?php echo (isset($config['mantenimiento']) && $config['mantenimiento']) ? 'checked' : ''; ?>>
										<strong>Modo mantenimiento.</strong> Solo equipo directivo tiene acceso a la Intranet.
									</label>
								</div>

								<br>

								<div class="form-group">
									<label for="ip_blacklist" class="col-sm-<?php echo $tam_label; ?> control-label">IP Blacklist</label>
									<div class="col-sm-<?php echo $tam_control; ?>">
										<input type="text" class="form-control" id="ip_blacklist" name="ip_blacklist" value="<?php echo isset($config['ip_blacklist']) ? $config['ip_blacklist'] : ''; ?>" maxlength="255">
									</div>
								</div>

							</div>

						</div><!-- /.col-sm-6 -->


						<div class="col-sm-6">

							<div class="well">

								<h3><i class="fas fa-database fa-fw"></i> Base de datos</h3>
								<br>

								<?php $tam_label = 4; ?>
								<?php $tam_control = 7; ?>

								<div class="form-group">
									<label for="db_host" class="col-sm-<?php echo $tam_label; ?> control-label">Servidor <span class="text-danger">*</span></label>
									<div class="col-sm-<?php echo $tam_control; ?>">
									  <input type="text" class="form-control" id="db_host" name="db_host" value="<?php echo $config['db_host']; ?>" data-error="La dirección servidor de base de datos no es válida" required>
									  <div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="db_name" class="col-sm-<?php echo $tam_label; ?> control-label">Base de datos <span class="text-danger">*</span></label>
									<div class="col-sm-<?php echo $tam_control; ?>">
									  <input type="text" class="form-control" id="db_name" name="db_name" value="<?php echo $config['db_name']; ?>" data-error="El nombre de la base de datos no es válido" required>
									  <div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="db_user" class="col-sm-<?php echo $tam_label; ?> control-label">Usuario <span class="text-danger">*</span></label>
									<div class="col-sm-<?php echo $tam_control; ?>">
									  <input type="text" class="form-control" id="db_user" name="db_user" value="<?php echo $config['db_user']; ?>" data-error="El nombre de usuario de la base de datos no es válido" required>
									  <div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="db_pass" class="col-sm-<?php echo $tam_label; ?> control-label">Contraseña <span class="text-danger">*</span></label>
									<div class="col-sm-<?php echo $tam_control; ?>">
									  <input type="text" class="form-control" id="db_pass" name="db_pass" value="<?php echo $config['db_pass']; ?>" data-error="La contraseña de la base de datos no es válida" required>
									  <div class="help-block with-errors"></div>
									</div>
								</div>

							</div>

							<div class="well">

								<h3><i class="fas fa-graduation-cap fa-fw"></i> Curso escolar</h3>
								<br>

								  <?php $tam_label = 4; ?>
								  <?php $tam_control = 7; ?>

								  <div class="form-group">
								    <label for="curso_escolar" class="col-sm-<?php echo $tam_label; ?> control-label">Curso escolar <span class="text-danger">*</span></label>
								    <div class="col-sm-<?php echo $tam_control; ?>">
								      <input type="text" class="form-control" id="curso_escolar" name="curso_escolar" value="<?php echo $config['curso_actual']; ?>" required>
								      <div class="help-block with-errors"></div>
								    </div>
								  </div>

								  <div class="form-group">
								    <label for="fecha_inicio" class="col-sm-<?php echo $tam_label; ?> control-label">Fecha de inicio <span class="text-danger">*</span></label>
								    <div class="col-sm-<?php echo $tam_control; ?>">
								      <input type="text" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo $config['curso_inicio']; ?>" required>
								      <div class="help-block with-errors"></div>
								    </div>
								  </div>

								  <div class="form-group">
								    <label for="fecha_final" class="col-sm-<?php echo $tam_label; ?> control-label">Fecha final <span class="text-danger">*</span></label>
								    <div class="col-sm-<?php echo $tam_control; ?>">
								      <input type="text" class="form-control" id="fecha_final" name="fecha_final" value="<?php echo $config['curso_fin']; ?>" required>
								      <div class="help-block with-errors"></div>
								    </div>
								  </div>

							</div>

							<div class="well">

								<h3><i class="far fa-envelope fa-fw"></i> Configuración SMTP</h3>
								<br>

								<?php $tam_label = 4; ?>
								<?php $tam_control = 7; ?>

								<div class="checkbox">
									<label>
										<input type="checkbox" id="email_smtp" name="email_smtp" value="1" <?php echo (isset($config['email_smtp']['isSMTP']) && $config['email_smtp']['isSMTP']) ? 'checked' : ''; ?>>
										<strong>Utilizar servidor SMTP</strong>
										<p class="help-block">Si no se configura un servidor SMTP se utilizará el servicio sendmail.</p>
									</label>
								</div>

								<div class="form-group">
									<label for="email_smtp_hostname" class="col-sm-<?php echo $tam_label; ?> control-label">Servidor <span class="text-danger required-field">*</span></label>
									<div class="col-sm-<?php echo $tam_control; ?>">
									  <input type="text" class="form-control" id="email_smtp_hostname" name="email_smtp_hostname" value="<?php echo $config['email_smtp']['hostname']; ?>" data-error="La dirección del servidor SMTP no es válida" placeholder="smtp.gmail.com">
									  <div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="email_smtp_port" class="col-sm-<?php echo $tam_label; ?> control-label">Puerto <span class="text-danger required-field">*</span></label>
									<div class="col-sm-<?php echo $tam_control; ?>">
									  <input type="text" class="form-control" id="email_smtp_port" name="email_smtp_port" value="<?php echo $config['email_smtp']['port']; ?>" data-error="El puerto del servidor SMTP no es válido" placeholder="587">
									  <div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="email_smtp_username" class="col-sm-<?php echo $tam_label; ?> control-label">Usuario <span class="text-danger required-field">*</span></label>
									<div class="col-sm-<?php echo $tam_control; ?>">
									  <input type="text" class="form-control" id="email_smtp_username" name="email_smtp_username" value="<?php echo $config['email_smtp']['username']; ?>" data-error="El nombre de usuario del servidor SMTP no es válido" placeholder="intranet@<?php echo $config['dominio']; ?>">
									  <div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="email_smtp_password" class="col-sm-<?php echo $tam_label; ?> control-label">Contraseña <span class="text-danger required-field">*</span></label>
									<div class="col-sm-<?php echo $tam_control; ?>">
									  <input type="text" class="form-control" id="email_smtp_password" name="email_smtp_password" value="<?php echo $config['email_smtp']['password']; ?>" data-error="La contraseña del servidor SMTP no es válida">
									  <div class="help-block with-errors"></div>
									</div>
								</div>

							</div>

						</div><!-- /.col-sm-6 -->

					</div><!-- /.row -->

				</div><!-- /.tab-pane -->

				<!-- CONFIGURACIÓN MÓDULOS -->
				<div role="tabpanel" class="tab-pane" id="modulos">

					<div class="well">
						<h3><i class="fas fa-cubes"></i> Configuración de módulos</h3>
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
									<li><a href="#mod_convivencia" aria-controls="mod_convivencia" role="tab" data-toggle="tab">Aula de Convivencia</a></li>
									<li><a href="#mod_matriculacion" aria-controls="mod_matriculacion" role="tab" data-toggle="tab">Matriculación</a></li>
								</ul>
							</div>

							<div class="tab-content col-sm-7" style="border-left: 3px solid #dce4ec; padding-left: 45px;">

								<!-- MÓDULO: BIBLIOTECA -->
							    <div role="tabpanel" class="tab-pane active" id="mod_biblioteca">

							    	<div class="form-group">
								    	<div class="checkbox">
								    		<label>
					    			    		<input type="checkbox" name="mod_biblioteca" value="1" <?php echo (isset($config['mod_biblioteca']) && $config['mod_biblioteca']) ? 'checked' : ''; ?>>
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
						    		      <input type="text" class="form-control" id="mod_biblioteca_web" name="mod_biblioteca_web" value="<?php echo str_replace('http://', '',$config['mod_biblioteca_web']); ?>">
						    		    </div>
							    	</div>

							    </div>


							    <!-- MÓDULO: CENTRO BILINGÜE -->
							    <div role="tabpanel" class="tab-pane" id="mod_bilingue">

							    	<div class="form-group">
								    	<div class="checkbox">
								    		<label>
					    			    		<input type="checkbox" name="mod_bilingue" value="1" <?php echo (isset($config['mod_bilingue']) && $config['mod_bilingue']) ? 'checked' : ''; ?>>
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
					    			    		<input type="checkbox" name="mod_centrotic" value="1" <?php echo (isset($config['mod_centrotic']) && $config['mod_centrotic']) ? 'checked' : ''; ?>>
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
							    <div role="tabpanel" class="tab-pane" id="mod_documentos" <?php echo (isset($config['mod_documentos']) && $config['mod_documentos']) ? 'checked' : ''; ?>>

							    	<div class="form-group">
								    	<div class="checkbox">
								    		<label>
								    			<input type="checkbox" name="mod_documentos" value="1" <?php echo (isset($config['mod_documentos']) && $config['mod_documentos']) ? 'checked' : ''; ?>>
								    			<strong>Documentos</strong>
								    			<p class="help-block">Directorio en el Servidor local donde tenemos documentos públicos que queremos administrar (visualizar, eliminar, subir, compartir, etc.) con la Intranet.</p>
								    		</label>
								    	</div>
								    </div>

							    	<div class="form-group">
							    		<label for="mod_documentos_dir">Directorio público</label>
							    	    <input type="text" class="form-control" id="mod_documentos_dir" name="mod_documentos_dir" value="<?php echo $config['mod_documentos_dir']; ?>">
							    	</div>

							    	<div class="checkbox">
			    			    		<label>
			    			    			<input type="checkbox" name="mod_documentos_recursos" value="1" <?php echo (isset($config['mod_documentos_recursos']) && $config['mod_documentos_recursos']) ? 'checked' : ''; ?>>
			    			    			<strong>Recursos</strong>
			    			    			<p class="help-block">Creará una carpeta por cada unidad del Centro para que el profesorado pueda almacenar recursos educativos, visibles desde la <em>Página del Centro</em>.</p>
			    			    		</label>
			    			    	</div>

							    	<div class="checkbox">
							    		<label>
							    			<input type="checkbox" name="mod_documentos_biblioteca" value="1" <?php echo (isset($config['mod_documentos_biblioteca']) && $config['mod_documentos_biblioteca']) ? 'checked' : ''; ?>>
							    			<strong>Biblioteca</strong>
							    			<p class="help-block">Creará una carpeta donde el personal de Biblioteca puede subir documentos de interés para la comunidad educativa.</p>
							    		</label>
							    	</div>

							    	<div class="checkbox">
							    		<label>
							    			<input type="checkbox" name="mod_documentos_departamentos" value="1" <?php echo (isset($config['mod_documentos_departamentos']) && $config['mod_documentos_departamentos']) ? 'checked' : ''; ?>>
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
								    			<input type="checkbox" name="mod_sms" value="1" <?php echo (isset($config['mod_sms']) && $config['mod_sms']) ? 'checked' : ''; ?>>
								    			<strong>Envío de SMS</strong>
								    			<p class="help-block">Pone en funcionamiento el envío de SMS en distintos lugares de la Intranet (Problemas de convivencia, faltas de asistencia, etc.). La aplicación está preparada para trabajar con la API de <a href="http://www.trendoo.es/" target="_blank">Trendoo</a>.</p>
								    		</label>
								    	</div>
								    </div>

							    	<div class="form-group">
							    		<label for="mod_sms_id">Nombre de identificación (<abbr title="Transmission Path Originating Address">TPOA</abbr>)</label>
							    	    <input type="text" class="form-control" id="mod_sms_id" name="mod_sms_id" value="<?php echo $config['mod_sms_id']; ?>" maxlength="11">
							    	    <p class="help-block">11 caracteres como máximo.</p>
							    	</div>

							    	<div class="form-group">
							    		<label for="mod_sms_user">Usuario</label>
							    	    <input type="text" class="form-control" id="mod_sms_user" name="mod_sms_user" value="<?php echo $config['mod_sms_user']; ?>">
							    	</div>

							    	<div class="form-group">
							    		<label for="mod_sms_pass">Contraseña</label>
							    	    <input type="text" class="form-control" id="mod_sms_pass" name="mod_sms_pass" value="<?php echo $config['mod_sms_pass']; ?>">
							    	</div>

							    </div>

							    <!-- MÓDULO: NOTIFICACIONES -->
							    <div role="tabpanel" class="tab-pane" id="mod_notificaciones">

							    	<div class="form-group">
							        	<div class="checkbox">
							        		<label>
							        			<input type="checkbox" name="mod_notificaciones" value="1" <?php echo (isset($config['mod_notificaciones']) && $config['mod_notificaciones']) ? 'checked' : ''; ?>>
							        			<strong>Notificar a los profesores con tareas pendientes</strong>
							        			<p class="help-block">Pone en funcionamiento el envío de SMS o correo electrónico a los profesores que no hayan accedido a la aplicación hace más de 4 días o tengan tareas pendientes: más de 25 mensajes sin leer, informes de tareas o tutoría sin rellenar.</p>
							        		</label>
							        	</div>
							        </div>

							        <div class="form-group">
							        	<label for="mod_notificaciones_dominios">Lista de dominios permitidos (separados por coma)</label>
							    	    <input type="text" class="form-control" id="mod_notificaciones_dominios" name="mod_notificaciones_dominios" value="<?php echo $config['mod_notificaciones_dominios']; ?>">
							        </div>

							        <div class="form-group">
							        	<div class="checkbox">
							        		<label>
							        			<input type="checkbox" name="mod_notificaciones_asistencia" value="1" <?php echo (isset($config['mod_notificaciones_asistencia']) && $config['mod_notificaciones_asistencia']) ? 'checked' : ''; ?>>
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
								    			<input type="checkbox" id="check_asistencia" name="mod_asistencia" value="1" <?php echo (isset($config['mod_asistencia']) && $config['mod_asistencia']) ? 'checked' : ''; ?>>
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
								    			<input type="checkbox" id="check_horarios" name="mod_horarios" value="1" <?php echo (isset($config['mod_horarios']) && $config['mod_horarios']) ? 'checked' : ''; ?>>
								    			<strong>Horarios</strong>
								    			<p class="help-block">Si disponemos de un archivo de Horario en formato XML (como el que se utiliza para subir a Séneca) o DEL (como el que genera el programa Horw) para importar sus datos a la Intranet. Aunque no obligatoria, esta opción es necesaria si queremos hacernos una idea de todo lo que la aplicación puede ofrecer.</p>
								    		</label>
								    	</div>
								    </div>

							    </div>


							    <!-- MÓDULO: AULA DE CONVIVENCIA -->
							    <div role="tabpanel" class="tab-pane" id="mod_convivencia">

							    	<div class="form-group">
								    	<div class="checkbox">
								    		<label>
								    			<input type="checkbox" id="check_convivencia" name="mod_convivencia" value="1" <?php echo (isset($config['mod_convivencia']) && $config['mod_convivencia']) ? 'checked' : ''; ?>>
								    			<strong>Aula de Convivencia</strong>
								    			<p class="help-block">Si el Centro dispone de un <em>Aula de Convivencia</em> donde son enviados los alumnos cuando un profesor los expulsa de clase, o bien donde realizan sus tareas los alumnos con determinados problemas de conducta que han sido seleccionados por la Jefatura de Estudios. Este módulo supone que em módulo de horarios ha sido marcao y los datos del horario del Centro se han incorporado a la aplicación</p>
								    		</label>
								    	</div>
								    </div>

							    </div>


							    <!-- MÓDULO: MATRICULACIÓN -->
							    <div role="tabpanel" class="tab-pane" id="mod_matriculacion">

							    	<div class="form-group">
								    	<div class="checkbox">
								    		<label>
								    			<input type="checkbox" name="mod_matriculacion" value="1" <?php echo (isset($config['mod_matriculacion']) && $config['mod_matriculacion']) ? 'checked' : ''; ?>>
								    			<strong>Matriculación</strong>
								    			<p class="help-block">Este módulo permite matricular a los alumnos desde la propia aplicación o bien desde la página pública del Centro incluyendo el código correspondiente. Requiere que cada Centro personalice las materias y optativas que va a ofrecer a sus Alumnos.</p>
								    		</label>
								    	</div>
								    </div>

							    	<div class="form-group">
								    	<div class="checkbox">
								    		<label>
								    			<input type="checkbox" name="mod_transporte_escolar" value="1" <?php echo (isset($config['mod_transporte_escolar']) && $config['mod_transporte_escolar']) ? 'checked' : ''; ?>>
								    			<strong>Transporte escolar</strong>
								    			<p class="help-block">Activa la selección de transporte escolar</p>
								    		</label>
								    	</div>
								    </div>

							    </div>
							  </div>
						</div>

					</div>

				</div><!-- /.tab-pane -->

				<!-- APIS -->
				<div role="tabpanel" class="tab-pane" id="apis">
					<div class="row">

						<div class="col-sm-12">

							<div class="well">
								<h3><i class="fas fa-key"></i> APIs</h3>
								<br>

								<div class="row">
									<div class="col-sm-4" style="border-right: 3px solid #dce4ec; margin-right: -3px;">
										<ul class="nav nav-pills nav-stacked" role="tablist">
											<li class="active"><a href="#api_tinymce" aria-controls="api_tinymce" role="tab" data-toggle="tab">Editor TinyMCE</a></li>
											<?php if (isset($_SESSION['pagina_centro']) && $_SESSION['pagina_centro']): ?>
											<li><a href="#api_google_analytics" aria-controls="api_google_analytics" role="tab" data-toggle="tab">Google Analytics</a></li>
											<li><a href="#api_google_maps" aria-controls="api_google_maps" role="tab" data-toggle="tab">Google Maps</a></li>
											<li><a href="#api_google_recaptcha" aria-controls="api_google_recaptcha" role="tab" data-toggle="tab">Google reCaptcha</a></li>
											<li><a href="#api_facebook_chat" aria-controls="api_facebook_chat" role="tab" data-toggle="tab">Facebook Chat Plugin</a></li>
											<?php endif; ?>
										</ul>
									</div>

									<div class="tab-content col-sm-7" style="border-left: 3px solid #dce4ec; padding-left: 45px;">

										<!-- API: TinyMCE -->
									    <div role="tabpanel" class="tab-pane active" id="api_tinymce">

									    	<div class="form-group">
									    		<label for="cmp_api_tinymce_key">API Key</label>
						    			    	<input type="text" class="form-control" id="cmp_api_tinymce_key" name="api_tinymce_key" placeholder="no-api-key" value="<?php echo (isset($config['api_tinymce_key']) && $config['api_tinymce_key']) ? $config['api_tinymce_key'] : ''; ?>">
					    			    	</div>
					    			    </div><!-- /.tab-panel -->

					    			    <?php if (isset($_SESSION['pagina_centro']) && $_SESSION['pagina_centro']): ?>
					    			    <!-- API: Google Analytics -->
									    <div role="tabpanel" class="tab-pane" id="api_google_analytics">

									    	<div class="form-group">
									    		<label for="cmp_api_google_analytics_tracking_id">GA Tracking ID</label>
						    			    	<input type="text" class="form-control" id="cmp_api_google_analytics_tracking_id" name="api_google_analytics_tracking_id" placeholder="YOUR_GA_TRACKING_ID" value="<?php echo (isset($config['api_google_analytics_tracking_id']) && $config['api_google_analytics_tracking_id']) ? $config['api_google_analytics_tracking_id'] : ''; ?>">
					    			    	</div>

					    			    	<p class="help-block">Consigue el ID de seguimiento para usar la API de Google Analytics en <a href="https://analytics.google.com/analytics/" target="_blank">https://analytics.google.com/analytics/</a></p>
					    			    </div><!-- /.tab-panel -->

					    			     <!-- API: Google Maps -->
									    <div role="tabpanel" class="tab-pane" id="api_google_maps">

									    	<div class="form-group">
									    		<label for="cmp_api_google_maps_key">API key</label>
						    			    	<input type="text" class="form-control" id="cmp_api_google_maps_key" name="api_google_maps_key" placeholder="YOUR_API_KEY" value="<?php echo (isset($config['api_google_maps_key']) && $config['api_google_maps_key']) ? $config['api_google_maps_key'] : ''; ?>">
					    			    	</div>

					    			    	<p class="help-block">Consigue la clave para usar la API de Google Maps Javascript en <a href="https://console.cloud.google.com/" target="_blank">https://console.cloud.google.com/</a></p>

					    			    	<br>

					    			    	<div class="form-group">
									    		<label for="cmp_api_google_maps_latitude">Latitud</label>
						    			    	<input type="text" class="form-control" id="cmp_api_google_maps_latitude" name="api_google_maps_latitude" placeholder="36.4295948" value="<?php echo (isset($config['api_google_maps_latitude']) && $config['api_google_maps_latitude']) ? $config['api_google_maps_latitude'] : ''; ?>">
					    			    	</div>

					    			    	<div class="form-group">
									    		<label for="cmp_api_google_maps_langitude">Longitud</label>
						    			    	<input type="text" class="form-control" id="cmp_api_google_maps_langitude" name="api_google_maps_longitude" placeholder="-5.1544486" value="<?php echo (isset($config['api_google_maps_longitude']) && $config['api_google_maps_longitude']) ? $config['api_google_maps_longitude'] : ''; ?>">
					    			    	</div>

					    			    	<div class="form-group">
									    		<label for="cmp_api_google_maps_zoom">Zoom</label>
						    			    	<select class="form-control" id="cmp_api_google_maps_zoom" name="api_google_maps_zoom">
						    			    		<?php for ($zoom=0; $zoom < 19; $zoom++): ?>
						    			    		<option value="<?php echo $zoom; ?>" <?php echo ((isset($config['api_google_maps_zoom']) && $config['api_google_maps_zoom'] == $zoom) || (! isset($config['api_google_maps_zoom']) && $zoom == 15)) ? ' selected' : ''; ?>><?php echo $zoom; ?></option>
						    			    		<?php endfor; ?>
						    			    	</select>
					    			    	</div>

					    			    	<p class="help-block">Puedes obtener las coordenadas de tu centro educativo en <a href="https://www.coordenadas-gps.com" target="_blank">https://www.coordenadas-gps.com</a></p>
					    			    	
					    			    </div><!-- /.tab-panel -->

					    			    <!-- API: Google reCaptcha -->
									    <div role="tabpanel" class="tab-pane" id="api_google_recaptcha">

									    	<div class="form-group">
									    		<label for="cmp_api_google_recaptcha_key">Site Key</label>
						    			    	<input type="text" class="form-control" id="cmp_api_google_recaptcha_key" name="api_google_recaptcha_key" placeholder="YOUR_SITE_KEY" value="<?php echo (isset($config['api_google_recaptcha_key']) && $config['api_google_recaptcha_key']) ? $config['api_google_recaptcha_key'] : ''; ?>">
					    			    	</div>

					    			    	<div class="form-group">
									    		<label for="cmp_api_google_recaptcha_secret">Secret Key</label>
						    			    	<input type="text" class="form-control" id="cmp_api_google_recaptcha_secret" name="api_google_recaptcha_secret" placeholder="YOUR_SITE_KEY" value="<?php echo (isset($config['api_google_recaptcha_secret']) && $config['api_google_recaptcha_secret']) ? $config['api_google_recaptcha_secret'] : ''; ?>">
					    			    	</div>

					    			    	<p class="help-block">Consigue la clave para usar la API de Google reCAPTCHA v2 en <a href="https://www.google.com/recaptcha/admin/create" target="_blank">https://www.google.com/recaptcha/admin/create</a></p>
					    			    </div><!-- /.tab-panel -->

					    			    <!-- API: Facebook Chat -->
									    <div role="tabpanel" class="tab-pane" id="api_facebook_chat">

									    	<div class="form-group">
									    		<label for="cmp_api_facebook_chat_page_id">Page ID</label>
						    			    	<input type="text" class="form-control" id="cmp_api_facebook_chat_page_id" name="api_facebook_chat_page_id" placeholder="YOUR_PAGE_ID" value="<?php echo (isset($config['api_facebook_chat_page_id']) && $config['api_facebook_chat_page_id']) ? $config['api_facebook_chat_page_id'] : ''; ?>">
					    			    	</div>

					    			    	<p class="help-block">Lea la documentación <a href="https://developers.facebook.com/docs/messenger-platform/discovery/facebook-chat-plugin" target="_blank">https://developers.facebook.com/docs/messenger-platform/discovery/facebook-chat-plugin</a></p>

					    			    	<br>

					    			    	<div class="form-group">
									    		<label for="cmp_api_facebook_chat_theme_color">Color del chat</label>
									    		<div class="input-group" id="colorpicker1">
							    			    	<input type="text" class="form-control" id="cmp_api_facebook_chat_theme_color" name="api_facebook_chat_theme_color" placeholder="#0084ff" value="<?php echo (isset($config['api_facebook_chat_theme_color']) && $config['api_facebook_chat_theme_color']) ? $config['api_facebook_chat_theme_color'] : '#0084ff'; ?>">
							    			    	<span class="input-group-addon" style="background-color: #dce4ec; "><i></i></span>
						    			    	</div>
					    			    	</div>

					    			    	<div class="form-group">
									    		<label for="cmp_api_facebook_chat_welcome">Mensaje de bienvenida</label>
									    		<input type="text" class="form-control" id="cmp_api_facebook_chat_welcome" name="api_facebook_chat_welcome" placeholder="¡Hola! ¿En qué te podemos ayudar?" value="<?php echo (isset($config['api_facebook_chat_welcome']) && $config['api_facebook_chat_welcome']) ? $config['api_facebook_chat_welcome'] : '¡Hola! ¿En qué te podemos ayudar?'; ?>">
					    			    	</div>
					    			    	
					    			    </div><!-- /.tab-panel -->
					    				<?php endif; ?>

					    			</div><!-- /.tab-content -->
					    		</div><!-- /.row -->
					    	</div><!-- /.well -->

						</div><!-- /.col-sm-6 -->

					</div><!-- /.row -->
				</div><!-- /.tab-panel -->


				<div class="row">

					<div class="col-sm-12">

						<button type="submit" class="btn btn-primary" name="config">Guardar cambios</button>
						<a href="../xml/index.php" class="btn btn-default">Volver</a>

					</div>

				</div>

			</div><!-- /.tab-content -->

		</form>

	</div><!-- /.container -->


	<?php include('../pie.php'); ?>

	<script src="../js/validator/validator.min.js"></script>
	<script>
	$(document).ready(function()
	{
	    $('#form-instalacion').validator();

		$('#email_smtp').on('click', function() {
			if( $(this).is(':checked') ){
				$(".required-field").show();
				$("#email_smtp_hostname").prop('required',true);
				$("#email_smtp_port").prop('required',true);
				$("#email_smtp_username").prop('required',true);
				$("#email_smtp_password").prop('required',true);
			} else {
				$(".required-field").hide();
				$("#email_smtp_hostname").removeAttr('required');
				$("#email_smtp_port").removeAttr('required');
				$("#email_smtp_username").removeAttr('required');
				$("#email_smtp_password").removeAttr('required');
			}
		});
	});
	</script>
	<script type="text/javascript">
	$('#colorpicker1').colorpicker();
	</script>

</body>
</html>
