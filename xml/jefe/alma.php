<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array('z', '1'));

if (isset($_FILES['archivo1'])) {$archivo1 = $_FILES['archivo1'];}
if (isset($_FILES['archivo2'])) {$archivo2 = $_FILES['archivo2'];}

// Creación de la tabla alma

include("../../menu.php");

if (file_exists('config_alma.php')) {
	include('config_alma.php');
}
?>

<div class="container">

	<div class="page-header">
		<h2>Administración <small> Actualización de alumnos</small></h2>
	</div>

	<div id="wrap" class="row">

		<div class="col-sm-8 col-sm-offset-2">

			<div class="well">

				<?php  if($archivo1 and $archivo2){

			// Importamos los datos del fichero CSV (todos_alumnos.csv) en la tabla alma.
			$fp = fopen ($_FILES['archivo1']['tmp_name'] , "r" ) or die('<div align="center">
			<div class="alert alert-danger alert-block fade in">
	        <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCIÓN:</h5>
			No se ha podido abrir el archivo RegAlum.txt. O bien te has olvidado de enviarlo o el archivo está corrompido.
			</div></div><br />
			<div align="center">
			  <input type="button" value="Volver atrás" name="boton" onClick="history.back(2)" class="btn btn-inverse" />
			</div>');

			while (!feof($fp))
			{
				$num_linea++;
				$linea=fgets($fp);
				$tr=explode("|",$linea);
				if ($num_linea == 7) {						
					$num_col = count($tr);
					foreach ($tr as $key => $value) {
						//echo "$key ==> $value<br>";
						$value = trim(utf8_encode($value));
						$campos_seneca[] = $value;
						foreach ($campos as $key2 => $value2) {
							if($value == $key2){
								//echo "$value -->> $key2<br>";
								$val_ok[] = $key;
								//echo $key."<br>";
								$campo = $value2;							
							}
						}
					}
				break;
				}
			}

			// Comprobación de campos añadidos por Séneca			
			$msg_error="";
			$num=1;

			foreach ($val_ok as $key_ok => $value_ok) {
				if ($key_ok != $value_ok) {
					$valor_campo = $key_ok;

					foreach ($campos as $key => $value) {
						
						$num++;	

						if ($num==$value_ok) {
							foreach ($campos as $key2 => $value2) {
								$num2++;
								if ($num2==($value_ok-1)) {
									$anterior = $value2;
									break;
								}
							}

							$clave_dato = $key_ok+1;
							$msg_error .=  "El siguiente campo ha sido añadido por Séneca y va a ser creado en la tabla <b>Alma</b>:<br><em> (".$campos_seneca[$key_ok].")</em><br><u>Debes recargar la página o volver a enviar los archivos para actualizar los nuevos datos.</u> Si Séneca ha añadido varios campos, este mensaje volverá a aparecer.";

								$extra_alma = 'ALTER TABLE `alma` ADD `'.$campos_seneca[$clave_dato-1].'` varchar(255) NULL AFTER `'.$anterior.'`';

								$mystring = $campos_texto;
								$findme   = '"'.$key.'"=>"'.$value.'", ';								
								$extra = strlen($findme);
								$pos = strrpos($mystring, $findme)+$extra;

								$nuevo_texto = substr($campos_texto, 0,$pos);
								$nuevo_texto .= '"'.$campos_seneca[$key_ok].'"=>"'.$campos_seneca[$key_ok].'", ';
								$nuevo_texto .= substr($campos_texto, $pos,strlen($campos_texto));


								// CREACIÓN DEL ARCHIVO DE CONFIGURACIÓN
								if($file = fopen('config_alma.php', 'w+'))
								{
									fwrite($file, "<?php \r\n");

									fwrite($file, "\r\n// CONFIGURACIÓN MÓDULO DE ACTUALIZACIÓN DE ALUMNOS\r\n");

									fwrite($file, "\$campos\t= array($nuevo_texto);\r\n");
									fwrite($file, "\$campos_texto\t= '$nuevo_texto';\r\n");

									if(stristr($alumnos, $extra_alma)==FALSE){ 
										fwrite($file, "\$alumnos\t= '$alumnos\r\n$extra_alma;';\r\n");
									}

									fwrite($file, "\r\n\r\n// Fin del archivo de configuración");

									fclose($file);
								}

							}

						}

					}

				}

			// Copia de Seguridad

			mysqli_query($db_con, "DROP TABLE alma_seg") ;
			mysqli_query($db_con, "create table alma_seg select * from alma");		
			mysqli_query($db_con, "drop TABLE alma");

			$tr_al = explode(";", $alumnos);
			$num_cols = count($tr_al);

			for ($i=0; $i < $num_cols; $i++) { 
				mysqli_query($db_con, $tr_al[$i]);
			}

			mysqli_query($db_con,$extra_alma);

			if ($clave_dato > 0) {
				echo '<div align="center">
				<div class="alert alert-warning alert-block fade in">
		        <button type="button" class="close" data-dismiss="alert">&times;</button>
				<h5>ATENCIÓN:</h5>'.$msg_error.'
				</div></div><br /><br>';

				echo "</div></div></div></div>";
				
				include("../../pie.php");
				
				exit();

			}

			while (!feof($fp))
			{

				$num_linea++;

				$linea="";
				$lineasalto="";
				$dato="";
				$linea=fgets($fp);

				// En la línea 9 es donde comienza el listado de alumnos
				if ($num_linea > 7) {
					$tr=explode("|",$linea);
					$lineasalto = "INSERT INTO alma VALUES (";
					$n_dato="";
						foreach ($tr as $valor){
							$n_dato++;
								$dato.= "\"". mysqli_real_escape_string($db_con, trim(utf8_encode($valor))) . "\", ";
						}
					$dato=substr($dato,0,strlen($dato)-2);
					$lineasalto.=$dato;
					$lineasalto.=");";

					$consulta=explode(',',$lineasalto);
					//Comprobamos que la matrícula no haya sido anulada o trasladada para añadirla
					if (!preg_match('*Anulada*', $consulta[2]) and !preg_match('*Trasladada*',$consulta[2])){
						mysqli_query($db_con, $lineasalto);
						//echo "$lineasalto<br>";
					}
				}
			}

			fclose($fp);

			// Comprobación de seguridad
			$spv = mysqli_query($db_con, "select * from alma");
			if (mysqli_num_rows($spv)<9) {

				echo '<div align="center">
				<div class="alert alert-warning alert-block fade in">
		        <button type="button" class="close" data-dismiss="alert">&times;</button>
				<h5>ATENCIÓN:</h5>
				Parece que hemos tenido un problema al importar los datos de los alumnos. Vamos a dejar las cosas como estaban antes de iniciar la actualización, pero debes buscar ayuda.
				</div></div><br />
				<div align="center">
				  <input type="button" value="Volver atrás" name="boton" onClick="history.back(2)" class="btn btn-inverse" />
				</div><br>';

				mysqli_query($db_con,"drop table alma");
				mysqli_query($db_con,"create table alma Select * from alma_seg");
				mysqli_query($db_con,"ALTER TABLE `alma` ADD PRIMARY KEY(`CLAVEAL`)");	?>

				<?php echo "</div></div></div></div>"; ?>
				<?php include("../../pie.php");	?>
				<?php exit(); ?>

			<?php }

			// Descomprimimos el zip de las calificaciones en el directorio exporta/
			include('../../lib/pclzip.lib.php');
			// Borramos archivos antiguos
			$files = glob('../exporta/*');
				foreach($files as $file)
				{
					if(is_file($file) and stristr($file, "index")==FALSE)
					unlink($file);
				}

			$archive = new PclZip($_FILES['archivo2']['tmp_name']);
			if ($archive->extract(PCLZIP_OPT_PATH, '../exporta') == 0)
			{
				die("Error : ".$archive->errorInfo(true));
			}

			// Procesamos los datos de RegAlum para crear la tabla alma
			$crear = "ALTER TABLE  alma
			ADD  `COMBASI` VARCHAR( 250 ) NULL FIRST ,
			ADD  `APELLIDOS` VARCHAR( 40 ) NULL AFTER  `UNIDAD`,
			ADD  `CLAVEAL1` VARCHAR( 8 ) NULL AFTER  `CLAVEAL`,
			ADD  `PADRE` VARCHAR( 78 ) NULL AFTER  `CLAVEAL1`
			";
					mysqli_query($db_con, $crear);

					// Apellidos unidos formando un solo campo.
					$SQL2 = "SELECT apellido1, apellido2, CLAVEAL, NOMBRE FROM  alma";
					$result2 = mysqli_query($db_con, $SQL2);
					while  ($row2 = mysqli_fetch_array($result2))
					{
						$apellidos = trim($row2[0]). " " . trim($row2[1]);
						$apellidos1 = trim($apellidos);
						$nombre = $row2[3];
						$nombre1 = trim($nombre);
						$actualiza1= "UPDATE alma SET APELLIDOS = \"". $apellidos1 . "\", NOMBRE = \"". $nombre1 . "\" where CLAVEAL = \"". $row2[2] . "\"";
						mysqli_query($db_con, $actualiza1);
					}

					// Apellidos y nombre del padre.
					$SQL3 = "SELECT PRIMERAPELLIDOTUTOR, SEGUNDOAPELLIDOTUTOR, NOMBRETUTOR, CLAVEAL FROM  alma";
					$result3 = mysqli_query($db_con, $SQL3);
					while  ($row3 = mysqli_fetch_array($result3))
					{
						$apellidosP = trim($row3[2]). " " . trim($row3[0]). " " . trim($row3[1]);
						$apellidos1P = trim($apellidosP);
						$actualiza1P= "UPDATE alma SET PADRE = \"". $apellidos1P . "\" where CLAVEAL = \"". $row3[3] . "\"";
						mysqli_query($db_con, $actualiza1P);
					}

					// Eliminación de campos innecesarios por repetidos
					$SQL3 = "ALTER TABLE alma
			  DROP `apellido1`,
			  DROP `Alumno/a`,
			  DROP `apellido2`";
					$result3 = mysqli_query($db_con, $SQL3);
					$cambiar_nombre = "ALTER TABLE alma MODIFY COLUMN NOMBRE VARCHAR(30) AFTER APELLIDOS";
					mysqli_query($db_con, $cambiar_nombre);

					// Eliminación de alumnos dados de baja
					$SQL4 = "DELETE FROM alma WHERE unidad = ''";
					$result4 = mysqli_query($db_con, $SQL4);
					// Eliminación de alumnos dados de baja
					$SQL5 = "DELETE FROM alma WHERE unidad = 'Unida'";
					$result5 = mysqli_query($db_con, $SQL5);

					// Exportamos códigos de asignaturas de los alumnos y CLAVEAL1 para las consultas de evaluación
					include("exportacodigos.php");

					// Eliminamos alumnos sin asignaturas que tienen la matricula pendiente, y que no pertenecen a los Ciclos
					$SQL6 = "DELETE FROM alma WHERE (COMBASI IS NULL and (curso like '%E.S.O.%' or curso like '%Bach%' or curso like '%F.P.B%') and ESTADOMATRICULA not like 'Obtiene T%' and ESTADOMATRICULA not like 'Repit%' and ESTADOMATRICULA not like 'Promocion%' and ESTADOMATRICULA not like 'Pendiente de confirma%' and ESTADOMATRICULA not like 'Traslad%') or (COMBASI IS NULL and ESTADOMATRICULA like 'Traslad%')";
					$result6 = mysqli_query($db_con, $SQL6);

					// Elñiminamos alumnos de FPB o Ciclos que obtuenen título o matrícuña anulada

					$SQL7 = "DELETE FROM alma WHERE (curso not like '%E.S.O.%' and curso not like '%Bach%') and (ESTADOMATRICULA like 'Obtiene T%' or ESTADOMATRICULA like 'Baja de of%' or ESTADOMATRICULA like 'Baja de of%')";
					$result7 = mysqli_query($db_con, $SQL7);

					// Buscamos asignaturas en backup o bien creamos una asignatura ficticia para que los alumnos sin Asignaturas puedan aparecer en las listas

					$SQL8 = mysqli_query($db_con,"select claveal, combasi from alma where combasi IS NULL AND (curso not like '%E.S.O.%' AND curso not like '%Bachiller%') OR curso like '4%' OR curso like '2º de Bachillerato%'");
					while ($bck = mysqli_fetch_array($SQL8)) {
						$seg = mysqli_query($db_con,"select claveal, combasi, claveal1 from alma_seg where claveal='".$bck[0]."'");
						if(mysqli_num_rows($seg)>0){
							$upd = mysqli_fetch_array($seg);
							if ($upd[1]!=='Sin_Asignaturas' && $upd[1]!=="") {
								mysqli_query($db_con,"update alma set combasi = '".$upd[1]."', claveal1='".$upd[2]."' where claveal='".$upd[0]."'");
							}	
						}
					}

					$SQL9 = "update alma set combasi = 'Sin_Asignaturas' where combasi IS NULL";
					mysqli_query($db_con, $SQL9);
					echo '<div class="alert alert-success alert-block fade in">
			            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ALUMNOS DEL CENTRO:</h5> los Alumnos se han introducido correctamente en la Base de datos.
			</div>';

					include("actualizar.php");

					if (isset($config['mod_sms']) && $config['mod_sms']) {
						include("crear_hermanos.php");
					}
				}
				else{
					echo '<div align="center"><div class="alert alert-danger alert-block fade in">
			            <button type="button" class="close" data-dismiss="alert">&times;</button>
						<h5>ATENCIÓN:</h5>
			Parece que te está olvidando de enviar todos los archivos con los datos de los alumnos. Asegúrate de enviar ambos archivos descargados desde Séneca.
			</div></div><br />';
				}

				// Si se ha creado la tabla matriculas y el mes es mayor que sept. y menor que Diciembre, actualizamos los datos de alma con los datos de la tabla matriculas.
				$matr = mysqli_query($db_con, "select * from matriculas");
				if (mysqli_num_rows($matr)>0 and (date('m')>8 and date('m')<11)) {
					$pro = mysqli_query($db_con, "select claveal,	apellidos, nombre,	provincia,	domicilio,	localidad,	dni, padre,	dnitutor, telefono1, telefono2, nacido, madre, dnitutor2 from matriculas where curso like '%ESO%'");
					while ($prf = mysqli_fetch_array($pro)) {

						$pap = explode(", ",$prf[7]);
						$papa = $pap[1]." ".$pap[0];
						$papa=trim($papa);

						$mam = explode(", ",$prf[12]);
						$nombretutor2 = $mam[1];
						$apel_mam = explode(" ",$mam[0]);
						$primerapellidotutor2 = $apel_mam[0];
						$segundoapellidotutor2 = "$apel_mam[1] $apel_mam[2] $apel_mam[3]";
						$segundoapellidotutor2=trim($segundoapellidotutor2);

						$alm = mysqli_query($db_con, "select claveal,	apellidos,	nombre,	provinciaresidencia, domicilio, localidad, dni, padre, dnitutor, telefono, telefonourgencia, localidadnacimiento, primerapellidotutor2, segundoapellidotutor2, nombretutor2, dnitutor2 from alma where claveal = '$prf[0]' and (apellidos not like '$prf[1]' or nombre not like '$prf[2]' or provinciaresidencia not like '$prf[3]' or domicilio not like '$prf[4]' or localidad not like '$prf[5]' or dni not like '$prf[6]' or padre not like '$papa'  or telefono not like '$prf[9]' or telefonourgencia not like '$prf[10]' or localidadnacimiento not like '$prf[11]' or dnitutor2 not like '$prf[13]')");

						if (mysqli_num_rows($alm)>0) {

							$num+=1;
							$alma = mysqli_fetch_array($alm);

							$com = explode(", ",$prf[7]);
							$nom = trim($com[1]);
							$apel = explode(" ", $com[0]);
							$apel1 = $apel[0];
							$apel2 = $apel[1]." ".$apel[2]." ".$apel[3]." ".$apel[4];
							$apel2 = trim($apel2);

							$com2 = explode(", ",$prf[12]);
							$nom2 = trim($com2[1]);
							$apel0 = explode(" ", $com2[0]);
							$apel21 = $apel0[0];
							$apel22 = $apel0[1]." ".$apel0[2]." ".$apel0[3]." ".$apel0[4];
							$apel22 = trim($apel22);

							$padre_alma = ", padre = '$nom $apel1 $apel2'";
							$padre_completo = ", nombretutor = '$nom', primerapellidotutor = '$apel1', segundoapellidotutor = '$apel2'";
							$madre_completo = ", nombretutor2 = '$nom2', primerapellidotutor2 = '$apel21', segundoapellidotutor2 = '$apel22', dnitutor2 = '$prf[13]'";


						 mysqli_query($db_con, "update alma set apellidos = '$prf[1]', nombre = '$prf[2]', provinciaresidencia = '$prf[3]', domicilio = '$prf[4]', localidad = '$prf[5]', dni = '$prf[6]', padre = '$prf[7]', dnitutor = '$prf[8]', telefono = '$prf[9]', telefonourgencia = '$prf[10]', localidadnacimiento = '$prf[11]' $padre_alma $padre_completo $madre_completo where claveal = '$prf[0]'");
						 $num_filas+=mysqli_affected_rows($db_con);
						}
					}
					echo '<br />
				<div align="center"><div class="alert alert-warning alert-block fade in">
			            <button type="button" class="close" data-dismiss="alert">&times;</button>
						<h5>ATENCIÓN:</h5>
			Se han modificado los datos personales de '.$num_filas.' alumnos para ajustarlos a la tabla de las matrículas. Este proceso se termina el mes de Diciembre, momento en el que los adminstrativos han podido registrar los nuevos datos en Séneca. </div></div><br />';
				}


			// Si se ha creado la tabla matriculas_bach y el mes es mayor que sept. y menor que Diciembre, actualizamos los datos de alma con los datos de la tabla matriculas_bach.
				$matr = mysqli_query($db_con, "select * from matriculas_bach");
				if (mysqli_num_rows($matr)>0 and (date('m')>8 and date('m')<13)) {
					$pro = mysqli_query($db_con, "select claveal, apellidos, nombre, provincia,	domicilio,	localidad,	dni, padre,	dnitutor, telefono1, telefono2, nacido, madre, dnitutor2 from matriculas_bach where curso like '%BACH%'");
					while ($prf = mysqli_fetch_array($pro)) {

						$pap = explode(", ",$prf[7]);
						$papa = $pap[1]." ".$pap[0];
						$papa=trim($papa);

						$mam = explode(", ",$prf[12]);
						$nombretutor2 = $mam[1];
						$apel_mam = explode(" ",$mam[0]);
						$primerapellidotutor2 = $apel_mam[0];
						$segundoapellidotutor2 = "$apel_mam[1] $apel_mam[2] $apel_mam[3]";
						$segundoapellidotutor2=trim($segundoapellidotutor2);

						$alm = mysqli_query($db_con, "select claveal,	apellidos,	nombre,	provinciaresidencia, domicilio, localidad, dni, padre, dnitutor, telefono, telefonourgencia, localidadnacimiento, primerapellidotutor2, segundoapellidotutor2, nombretutor2, dnitutor2 from alma where claveal = '$prf[0]' and (apellidos not like '$prf[1]' or nombre not like '$prf[2]' or provinciaresidencia not like '$prf[3]' or domicilio not like '$prf[4]' or localidad not like '$prf[5]' or dni not like '$prf[6]' or padre not like '$papa'  or telefono not like '$prf[9]' or telefonourgencia not like '$prf[10]' or localidadnacimiento not like '$prf[11]' or dnitutor2 not like '$prf[13]')");

						if (mysqli_num_rows($alm)>0) {

							$num+=1;
							$alma = mysqli_fetch_array($alm);

							$com = explode(", ",$prf[7]);
							$nom = trim($com[1]);
							$apel = explode(" ", $com[0]);
							$apel1 = $apel[0];
							$apel2 = $apel[1]." ".$apel[2]." ".$apel[3]." ".$apel[4];
							$apel2 = trim($apel2);

							$com2 = explode(", ",$prf[12]);
							$nom2 = trim($com2[1]);
							$apel0 = explode(" ", $com2[0]);
							$apel21 = $apel0[0];
							$apel22 = $apel0[1]." ".$apel0[2]." ".$apel0[3]." ".$apel0[4];
							$apel22 = trim($apel22);

							$padre_alma = ", padre = '$nom $apel1 $apel2'";
							$padre_completo = ", nombretutor = '$nom', primerapellidotutor = '$apel1', segundoapellidotutor = '$apel2'";
							$madre_completo = ", nombretutor2 = '$nom2', primerapellidotutor2 = '$apel21', segundoapellidotutor2 = '$apel22', dnitutor2 = '$prf[13]'";


						 mysqli_query($db_con, "update alma set apellidos = '$prf[1]', nombre = '$prf[2]', provinciaresidencia = '$prf[3]', domicilio = '$prf[4]', localidad = '$prf[5]', dni = '$prf[6]', padre = '$prf[7]', dnitutor = '$prf[8]', telefono = '$prf[9]', telefonourgencia = '$prf[10]', localidadnacimiento = '$prf[11]' $padre_alma $padre_completo $madre_completo where claveal = '$prf[0]'");
						 $num_filas+=mysqli_affected_rows();
						}
					}
					echo '<br />
				<div align="center"><div class="alert alert-warning alert-block fade in">
			            <button type="button" class="close" data-dismiss="alert">&times;</button>
						<h5>ATENCIÓN:</h5>
			Se han modificado los datos personales de '.$num_filas.' alumnos para ajustarlos a la tabla de las matrículas. Este proceso se termina el mes de Diciembre, momento en el que los adminstrativos han podido registrar los nuevos datos en Séneca. </div></div><br />';
				}

				// Asignaturas y alumnos con pendientes
				include("asignaturas.php");

				?>

				<div class="text-center">
					 <a href="../index.php" class="btn btn-primary">Volver a Administración</a>
				</div>

			</div><!-- /.well -->

		</div><!-- /.col-sm-8 -->

	</div><!-- /.row -->

</div><!-- /.container -->

<?php include("../../pie.php");	?>



</body>
</html>
