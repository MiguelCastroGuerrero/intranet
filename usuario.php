<?php
require("bootstrap.php");

// Comprobamos si el usuario es profesor
$result_profesor = mysqli_query($db_con, "SELECT `idprofesor` FROM `profesores_seneca` WHERE `nomprofesor` = '".$_SESSION['profi']."' LIMIT 1");
$esProfesor = (mysqli_num_rows($result_profesor)) ? 1 : 0;

// Comprobamos los dominios permitidos para registrar el correo electrónico
if (isset($config['mod_notificaciones_dominios']) && ! empty($config['mod_notificaciones_dominios'])) {
	$listaDominiosPermitidosCorreos = '';

	$correos_dominios_permitidos = explode(',', $config['mod_notificaciones_dominios']);
	array_push($correos_dominios_permitidos, "g.educaand.es", "m.educaand.es");
	foreach ($correos_dominios_permitidos as $correo_dominio_permitido) {
		$listaDominiosPermitidosCorreos .= '@'.trim($correo_dominio_permitido).', ';
	}
	$listaDominiosPermitidosCorreos = rtrim($listaDominiosPermitidosCorreos, ', ');
}
else {
	$listaDominiosPermitidosCorreos = '';

	$correos_dominios_permitidos = array();
	array_push($correos_dominios_permitidos, "g.educaand.es", "m.educaand.es");
	foreach ($correos_dominios_permitidos as $correo_dominio_permitido) {
		$listaDominiosPermitidosCorreos .= '@'.trim($correo_dominio_permitido).', ';
	}
	$listaDominiosPermitidosCorreos = rtrim($listaDominiosPermitidosCorreos, ', ');
}


// Envío de formularios
if (isset($_POST['registrarCorreo'])) {
	if (checkToken()) {
		if (isset($_POST['email']) && ! empty($_POST['email'])) {
			$cmp_correo = limpiarInput(trim($_POST['email']), 'alphanumericspecial');
			if (filter_var($cmp_correo, FILTER_VALIDATE_EMAIL)) {

				$esDominioPermitido = false;
				if (isset($config['mod_notificaciones_dominios']) && ! empty($config['mod_notificaciones_dominios'])) {
					foreach ($correos_dominios_permitidos as $correo_dominio_permitido) {
						if (strpos($cmp_correo, '@'.trim($correo_dominio_permitido)) !== false) {
							$esDominioPermitido = true;
						}
					}
				}

				if (! $esProfesor || ($esProfesor && (strpos($cmp_correo, '@'.'juntadeandalucia.es') !== false || strpos($cmp_correo, '@'.$_SERVER['SERVER_NAME']) !== false || $esDominioPermitido !== false))) {
					mysqli_query($db_con, "UPDATE `c_profes` SET `correo` = '".$cmp_correo."', `correo_verificado` = 0 WHERE `idea` = '".$_SESSION['ide']."' LIMIT 1");
					correoValidacion();
				}
				else {
					$msg_email_error = "Debe introducir una dirección de correo electrónico @juntadeandalucia.es".((isset($listaDominiosPermitidosCorreos) && ! empty($listaDominiosPermitidosCorreos)) ? ", ".$listaDominiosPermitidosCorreos : "")." o @".$_SERVER['SERVER_NAME'];
				}
			}
			else {
				$msg_email_error = "Debe introducir una dirección de correo electrónico corporativa válida.";
			}
		}
		else {
			$msg_email_error = "Debe introducir una dirección de correo electrónico corporativa válida.";
		}
	}
	else {
		$msg_email_error = "Token CSRF no válido.";
	}	
}

if (isset($_POST['registrarTelefono'])) {
	if (checkToken()) {
		if (isset($_POST['telefono']) && (strlen(trim($_POST['telefono'])) == 9)) {
			$cmp_telefono = limpiarInput(trim($_POST['telefono']), 'numeric');
			if (preg_match("/^[6|7][0-9]{8}$/", $cmp_telefono)) {
				mysqli_query($db_con, "UPDATE `c_profes` SET `telefono` = '".$cmp_telefono."' WHERE `idea` = '".$_SESSION['ide']."' LIMIT 1");
			}
			else {
				$msg_telefono_error = "Debe introducir un número de teléfono móvil válido.";
			}
		}
		elseif (! isset($_POST['telefono']) || (isset($_POST['telefono']) && strlen(trim($_POST['telefono'])) == 0)) {
			mysqli_query($db_con, "UPDATE `c_profes` SET `telefono` = '' WHERE `idea` = '".$_SESSION['ide']."' LIMIT 1");
		}
		else {
			$msg_telefono_error = "Debe introducir un número de teléfono móvil válido.";
		}
	}
	else {
		$msg_telefono_error = "Token CSRF no válido.";
	}
}

if (! isset($_SESSION['session_seneca']) || isset($_SESSION['session_seneca']) && $_SESSION['session_seneca'] == 0) {
	if (isset($_POST['registrarClave'])) {
		if (checkToken()) {
			if ((isset($_POST['password']) && ! empty($_POST['password'])) && (isset($_POST['new_password']) && ! empty($_POST['new_password'])) && (isset($_POST['repeat_password']) && ! empty($_POST['repeat_password']))) {
				$cmp_password = limpiarInput(trim($_POST['password']), 'alphanumericspecial');
				$cmp_new_password = limpiarInput(trim($_POST['new_password']), 'password');
				$cmp_repeat_password = limpiarInput(trim($_POST['repeat_password']), 'password');

				// Obtenemos el hash de la contraseña actual
				$result = mysqli_query($db_con, "SELECT `pass` FROM `c_profes` WHERE `idea` = '".$_SESSION['ide']."' LIMIT 1");
				if (mysqli_num_rows($result)) {
					$row = mysqli_fetch_array($result);
					$usuario['password_hash'] = $row['pass'];
				}

				if (sha1($cmp_password) == $usuario['password_hash'] || password_verify($cmp_password, $usuario['password_hash'])) {
					if (preg_match("((?=.*\d)(?=.*[a-z])(?=.*[A-z])(?=.*[!\"#$%&'()*+,-./:;<=>?@[\]^_`{|}~]).{8,20})", $cmp_new_password)) {

						if ($cmp_password === $cmp_new_password) {
							$msg_password_error = "La nueva contraseña no puede ser la misma que la actual.";
						}
						elseif ($cmp_new_password === $cmp_repeat_password) {
							$hash_clave_sha1 = sha1($cmp_new_password);
							$hash_clave_bcrypt = intranet_password_hash($cmp_new_password);

							$result = mysqli_query($db_con, "UPDATE `c_profes` SET `pass` = '".$hash_clave_bcrypt."' WHERE `idea` = '".$_SESSION['ide']."' LIMIT 1");
								
							if ($result) {
								if (isset($_SESSION['cambiar_clave']) && $_SESSION['cambiar_clave']) {
									unset($_SESSION['cambiar_clave']);
								}
								$msg_password_success = "La contraseña ha sido modificada.";
							}
							else {
								$msg_password_error = "Se ha producido un error al modificar la contraseña.";
							}

						}
						else {
							$msg_password_error = "Las contraseñas no coinciden.";
						}
						
					}
					else {
						$msg_password_error = "La contraseña no cumple los requisitos de seguridad.";
					}
				}
				else {
					$msg_password_error = "La contraseña actual no es correcta.";
				}
			}
			else {
				$msg_password_error = "Debe introducir una contraseña.";
			}
		}
		else {
			$msg_password_error = "Token CSRF no válido.";
		}
	}
}

if (isset($_POST['registrarFoto'])) {
	
	if (checkToken()) {
		$fotografia = $_FILES['foto']['tmp_name'];
		
		if (empty($fotografia)) {
			$msg_foto_error = "Debe subir una fotografía en formato JPEG.";
		}
		else {
			require('./lib/class.Images.php');
			$image = new Image($fotografia);
			$image->resize(240,320,'crop');
			$image->save($_SESSION['ide'], './xml/fotos_profes/', 'jpg');		
		}
	}
	else {
		$msg_foto_error = "Token CSRF no válido.";
	}
}

if (isset($_POST['registrarMostrarNombre'])) {
	if (checkToken()) {
		if (isset($_POST['mostrarNombre']) && ! empty($_POST['mostrarNombre'])) {
			$cmp_mostrarNombre = limpiarInput(trim($_POST['mostrarNombre']), 'alpha');

			switch ($cmp_mostrarNombre) {
				case 'nombreCompleto':
					mysqli_query($db_con, "UPDATE `c_profes` SET `rgpd_mostrar_nombre` = '1' WHERE `idea` = '".$_SESSION['ide']."' LIMIT 1");
					break;

				case 'nombreIniciales':
					mysqli_query($db_con, "UPDATE `c_profes` SET `rgpd_mostrar_nombre` = '0' WHERE `idea` = '".$_SESSION['ide']."' LIMIT 1");
					break;

				default:
					$msg_mostrarNombre_error = "Debe elegir entre una de las opciones.";
					break;
			}
		}
		else {
			$msg_mostrarNombre_error = "Debe elegir entre una de las opciones.";
		}
	}
	else {
		$msg_mostrarNombre_error = "Token CSRF no válido.";
	}	
}

if (isset($_POST['registrarTema'])) {
	if (checkToken()) {
		if (isset($_POST['tema']) && ! empty($_POST['tema'])) {
			$cmp_tema = limpiarInput(trim($_POST['tema']), 'alphanumericspecial');
			$cmp_tema_inverso = (isset($_POST['temaInverso']) && $_POST['temaInverso'] == 1) ? 'navbar-inverse' : 'navbar-default';

			$result = mysqli_query($db_con, "SELECT `tema`, `fondo` FROM `temas` WHERE `idea` = '".$_SESSION['ide']."' LIMIT 1");
			if (mysqli_num_rows($result)) {
				$row = mysqli_fetch_array($result);
				$tema['tema'] = $row['tema'];
				$tema['fondo'] = $row['fondo'];

				mysqli_query($db_con, "UPDATE `temas` SET `tema` = '".$cmp_tema."', `fondo` = '".$cmp_tema_inverso."' WHERE `idea` = '".$_SESSION['ide']."' LIMIT 1");
			}
			else {
				mysqli_query($db_con, "INSERT INTO `temas` (`idea`, `tema`, `fondo`) VALUES ('".$_SESSION['ide']."', '".$cmp_tema."', '".$cmp_tema_inverso."')");
			}

			$_SESSION['tema'] = $cmp_tema;
			$_SESSION['fondo'] = $cmp_tema_inverso;
			
		}
		else {
			$msg_tema_error = "Debe elegir entre uno de los temas disponibles.";
		}
	}
	else {
		$msg_tema_error = "Token CSRF no válido.";
	}
}


// Obtenemos la información del usuario
$usuario = array();
$result = mysqli_query($db_con, "SELECT `pass`, `correo`, `correo_verificado`, `telefono`, `telefono_verificado`, `rgpd_mostrar_nombre` FROM `c_profes` WHERE `idea` = '".$_SESSION['ide']."' LIMIT 1") or die (mysqli_error($db_con));
if (mysqli_num_rows($result)) {
	$row = mysqli_fetch_array($result);
	$usuario['password_hash'] = $row['pass'];
	$usuario['email'] = $row['correo'];
	$usuario['email_verificado'] = $row['correo_verificado'];
	$usuario['telefono'] = $row['telefono'];
	$usuario['telefono_verificado'] = $row['telefono_verificado'];
	$usuario['rgpd_mostrar_nombre'] = $row['rgpd_mostrar_nombre'];
}

// Obtenemos las preferencias del usuario sobre el diseño de la Intranet
$tema = array();
$result = mysqli_query($db_con, "SELECT `tema`, `fondo` FROM `temas` WHERE `idea` = '".$_SESSION['ide']."' LIMIT 1");
if (mysqli_num_rows($result)) {
	$row = mysqli_fetch_array($result);
	$tema['tema'] = $row['tema'];
	$tema['fondo'] = $row['fondo']; 
}
else {
	$tema['tema'] = "bootstrap.min.css";
	$tema['fondo'] = "navbar-default"; 
}

// Obtenemos los accesos a la Intranet
$accesos = array();
$result = mysqli_query($db_con, "SELECT id, fecha, ip, useragent FROM reg_intranet WHERE profesor = '".$_SESSION['ide']."' ORDER BY fecha DESC LIMIT 10");

while ($row = mysqli_fetch_array($result)) {
	$u_agent = getBrowser($row['useragent']);
	
	if (! isPrivateIP($row['ip'])) {
		$meta_geoip = @unserialize(file_get_contents('http://ip-api.com/php/'.$row['ip'].'?lang=es'));
		if ($meta_geoip['status'] == "fail") {
			$localizacion = $config['centro_localidad'].', Andalucía, '.'España';
			$isp = "Desconocido";
			$asn = "Desconocido";
		}
		else {
			$localizacion = $meta_geoip['city'].', '.$meta_geoip['regionName'].', '.$meta_geoip['country'];
			$isp = $meta_geoip['isp'];
			$asn = $meta_geoip['as'];
		}
		
	}
	else {
		$localizacion = $config['centro_localidad'].', Andalucía, '.'España';
		$isp = "Desconocido";
		$asn = "Desconocido";
	}

	$acceso = array(
		'id_acceso' => $row['id'],
		'fecha' => strftime('%d %b, %Y a las %H:%M:%S horas', strtotime($row['fecha'])),
		'ip' => $row['ip'],
		'localizacion' => $localizacion,
		'isp' => $isp,
		'asn' => $asn,
		'plataforma' => $u_agent['platform'],
		'plataforma_version' => $u_agent['platform_version'],
		'navegador' => $u_agent['browser_name'],
		'navegador_version' => $u_agent['browser_version']
	);

	array_push($accesos, $acceso);
}

// Generamos token CSRF
$html_token = outputToken();

include("menu.php");
?>
	<style type="text/css">
	h4.panel-title > a {
		display: block;
		text-decoration: none;
	}
	h4.panel-title > a:hover {
		text-decoration: none;
	}
	</style>
	<div class="<?php echo ($_SESSION['fondo'] == 'navbar-default') ? 'navbar-inverse' : 'navbar-default'; ?>" style="margin-top: -20px; margin-bottom: 20px;">
		<div class="container">
			<div class="page-header" style="border: 0 !important;">
				<div class="media">
					<div class="media-left">
						<?php $fotoProfesor = obtener_foto_profesor($_SESSION['ide']); ?>
						<?php if ($fotoProfesor): ?>
						<div class="img-circle" style="width: 105px; height: 105px; overflow: hidden; ">
							<img src="./xml/fotos_profes/<?php echo $fotoProfesor; ?>?aleatorio=<?php uniqid(); ?>" class="img-responsive" style="margin-top: -10px;" alt="<?php echo $_SESSION['profi']; ?>">
						</div>
						<?php else: ?>
						<i class="far fa-user-circle fa-7x"></i>
						<?php endif; ?>
					</div>
					<div class="media-body" style="padding-top: 20px; padding-left: 15px;">
						<h2 class="grey-light media-heading"><?php echo $_SESSION['profi']; ?></h2>
						<?php if ($_SESSION['dpt'] == 'Admin' || $_SESSION['dpt'] == 'Administracion' || $_SESSION['dpt'] == 'Conserjeria' || $_SESSION['dpt'] == 'Auxiliar de Conversacion' || $_SESSION['dpt'] == 'Educador' || $_SESSION['dpt'] == 'Mentor acompañante' || $_SESSION['dpt'] == 'Monitor de Interpretación de Lengua de Signos'  || $_SESSION['dpt'] == 'Servicio Técnico y/o Mantenimiento'): ?>
						<p class="text-muted"><?php echo $_SESSION['dpt']; ?></p>
						<?php else: ?>
						<p class="text-muted">Departamento de <?php echo $_SESSION['dpt']; ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container">

		<div>
		  <?php if (! isset($_SESSION['cambiar_clave']) || isset($_SESSION['cambiar_clave']) && $_SESSION['cambiar_clave'] != 1): ?>
		  <!-- Nav tabs -->
		  <ul id="informacionUsuario" class="nav nav-tabs nav-justified" role="tablist">
		    <li role="presentation" class="active"><a href="#cuenta" aria-controls="cuenta" role="tab" data-toggle="tab">Cuenta</a></li>
		    <li role="presentation"><a href="#privacidad" aria-controls="privacidad" role="tab" data-toggle="tab">Privacidad</a></li>
		    <li role="presentation"><a href="#seguridad" aria-controls="seguridad" role="tab" data-toggle="tab">Seguridad</a></li>
		    <li role="presentation"><a href="#preferencias" aria-controls="preferencias" role="tab" data-toggle="tab">Preferencias</a></li>
		  </ul>
		  <?php endif ;?>

		  <!-- Tab panes -->
		  <div class="tab-content">

		  	<!-- Panel de información de cuenta -->
		    <div role="tabpanel" class="tab-pane active" id="cuenta">

		    	<?php if (isset($_SESSION['cambiar_clave']) && $_SESSION['cambiar_clave']): ?>
		      	<h3>Lo primero es lo primero. Cambie la contraseña por una más segura.</h3>
		      	<?php else: ?>
		      	<h3>Información sobre la cuenta</h3>
		      	<?php endif; ?>

		    	<br>

		    	<div class="panel-group" id="cuentaAccordion" role="tablist" aria-multiselectable="true">
		    	  <?php if (! isset($_SESSION['cambiar_clave']) || isset($_SESSION['cambiar_clave']) && $_SESSION['cambiar_clave'] != 1): ?>
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="cuentaHeadingOne">
				      <h4 class="panel-title">
				        <a role="button" data-toggle="collapse" data-parent="#cuentaAccordion" href="#cuentaCollapseEmail" aria-expanded="false" aria-controls="cuentaCollapseEmail">
				          Correo electrónico<br>
				          <small class="text-muted">Añade o modifica la dirección de correo electrónico para recibir comunicaciones.</small>
				        </a>
				      </h4>
				    </div>
				    <div id="cuentaCollapseEmail" class="panel-collapse collapse" role="tabpanel" aria-labelledby="cuentaHeadingOne">
				      <div class="panel-body">
				      	<?php if ($esProfesor): ?>
				      	<p class="help-block">Por motivos de seguridad y en cumplimiento al derecho a la desconexión digital en el ámbito laboral regulado en la <a href="https://www.boe.es/eli/es/lo/2018/12/05/3/con#a8-10" target="_blank">Ley Orgánica 3/2018, de 5 de diciembre, de Protección de Datos y garantía de los derechos digitales (LOPDGDD)</a>, utilice el correo corporativo de la Consejería de Educación de la Junta de Andalucía <strong>edu@juntadeandalucia.es</strong> que puede solicitar en la aplicación Séneca, pantalla Utilidades, Correo corporativo. También puede usar una dirección de correo electrónico <strong><?php echo ((isset($listaDominiosPermitidosCorreos) && ! empty($listaDominiosPermitidosCorreos)) ? $listaDominiosPermitidosCorreos." o " : ""); ?>@<?php echo $_SERVER['SERVER_NAME']; ?></strong>, si el Centro se la ha proporcionado.</p>
				      	<?php endif; ?>

				      	<br>
				      	<?php if (isset($msg_email_error)): ?>
				      	<p class="text-danger"><?php echo $msg_email_error; ?></p>
				      	<?php endif; ?>

				        <form action="?tab=cuenta&pane=email" method="post" class="form-horizontal">
				        	<?php echo $html_token; ?>

				        	<div class="form-group">
				        		<label for="email" class="col-sm-3 control-label">Correo electrónico</label>
				        		<div class="col-sm-4">
				        			<input type="email" id="email" name="email" class="form-control" value="<?php echo (isset($usuario['email']) && ! empty($usuario['email'])) ? $usuario['email'] : ''; ?>">
				        		</div>
				        		<?php if (! empty($usuario['email'])): ?>
				        		<div class="col-sm-3">
				        			<?php if ($usuario['email_verificado'] == 1): ?>
				        			<p class="form-control-static text-success">Verificado</p>
				        			<?php else: ?>
				        			<p class="form-control-static text-danger">No verificado</p>
				        			<?php endif; ?>
				        		</div>
				        		<?php endif; ?>
				        	</div>

							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-10">
									<button type="submit" class="btn btn-default" name="registrarCorreo">Guardar cambios</button>
								</div>
							</div>
				        </form>

				      </div>
				    </div>
				  </div>
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="cuentaHeadingTwo">
				      <h4 class="panel-title">
				        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#cuentaAccordion" href="#cuentaCollapseTelefono" aria-expanded="false" aria-controls="cuentaCollapseTelefono">
				          Número de teléfono móvil<br>
				          <small class="text-muted">Añade un número de teléfono móvil para recibir comunicaciones SMS</small>
				        </a>
				      </h4>
				    </div>
				    <div id="cuentaCollapseTelefono" class="panel-collapse collapse" role="tabpanel" aria-labelledby="cuentaHeadingTwo">
				      <div class="panel-body">

				      	<?php if (isset($msg_telefono_error)): ?>
				      	<p class="text-danger"><?php echo $msg_telefono_error; ?></p>
				      	<?php endif; ?>

				    	<form action="?tab=cuenta&pane=telefono" method="post" class="form-horizontal">
				    		<?php echo $html_token; ?>

				        	<div class="form-group">
				        		<label for="telefono" class="col-sm-3 control-label">Teléfono móvil</label>
				        		<div class="col-sm-4">
				        			<input type="tel" id="telefono" name="telefono" class="form-control" maxlength="9" value="<?php echo (isset($usuario['telefono']) && ! empty($usuario['telefono'])) ? $usuario['telefono'] : ''; ?>">
				        		</div>
				        		<?php if (! empty($usuario['telefono'])): ?>
				        		<div class="col-sm-3">
				        			<?php if ($usuario['telefono_verificado'] == 1): ?>
				        			<p class="form-control-static text-success">Verificado</p>
				        			<?php else: ?>
				        			<p class="form-control-static text-danger">No verificado</p>
				        			<?php endif; ?>
				        		</div>
				        		<?php endif; ?>
				        	</div>

							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-10">
									<button type="submit" class="btn btn-default" name="registrarTelefono">Guardar cambios</button>
								</div>
							</div>
				        </form>

				      </div>
				    </div>
				  </div>
				  <?php endif; ?>
				  <?php if (! isset($_SESSION['session_seneca']) || (isset($_SESSION['session_seneca']) && $_SESSION['session_seneca'] != 1)): ?>
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="cuentaHeadingThree">
				      <h4 class="panel-title">
				        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#cuentaAccordion" href="#cuentaCollapsePassword" aria-expanded="false" aria-controls="cuentaCollapsePassword">
				          Cambiar contraseña<br>
				          <small class="text-muted">Elige una contraseña única para proteger tu cuenta</small>
				        </a>
				      </h4>
				    </div>
				    <div id="cuentaCollapsePassword" class="panel-collapse collapse" role="tabpanel" aria-labelledby="cuentaHeadingThree">
				      <div class="panel-body">

				      	<?php if (isset($msg_password_error)): ?>
				      	<p class="text-danger"><?php echo $msg_password_error; ?></p>
				      	<?php endif; ?>

				      	<?php if (isset($msg_password_success)): ?>
				      	<p class="text-success"><?php echo $msg_password_success; ?></p>
				      	<?php endif; ?>
				        
				        <form action="?tab=cuenta&pane=password" method="post" class="form-horizontal">
				        	<?php echo $html_token; ?>

				        	<div class="row">
				        		<div class="col-sm-7">
				        			<div class="form-group">
						        		<label for="password" class="col-sm-5 control-label">Contraseña actual</label>
						        		<div class="col-sm-7">
						        			<input type="password" id="password" name="password" class="form-control" maxlength="20" value="">
						        		</div>
						        	</div>

						        	<div class="form-group">
						        		<label for="new_password" class="col-sm-5 control-label">Nueva contraseña</label>
						        		<div class="col-sm-7">
						        			<input type="password" id="new_password" name="new_password" class="form-control"  maxlength="20" value="">
						        		</div>
						        	</div>

						        	<div class="form-group">
						        		<label for="repeat_password" class="col-sm-5 control-label">Repite la contraseña</label>
						        		<div class="col-sm-7">
						        			<input type="password" id="repeat_password" name="repeat_password" class="form-control"  maxlength="20" value="">
						        		</div>
						        	</div>
				        		</div>

				        		<div class="col-sm-5">
				        			<p>La clave debe cumplir las siguientes condiciones:</p>

				        			<ul>
										<li>Tener al menos una longitud de 8 caracteres y 20 como máximo.</li>
										<li>Contener al menos una letra, un número y un signo de puntuación o un símbolo.</li>
										<li>Los símbolos aceptados son !"#$%&'()*+,-./:;»=>?@[\]^_`{|}~</li>
										<li>Las letras acentuadas y las eñes no están admitidas.</li>
										<li>No ser similar al nombre de usuario.</li>
										<li>No ser similar a su D.N.I. o pasaporte.</li>
									</ul>
				        		</div>

				        	</div>

							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-10">
									<button type="submit" class="btn btn-default" name="registrarClave">Guardar cambios</button>
								</div>
							</div>
				        </form>

				      </div>
				    </div>
				  </div>
				  <?php endif; ?>
				  <?php if (! isset($_SESSION['cambiar_clave']) || isset($_SESSION['cambiar_clave']) && $_SESSION['cambiar_clave'] != 1): ?>
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="cuentaHeadingFour">
				      <h4 class="panel-title">
				        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#cuentaAccordion" href="#cuentaCollapseFoto" aria-expanded="false" aria-controls="cuentaCollapseFoto">
				          Cambiar foto de perfil<br>
				          <small class="text-muted">Añade o modifica la foto de perfil de tu cuenta</small>
				        </a>
				      </h4>
				    </div>
				    <div id="cuentaCollapseFoto" class="panel-collapse collapse" role="tabpanel" aria-labelledby="cuentaHeadingFour">
				      <div class="panel-body">

				      	<?php if (isset($msg_foto_error)): ?>
				      	<p class="text-danger"><?php echo $msg_foto_error; ?></p>
				      	<?php endif; ?>

				    	<form action="?tab=cuenta&pane=foto" method="post" enctype="multipart/form-data" class="form-horizontal">
				    		<?php echo $html_token; ?>

				        	<div class="row">
				        		<div class="col-sm-7">

				        			<div class="text-center">
					        			<?php $fotoProfesor = obtener_foto_profesor($_SESSION['ide']); ?>
										<?php if ($fotoProfesor): ?>
										<div class="img-circle" style="width: 200px; height: 200px; overflow: hidden; margin: 0 auto;">
											<img src="./xml/fotos_profes/<?php echo $fotoProfesor; ?>" class="img-responsive" style="margin-top: -20px;" alt="<?php echo $_SESSION['profi']; ?>">
										</div>
										<?php else: ?>
										<i class="far fa-user-circle" style="font-size: 14em;"></i>
										<?php endif; ?>
									</div>

									<br>

									<div class="form-group">
										<label for="foto" class="col-sm-5 control-label">Subir una foto (formato JPEG)</label>
										<div class="col-sm-7">
											<input type="file" id="foto" name="foto" accept="image/jpeg">
											<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
										</div>
									</div>

				        		</div>

				        		<div class="col-sm-5">
				        			<p>La foto debe cumplir la norma especificada:</p>

				        			<ul>
										<li>Tener el fondo de un único color, liso y claro.</li>
										<li>La foto ha de ser reciente y tener menos de 6 meses de antigüedad.</li>
										<li>Foto tipo carnet, la imagen no puede estar inclinada, tiene que mostrar la cara claramente de frente.</li>
										<li>Fotografía de cerca que incluya la cabeza y parte superior de los hombros, la cara ocuparía un 70-80% de la fotografía.</li>
										<li>Fotografía perfectamente enfocada y clara.</li>
									</ul>
				        		</div>

				        	</div>

							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-10">
									<button type="submit" class="btn btn-default" name="registrarFoto">Guardar cambios</button>
								</div>
							</div>
				        </form>

				      </div>
				    </div>
				  </div>
				  <?php endif; ?>
				</div>

		    </div><!-- /.tab-pane -->

		    <!-- Panel de privacidad -->
		    <div role="tabpanel" class="tab-pane" id="privacidad">

		    	<h3>Cómo ven los demás tu información</h3>

		    	<br>
		    	
		    	<div class="panel-group" id="privacidadAccordion" role="tablist" aria-multiselectable="true">
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="privacidadHeadingOne">
				      <h4 class="panel-title">
				        <a role="button" data-toggle="collapse" data-parent="#privacidadAccordion" href="#privacidadCollapseMostrarNombre" aria-expanded="false" aria-controls="privacidadCollapseMostrarNombre">
				          Quién puede ver mi nombre y apellidos<br>
				          <small class="text-muted">Elige cómo quieres que se muestre tu nombre y apellidos en la página web del Centro.</small>
				        </a>
				      </h4>
				    </div>
				    <div id="privacidadCollapseMostrarNombre" class="panel-collapse collapse" role="tabpanel" aria-labelledby="privacidadHeadingOne">
				      <div class="panel-body">
				      	<p class="help-block">Elige cómo quieres que se muestre tu nombre y apellidos en la página web del Centro. En la Intranet siempre se verá tu nombre completo.</p>

				      	<?php if (isset($msg_mostrarNombre_error)): ?>
				      	<p class="text-danger"><?php echo $msg_mostrarNombre_error; ?></p>
				      	<?php endif; ?>

				        <form action="?tab=privacidad&pane=mostrarNombre" method="post" class="form-horizontal">
				        	<?php echo $html_token; ?>

				        	<div class="radio col-sm-offset-3">
							  <label>
							    <input type="radio" name="mostrarNombre" id="mostrarNombre1" value="nombreCompleto"<?php echo (! isset($usuario['rgpd_mostrar_nombre']) || isset($usuario['rgpd_mostrar_nombre']) && $usuario['rgpd_mostrar_nombre'] == 1) ? ' checked' : ''; ?>>
							    <?php echo $_SESSION['profi']; ?> <span class="text-muted">(nombre completo)</span>
							  </label>
							</div>
							<div class="radio col-sm-offset-3">
							  <label>
							    <input type="radio" name="mostrarNombre" id="mostrarNombre2" value="nombreIniciales"<?php echo (isset($usuario['rgpd_mostrar_nombre']) && $usuario['rgpd_mostrar_nombre'] == 0) ? ' checked' : ''; ?>>
							    <?php echo nombreIniciales($_SESSION['profi']); ?> <span class="text-muted">(iniciales)</span>
							  </label>
							</div>

							<br>

							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-10">
									<button type="submit" class="btn btn-default" name="registrarMostrarNombre">Guardar cambios</button>
								</div>
							</div>
				        </form>
				      </div>
				    </div>
				  </div>

				</div>

		    </div><!-- /.tab-panel -->

		    <!-- Panel de seguridad -->
		    <div role="tabpanel" class="tab-pane" id="seguridad">
		    	
		    	<h3>Dónde has iniciado sesión</h3>

		    	<br>
		    	
		    	<div class="panel-group" id="seguridadAccordion" role="tablist" aria-multiselectable="true">
		    	  <?php $i = 1; ?>
		    	  <?php foreach ($accesos as $acceso): ?>
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="seguridadHeading<?php echo $i; ?>">
				      <h4 class="panel-title">
				        <a role="button" data-toggle="collapse" data-parent="#seguridadAccordion" href="#seguridadCollapse<?php echo $i; ?>" aria-expanded="false" aria-controls="seguridadCollapse<?php echo $i; ?>">
				          <?php echo $acceso['fecha'] ?> <?php echo ($acceso['id_acceso'] == $_SESSION['id_pag']) ? '<span class="label label-success">Sesión actual</span>' : ''; ?><br>
				          <small class="text-muted"><?php echo $acceso['navegador'].' '.$acceso['navegador_version']; ?> en <?php echo ltrim($acceso['plataforma'].' '.$acceso['plataforma_version']); ?></small>
				        </a>
				      </h4>
				    </div>
				    <div id="seguridadCollapse<?php echo $i; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="seguridadHeading<?php echo $i; ?>">
				      <div class="panel-body">
				   		<div class="">
				   			<dl class="dl-horizontal">
							  <dt>Localización</dt>
							  <dd><?php echo $acceso['localizacion']; ?><br>(Ubicación basada en localización IP)</dd>
							</dl>

							<dl class="dl-horizontal">
							  <dt>Navegador</dt>
							  <dd><?php echo ltrim($acceso['navegador'].' '.$acceso['navegador_version']); ?></dd>
							</dl>

							<dl class="dl-horizontal">
							  <dt>Plataforma</dt>
							  <dd><?php echo ltrim($acceso['plataforma'].' '.$acceso['plataforma_version']); ?></dd>
							</dl>

							<dl class="dl-horizontal">
							  <dt>Dirección IP<?php echo (strlen($acceso['ip']) < 16) ? 'v4' : 'v6'; ?></dt>
							  <dd><?php echo $acceso['ip']; ?></dd>
							</dl>

							<dl class="dl-horizontal">
							  <dt>Proveedor</dt>
							  <dd><?php echo $acceso['isp'].' ('.$acceso['asn'].')'; ?></dd>
							</dl>
				   		</div>
				      </div>
				    </div>
				  </div>
				  <?php $i++; ?>
				  <?php endforeach; ?>
				</div>

		    </div><!-- /.tab-pane -->

		    <!-- Panel preferencias de la Intranet -->
		    <div role="tabpanel" class="tab-pane" id="preferencias">

		    	<h3>Personaliza la Intranet</h3>

		    	<br>

		    	<div class="panel-group" id="preferenciasAccordion" role="tablist" aria-multiselectable="true">
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="preferenciasHeadingOne">
				      <h4 class="panel-title">
				        <a role="button" data-toggle="collapse" data-parent="#preferenciasAccordion" href="#preferenciasCollapseTema" aria-expanded="false" aria-controls="preferenciasCollapseTema">
				          Tema de la Intranet<br>
				          <small class="text-muted">Cambia el aspecto que presentan las páginas de la Intranet</small>
				        </a>
				      </h4>
				    </div>
				    <div id="preferenciasCollapseTema" class="panel-collapse collapse" role="tabpanel" aria-labelledby="preferenciasHeadingOne">
				      <div class="panel-body">
				      	
				      	<p class="help-block">El aspecto que presentan las páginas de la Intranet puede ser modificado mediante temas. La aplicación contiene un conjunto de temas que modifican los distintos elementos que constituyen su presentación visual: el tipo de letra, los fondos, botones, etiquetas, colores de los distintos elementos, barra de navegación, etc. </p>

				    	<br>

				    	<form action="?tab=preferencias&pane=tema" method="post" class="form-horizontal">
				    		<?php echo $html_token; ?>

				    		<div class="row">
				    		
					    		<div class="col-sm-7">
					    			<br>

					    			<div class="form-group">
										<label for="tema" class="col-sm-5 control-label">Seleccione tema</label>
										<div class="col-sm-6">
											<select class="form-control" id="tema" name="tema">
												<optgroup label="Temas de la aplicación">
													<?php $d = dir("./css/temas/"); ?>
													<?php while (false !== ($entry = $d->read())): ?>
													<?php if (stristr($entry,".css")==TRUE and !($entry=="bootstrap.min.css")): ?>
													<?php $exp_stylesheet_name = explode('-', $entry); ?>
													<?php $stylesheet_name = ucfirst(trim(rtrim($exp_stylesheet_name[1], '.css'))); ?>
													<?php if ($stylesheet_name == 'Standard') $stylesheet_name = 'Bootstrap'; ?>
													<option value="temas/<?php echo $entry; ?>" <?php echo (stristr($tema['tema'], $entry)==TRUE) ? 'selected' : ''; ?>><?php echo $stylesheet_name; ?></option>
													<?php endif; ?>
													<?php endwhile; ?>
													<?php $d->close(); ?>
												</optgroup>
												<optgroup label="Tema por defecto">
													<option value="bootstrap.min.css" <?php echo ($tema['tema']=="bootstrap.min.css") ? 'selected' : ''; ?>>Flaty</option>
												</optgroup>
											</select>
										</div>
									</div>

									<div class="col-sm-offset-5">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="temaInverso" value="1"<?php echo (isset($tema['fondo']) && $tema['fondo'] == "navbar-inverse") ? ' checked' : '' ?>>
												Color secundario como fondo del menú superior.
											</label>
										</div>

									</div>
								
								</div>

					    		<div class="col-sm-5">

									<img id="theme-preview" class="img-responsive" src="./img/temas/intranet.png" alt="">

					    		</div>

					    	</div>
					    	
							<br>

							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-10">
									<button type="submit" class="btn btn-default" name="registrarTema">Guardar cambios</button>
								</div>
							</div>

						</form>

				      </div>
				    </div>
				  </div>

				</div>

		    </div><!-- /.tab-pane -->
		  </div>

		</div>
	</div>

<?php include("pie.php"); ?>

<script>
	function getQueryVariable(variable)
	{
	   var query = window.location.search.substring(1);
	   var vars = query.split("&");
	   for (var i=0;i<vars.length;i++) {
	           var pair = vars[i].split("=");
	           if(pair[0] == variable){return pair[1];}
	   }
	   return(false);
	}

	$(document).ready(function() {
		// Selección de pestaña
		var tab = getQueryVariable("tab");
		var pane = getQueryVariable("pane");

		if (tab) $('#informacionUsuario a[href="#' + tab + '"]').tab('show');
		if (pane) {
			switch (pane) {
				case 'email' : $('#cuentaCollapseEmail').collapse('show'); break;
				case 'telefono' : $('#cuentaCollapseTelefono').collapse('show'); break;
				case 'password' : $('#cuentaCollapsePassword').collapse('show'); break;
				case 'foto' : $('#cuentaCollapseFoto').collapse('show'); break;
				case 'mostrarNombre' : $('#privacidadCollapseMostrarNombre').collapse('show'); break;
				case 'tema' : $('#preferenciasCollapseTema').collapse('show'); break;
			}
		}

		// Selección de tema
		var tema = $('#tema').val();
		if (tema != 'bootstrap.min.css') {
			var exp_tema = tema.split('-');
			var name = exp_tema[1].replace('.css','.png');
		}
		else {
			var name = 'intranet.png';
		}

		$('#theme-preview').attr('src','./img/temas/' + name);
	});

	$('#tema').on({
	    'change': function(){
	    		var tema = $('#tema').val();
					if (tema != 'bootstrap.min.css') {
		    		var exp_tema = tema.split('-');
		    		var name = exp_tema[1].replace('.css','.png');
		    	}
		    	else {
		    		var name = 'intranet.png';
		    	}

	        $('#theme-preview').attr('src','./img/temas/' + name);
	    }
	});
	</script>

</body>
</html>
