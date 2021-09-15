<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array('z', '1'));

include("../../menu.php");
?>

<div class="container">

	<div class="page-header">
	  <h2>Administración <small> Departamentos del Centro</small></h2>
	</div>

	<div id="status-loading" class="text-center">
		<span class="lead"><span class="far fa-circle-o-notch fa-spin"></span> Cargando...</span>
	</div>

	<div id="wrap" class="row" style="display: none;">

		<div class="col-sm-8 col-sm-offset-2">

			<div class="well">
				<?php
				if(isset($_FILES['archivo'])){
				// BacKup de la tabla
				mysqli_query($db_con, "drop table departamentos_seg");
				mysqli_query($db_con, "create table departamentos_seg select * from departamentos");

				 //  Estructura de tabla para la tabla `departamento_temp`
				mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `departamento_temp` (
				  `NOMBRE` varchar(64) DEFAULT NULL,
				  `DNI` varchar(10) DEFAULT NULL,
				  `DEPARTAMENTO` varchar(80) DEFAULT NULL,
				  `CARGO` varchar(10) DEFAULT NULL,
				  `idea` varchar(12) NOT NULL DEFAULT '',
				  `fechatoma` DATE NOT NULL DEFAULT '0000-00-00',
				  `fechacese` DATE NOT NULL DEFAULT '0000-00-00',
				  PRIMARY KEY (`idea`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;");

				if(! isset($_POST['actualizar'])){
					mysqli_query($db_con, "DELETE FROM departamentos WHERE idea <> 'admin' AND departamento <> 'Administracion' AND departamento <> 'Conserjeria' AND departamento <> 'Educador' AND departamento <> 'Servicio Técnico y/o Mantenimiento' AND cargo NOT LIKE '%1%'");
				}


				// Importamos los datos del fichero CSV
				$handle = fopen ($_FILES['archivo']['tmp_name'] , "r" ) or die('<br /><div align="center"><div class="alert alert-danger alert-block fade in">
				            <button type="button" class="close" data-dismiss="alert">&times;</button>
							<h5>ATENCIÓN:</h5>
				No se ha podido abrir el archivo RelPerCen.txt. O bien te has olvidado de enviarlo o el archivo está corrompido.
				</div></div><br />
				<div align="center">
				  <input type="button" value="Volver atrás" name="boton" onClick="history.back(2)" class="btn btn-inverse" />
				</div><br />');
				$fila = 0;
				while (($data1 = fgetcsv($handle, 1000, "|")) !== FALSE)
				{
					$fila++;

					if ($fila > 8) {
						$dep_mod = trim(utf8_encode($data1[2]));
						$dep_mod = str_replace("(Inglés)","",$dep_mod);
						$dep_mod = str_replace("(Francés)","",$dep_mod);
						$dep_mod = str_replace("(Alemán)","",$dep_mod);
						$dep_mod = str_replace(" P.E.S.","",$dep_mod);
						$dep_mod = str_replace(" P.T.F.P.","",$dep_mod);
						$dep_mod = str_replace("(Secundaria)","",$dep_mod);
						$dep_mod = trim($dep_mod);

						$fechatoma_exp = explode('/', trim(utf8_encode($data1[3])));
						$fechatoma = $fechatoma_exp[2].'-'.$fechatoma_exp[1].'-'.$fechatoma_exp[0];
						if ($data1[4] != "") {
							$fechacese_exp = explode('/', trim(utf8_encode($data1[4])));
							$fechacese = $fechacese_exp[2].'-'.$fechacese_exp[1].'-'.$fechacese_exp[0];
						}

						if ($fechacese == "--") {
							$fechacese = "0000-00-00";
						}

						$datos1 = "INSERT INTO `departamento_temp` (`nombre`, `dni`, `departamento`, `idea`, `fechatoma`, `fechacese`) VALUES (\"". trim(utf8_encode($data1[0])) . "\",\"". trim(utf8_encode($data1[1])) . "\",\"". $dep_mod . "\",\"". trim(utf8_encode($data1[6])) . "\",\"". $fechatoma . "\",\"". $fechacese . "\")";
						mysqli_query($db_con, $datos1);
					}
				}
				fclose($handle);
				$borrarvacios = "delete from departamento_temp where DNI = ''";
				mysqli_query($db_con, $borrarvacios);
				$borrarpuesto = "delete from departamento_temp where DEPARTAMENTO LIKE '%Puesto%'";
				mysqli_query($db_con, $borrarpuesto);
				// Eliminar duplicados e insertar nuevos
				$elimina = "SELECT DISTINCT nombre, dni, departamento, idea, fechatoma, fechacese FROM departamento_temp where dni NOT IN (SELECT DISTINCT dni FROM departamentos WHERE idea <> 'admin' AND departamento <> 'Administracion' AND departamento <> 'Conserjeria' AND departamento <> 'Servicio Técnico y/o Mantenimiento')";
				$elimina1 = mysqli_query($db_con, $elimina);
				 if(mysqli_num_rows($elimina1) > 0)
				{
				echo "
				<br /><div align='center'><div class='alert alert-success alert-block fade in'>
				            <button type='button'' class='close' data-dismiss='alert'>&times;</button>
				Tabla <strong>Departamentos</strong>: los siguientes Profesores han sido añadidos a la tabla. <br>Comprueba los registros creados:</div></div>";
				while($elimina2 = mysqli_fetch_array($elimina1))
				{
				echo "<li>".$elimina2[0] . " -- " . $elimina2[1] . " -- " . $elimina2[2] . "</li>";
				  $SQL6 = "insert into departamentos (nombre, dni, departamento, idea, fechatoma, fechacese) VALUES (\"". $elimina2[0] . "\",\"". $elimina2[1] . "\",\"". $elimina2[2] . "\",\"". $elimina2[3] . "\",\"". $elimina2[4] . "\",\"". $elimina2[5] . "\")";
				  $result6 = mysqli_query($db_con, $SQL6);
				}
				echo "<br />";
				}
				else {
					echo '<br /><div align="center"><div class="alert alert-warning alert-block fade in">
				            <button type="button" class="close" data-dismiss="alert">&times;</button>
							<h5>ATENCIÓN:</h5>
				Tabla <strong>Departamentos</strong>: No se ha añadido ningún registro a la tabla.
				</div></div>';
					}

				// Actualizamos nombre de los departamentos en la tabla y tablas relacionadas
				include("actualiza_dep.php");
				// Registramos los tutores desde FTUTORES
				$tut0=mysqli_query($db_con, "select distinct tutor from FTUTORES");
				while($tut=mysqli_fetch_array($tut0))
				{
				$cargo0=mysqli_query($db_con, "select cargo from departamentos where nombre = '$tut[0]'");
				$cargo1=mysqli_fetch_array($cargo0);
				$cargo_tutor="2".$cargo1[0];
				if(strstr($cargo1[0],"2")==TRUE){}else{
				mysqli_query($db_con, "update departamentos set cargo = '$cargo_tutor' where  nombre='$tut[0]'");
				}
				}

				// ACTUALIZAMOS FECHA DE TOMA Y FECHA DE CESE
				$handle = fopen ($_FILES['archivo']['tmp_name'] , "r" );
				while (($data1 = fgetcsv($handle, 1000, "|")) !== FALSE) {

					$usridea = trim(utf8_encode($data1[6]));
					$fechatoma_exp = explode('/', trim(utf8_encode($data1[3])));
					$fechatoma = $fechatoma_exp[2].'-'.$fechatoma_exp[1].'-'.$fechatoma_exp[0];
					$fechacese_exp = explode('/', trim(utf8_encode($data1[4])));
					$fechacese = $fechacese_exp[2].'-'.$fechacese_exp[1].'-'.$fechacese_exp[0];

					mysqli_query($db_con, "UPDATE departamentos SET fechatoma = '$fechatoma', fechacese = '$fechacese' WHERE idea = '$usridea'");
				}
				fclose($handle);

				// Usuario
				  // Actualización de IDEA de los Profesores del Centro.
				$SQL1 = "select distinct nombre, dni, idea, fechacese from departamentos where nombre NOT IN (select distinct profesor from c_profes) and departamento <> 'Conserjeria' and idea <> 'admin'";
				$result1 = mysqli_query($db_con, $SQL1);
				$total = mysqli_num_rows($result1);
				if ($total !== 0)
				{
					echo "<div class='form-group success'><p class='help-block' style='text-align:left'>Tabla <strong>c_profes</strong>: los nuevos Profesores han sido añadidos a la tabla de usuarios de la Intranet. <br>Comprueba en la lista de abajo los registros creados:</p></div>";
				while  ($row1= mysqli_fetch_array($result1))
				 {
					 if (! empty($row1['fechacese']) && $row1['fechacese'] != "0000-00-00" && (strtotime(date('Y-m-d')) >= strtotime($row1['fechacese']))) $estado = 1;
					 else $estado = 0;

				$SQL2 = "INSERT INTO c_profes (profesor, dni, pass, idea, estado) VALUES (\"". $row1[0]. "\",\"". $row1[1] . "\",\"". sha1($row1[1]) . "\",\"". $row1[2] . "\", \"". $estado ."\")";
				echo "<li>".$row1[0] . "</li>";
				$result2 = mysqli_query($db_con, $SQL2);
				}
				echo "<br />";
				}

				mysqli_query($db_con, "drop table departamento_temp");

				//------------------------------------------------------------------------------------------------------------
				//  Profesores TIC
					$borrar = "truncate table usuarioprofesor";
					mysqli_query($db_con, $borrar);
				// Primera parte, trabajamos sobre alma, que se actualiza regularmente.
				$profesores = "select distinct nombre, idea from departamentos";
				$sqlprof = mysqli_query($db_con, $profesores);
				while ($sqlprof0 = mysqli_fetch_array($sqlprof)) {
					$nombreorig = $sqlprof0[0];
					$usuario = $sqlprof0[1];
					$insertar = "insert into usuarioprofesor set nombre = '$nombreorig', usuario = '$usuario', perfil = 'p'";
					mysqli_query($db_con, $insertar);
				}
				$repetidos = mysqli_query($db_con, "select usuario from usuarioprofesor");
				while($num = mysqli_fetch_row($repetidos))
				{
				$n_a = "";
				$repetidos1 = mysqli_query($db_con, "select usuario, nombre from usuarioprofesor where usuario = '$num[0]'");
				if (mysqli_num_rows($repetidos1) > 1) {
				while($num1 = mysqli_fetch_row($repetidos1))
				{
				$n_a = $n_a +1;
				$nuevo = $num1[0].$n_a;
				mysqli_query($db_con, "update usuarioprofesor set usuario = '$nuevo' where nombre = '$num1[1]'");
				}
				}
				}
				mysqli_query($db_con, "delete from usuarioprofesor where usuario like 'pprofesor%'");
				}
				else{
					echo '<br /><div align="center"><div class="alert alert-danger alert-block fade in">
				            <button type="button" class="close" data-dismiss="alert">&times;</button>
							<h5>ATENCIÓN:</h5>
				Parece que te está olvidando de enviar el archivo con los datos de los Profesores. Asegúrate de enviar el archivo descargado desde Séneca.
				</div></div><br />';
				}

				// CALENDARIO
				$result = mysqli_query($db_con, "SELECT nombre, idea FROM departamentos");
				while ($row = mysqli_fetch_assoc($result)) {
					$exp_nombre = explode(',', $row['nombre']);
					$nombre = trim($exp_nombre[1]);
					if ($nombre == '') {
						$exp_nombre = explode(' ', $row['nombre']);
						$nombre = trim($exp_nombre[0]);
					}
					$idea = $row['idea'];


					$calendarioExiste = mysqli_query($db_con, "SELECT id FROM calendario_categorias WHERE profesor='$idea'");
					if (! mysqli_num_rows($calendarioExiste)) {
						$query = "INSERT INTO `calendario_categorias` (`nombre`, `fecha`, `profesor`, `color`, `espublico`) VALUES ('$nombre', '".date('Y-m-d')."', '$idea', '#3498db', 0)";
						mysqli_query($db_con, $query);
					}
					mysqli_free_result($calendarioExiste);
				}
				mysqli_free_result($result);

				?>

				<div class="text-center">
					 <a href="../index.php" class="btn btn-primary">Volver a Administración</a>
				</div>

			</div><!-- /.well -->

		</div><!-- /.col-sm-8 -->

	</div><!-- /.row -->

</div><!-- /.container -->

<?php include("../../pie.php");	?>

<script>
function espera() {
	document.getElementById("wrap").style.display = '';
	document.getElementById("status-loading").style.display = 'none';
}
window.onload = espera;
</script>

</body>
</html>
