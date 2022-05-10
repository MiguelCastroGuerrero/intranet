<?php
require('../bootstrap.php');

require_once(INTRANET_DIRECTORY."/lib/phpmailer/PHPMailerAutoload.php");

$profe = $_SESSION['profi'];

if ($config['mod_sms']) {
	if (isset($_GET['curso'])) {$curso = $_GET['curso'];}elseif (isset($_POST['curso'])) {$curso = $_POST['curso'];}else{unset($curso);}

	if (isset($_GET['hermanos'])) {$hermanos = $_GET['hermanos'];}elseif (isset($_POST['hermanos'])) {$hermanos = $_POST['hermanos'];}else{$hermanos="";}
	if (isset($_GET['login'])) {$login = $_GET['login'];}elseif (isset($_POST['login'])) {$login = $_POST['login'];}else{$login="";}
	if (isset($_GET['password'])) {$password = $_GET['password'];}elseif (isset($_POST['password'])) {$password = $_POST['password'];}else{$password="";}
	if (isset($_GET['extid'])) {$extid = $_GET['extid'];}elseif (isset($_POST['extid'])) {$extid = $_POST['extid'];}else{$extid="";}
	if (isset($_GET['mobile'])) {$mobile = $_GET['mobile'];}elseif (isset($_POST['mobile'])) {$mobile = $_POST['mobile'];}else{$mobile="";}
	if (isset($_GET['numero'])) {$numero = $_GET['numero'];}elseif (isset($_POST['numero'])) {$numero = $_POST['numero'];}else{unset($numero);}

	// Si se han mandado datos desde el Formulario principal de mas abajo...

	if(isset($curso)) {

		// Fechas y demás...
		$fechasp0=explode("-",$_POST['fecha12']);
		$fechasp1=$fechasp0[2]."-".$fechasp0[1]."-".$fechasp0[0];
		$fechasp11=$fechasp0[0]."-".$fechasp0[1]."-".$fechasp0[2];
		$fechasp2=explode("-",$_POST['fecha22']);
		$fechasp3=$fechasp2[2]."-".$fechasp2[1]."-".$fechasp2[0];
		$fechasp31=$fechasp2[0]."-".$fechasp2[1]."-".$fechasp2[2];
		$nivel_sms = "and curso like '$curso'";

		mysqli_query($db_con,"drop table faltastemp2");
		$SQLTEMP = "create table faltastemp2 SELECT FALTAS.CLAVEAL, falta, (count(*)) AS numero FROM  FALTAS, alma where alma.claveal=FALTAS.claveal and falta = 'F' and date(FALTAS.fecha) >= '$fechasp1' and date(FALTAS.fecha) <= '$fechasp3' $nivel_sms group by FALTAS.claveal";
		//echo $SQLTEMP;
		$resultTEMP= mysqli_query($db_con, $SQLTEMP);
		mysqli_query($db_con, "ALTER TABLE faltastemp2 ADD INDEX ( claveal ) ");

		mysqli_query($db_con,"delete FROM faltastemp2 where numero < '".$_POST['numero']."'");

		$SQL0 = "SELECT distinct CLAVEAL FROM  faltastemp2 where numero >= '".$_POST['numero']."'";
		//echo $SQL0;
		$result0 = mysqli_query($db_con, $SQL0);

		while ($row0 = mysqli_fetch_array($result0)){
			$claveal = $row0[0];
			$clave_carta .= $claveal.",";
			$SQL3 = "SELECT distinct alma.claveal, alma.telefono, alma.telefonourgencia, alma.apellidos, alma.nombre, alma.unidad
	from alma where alma.claveal like '$claveal' and (alma.telefono not in (select telefono from hermanos)
	or alma.telefonourgencia not in (select telefonourgencia from hermanos))";
			//echo "$SQL3<br>";
			$result3 = mysqli_query($db_con, $SQL3);
			$rowsql3 = mysqli_fetch_array($result3);
			$tfno2 = $rowsql3[1];
			$tfno_u2 = $rowsql3[2];
			$apellidos = $rowsql3[3];
			$nombre = $rowsql3[4];
			$unidad = $rowsql3[5];
			$nombre_alumno = "$nombre $apellidos";

// Envío de Email
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

			if (strlen($correo)>0) {

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
	$message = str_replace('{{titulo}}', 'Comunicación de faltas de asistencia', $message);
	$message = str_replace('{{contenido}}', 'Le comunicamos que entre el '.$_POST['fecha12'].' y el '.$_POST['fecha22'].' su hijo/a de '.$unidad.' ha faltado al menos '.$_POST['numero'].' horas al Centro sin haber presentado ninguna justificación.<br>Puede conseguir información más detallada en la página del Alumnado de nuestra web https://'.$config['dominio'].', o bien contactando con Jefatura de Estudios del Centro.<br><br><hr>Este correo es informativo. Por favor, no responder a esta dirección de correo.', $message);
	$message = str_replace('{{autor}}', 'Jefatura de estudios', $message);

	$mail->msgHTML(utf8_decode($message));
	$mail->Subject = utf8_decode('Comunicación de faltas de asistencia');
	$mail->AltBody = 'Le comunicamos que entre el '.$_POST['fecha12'].' y el '.$_POST['fecha22'].' su hijo/a de '.$unidad.' ha faltado al menos '.$_POST['numero'].' horas al Centro sin haber presentado ninguna justificación.<br>Puede conseguir información más detallada en la página del Alumnado de nuestra web https://'.$config['dominio'].', o bien contactando con la Jefatura de Estudios del Centro.<br><br><hr>Este correo es informativo. Por favor, no responder a esta dirección de correo.';

	$mail->AddAddress($correo, $nombre_alumno);
	$mail->Send();
			}

// Fin envío de correo.

// Telefonos móviles o sin telefono
			if(substr($tfno2,0,1)=="6" or substr($tfno2,0,1)=="7"){$mobil2=$tfno2;$sin="";}elseif((substr($tfno_u2,0,1)=="6" or substr($tfno_u2,0,1)=="7") and !(substr($tfno2,0,1)=="6") or substr($tfno2,0,1)=="7"){$mobil2=$tfno_u2;$sin="";}else{$mobil2="";$sin=$claveal;}

			if(strlen($mobil2) > 0)
			{

				// Variables del memnsaje
	$tr_curso = explode("(",$curso);
	$niv = $tr_curso[0];

	$text = "Entre el ".$_POST['fecha12']." y el ".$_POST['fecha22']." su hijo/a de ".$niv." ha faltado al menos ".$_POST['numero']." horas injustificadas al centro. Mas info en http://".$config['dominio'];

        // Registramos intervención de tutoría
        $causa = "Faltas de Asistencia";
        $observaciones = "Comunicación de Faltas de Asistencia a la familia del Alumno.";
        $accion = "Envío de SMS";
        $tuto = "Jefatura de Estudios";
        $fecha2 = date('Y-m-d');
        mysqli_query($db_con, "insert into tutoria (apellidos, nombre, tutor,unidad,observaciones,causa,accion,fecha,claveal) values ('".$apellidos."','".$nombre."','".$tuto."','".$unidad."','".$observaciones."','".$causa."','".$accion."','".$fecha2."','".$claveal."')");

	// ENVIO DE SMS
    $auth = smsLogin($config['mod_sms_user'], $config['mod_sms_pass']);
    
    $smsSent = sendSMS($auth, array(
        "message" => $text,
        "message_type" => MESSAGE_HIGH_QUALITY,
        "returnCredits" => false,
        "recipient" => array("+34".$mobil2),
        "sender" => $config['mod_sms_id']
    ));

    if ($smsSent->result == "OK") {
        mysqli_query($db_con, "insert into sms (fecha,telefono,mensaje,profesor) values (now(),'$mobil2','$text','Jefatura de Estudios')");
        $mobile2 .= $mobil2.",";
    }

    	}
		if(strlen($sin) > 0){$sin2 .= $sin.";";}
	}
		// Identificador del mensaje
		$sms_n = mysqli_query($db_con, "select max(id) from sms");
		$n_sms =mysqli_fetch_array($sms_n);
		$extid = $n_sms[0]+1;
}

	?>
	<?php
	include("../menu.php");
	include("menu.php");
	?>
<div class="container">

<div class="page-header">
<h2>SMS <small> Comunicación de Faltas de Asistencia a los Padres </small></h2>
</div>
<br>
<div class="row">
<?php
if ($hermanos) {
	include("hermanos.php");
}
// Enviamos los datos
if(isset($curso))
{

if(strlen($mobile2) > 0){
	echo '<div class="alert alert-success alert-block fade in" align="left">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
El mensaje SMS se ha enviado correctamente para los alumnos con faltas sin justificar de '. $curso.'.<br>Una nueva acción tutorial ha sido también registrada.
          </div><br />';
}
else{
	echo '<div class="alert alert-danger alert-block fade in" align="left">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
No se ha enviado ningún SMS a los alumnos de '. $curso.'. O bien ningún alumno tiene más de '.$_POST['numero'].' faltas o bien no están registrados los teléfonos móviles de los mismos (en cuyo caso aparecerá un mensaje más abajo indicando los alumnos sin móvil).
          </div><br />';
}
if(strlen($sin2) > '0'){
	echo '<div class="alert alert-warning alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4>ATENCIÓN:</h4>
<p align="left">Los siguientes alumnos no tienen teléfono móvil al que enviar comunicación de Faltas de Asistencia:</p>
<ul style="text-align:left;">';
	// Marcamos alumnos sin movil para enviarlos al tutor
	$sin2 = substr($sin2,0,strlen($sin2)-1);
	$sin0 = explode(";",$sin2);
	foreach ($sin0 as $no_tel)
	{
		$no_tel0 = mysqli_query($db_con, "select apellidos, nombre, unidad, telefono, claveal from alma where claveal = '$no_tel'");
		$dat_al = mysqli_fetch_array($no_tel0);
		$herm = mysqli_query($db_con, "select telefono from hermanos where telefono = '$dat_al[4]'");
		if (mysqli_num_rows($herm) == '1' or empty($dat_al[4])) {}
		else {
			$clave_sin .= $dat_al[5].";";
			echo "<li>$dat_al[1] $dat_al[0] => $dat_al[2]</li>";
		}
	}
	echo "</ul></div>";
}
}

$fecha_inicio_0 = mysqli_query($db_con, "select date_add(curdate(),interval -21 day)");
$fecha_inicio = mysqli_fetch_array($fecha_inicio_0);
$anterior = $fecha_inicio[0];
$fc1 = explode("-",$anterior);
$fech1 = "$fc1[2]-$fc1[1]-$fc1[0]";
$fecha_fin_0 = mysqli_query($db_con, "select date_add(curdate(),interval -7 day)");
$fecha_fin = mysqli_fetch_array($fecha_fin_0);
$posterior = $fecha_fin[0];
$fc2 = explode("-",$posterior);
$fech2 = "$fc2[2]-$fc2[1]-$fc2[0]";
?>
<div class="col-sm-5 col-sm-offset-1">
<div class="well well-large">
<form enctype='multipart/form-data' action='sms_cpadres.php'
	method='post'><br />

<legend align="center">Selecciona el rango de fechas</legend>

<div class="form-group" id="datetimepicker1"><label>Inicio</label>
<div class="input-group"><input name="fecha12" type="text"
	class="form-control"
	value="<?php if(isset($_POST['fecha12'])){ echo $_POST['fecha12'];}else{echo $fech1;} ?>"
	data-date-format="DD-MM-YYYY" id="fecha12"> <span
	class="input-group-addon"><i class="far fa-calendar"></i></span></div>
</div>

<div class="form-group" id="datetimepicker2"><label>Fin</label>
<div class="input-group"><input name="fecha22" type="text"
	class="form-control"
	value="<?php if(isset($_POST['fecha22'])){ echo $_POST['fecha22'];}else{echo $fech2;}  ?>"
	data-date-format="DD-MM-YYYY" id="fecha22"> <span
	class="input-group-addon"><i class="far fa-calendar"></i></span></div>
</div>

<div class="form-group">
<label class="control-label" for='numero'> Número mínimo de Faltas</label>
<INPUT name="numero" type="text" id="numero" class="form-control" maxlength="3" value="1" required />
</div>

</div>
</div>

<div class="col-sm-5 ">
<div class="well well-large">

<div class="form-group"><?php
$cursos_sen = mysqli_query($db_con, "select nomcurso from cursos");
$n_c=1;
while ($cursos_seneca = mysqli_fetch_array($cursos_sen)) {
	$n_c+=1;
	echo '<input name="curso" type="submit" value="'.$cursos_seneca[0].'"
				class="btn btn-primary btn-block" />';
}
?> <input name="hermanos" type="submit" value='Hermanos'
	class="btn btn-primary btn-block" /></div>

</form>
</div>
</div>
</div>
</div>
<?php
// Tabla temporalñ y recogida de datos
mysqli_query($db_con, "DROP table `faltastemp2`");
}
else {
	echo '<div align="center"><div class="alert alert-warning alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4>ATENCIÓN:</h4>
El módulo de envío de SMS debe ser activado en la Configuración general de la Intranet para poder accede a estas páginas, y ahora mismo está desactivado.
          </div></div>';
}
?>
<?php include("../pie.php");?>
<script>
$(function ()
{
	$('#datetimepicker1').datetimepicker({
		language: 'es',
		pickTime: false
	});

	$('#datetimepicker2').datetimepicker({
		language: 'es',
		pickTime: false
	});
});
</script>
</body>
</html>
