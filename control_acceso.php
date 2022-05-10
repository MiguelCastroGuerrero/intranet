<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed');

if ($config['mod_notificaciones']) {

	if (date('G')>='9' and date('G')<='11') {

	$hoy = date('Y-m-d');

	$acc = mysqli_query($db_con,"SELECT fecha FROM acceso_dias WHERE DATE(fecha)='$hoy'");

	if(! mysqli_num_rows($acc)) {

		// Restricciones de fecha: evitamos vacaciones y festivos.

		$fiesta="";
		$suma_dias="";

		$fecha_actual = strtotime($hoy);
		$ayer = strtotime($hoy)-86400;
		$antesdeayer = strtotime($hoy)-172800;
		$uno_antesdeayer = strtotime($hoy)-259200;

		// Comenzamos 10 días después del comienzo de curso para dar tiempo a que los Centros se organicen.
		$fecha_inicio_curso = strtotime($config['curso_inicio'])+36000;
		$fecha_fin_curso = strtotime($config['curso_fin']);

		//$fecha_fin_curso = "1491816800";

		if ($fecha_actual > $fecha_inicio_curso and $fecha_actual < $fecha_fin_curso) {

		//Si lunes, $suma_dias = 1;
		//Si sábado o domingo $fiesta=1;

		if (date('N')>5) {
			$fiesta = 1;
		}
		elseif (date('N')==1) {
			$suma_dias = 1;
			$dias_faltas+=3;
		}
		elseif (date('N')==2) {
			$dias_faltas+=4;
		}
		elseif (date('N')>2 AND date('N')<6) {
			$dias_faltas+=2;
		}

		// Fiestas y Vacaciones

		$fest = mysqli_query($db_con,"SELECT DISTINCT fecha, nombre FROM festivos");
		while ($fstv = mysqli_fetch_array($fest)) {

			$festivo = strtotime($fstv[0]);
			$nombre_fiesta = $fstv[1];

			if ($fecha_actual==$festivo) {
				$fiesta = 1;
				$suma_dias = 1;
			}
			elseif ($ayer==$festivo) {
				$fiesta = 1;
				$dias_faltas++;
			}
			elseif ($antesdeayer==$festivo and (stristr($nombre_fiesta, "Semana Blanca")==TRUE OR stristr($fnombre_fiesta, "Semana Santa")==TRUE OR stristr($nombre_fiesta, "Navidad")==TRUE)) {
				$fiesta2 = 1;
				$dias_faltas++;
			}
			elseif ($uno_antesdeayer==$festivo and (stristr($nombre_fiesta, "Semana Blanca")==TRUE OR stristr($nombre_fiesta, "Semana Santa")==TRUE OR stristr($nombre_fiesta, "Navidad")==TRUE)) {
				$fiesta2 = 1;
				$dias_faltas++;
			}
		}

		if ($fiesta != 1 and $fiesta2 != 1) {

			// Mensaje a Profesores que llevan más de 5 días sin entrar en la intranet

			$result = mysqli_query($db_con, "SELECT DISTINCT profesor, MAX(DATE(fecha)) AS ultima, DATEDIFF('".date('Y-m-d')."', MAX(fecha)) AS numdias FROM reg_intranet WHERE profesor IN (SELECT idea FROM departamentos where departamento not like 'Admin' and departamento not like 'Administracion' and departamento not like 'Conserjeria') and idea not in (select distinct profesor from acceso where profesor='$profe_ultima' and fecha='$fecha' and clase='1') GROUP BY profesor HAVING numdias > 5 ORDER BY `numdias` DESC");

			while ($row = mysqli_fetch_array($result)) {
				$profe_ultima = $row['profesor'];
				$ultima = $row['ultima'];
				$dias = $row['numdias'];
				if ($dias > 4) {
					$repite = mysqli_query($db_con,"select distinct profesor from acceso where profesor='$profe_ultima' and fecha='$hoy' and clase='1' and observaciones='$ultima'");
					if (!mysqli_num_rows($repite)>0) {
					$num++;
					mysqli_query($db_con,"INSERT INTO acceso (profesor, fecha, clase, observaciones) VALUES ('$profe_ultima', '$hoy', '1', '$ultima')");
					}
				}
			}
			
			// Mensaje a Profesores que llevan más de 2 dias sin poner faltas

			if ($config['mod_asistencia']==1 && isset($config['mod_notificaciones_asistencia']) && $config['mod_notificaciones_asistencia'] == 1) {

			$result = mysqli_query($db_con, "SELECT DISTINCT FALTAS.profesor, MAX(DATE(fecha)) AS ultima, DATEDIFF('$hoy', MAX(fecha)) AS numdias FROM FALTAS WHERE FALTAS.profesor IN (SELECT idprofesor FROM profesores_seneca, departamentos where profesores_seneca.nomprofesor = departamentos.nombre AND departamentos.departamento not like 'Admin' and departamento not like 'Administracion' and departamento not like 'Conserjeria' and departamentos.cargo not like '%1%') and FALTAS. profesor in (select distinct c_prof from horw_faltas) GROUP BY profesor HAVING numdias >'$dias_faltas' ORDER BY `numdias` DESC");

				while ($row = mysqli_fetch_array($result)) {
					$profe_horw = mysqli_query($db_con,"select distinct c_prof from horw_faltas where c_prof = '".$row['profesor']."'");
					if (mysqli_num_rows($profe_horw)>0) {
						$profe_ya = mysqli_query($db_con,"select idea from departamentos where nombre like (select nomprofesor from profesores_seneca where idprofesor = '".$row['profesor']."')");
						$profe_ya_q = mysqli_fetch_array($profe_ya);
						$profe_ultima = $profe_ya_q['idea'];
						
						$ultima = $row['ultima'];
						$dias = $row['numdias'];
							
						if ($dias > 2) {
							$ayer_f = date("Y-m-d",strtotime($hoy."- 1 day")); 
							$antesdeayer_f = date("Y-m-d",strtotime($hoy."- 2 day"));

							$repite = mysqli_query($db_con,"select distinct profesor from acceso where profesor='$profe_ultima' and (fecha='$hoy' OR fecha = '$ayer_f' OR fecha = '$antesdeayer_f') and clase='6'");
							if (!mysqli_num_rows($repite)>0) {
							$num++;
							mysqli_query($db_con,"INSERT INTO acceso (profesor, fecha, clase, observaciones) VALUES ('$profe_ultima', '$hoy', '6', '$ultima')");
						}
					}
				}
			}
		}
	}


	if ($fiesta != 1) {

	// Mensajes sin leer de los profesores

	mysqli_query($db_con,"CREATE TABLE mens_temp SELECT * FROM mens_profes WHERE recibidoprofe='0' AND profesor IN (SELECT idea FROM departamentos) LIMIT 3000");

	$result = mysqli_query($db_con, "SELECT DISTINCT idea FROM departamentos ORDER BY idea");

		while ($row = mysqli_fetch_array($result)) {
		$profe_mens = $row[0];
		$result2 = mysqli_query($db_con, "SELECT * FROM mens_temp WHERE profesor = '$profe_mens' AND recibidoprofe='0'");

			$sin_leer = mysqli_num_rows($result2);
			if ($sin_leer > 25) {
				$num++;
				mysqli_query($db_con,"INSERT INTO acceso (profesor, fecha, clase, observaciones) VALUES ('$profe_mens', '$hoy', '2', '$sin_leer')");
				}
			}
		}
	}
	mysqli_query($db_con,"drop table mens_temp");


	// INFORMES DE TAREAS A PUNTO DE CUMPLIR

	$grupo = "";
	$id = "";
	$claveal = "";
	$profeso = "";
	$codasi = "";
	$curso = "";
	$asignatura = "";
	$idea = "";

	$fchs = mysqli_query($db_con,"SELECT * FROM tareas_alumnos WHERE DATE(fecha) = '$hoy'");
	while ($al_fecha = mysqli_fetch_array($fchs)) {

		$grupo = $al_fecha['unidad'];
		$id = $al_fecha['ID'];
		$claveal = $al_fecha['CLAVEAL'];

		$alma = mysqli_query($db_con,"SELECT combasi, curso FROM alma WHERE alma.claveal = '$claveal'");
		$combasi = mysqli_fetch_array($alma);

		$curso = $combasi[1];
		$asignaturas =substr($combasi[0],0,-1);
		$tr_combasi = explode(":", $asignaturas);
		foreach ($tr_combasi as $codasi) {

			$asigna1 = mysqli_query($db_con, "SELECT nombre FROM asignaturas WHERE codigo = '$codasi' AND curso = '$curso' AND abrev NOT LIKE '%\_%'");
			$asigna2 = mysqli_fetch_array($asigna1);
			$asignatura = $asigna2[0];

			$profe_asig0 = mysqli_query($db_con,"SELECT * FROM tareas_profesor WHERE id_alumno = '$id' AND asignatura = '$asignatura'");
			if (mysqli_num_rows($profe_asig0)>0) { }
				else{
					$hay_profe = mysqli_query($db_con,"SELECT profesor, idea FROM profesores, departamentos WHERE nombre=profesor AND materia = '$asignatura' AND grupo = '$grupo'");
					while ($hay_pr = mysqli_fetch_array($hay_profe)) {
						$idea = $hay_pr[1];
						$profeso = $hay_pr[0];
						$nc = $claveal;
						$sel = mysqli_query($db_con,"SELECT alumnos FROM grupos WHERE profesor = '$profeso' AND curso = '$grupo' AND asignatura = '$codasi'");
						$hay_grupo = mysqli_num_rows($sel);
						if ($hay_grupo>0) {
							$sel_al = mysqli_fetch_array($sel);
							$al_sel = explode(",",$sel_al[0]);
							$hay_al="";
							foreach($al_sel as $num_al){
								if ($num_al == $nc) {
									$hay_al = "1";;
								}
							}
						}
						if ($hay_al=="1" or $hay_grupo<1) {
							$num++;
							mysqli_query($db_con,"INSERT INTO acceso (profesor, fecha, clase, observaciones) VALUES ('$idea', '$hoy', '3', '$id')");
						}
					}
				}
			}
		}



	// INFORMES DE TUTORIA A PUNTO DE CUMPLIR

	$grupo = "";
	$id = "";
	$claveal = "";
	$profeso = "";
	$codasi = "";
	$curso = "";
	$asignatura = "";
	$idea = "";

	$fchs = mysqli_query($db_con,"SELECT * FROM infotut_alumno WHERE DATE(F_ENTREV) = '$hoy'");
	while ($al_fecha = mysqli_fetch_array($fchs)) {

		$grupo = $al_fecha['unidad'];
		$id = $al_fecha['ID'];
		$claveal = $al_fecha['CLAVEAL'];

		$alma = mysqli_query($db_con,"SELECT combasi, curso FROM alma WHERE alma.claveal = '$claveal'");
		$combasi = mysqli_fetch_array($alma);

		$curso = $combasi[1];
		$asignaturas =substr($combasi[0],0,-1);
		$tr_combasi = explode(":", $asignaturas);
		foreach ($tr_combasi as $codasi) {

			$asigna1 = mysqli_query($db_con, "SELECT nombre FROM asignaturas WHERE codigo = '$codasi' and curso = '$curso' and abrev not like '%\_%'");
			$asigna2 = mysqli_fetch_array($asigna1);
			$asignatura = $asigna2[0];

			$profe_asig0 = mysqli_query($db_con,"SELECT * FROM infotut_profesor WHERE id_alumno = '$id' and asignatura = '$asignatura'");
			if (! mysqli_num_rows($profe_asig0)>0) {
				// Evitamos al Tutor.
				$hay_profe = mysqli_query($db_con,"SELECT profesor, idea FROM profesores, departamentos WHERE nombre=profesor and materia = '$asignatura' and grupo = '$grupo' and profesor not like (select tutor from FTUTORES where unidad='$grupo')");
				while ($hay_pr = mysqli_fetch_array($hay_profe)) {
					$idea = $hay_pr[1];
					$profeso = $hay_pr[0];
					$nc = $claveal;
					$sel = mysqli_query($db_con,"SELECT alumnos FROM grupos WHERE profesor = '$profeso' and curso = '$grupo' and asignatura = '$codasi'");
					$hay_grupo = mysqli_num_rows($sel);
					if ($hay_grupo>0) {
						$sel_al = mysqli_fetch_array($sel);
						$al_sel = explode(",",$sel_al[0]);
						$hay_al="";
						foreach($al_sel as $num_al){
							if ($num_al == $nc) {
								$hay_al = "1";;
							}
						}
					}
					if ($hay_al=="1" or $hay_grupo<1) {
						$num++;
						mysqli_query($db_con,"INSERT INTO acceso (profesor, fecha, clase, observaciones) VALUES ('$idea','$hoy','4','$id')");
					}
				}
			}
		}
	}


	// INFORMES DE ABSENTISMO A PUNTO DE CUMPLIR

	$date=date_create($hoy);
	date_sub($date,date_interval_create_from_date_string("7 days"));
	$nuevo = date_format($date,"Y-m-d");
	$siete_dias = "date(fecha_registro) = '$nuevo'";

	$result0 = mysqli_query($db_con,"SELECT * FROM absentismo WHERE $siete_dias and orientacion IS NULL");
	$result1 = mysqli_query($db_con,"SELECT distinct unidad FROM absentismo WHERE $siete_dias and tutoria IS NULL");

	if (mysqli_num_rows($result1) > 0)
		{
			while ($absentismo_tutor = mysqli_fetch_array($result1)) {
				$tut = mysqli_query($db_con,"select idea from FTUTORES, departamentos where tutor=nombre and unidad = '$absentismo_tutor[0]'");
				$tutor_abs = mysqli_fetch_array($tut);
				$idea_tutor = $tutor_abs[0];

				mysqli_query($db_con,"INSERT INTO acceso (profesor, fecha, clase, observaciones) VALUES ('$idea_tutor','$hoy','5','$id')");
			}
		}

	if (mysqli_num_rows($result0) > 0)
		{
			$orient = mysqli_query($db_con,"select nombre from departamentos where cargo like '%4%' and cargo like '%8%'");
			$orientacion_abs = mysqli_fetch_array($orient);
			$idea_orientacion = $orientacion_abs[0];

			mysqli_query($db_con,"INSERT INTO acceso (profesor, fecha, clase, observaciones) VALUES ('$idea_orientacion','$hoy','5','$id')");
		}


	// ENVÍO DE MENSAJES SMS U OTROS A LOS PROFESORES

	$pr_sms = mysqli_query($db_con,"SELECT DISTINCT profesor FROM acceso WHERE DATE( fecha ) =  '$hoy' and profesor not in (select idea from departamentos where departamento like 'Administracion') and profesor not in (select idea from ausencias, departamentos where nombre=profesor and date(inicio)<='$hoy' and date(fin)>='$hoy')");

	while ($p_sms = mysqli_fetch_array($pr_sms)){

		$clase="";
		$profe_sms = $p_sms[0];

		$SQL0 = "SELECT DISTINCT clase FROM acceso WHERE profesor = '$profe_sms' AND DATE(fecha)='$hoy'";
		$result0 = mysqli_query($db_con, $SQL0);
		while ($row0 = mysqli_fetch_array($result0)){
			$clase.=$row0[0];
		}

		$movil="";
		$texto="";

		$text_0 = "Te recordamos que";
		$text_1 = " hace más de 4 días que no compruebas el estado de tus tareas en la Intranet y es necesario que lo hagas con regularidad;";
		$text_2 = " tienes más de 25 mensajes pendientes de lectura;";
		$text_3 = " tienes que presentar hoy informes de tareas;";
		$text_4 = " tienes que presentar hoy informes de tutoría.";
		$text_5 = " hoy es la fecha límite para presentar los informes de absentismo de tus alumnos.";
		$text_6 = " hace varios días que no registras en la Intranet las faltas de asistencia de tus alumnos. Contacta con Jefatura si estás teniendo algún problema para hacerlo.";

		$texto = $text_0;
		$reg_faltas = 0;

		if (strstr($clase,"1")==TRUE) { $texto.= $text_1; }
		if (strstr($clase,"2")==TRUE) { $texto.= $text_2; }
		if (strstr($clase,"3")==TRUE) { $texto.= $text_3; }
		if (strstr($clase,"4")==TRUE) { $texto.= $text_4; }
		if (strstr($clase,"5")==TRUE) { $texto.= $text_5; }
		if (strstr($clase,"6")==TRUE) { $texto.= $text_6; $reg_faltas = '1';}

		$texto_ies = "; ".substr($config['centro_denominacion'],0,4);
		$nombre_ies = ". ".substr($config['centro_denominacion'],0,4);
		if (stristr($texto, $texto_ies)) {
			$texto = str_ireplace($texto_ies, $nombre_ies, $texto);
		}

		// Telefonos móviles o sin telefono
		$tfn = mysqli_query($db_con,"SELECT telefono FROM c_profes WHERE idea = '$profe_sms'");
		$tfno = mysqli_fetch_array($tfn);
		$movil = $tfno[0];

		// El Centro tiene envío de SMS
		if ($config['mod_sms']==1 and strlen($movil)==9 and $reg_faltas == 0) {

			$auth = smsLogin($config['mod_sms_user'], $config['mod_sms_pass']);

			$smsSent = sendSMS($auth, array(
			    "message" => $texto,
			    "message_type" => MESSAGE_HIGH_QUALITY,
			    "returnCredits" => false,
			    "recipient" => array("+34".$movil),
			    "sender" => $config['mod_sms_id']
			));

			if ($smsSent->result == "OK") {
			    mysqli_query($db_con, "INSERT INTO sms (fecha, telefono, mensaje, profesor) values (NOW(), '$movil', '$texto', 'Direccion')");
			}	

		}
		// De lo contrario, enviamos un correo
		elseif($config['mod_sms']<>1 or strlen($movil)<>9 or $reg_faltas == 1){

			//Buscamos correo del profe
			$mail0=mysqli_query($db_con, "SELECT correo, PROFESOR FROM c_profes WHERE idea='$profe_sms'");
			if (mysqli_num_rows($mail0)>0) {
				$numcor++;
			}
			$mail1=mysqli_fetch_row($mail0);
			$direccion = $mail1[0];
			$nombre = $mail1[1];
			$exp_nombre = explode(", ", $nombre);
			$nombre_profe = trim($exp_nombre[1])." ".trim($exp_nombre[0]);

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
			$message = str_replace('{{titulo}}', 'Comunicación de tareas pendientes en el Centro', $message);
			$message = str_replace('{{contenido}}', $texto, $message);
			$message = str_replace('{{autor}}', 'Dirección del Centro', $message);

			$mail->msgHTML(utf8_decode($message));
			$mail->Subject = utf8_decode('Comunicación de tareas pendientes en el Centro');
			$mail->AltBody = utf8_decode($texto);
			$mail->AddAddress($direccion, utf8_decode($nombre_profe));
			if($mail->Send()){

			$correo_ya = mysqli_query($db_con,"select * from correos where correo = '$direccion' and texto = '$texto' and date(fecha) = '$hoy'");
			if (!mysqli_num_rows($correo_ya)>0) {
				mysqli_query($db_con,"INSERT INTO correos (destino, correo, fecha, texto) VALUES ('$nombre', '$direccion', NOW(), '$texto')");
				}
			}			
		}
	}

		// Registramos la fecha para no volver a repetir
		mysqli_query($db_con,"INSERT INTO acceso_dias (fecha, numero) VALUES ('$hoy', '$num')");

		}
	}

}
