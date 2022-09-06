<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed');

if (strlen($id)>0 and $grave == "muy grave" and stristr($_SESSION['cargo'],'1') == TRUE) {
	$confirm = mysqli_query($db_con,"select confirmado from Fechoria where confirmado = '1' and id = '$id'");
	if (mysqli_num_rows($confirm)>0) {
		$confirma_db = '1';
	}
}

// Control de errores en archivo adjunto
if (! isset($id) && ! empty($_FILES['adjunto']['tmp_name'])) {
	$_adjuntoError = 0;
	$_nombreAdjunto = "";
	$_adjunto = $_FILES['adjunto'];
	$_maxfilesize = php_directive_value_to_bytes('upload_max_filesize');

	if (strpos("image", $_adjunto['type']) !== 0 && $_adjunto['type'] != "application/pdf") {
		echo '
			<div align="center">
				<div class="alert alert-danger alert-block fade in">
	      	<button type="button" class="close" data-dismiss="alert">&times;</button>
	        El archivo adjunto debe tener formato de imagen o PDF.
	      </div>
			</div>';
	}
	elseif ($_adjunto['size'] > $_maxfilesize) {
		echo '
			<div align="center">
				<div class="alert alert-danger alert-block fade in">
	      	<button type="button" class="close" data-dismiss="alert">&times;</button>
	        El archivo adjunto debe tener un tamaño inferior a '.(ini_get('upload_max_filesize')).'b.
	      </div>
			</div>';
	}
	elseif ($_adjunto['error'] != 0) {
		echo '
			<div align="center">
				<div class="alert alert-danger alert-block fade in">
	      	<button type="button" class="close" data-dismiss="alert">&times;</button>
	        El archivo adjunto no ha podido ser subido al servidor.
	      </div>
			</div>';
	}
	else {
		$dir_subida = "./adjuntos/"; // Añadir / al final

		if (file_exists($dir_subida)) {
			$hash_file = hash_file('md5', $_adjunto['tmp_name']);
			$dir_archivo = $dir_subida . $hash_file . '_' . basename($_adjunto['name']);

			if (! move_uploaded_file($_adjunto['tmp_name'], $dir_archivo)) {
				echo '
					<div align="center">
						<div class="alert alert-danger alert-block fade in">
			      	<button type="button" class="close" data-dismiss="alert">&times;</button>
			        Ha ocurrido un error al adjuntar el archivo.
			      </div>
					</div>';
			}
			else {
				$_nombreAdjunto = ltrim($dir_archivo, $dir_subida);
			}
		}
		else {
			echo '
				<div align="center">
					<div class="alert alert-danger alert-block fade in">
		      	<button type="button" class="close" data-dismiss="alert">&times;</button>
		        El archivo adjunto no ha podido ser subido al servidor.
		      </div>
				</div>';
		}
	}
}
else {

	$result_adjunto = mysqli_query($db_con, "SELECT `adjunto` FROM `Fechoria` WHERE `id` = $id LIMIT 1");
	$row_adjunto = mysqli_fetch_array($result_adjunto);

	if (! empty($row_adjunto['adjunto'])) {
		$_nombreAdjunto = $row_adjunto['adjunto'];
	}
	else {
		$_nombreAdjunto = "";
	}
}

// Control de errores
if (! $notas or ! $grave or ! $_POST['nombre'] or ! $asunto or ! $fecha or ! $informa or $fecha=='0000-00-00') {
	echo '<div align="center"><div class="alert alert-danger alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<legend>ATENCI&Oacute;N:</legend>
            No has introducido datos en alguno de los campos, y <strong>todos son obligatorios</strong>.<br> Vuelve atr&aacute;s, rellena los campos vac&iacute;os e int&eacute;ntalo de nuevo.
          </div></div>';
}
elseif (strlen ($notas) < '50' ) {
	echo '<div align="center"><div class="alert alert-danger alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<legend>ATENCI&Oacute;N:</legend>
            La descripci&oacute;n de lo sucedido es demasiado breve. Es necesario que proporciones m&aacute;s detalles de lo ocurrido para que Jefatura de Estudios y Tutor puedan hacerse una idea precisa del suceso.<br />Vuelve atr&aacute;s e int&eacute;ntalo de nuevo.
          </div></div>';
}
elseif (isset($_POST['nombre'])) {

if (is_array($nombre)) {
	$num_a = count($_POST['nombre']);
}
else{
	$num_a=1;
}

// Actualizar datos
if ($_POST['submit2']) {
}
else{
	$dia0 = explode ( "-", $fecha );
	$fecha3 = "$dia0[2]-$dia0[1]-$dia0[0]";
}

$z=0;
for ($i=0;$i<$num_a;$i++){
	if ($num_a==1 and !is_array($nombre)) {
		$claveal = $nombre;
	}
	else{
	$claveal = $nombre[$i];
	}

	$sms_enviado="";
	if ($confirmado=="") {
		$confirmado="0";
	}
	$notas = trim($notas);
	$ya_esta = mysqli_query($db_con, "select claveal, fecha, grave, asunto, notas, informa, confirmado from Fechoria where claveal = '$claveal' and fecha = '$fecha3' and grave = '$grave' and asunto = '$asunto' and informa = '$informa' and notas = '$notas' and confirmado='$confirmado'");

	if (mysqli_num_rows($ya_esta)>0) {
		echo '<br /><div align="center"><div class="alert alert-warning alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <legend>Atenci&oacute;n:</legend>
            Ya hay un problema de convivencia registrado que contiene los mismos datos que est&aacute;s enviando, y no queremos repetirlos... .
          </div></div><br />';
	}
	else{

		$alumno = mysqli_query($db_con, "SELECT distinct alma.APELLIDOS, alma.NOMBRE, alma.unidad, alma.matriculas, alma.CLAVEAL, alma.TELEFONO, alma.TELEFONOURGENCIA FROM alma WHERE alma.claveal = '$claveal'" );
		$rowa = mysqli_fetch_array ( $alumno );
		$apellidos = trim ( $rowa [0] );
		$nombre_alum = trim ( $rowa [1] );
		$unidad = trim ( $rowa [2] );
		$tfno = trim ( $rowa [5] );
		$tfno_u = trim ( $rowa [6] );
		$message = "Su hijo/a ha cometido una falta contra las normas de convivencia del Centro. Hable con su hijo/a y, ante cualquier duda, consulte en http://".$config['dominio'];

		// Comprobamos si se envía notificación para problemas leves
		if ($grave == "leve") {
			if (isset($config['convivencia']['notificaciones_padres_leves']) && $config['convivencia']['notificaciones_padres_leves'] == 1) {
				$enviar_notificacion = 1;
			}
			else {
				$enviar_notificacion = 0;
			}
		}
		else { // Si el problema es grave o muy grave, envía SMS o email
			$enviar_notificacion = 1;
		}

		// SMS

		$ya_sms = mysqli_query($db_con, "select * from sms where profesor = '".$_SESSION['profi']."' and date(fecha)='$fecha3' and mensaje like '%falta contra las normas%' and (telefono = '$tfno' or telefono = '$tfno_u')");
		if (mysqli_num_rows($ya_sms)>0) {
			$sms_ya = 1;
			}
		else{
			$sms_ya = 0;
			}

		if ($config['mod_sms'] && $sms_ya == 0 && (! isset($config['convivencia']['notificaciones_padres']) || (isset($config['convivencia']['notificaciones_padres']) && $config['convivencia']['notificaciones_padres']))) {

			$hora_f = date ( "G" );

			if (($enviar_notificacion == 1) and (substr ( $tfno, 0, 1 ) == "6" or substr ( $tfno, 0, 1 ) == "7" or substr ( $tfno_u, 0, 1 ) == "6" or substr ( $tfno_u, 0, 1 ) == "7") and $hora_f > '8' and $hora_f < '17') {
				$sms_n = mysqli_query($db_con, "select max(id) from sms" );
				$n_sms = mysqli_fetch_array ( $sms_n );
				$extid = $n_sms [0] + 1;

				if (substr ( $tfno, 0, 1 ) == "6" or substr ( $tfno, 0, 1 ) == "7") {
					$mobile = $tfno;
				} else {
					$mobile = $tfno_u;
				}

				if(strlen($mobile) == 9) {

					// ENVIO DE SMS
					$auth = smsLogin($config['mod_sms_user'], $config['mod_sms_pass']);

					$smsSent = sendSMS($auth, array(
					    "message" => $message,
					    "message_type" => MESSAGE_HIGH_QUALITY,
					    "returnCredits" => false,
					    "recipient" => array("+34".$mobile),
					    "sender" => $config['mod_sms_id']
					));

					if (($grave == "muy grave" and $_POST['confirmado']!="1") or $confirma_db=='1') {
						// No debería haber condiciones vacías
					}
					else{
						if ($smsSent->result == "OK") {
							$sms_enviado=1;
			   				mysqli_query($db_con, "insert into sms (fecha,telefono,mensaje,profesor) values (now(),'$mobile','$message','$informa')");

			   				// Registro de Tutoría
							$fecha2 = date ( 'Y-m-d' );
							$observaciones = $message;
							$accion = "Envío de SMS";
							$causa = "Problemas de convivencia";
							mysqli_query($db_con, "insert into tutoria (apellidos, nombre, tutor,unidad,observaciones,causa,accion,fecha, claveal) values ('" . $apellidos . "','" . $nombre_alum . "','" . $informa . "','" . $unidad ."','" . $observaciones . "','" . $causa . "','" . $accion . "','" . $fecha2 . "','" . $claveal . "')" );
						}

					}
				}
				else {
					echo "
					<div class=\"alert alert-error\">
						<strong>Error:</strong> No se pudo enviar el SMS al teléfono (+34) ".$mobile.". Corrija la información de contacto del alumno/a en Séneca e importe los datos nuevamente.
					</div>
					<br>";
				}
			}
		}

		// FIN SMS

		// Envío de Email
		if (($enviar_notificacion == 1) && (! isset($config['convivencia']['notificaciones_padres']) || (isset($config['convivencia']['notificaciones_padres']) && $config['convivencia']['notificaciones_padres']))) {
		 $cor_control = mysqli_query($db_con,"select correo from control where claveal='$claveal'");
		 $cor_alma = mysqli_query($db_con,"select correo from alma where claveal='$claveal'");
		 if(mysqli_num_rows($cor_alma)>0){
		 	$correo1=mysqli_fetch_array($cor_alma);
		 	$correo = $correo1[0];
		 }
		 elseif(mysqli_num_rows($cor_control)>0){
		 	$correo2=mysqli_fetch_array($cor_control);
		 	$correo = $correo2[0];
		 }
		 if (strlen($correo)>0 and $sms_enviado != 1) {

		 	 require_once(INTRANET_DIRECTORY."/lib/phpmailer/PHPMailerAutoload.php");
		 	 $mail = new PHPMailer();
		 	 if (isset($config['email_smtp']['isSMTP']) && $config['email_smtp']['isSMTP']) {
				$mail->isSMTP();
				$mail->Host = $config['email_smtp']['hostname'];
				$mail->SMTPAuth = $config['email_smtp']['smtp_auth'];
				$mail->Port = $config['email_smtp']['port'];
				$mail->SMTPSecure = $config['email_smtp']['smtp_secure'];

				$mail->Username = $config['email_smtp']['username'];
				$mail->Password = $config['email_smtp']['password'];

				$mail->setFrom($config['email_smtp']['username'], utf8_decode($config['centro_denominacion']));
			 }
			 else {
				$mail->Host = "localhost";
				$mail->setFrom('no-reply@'.$config['dominio'], utf8_decode($config['centro_denominacion']));
			 }
		 	 $mail->IsHTML(true);

		 	 $message = file_get_contents(INTRANET_DIRECTORY.'/lib/mail_template/index.htm');
		 	 $message = str_replace('{{dominio}}', $config['dominio'], $message);
		 	 $message = str_replace('{{centro_denominacion}}', $config['centro_denominacion'], $message);
		 	 $message = str_replace('{{centro_codigo}}', $config['centro_codigo'], $message);
		 	 $message = str_replace('{{centro_direccion}}', $config['centro_direccion'], $message);
		 	 $message = str_replace('{{centro_codpostal}}', $config['centro_codpostal'], $message);
		 	 $message = str_replace('{{centro_localidad}}', $config['centro_localidad'], $message);
		 	 $message = str_replace('{{centro_provincia}}', $config['centro_provincia'], $message);
		 	 $message = str_replace('{{centro_telefono}}', $config['centro_telefono'], $message);
		 	 $message = str_replace('{{centro_fax}}', $config['centro_fax'], $message);
		 	 $message = str_replace('{{centro_email}}', $config['centro_email'], $message);
		 	 $message = str_replace('{{titulo}}', 'Comunicación de problemas de convivencia', $message);
		 	 $message = str_replace('{{contenido}}', 'Le comunicamos que, con fecha '.$fecha.', su hijo/a ha cometido una falta '.$grave.' contra las normas de convivencia del Centro. El tipo de falta es el siguiente: '.$asunto.'.<br>Le recordamos que puede conseguir información más detallada en la página de Alumnado de nuestra web https://'.$config['dominio'].', o bien contactando con Jefatura de Estudios del Centro.<br><br><hr>Este correo es informativo. Por favor, no responda a esta dirección de correo.', $message);
			 $message = str_replace('{{autor}}', 'Jefatura de estudios', $message);

		 	 $mail->msgHTML(utf8_decode($message));
		 	 $mail->Subject = utf8_decode('Comunicación de problemas de convivencia');
		 	 $mail->AltBody = utf8_decode('Le comunicamos que, con fecha '.$fecha.', su hijo/a ha cometido una falta '.$grave.' contra las normas de convivencia del Centro. El tipo de falta es el siguiente: '.$asunto.'.<br>Le recordamos que puede conseguir información más detallada en la página de Alumando de nuestra web https://'.$config['dominio'].', o bien contactando con Jefatura de Estudios del Centro.<br><br><hr>Este correo es informativo. Por favor, no responder a esta dirección de correo.');

		 	 $mail->AddAddress($correo, $nombre_alumno);
		 	 if (($grave == "muy grave" and $_POST['confirmado']!="1") or $confirma_db=='1') {
				}
				else{
					$mail->Send();
					// Registro de Tutoría
					$fecha2 = date ( 'Y-m-d' );
					$observaciones = $message;
					$accion = "Env&iacute;o de SMS";
					$causa = "Problemas de convivencia";
					mysqli_query($db_con, "insert into tutoria (apellidos, nombre, tutor,unidad,observaciones,causa,accion,fecha, claveal) values ('" . $apellidos . "','" . $nombre_alum . "','" . $informa . "','" . $unidad ."','" . $observaciones . "','" . $causa . "','" . $accion . "','" . $fecha2 . "','" . $claveal . "')" );
				}

		 	}
		 	// Fin Correo
		 }

	$dia = explode ( "-", $fecha );
	$fecha2 = "$dia[2]-$dia[1]-$dia[0]";

	if ($_POST['submit2'] and !($grave == "muy grave"  and $_POST['confirmado']=="1" and $confirma_db != 1 and isset($id))) {
		mysqli_query($db_con, "update Fechoria set claveal='$nombre', asunto = '$asunto', notas = '$notas', grave = '$grave', medida = '$medida', expulsionaula = '$expulsionaula', informa='$informa', adjunto='$_nombreAdjunto' where id = '$id'");
	echo '<br /><div align="center"><div class="alert alert-success alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            Los datos se han actualizado correctamente.
          </div></div><br />';
	}
	elseif ($grave == "muy grave" and $_POST['submit1']=="Actualizar datos") {
		mysqli_query($db_con, "update Fechoria set claveal='$nombre', fecha='$fecha', asunto = '$asunto', notas = '$notas', grave = '$grave', medida = '$medida', expulsionaula = '$expulsionaula', informa='$informa', adjunto='$_nombreAdjunto', confirmado='1' where id = '$id'");
		echo '<br /><div align="center"><div class="alert alert-success alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            Los datos se han actualizado correctamente.
          </div></div><br />';
	}
	else{
		$query = "insert into Fechoria (CLAVEAL,FECHA,ASUNTO,NOTAS,INFORMA,grave,medida,expulsionaula,confirmado,adjunto) values ('" . $claveal . "','" . $fecha2 . "','" . mysqli_real_escape_string($db_con, $asunto) . "','" . mysqli_real_escape_string($db_con, $notas) . "','" . $informa . "','" . $grave . "','" . $medida . "','" . $expulsionaula . "','" . $confirmado . "', '".$_nombreAdjunto."')";
		  //echo $query."<br>";
		 $inserta = mysqli_query($db_con, $query) or die (mysqli_error($db_con));
		 if ($inserta) {
		 	$z++;
		 	}
		}
	}
}

if (! isset($id) && ! $id) {
	unset ($unidad);
	unset($nombre);
	unset ($id);
	unset ($claveal);
}
if ($z>0 and !($_POST['confirmado']=="1" and $confirma_db != 1)) {
	echo '<br /><div align="center"><div class="alert alert-success alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            Se han registrado correctamente los Problemas de Convivencia de '.$z.' alumnos.
          </div></div><br />';
	}
}
?>
