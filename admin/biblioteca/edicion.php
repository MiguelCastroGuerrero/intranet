<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1, 'c'));

include("../../menu.php");
include("menu.php");
?>
<br>
<div class="container">
<div class="row">
<div class="page-header">
  <h2>Biblioteca <small> Edición de morosos</small></h2>
</div>
<br>

<div class="col-sm-8 col-sm-offset-2">
<?php

if(isset($_POST['borrar'])){
	$i=0;
	$j=0;
	foreach ($_POST as $ide => $valor)
	{

		// Borrar registros

		if(($ide != 'borrar') and (!empty( $valor))){
			for($i=0; $i <= count($valor)-1; $i++){ $j+=1;
			$bor = mysqli_query($db_con, "delete from morosos where id='$valor[$i]'") or die("No se ha podido borrar");
			}

			echo '<div align="center"><div class="alert alert-success alert-block fade in">
			 <button type="button" class="close" data-dismiss="alert">&times;</button>
						<legend>ATENCI&Oacute;N:</legend>
			El proceso de borrado ha sido completado correctamente. Los alumnos no volver&aacute;n a aparecer en la lista.
				</div></div><br />';

						echo '<div align="center">
			  	<input type="button" value="Volver atr&aacute;s" name="boton" onClick="history.back(2)" class="btn btn-inverse" />
						</div>';	
		}
		elseif ($j==0)
		{
			echo '<div align="center"><div class="alert alert-danger alert-block fade in">
			 <button type="button" class="close" data-dismiss="alert">&times;</button>
						<legend>ATENCI&Oacute;N:</legend>
			No se ha podido borrar porque no has elegido ning&uacute;n alumno de la lista. Vuelve atr&aacute;s para solucionarlo.
				</div></div><br />
			<div align="center">
			  	<input type="button" value="Volver atr&aacute;s" name="boton" onClick="history.back(2)" class="btn btn-inverse" />
						</div>';			
		}
	}
}

// Registrar SMS y Fechorías

elseif(isset($_POST['registro']) or isset($_POST['sms']))
{

if(isset($_POST['registro'])){$registro=$_POST['registro'];}
if(isset($_POST['sms'])){$sms=$_POST['sms'];}

	$i=0;
	$j=0;
	$asunto='Retraso injustificado en la devolución de material a la Biblioteca del Centro';
	$informa='Jefatura de Estudios';
	$grave='grave';
	$medida='Amonestación escrita';
	$expulsionaula='0';
	$enviado='1';
	$recibido='0';
	$causa='Otras';
	$accion='Envío de SMS';

foreach ($_POST as $ide => $valor) {   

if(($ide !== 'registro') and (!empty( $valor)))
{ 
$envio='';
for($i=0; $i <= count($valor)-1; $i++)
{ 
$j+=1; 

$envio='-'.$valor[$i]; 

if (isset($_POST['registro'])) {
	$upd = mysqli_query($db_con, "update morosos set amonestacion='SI' where id='$valor[$i]'") or die ("No se ha podido actualizar el registro");
	}

	//localizo el alumno a través de la id de la tabla morosos.
	$al=mysqli_query($db_con, "select apellidos,nombre,curso from morosos where id='$valor[$i]'") or die ("error al localizar alumno");

	while($alu=mysqli_fetch_array($al)){

		$nombre=$alu[1];
		$apellido=$alu[0];
		$curso=$alu[2];
		//localizo la clave del alumno.
		$cla=mysqli_query($db_con, "SELECT claveal, unidad FROM alma WHERE nombre = '$nombre' AND apellidos = '$apellido' AND unidad = '$curso' LIMIT 1");
		while($clav=mysqli_fetch_array($cla)){

			$dia= date ('Y-m-d',time());
			$clave=$clav['claveal'];// echo $clave.'---'. $dia;
			$unidad=$clav['unidad']; //echo $nivel;
			//insertamos, por fÃ­n, la fechoría
		if (isset($_POST['registro'])) {
				// Registramos fechoría
				$fechoria = mysqli_query($db_con,  "insert into Fechoria (CLAVEAL,FECHA,ASUNTO,NOTAS,INFORMA,grave,medida,expulsionaula,enviado,recibido) values ('" . $clave . "','" . $dia . "','" . $asunto . "','" . $asunto . "','" . $informa . "','" . $grave . "','" . $medida . "','" . $expulsionaula . "','" . $enviado . "','" . $recibido . "')") or die ("Error al registrar fechoría");
				
				//Registramos la intervencion en la tabla tutorÃ­a, debido al tema de los SMS
				$tutoria=mysqli_query($db_con, "insert into tutoria (apellidos, nombre, tutor,unidad,observaciones,causa,accion,fecha, claveal,jefatura) values ('" . $apellido . "','" . $nombre . "','" . $informa . "','" . $unidad . "','" . $asunto . "','" . $causa . "','" . $accion . "','" . $dia . "','" . $clave . "','" . $recibido . "')" ) or die ("error al registrar accion en tabla tutoria");
			}

			$alumno = mysqli_query($db_con, " SELECT distinct APELLIDOS, NOMBRE, unidad, CLAVEAL, TELEFONO, TELEFONOURGENCIA FROM alma WHERE claveal = '$clave'" );
			$rowa = mysqli_fetch_array ( $alumno );
			$apellidos = trim ( $rowa [0] );
			$nombre = trim ( $rowa [1] );
			$unidad = trim ( $rowa [2] );
			$claveal = trim ( $rowa [3] );
			$tfno = trim ( $rowa [4] );
			$tfno_u = trim ( $rowa [5] );
			// SMS
			$sms_n = mysqli_query($db_con, "select max(id) from sms" );
			$n_sms = mysqli_fetch_array ( $sms_n );
			$extid = $n_sms [0] + 1;

			if (substr ( $tfno, 0, 1 ) == "6" or substr ( $tfno, 0, 1 ) == "7") {
				$mobile = $tfno;
			} 
			elseif (substr($tfno_u, 0, 1 ) == "6" or substr ( $tfno_u, 0, 1 ) == "7"){
				$mobile = $tfno_u;
			}
			else{
				$mobile = "";
				echo '<div align="center"><div class="alert alert-danger alert-block fade in">
				No se ha podido enviar SMS al alumno/a '.$nombre.' '.$apellidos.' de '.$unidad.' porque no lo tiene.
					</div></div>';						
				//echo "Sin teléfono: $claveal => $tfno -- $tfno_u<br>";
			}

			if ($registro) {
				$message = "Le comunicamos que su hijo/a ha cometido una falta contra las normas de Convivencia del Centro: No devolver en el tiempo debido material de la Biblioteca.";
			}
			else{
				$message = "Le comunicamos que su hijo/a tiene pendiente la devolución de material de la biblioteca. Debe devolverlo o aclarar cualquier posible situación con el responsable en los próximos cinco días. De no hacerlo podrá ser amonestado por el Centro";
			}
			
			if(strlen($mobile) == 9) {
			
				mysqli_query($db_con, "insert into sms (fecha,telefono,mensaje,profesor,claveal) values (now(),'$mobile','$message','$informa','$claveal')");
				
				// ENVIO DE SMS
				include_once(INTRANET_DIRECTORY . '/lib/trendoo/sendsms.php');
				$sms = new Trendoo_SMS();
				$sms->sms_type = SMSTYPE_GOLD_PLUS;
				$sms->add_recipient('+34'.$mobile);
				$sms->message = $message;
				$sms->sender = $config['mod_sms_id'];
				$sms->set_immediate();
				if($sms->validate()) {
					//$num++;
					$sms->send(); 
					//echo "$num ENVIADO: $apellidos, $nombre, $unidad<br>";
				}
				if (isset($_POST['sms'])) {
					mysqli_query($db_con, "update morosos set sms='SI' where id='$valor[$i]'") or die ("No se ha podido actualizar el registro SMS");
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
	}
}
elseif ($j==0)     {
		echo '<div align="center"><div class="alert alert-danger alert-block fade in">
 <button type="button" class="close" data-dismiss="alert">&times;</button>
			<legend>ATENCI&Oacute;N:</legend>
No se ha podido registrar la amonestaci&oacute;n porque no has elegido ning&uacute;n alumno de la lista. Vuelve atr&aacute;s para solucionarlo.
	</div></div><br />
<div align="center">
  	<input type="button" value="Volver atr&aacute;s" name="boton" onClick="history.back(2)" class="btn btn-inverse" />
			</div>';			
	}
	}
}
if (isset($_POST['registro'])) {
	echo '<div align="center"><div class="alert alert-success alert-block fade in">
 <button type="button" class="close" data-dismiss="alert">&times;</button>
			<legend>ATENCI&Oacute;N:</legend>
La amonestación se ha registrado con &eacute;xito. Los tutores de los alumnos recibirán el mensaje del problema de convivencia registrado y procederán a imprimir y entregar en Jefatura el impreso para los padres.
	</div></div><br />';
	echo "<div align='center'><a href='consulta.php' class='btn btn-default'>Volver a la Consulta de Préstamos</a></div>";
}
elseif(isset($_POST['sms'])){
	echo '<div align="center"><div class="alert alert-success alert-block fade in">
 <button type="button" class="close" data-dismiss="alert">&times;</button>
			<legend>ATENCI&Oacute;N:</legend>
Los mensajes SMS de aviso por retraso en la devolución de ejemplares de la Biblioteca han sido enviados correctamente.
	</div></div><br /></div>
</div>
</div>';
echo "<div align='center'><a href='consulta.php' class='btn btn-default'>Volver a la Consulta de Préstamos</a></div>";
}
?>
</div>
</div>
</div>

	<?php include("../../pie.php"); ?>
	
</body>
</html>
