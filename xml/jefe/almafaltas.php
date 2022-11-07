<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1));

if (isset($_FILES['archivo1'])) {$archivo1 = $_FILES['archivo1'];}
if (isset($_FILES['archivo2'])) {$archivo2 = $_FILES['archivo2'];}

include '../../menu.php';
?>

<div class="container">

	<div class="page-header">
		<h2>Administración <small> Creación de la tabla de alumnos</small></h2>
	</div>

	<div id="wrap" class="row">

		<div class="col-sm-8 col-sm-offset-2">

			<div class="well">

				<?php
				if($archivo1 and $archivo2){
					// Comprobamos si es la primera vez que se ha creado una base de datos.
					$comprobar_alma = mysqli_query($db_con, "select * from alma");
					$comprobar_mensajes = mysqli_query($db_con, "select * from mens_texto limit 10");
					$comprobar_reg_int = mysqli_query($db_con, "select * from reg_intranet limit 10");

					if ((mysqli_num_rows($comprobar_alma) > 5) && (mysqli_num_rows($comprobar_mensajes) > 5) && (mysqli_num_rows($comprobar_reg_int) > 5)) {
						include("copia_bd.php");
					}

					// Copia de Seguridad

					mysqli_query($db_con, "DROP TABLE alma_seg") ;
					mysqli_query($db_con, "create table alma_seg select * from alma");
					mysqli_query($db_con,"drop table alma");

					 // Creación de la tabla alma
					if (file_exists('config_alma.php')) {
						include_once('config_alma.php');
					}

					$tr_al = explode(";", $alumnos);
					$num_cols = count($tr_al);

					for ($i=0; $i < $num_cols; $i++) { 
						mysqli_query($db_con, $tr_al[$i]);
					}

					$SQL6 = "ALTER TABLE `alma` ADD PRIMARY KEY(`CLAVEAL`)";
					$result6 = mysqli_query($db_con, $SQL6);

					// Importamos los datos del fichero CSV (todos_alumnos.csv) en la tabla alma.

					$fp = fopen ($_FILES['archivo1']['tmp_name'] , "r" ) or die('<div align="center"><div class="alert alert-danger alert-block fade in">
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
							break;
						}
					}

					$n_col_tabla=0;
					$contar_c = mysqli_query($db_con,"show columns from alma");
					while($contar_col = mysqli_fetch_array($contar_c)){
						$n_col_tabla++;
					}

					if ($n_col_tabla != $num_col) {

						// Restauramos Copia de Seguridad porque Séneca ha modificado la estructura de RegAlum.txt
						mysqli_query($db_con, "insert into alma select * from alma_seg");
						echo '<br><div align="center"><div class="alert alert-danger alert-block fade in">
			            <button type="button" class="close" data-dismiss="alert">&times;</button>
						<h5>ATENCIÓN:</h5>
						No se han podido importar los datos de los alumnos porque Séneca ha modificado la estructura del archivo RegAlum.txt, bien porque ha añadido algún campo bien porque lo ha eliminado. Ahora mismo el archivo tiene '.$num_col.' campos de datos mientras que la tabla tiene '.$n_col_tabla.' columnas.
						<br>Se mantienen las tablas tal como estaban mientras actualizas la aplicación o lo comunicas a los desarrolladores para que estos puedan arreglar el asunto.
						</div></div><br />
						<div align="center">
						  <input type="button" value="Volver atrás" name="boton" onClick="history.back(2)" class="btn btn-inverse" />
						</div>';
						?>
									</div><!-- /.well -->

							</div><!-- /.col-sm-8 -->

						</div><!-- /.row -->

					</div><!-- /.container -->

					<?php include("../../pie.php");	?>

					<?php
					exit();
					}

					while (!feof($fp))
					{
						$num_linea++;

						$linea="";
						$lineasalto="";
						$dato="";
						$linea=fgets($fp);

						if ($num_linea > 8) {
							$lineasalto = "INSERT INTO alma VALUES (";
							$tr=explode("|",$linea);

							foreach ($tr as $valor){
								$dato.= "\"". mysqli_real_escape_string($db_con, trim(utf8_encode($valor))) . "\", ";
							}
							$dato=substr($dato,0,strlen($dato)-2);
							$lineasalto.=$dato;
							$lineasalto.=");";
							mysqli_query($db_con, $lineasalto);
						}
					}
					fclose($fp);

					// Descomprimimos el zip de las calificaciones en el directorio exporta/
					include('../../lib/pclzip.lib.php');

					// Borramos archivos antiguos
					$files = glob('../exporta/*');
					foreach($files as $file) {
						if(is_file($file) and stristr($file, "index")==FALSE)
						unlink($file);
					}

					$archive = new PclZip($_FILES['archivo2']['tmp_name']);
					if ($archive->extract(PCLZIP_OPT_PATH, '../exporta') == 0)
					{
						die("Error : ".$archive->errorInfo(true));
					}

					// Procesamos datos
					$crear = "ALTER TABLE  alma
			ADD  `COMBASI` VARCHAR( 250 ) NULL FIRST ,
			ADD  `APELLIDOS` VARCHAR( 40 ) NULL AFTER  `UNIDAD` ,
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

					// Eliminación de alumnos dados de baja
					$SQL4 = "DELETE FROM alma WHERE `unidad` = ''";
					$result4 = mysqli_query($db_con, $SQL4);

					include("exportacodigos.php");

					// Eliminamos alumnos sin asignaturas que tienen la matricula pendiente, y que no pertenecen a los Ciclos
					$SQL6 = "DELETE FROM alma WHERE (COMBASI IS NULL and (curso like '%E.S.O.%' or curso like '%Bach%' or curso like '%F.P.B%') and ESTADOMATRICULA not like 'Obtiene T%' and ESTADOMATRICULA not like 'Repit%' and ESTADOMATRICULA not like 'Promocion%' and ESTADOMATRICULA not like 'Pendiente de confirma%' and ESTADOMATRICULA not like 'Traslad%') or (COMBASI IS NULL and ESTADOMATRICULA like 'Traslad%')";
					$result6 = mysqli_query($db_con, $SQL6);

					// Eliminamos a los alumnoos de Ciclos con algun dato en estadomatricula
					//$SQL7 = "DELETE FROM alma WHERE ESTADOMATRICULA not like '' and ESTADOMATRICULA not like 'Obtiene T%' and ESTADOMATRICULA not like 'Repit%' and ESTADOMATRICULA not like 'Promocion%'  and ESTADOMATRICULA not like 'Pendiente de confirm%'";
					//mysqli_query($db_con, $SQL7);

					// Creamos una asignatura ficticia para que los alumnos sin Asignaturas puedan aparecer en las listas
					$SQL8 = "update alma set combasi = 'Sin_Asignaturas' where combasi IS NULL";
					mysqli_query($db_con, $SQL8);

					// Alumnos con hermanos
					include("crear_hermanos.php");

					// Asignaturas y alumnos con pendientes
					include("asignaturas.php");

					// Borramos archivos antiguos
					$files = glob('../exporta/*');
					foreach($files as $file) {
						if(is_file($file) and stristr($file, "index")==FALSE)
						unlink($file);
					}

				}
				else{
					echo '<div align="center"><div class="alert alert-danger alert-block fade in">
			            <button type="button" class="close" data-dismiss="alert">&times;</button>
						<legend>ATENCIÓN:</legend>
			Parece que te estás olvidando de enviar todos los archivos con los datos de los alumnos. Asegúrate de enviar ambos archivos descargados desde Séneca.
			</div></div><br />';
				}
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
