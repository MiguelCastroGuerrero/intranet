<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed');

// Fechas y demÃ¡s...
$fechasp0=explode("-",$_POST['fecha12']);
$fechasp1=$fechasp0[2]."-".$fechasp0[1]."-".$fechasp0[0];
$fechasp11=$fechasp0[0]."-".$fechasp0[1]."-".$fechasp0[2];
$fechasp2=explode("-",$_POST['fecha22']);
$fechasp3=$fechasp2[2]."-".$fechasp2[1]."-".$fechasp2[0];
$fechasp31=$fechasp2[0]."-".$fechasp2[1]."-".$fechasp2[2];

 $SQLTEMP = "create table faltastemp2 SELECT FALTAS.CLAVEAL, falta, (count(*)) AS numero FROM  FALTAS, alma, hermanos where FALTAS.claveal = alma.claveal and alma.telefono = hermanos.telefono and falta = 'F' and date(FALTAS.fecha) >= '$fechasp1' and date(FALTAS.fecha) <= '$fechasp3' group by FALTAS.claveal";
  //echo $SQLTEMP;
  $num='0';
  $resultTEMP= mysqli_query($db_con, $SQLTEMP);
  if ($resultTEMP) {
  
  mysqli_query($db_con, "ALTER TABLE faltastemp2 ADD INDEX ( claveal ) ");
  $SQL0 = "SELECT distinct CLAVEAL FROM  faltastemp2";
  $result0 = mysqli_query($db_con, $SQL0);
while ($row0 = mysqli_fetch_array($result0)): 
$claveal = $row0[0]; 			
	$SQL3 = "SELECT distinct alma.claveal, alma.telefono, alma.telefonourgencia, alma.apellidos, alma.nombre, alma.unidad 
	from alma where alma.claveal like '$claveal'";
	$result3 = mysqli_query($db_con, $SQL3);	
	$rowsql3 = mysqli_fetch_array($result3);
	$tfno2 = $rowsql3[1];	
	$tfno_u2 = $rowsql3[2];
	$apellidos = $rowsql3[3];
	$nombre = $rowsql3[4];
	$unidad = $rowsql3[5];
	// Telefonos mÃ³viles
	if(substr($tfno2,0,1)=="6" OR substr($tfno2,0,1)=="7"){$mobil2=$tfno2;}elseif((substr($tfno_u2,0,1)=="6" OR substr($tfno_u2,0,1)=="7") and !(substr($tfno2,0,1)=="6" OR substr($tfno2,0,1)=="7")){$mobil2=$tfno_u2;}else{$mobil2="";}
	//echo $mobil2;	
	// Variables para la acciÃ³n de tutorÃ­a
	$causa = "Faltas de Asistencia";	
	$observaciones = "ComunicaciÃ³n de Faltas de Asistencia a la familia del Alumno.";
	$accion = "EnvÃ­o de SMS";
	$tuto = "Jefatura de Estudios";
	$fecha2 = date('Y-m-d');
	mysqli_query($db_con, "insert into tutoria (apellidos, nombre, tutor,unidad,observaciones,causa,accion,fecha,claveal) values ('".$apellidos."','".$nombre."','".$tuto."','".$unidad."','".$observaciones."','".$causa."','".$accion."','".$fecha2."','".$claveal."')");
$nombrecor = explode(" ",$nombre);
$nombrecorto = $nombrecor[0];
$text = "Le comunicamos que su hijo/a $nombrecorto tiene Faltas de Asistencia sin justificar dentro del periodo del ".$_POST['fecha12']." al ".$_POST['fecha22'].". Contacte con su Tutor";

// Identificador del mensaje
$sms_n = mysqli_query($db_con, "select max(id) from sms");
$n_sms =mysqli_fetch_array($sms_n);
$extid = $n_sms[0]+1;

if(strlen($mobil2) == 9) {
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
	}
}
else {
	echo "
	<div class='alert alert-warning'>
		<strong>Error:</strong> No se pudo enviar el SMS al alumno ".$nombre." ".$apellidos." (".$unidad.") porque el telÃ©fono estÃ¡ vacÃ­o o contiene errores(".$mobil2."). Corrija la informaciÃ³n de contacto del alumno/a en SÃ©neca e importe los datos nuevamente.
	</div>
	<br>";
}

$num=$num+1;
endwhile;
  }
  if ($num>0) {
  	echo '<div align="center"><div class="alert alert-success alert-block fade in" align="left">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
El mensaje SMS se ha enviado correctamente para los hermanos del mismo nivel con faltas sin justificar.<br>Una nueva acciÃ³n tutorial ha sido tambiÃ©n registrada.
          </div></div><br />';
  }
  else{
  		echo '<div class="alert alert-danger alert-block fade in" align="left">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
No se ha enviado ningÃºn SMS a los alumnos de latabla Hermanos. O bien ningÃºn alumno tiene mÃ¡s de 4 faltas o bien no estÃ¡n registrados los telÃ©fonos mÃ³viles de los mismos.
          </div><br />';
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

// Tabla temporalÃ± y recogida de datos
 mysqli_query($db_con, "DROP table `faltastemp2`");
?>
