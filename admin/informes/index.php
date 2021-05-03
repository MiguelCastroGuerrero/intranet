<?php
require('../../bootstrap.php');

if (file_exists('../fechorias/config.php')) {
	include('../fechorias/config.php');
}

// Modificación de datos del alumno en la tabla Alma
if (isset($_POST['enviar_datos'])) {
	
	foreach ($_POST as $nombre => $valor) {
		${$nombre} = $valor;
	}

mysqli_query($db_con, "update alma set DNI='$DNI', fecha='$fecha', domicilio='$domicilio', localidad='$localidad', provinciaresidencia='$provinciaresidencia', telefono='$telefono', padre='$padre', telefonourgencia='$telefonourgencia', paisnacimiento='$paisnacimiento', correo='$correo', nacionalidad='nacionalidad', unidad='$unidad', dnitutor='$dnitutor', dnitutor2='$dnitutor2', nsegsocial='$nsegsocial' where claveal='$claveal'");
	
	$mensaje1 = 1;

	$todos = "Ver Informe Completo";

}
if(isset($_GET['todos'])){$todos = $_GET['todos'];}
if(isset($_GET['claveal'])){$claveal = $_GET['claveal'];}else{$claveal = $_POST['claveal'];}
if(isset($_GET['unidad'])){$unidad = $_GET['unidad'];}else{$unidad = $_POST['unidad'];}
if(isset($_POST['c_escolar'])){$c_escolar = $_POST['c_escolar'];}else{ $c_escolar=""; }
if(isset($_POST['nombre'])){$nombre = $_POST['nombre'];}else{ $nombre=""; }
if(isset($_POST['fecha1'])){$fecha1 = $_POST['fecha1'];}else{ $fecha1=""; }
if(isset($_POST['fecha2'])){$fecha2 = $_POST['fecha2'];}else{ $fecha2=""; }
if(isset($_POST['faltas'])){$faltas = $_POST['faltas'];}else{ $faltas=""; }
if(isset($_POST['faltasd'])){$faltasd = $_POST['faltasd'];}else{ $faltasd=""; }
if(isset($_POST['fechorias'])){$fechorias = $_POST['fechorias'];}else{ $fechorias=""; }
if(isset($_POST['notas'])){$notas = $_POST['notas'];}else{ $notas=""; }
if(isset($_POST['tutoria'])){$tutoria = $_POST['tutoria'];}else{ $tutoria=""; }
if(isset($_POST['horarios'])){$horarios = $_POST['horarios'];}else{ $horarios=""; }
if(isset($_POST['act_tutoria'])){$act_tutoria = $_POST['act_tutoria'];}else{ $act_tutoria=""; }


// Borramos alumno de la base de datos
if (isset($_GET['borrar']) and $_GET['borrar']==1) {
	mysqli_query($db_con, "delete from alma where claveal = '".$_GET['claveal']."'");
	if (mysqli_affected_rows($db_con)>0) {
		echo '<div align="center"><div class="alert alert-success alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
		El alumno ha sido eliminado de la base de datos del centro.
		</div></div>';
	}
}

$PLUGIN_DATATABLES = 1;
include('../../menu.php');
include("../informes/menu_alumno.php");


// COMPROBAMOS SI ES EL TUTOR
$esTutor = 0;
$result = mysqli_query($db_con, "SELECT * FROM FTUTORES WHERE tutor='".$_SESSION['profi']."' AND unidad = '$unidad'");
if (mysqli_num_rows($result)) $esTutor = 1;


if (file_exists(INTRANET_DIRECTORY . '/config_datos.php')) {
	if (!empty($c_escolar) && ($c_escolar != $config['curso_actual'])) {
		$exp_c_escolar = explode("/", $c_escolar);
		$anio_escolar = $exp_c_escolar[0];

		$db_con = mysqli_connect($config['db_host_c'.$anio_escolar], $config['db_user_c'.$anio_escolar], $config['db_pass_c'.$anio_escolar], $config['db_name_c'.$anio_escolar]);
		mysqli_query($db_con,"SET NAMES 'utf8'");
	}
	if (empty($c_escolar)){
		$c_escolar = $config['curso_actual'];
	}
}
else {
	$c_escolar = $config['curso_actual'];
}

if ($claveal) {
  	$result1 = mysqli_query($db_con, "SELECT DISTINCT apellidos, nombre, unidad, claveal, claveal1, numeroexpediente FROM alma WHERE claveal = '$claveal' ORDER BY apellidos");

	if ($row1 = mysqli_fetch_array($result1)) {
	  $claveal = $row1[3];
	  $unidad = $row1[2];
	  $claveal1 = $row1[4];
	  $apellido = $row1[0];
	  $nombrepil = $row1[1];
  }
}

$clave = explode(" --> ", $nombre);

if (!$claveal) {
	$claveal = $clave[1];
	$nombrealumno = explode(",",$clave[0]);
	$apellidos = $nombrealumno[0];
	$nombrepila = $nombrealumno[1];
	$apellido = trim($apellidos);
	$nombrepil = trim($nombrepila);
}
?>


	<div class="container">

		<!-- TITULO DE LA PAGINA -->
		<div class="page-header">
			<h2>Expediente académico del alumno/a <small> Curso académico: <?php echo $c_escolar?></small></h2>
			<h3><?php echo $apellido.', '.$nombrepil; ?></h3>
		</div>

		<?php 
		$nssocial = "";
		$seg_social = mysqli_query($db_con,"select nsegsocial from alma");
		if (mysqli_num_rows($seg_social)>0) { $nssocial = ", nsegsocial";	}
		$result = mysqli_query($db_con, "select distinct claveal, DNI, fecha, domicilio, localidad, provinciaresidencia, telefono, padre, matriculas, telefonourgencia, paisnacimiento, correo, nacionalidad, edad, curso, unidad, numeroexpediente, dnitutor, dnitutor2". $nssocial." from alma where claveal= '$claveal'"); ?>

		<?php if ($row = mysqli_fetch_array($result)):
		$nivel_alumno = $row['curso'];
		$tut = mysqli_query($db_con,"SELECT tutor FROM FTUTORES WHERE unidad = '".$row['unidad']."'");
		$tuto = mysqli_fetch_array($tut);
		$tr_tutor = explode(", ",$tuto['tutor']);
		$tutor = $tr_tutor[1]." ".$tr_tutor[0];

		if ($_SERVER['SERVER_NAME'] == "iesmonterroso.org") {
			$nombre_o = str_replace("Á", "A", $row1['nombre']);
			$apellidos_o = str_replace("Á", "A", $row1['apellidos']);
			$iniciales = strtolower(substr($nombre_o, 0,1).substr($apellidos_o, 0,1));
			$iniciales = str_ireplace($caracteres_no_permitidos, $caracteres_permitidos, $iniciales);
			$nombre = str_ireplace($caracteres_no_permitidos, $caracteres_permitidos, $nombre_o);
			$apellidos = str_ireplace($caracteres_no_permitidos, $caracteres_permitidos, $apellidos_o);

			$correo_gsuite = "al.".$row1['claveal'].'@'.$config['dominio'];
			$pass_gsuite = $iniciales.".".$row1['claveal'];

			$usuario_moodle = $row1['claveal'];
			$pass_moodle = substr(sha1($row1['claveal']),0,8);
		}
		else {
			$correo_gsuite = $row1['claveal'].'.alumno@'.$config['dominio'];
			$pass_gsuite = substr(sha1($row1['claveal']),0,8);

			$usuario_moodle = $row1['claveal'];
			$pass_moodle = substr(sha1($row1['claveal']),0,8);
		}

		?>

		<?php if ($mensaje1==1) { ?>
			<div align="center">
				<div class="alert alert-info alert-block fade in">
            	<button type="button" class="close" data-dismiss="alert">&times;</button>
					<p class="text-left">Los datos del alumno se han modificado correctamente en la base de datos de la Intranet. No olvides comunicar los cambios en la Administración del centro para actualizar los mismos datos en <strong>Séneca</strong>. Sólo entonces <em>la próxima actualización de alumnos registrará los cambios permanentemente</em>.</p>
				</div>
			</div>
		<?php } ?>
		<!-- SCAFFOLDING -->
		<div class="well">
		<div class="row">

			<!-- COLUMNA IZQUIERDA -->
			<div class="col-sm-2 text-center hidden-xs">
				<?php if ($foto = obtener_foto_alumno($claveal)): ?>
				<img class="img-thumbnail" src="../../xml/fotos/<?php echo $foto; ?>" style="width: 120px !important;" alt="<?php echo $apellido.', '.$nombrepil; ?>">
				<?php else: ?>
				<h2><span class="img-thumbnail far fa-user fa-fw fa-4x" style="width: 120px !important;"></span></h2>
				<?php endif; ?>
				<hr>
				<div align="left">
				<a class="btn btn-sm btn-primary btn-block" href="//<?php echo $config['dominio'];?>/intranet/admin/informes/cinforme.php?nombre_al=<?php echo $apellidos_o.", ".$nombre_o." --> ".$claveal;?>&unidad=<?php echo $unidad;?>"><i class="far fa-calendar fa-fw"></i> Informe histórico</a>
				<?php 
				if (stristr($_SESSION['cargo'],'1') == TRUE) { ?>
					<a class="btn btn-sm btn-danger btn-block" href="index.php?borrar=1&claveal=<?php echo $claveal;?>"  data-bs="tooltip" title="Esta acción borra el alumno de las tablas de alumnos de la Base de datos. Sólo utilizar en caso de una anomalía persistente y bien constatada (cuando el alumno aparece en la importación de datos de Séneca pero es absolutamente seguro que ya no está matriculado en el Centro, por ejemplo). Utilizar esta opción con mucho cuidado." data-bb="confirm-delete"><i class="far fa-trash-alt fa-fw\"></i> Borrar alumno</a>
				<?php } ?>
				</div>
			</div><!-- /.col-sm-2 -->
			
			<?php
			$fecha_nacimiento = new DateTime(cambia_fecha($row['fecha']));
			$hoy = new DateTime();
			$anos = $hoy->diff($fecha_nacimiento);
			$edad = $anos->y;
			?>
			<?php
			if(stristr($_SESSION['cargo'],'1') == TRUE) {
				$DNI = "<input class='form-control-sm col-md-12' type='text' name='DNI' value='".$row['DNI']."' />";
				$dnitutor = "<input class='form-control-sm col-md-12' type='text' name='dnitutor' value='".$row['dnitutor']."' />";
				$dnitutor2 = "<input class='form-control-sm col-md-12' type='text' name='dnitutor2' value='".$row['dnitutor2']."' />";
				$fecha = "<input class='form-control-sm col-md-12' type='text' name='fecha' value='".$row['fecha']."' />";
				$domicilio = "<input class='form-control-sm col-md-12' type='text' name='domicilio' value='".$row['domicilio']."' />";
				$localidad = "<input class='form-control-sm col-md-12' type='text' name='localidad' value='".$row['localidad']."' />";
				$provinciaresidencia = "<input class='form-control-sm col-md-12' type='text' name='provinciaresidencia' value='".$row['provinciaresidencia']."' />";
				$nacionalidad = "<input class='form-control-sm col-md-12' type='text' name='nacionalidad' value='".$row['nacionalidad']."' />";
				$telefono = "<input class='form-control-sm col-md-12' type='text' name='telefono' value='".$row['telefono']."' />";

				$telefonourgencia = "<a href='tel:<input class=\"form-control-sm col-md-12\" type=\"text\" name=\"telefono\" value=\"654258924\" />'><input class='form-control-sm col-md-12' type='text' name='telefonourgencia' value='".$row['telefonourgencia']."' /></a>";
				$unidad = "<input class='form-control-sm col-md-12' type='text' name='unidad' value='".$row['unidad']."' />";
				$correo = "<input class='form-control-sm col-md-12' type='text' name='correo' value='".$row['correo']."' />";
				$padre = "<input class='form-control-sm col-md-12' type='text' name='padre' value='".$row['padre']."' />";
				$nsegsocial = "<input class='form-control-sm col-md-12' type='text' name='nsegsocial' value='".$row['nsegsocial']."' />";
			}
			else{
				if (empty($row['DNI'])) {
					$DNI = '<span class="text-muted">Sin registrar</span>';
				}
				else{
					$DNI = $row['DNI'];
				}
				$dnitutor = $row['dnitutor'];
				if (empty($row['dnitutor2'])) {
					$dnitutor2 = '<span class="text-muted">Sin registrar</span>';
				}
				else{
					$dnitutor2 = $row['dnitutor2'];
				}
				$fecha = $row['fecha'];
				$domicilio = $row['domicilio'];
				$localidad = $row['localidad'];
				$provinciaresidencia = $row['provinciaresidencia'];
				$nacionalidad = $row['nacionalidad'];
				$telefono = $row['telefono'];
				if (empty($row['telefonourgencia'])) {
					$telefonourgencia = '<span class="text-muted">Sin registrar</span>';
				}
				else{
					$telefonourgencia = $row['telefonourgencia'];
				}
				$unidad = $row['unidad'];
				if (empty($row['correo'])) {
					$correo = '<span class="text-muted">Sin registrar</span>';
				}
				else{
					$correo = $row['correo'];
				}
				$padre = $row['padre'];
				if (empty($row['nsegsocial'])) {
					$nsegsocial = '<span class="text-muted">Sin registrar</span>';
				}
				else{
					$nsegsocial = $row['nsegsocial'];
				}
				
			}
			?>
			<!-- COLUMNA DERECHA -->
			<div class="col-sm-10">

				<div class="row">

					<form method="POST" action="index.php">

						<input type="hidden" value = "<?php echo $claveal; ?>" name="claveal">
						
						<div class="form-group">

							<div class="col-sm-6">

								<dl class="dl-horizontal">
								  <dt><abbr data-bs="tooltip" title="Número de Identificación Escolar">N.I.E.</abbr></dt>
								  <dd><?php echo ($row['claveal'] != "") ? $row['claveal']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Nº Expediente</dt>
								  <dd><?php echo ($row['numeroexpediente'] != "") ? $row['numeroexpediente']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Año académico</dt>
								  <dd><?php echo $c_escolar; ?></dd>						  
								  <dt>Fecha de nacimiento</dt>
								  <dd><?php echo ($row['fecha'] != "") ? $fecha: '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Edad</dt>
								  <dd><?php echo ($row['edad'] != "") ? $edad.' años': '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Domicilio</dt>
								  <dd><?php echo ($row['domicilio'] != "") ? $domicilio: '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Provincia de residencia</dt>
								  <dd><?php echo ($row['provinciaresidencia'] != "") ? $provinciaresidencia: '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Localidad</dt>
								  <dd><?php echo ($row['localidad'] != "") ? $localidad: '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Nacionalidad</dt>
								  <dd><?php echo ($row['nacionalidad'] != "") ? $nacionalidad: '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Teléfono</dt>
								  <dd><?php echo ($row['telefono'] != "") ? '<a href="tel:'.$telefono.'">'.$telefono.'</a>': '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Teléfono urgencias</dt>
								  <dd><?php echo $telefonourgencia; ?></dd>
								</dl>

							</div><!-- /.col-sm-6 -->

							<div class="col-sm-6">

								<dl class="dl-horizontal">
								  <dt>DNI / Pasaporte</dt>
								  <dd><?php echo $DNI; ?></dd>								  
								  <dt>Curso</dt>
								  <dd><?php echo ($row['curso'] != "") ? $row['curso']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Unidad</dt>
								  <dd><?php echo ($row['unidad'] != "") ? $unidad: '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Tutor</dt>
								  <dd><?php echo ($tutor != "") ? mb_convert_case($tutor, MB_CASE_TITLE, 'UTF-8'): '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>Repetidor/a</dt>
								  <dd><?php echo ($row['matriculas'] > 1) ? 'Sí': 'No'; ?></dd>
									<?php if (isset($config['convivencia']['puntos']['habilitado']) && $config['convivencia']['puntos']['habilitado']): ?>
									<dt>Puntos</dt>
									<dd><?php echo sistemaPuntos($row['claveal']); ?></dd>
									<?php endif; ?>									
								  <dt>Correo electrónico</dt>
								  <dd><?php echo $correo; ?></dd>								  	
								  <dt>Representante legal</dt>
								  <dd><?php echo ($row['padre'] != "") ? $padre: '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>DNI Tutor legal 1</dt>
								  <dd><?php echo ($row['dnitutor'] != "") ? $dnitutor: '<span class="text-muted">Sin registrar</span>'; ?></dd>
								  <dt>DNI Tutor legal 2</dt>
								  <dd><?php echo $dnitutor2; ?></dd>
								  <dt>Nº de Seguridad Social</dt>
								  <dd><?php echo $nsegsocial; ?></dd>
								  </dl>	
								  <?php 
								  if(stristr($_SESSION['cargo'],'1') == TRUE):
								  ?>					
								  <input class="form-group btn btn-info btn-sm pull-right" name="enviar_datos" type="submit" value="Modificar datos">	
								  <?php endif; ?>	  
							</div><!-- /.col-sm-6 -->
						</div><!-- /.form-group -->
					</form>
				</div><!-- /.row -->

				<?php if ((isset($config['mod_centrotic_moodle']) && $config['mod_centrotic_moodle']) || (isset($config['mod_centrotic_gsuite']) && $config['mod_centrotic_gsuite']) || (isset($config['mod_centrotic_office365']) && $config['mod_centrotic_office365'])): ?>
					<button class="btn btn-link btn-block" id="collapseButtonCredenciales" type="button" data-toggle="collapse" data-target="#collapseCredenciales" aria-expanded="false" aria-controls="collapseCredenciales"><span class="h5 mb-0 pb-0">Mostrar más <i class="fas fa-chevron-down fa-fw"></i></span></button>
					
					<div class="collapse pb-3" id="collapseCredenciales">

						<div class="row">
							<br>
							<?php if (isset($config['mod_centrotic_moodle']) && $config['mod_centrotic_moodle']): ?>
							<div class="col-sm-6">
								<h6 class="mb-3">
									Acceso a plataforma Moodle del Centro <a href="http://www.juntadeandalucia.es/averroes/centros-tic/<?php echo $config['centro_codigo']; ?>/moodle2/" target="_blank"><i class="fas fa-external-link-alt ml-1"></i></a>
								</h6>

								<dl class="dl-horizontal">
									<dt>Usuario</dt>
									<dd><?php echo $usuario_moodle; ?></dd>

									<dt>Contraseña</dt>
									<dd><?php echo $pass_moodle; ?></dd>
								</dl>
							</div>
							<?php endif; ?>

							<?php if ((isset($config['mod_centrotic_gsuite']) && $config['mod_centrotic_gsuite']) || (isset($config['mod_centrotic_office365']) && $config['mod_centrotic_office365'])): ?>
							<div class="col-sm-6">
								<h6 class="mb-3">
									<?php if (isset($config['mod_centrotic_gsuite']) && $config['mod_centrotic_gsuite']): ?>
									Acceso a Gmail / Classroom <a href="https://classroom.google.com/a/<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank"><i class="fas fa-external-link-alt ml-1"></i></a>
									<?php endif; ?>
									<?php if ((isset($config['mod_centrotic_gsuite']) && $config['mod_centrotic_gsuite']) && (isset($config['mod_centrotic_office365']) && $config['mod_centrotic_office365'])): ?>
									&nbsp;/&nbsp;
									<?php endif; ?>
									<?php if (isset($config['mod_centrotic_office365']) && $config['mod_centrotic_office365']): ?> 
									Microsoft 365 <a href="https://login.microsoftonline.com/?whr=<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank"><i class="fas fa-external-link-alt ml-1"></i></a>
									<?php endif; ?>
								</h6>

								<dl class="dl-horizontal">
									<dt>Usuario</dt>
									<dd><?php echo $correo_gsuite; ?></dd>

									<dt>Contraseña</dt>
									<dd><?php echo $pass_gsuite; ?></dd>
								</dl>
							</div>
							<?php endif; ?>

						</div>
					</div>
					<?php endif; ?>

			</div><!-- /.col-sm-10 -->

		</div><!-- /.row -->
		</div><!-- /.well -->


		<div class="row">

			<div class="col-sm-12">

				<style class="text/css">
				@media print {
					.tab-content>.tab-pane {
						display: block;
						visibility: inherit;
					}
				}
				</style>

				<ul class="nav nav-tabs nav-justified" role="tablist">
					<?php if (!($faltas == "" && $todos == "") && $config['mod_asistencia']): ?>
					<?php $tab1 = 1; ?>
				  <li <?php echo ($tab1) ? 'class="active"' : ''; ?>><a href="#asistencia" role="tab" data-toggle="tab">Asistencia</a></li>
				  <?php endif; ?>
				  <?php if (!($fechorias == "" && $todos == "")): ?>
				  <?php if(!isset($tab1)) $tab2 = 1; ?>
				  <li <?php echo ($tab2) ? 'class="active"' : ''; ?>><a href="#convivencia" role="tab" data-toggle="tab">Convivencia</a></li>
				  <?php endif; ?>
				  <?php if (!($notas == "" && $todos == "")): ?>
				  <?php if(!isset($tab1) && !isset($tab2)) $tab3 = 1; ?>
				  <li <?php echo ($tab3) ? 'class="active"' : ''; ?>><a href="#evaluaciones" role="tab" data-toggle="tab">Evaluaciones</a></li>
				  <?php endif; ?>
				  <?php if (!($tutoria == "" && $todos == "")): ?>
				  <?php if(!isset($tab1) && !isset($tab2) && !isset($tab3)) $tab4 = 1; ?>
				  <li <?php echo ($tab4) ? 'class="active"' : ''; ?>><a href="#tutoria" role="tab" data-toggle="tab">Tutoría</a></li>
				  <?php endif; ?>
				  <?php if (!($horarios == "" && $todos == "")): ?>
				  <?php if(!isset($tab1) && !isset($tab2) && !isset($tab3) && !isset($tab4)) $tab5 = 1; ?>
				  <li <?php echo ($tab5) ? 'class="active"' : ''; ?>><a href="#horario" role="tab" data-toggle="tab">Horario</a></li>
				  <?php endif; ?>
				  <?php if (!($act_tutoria == "" && $todos == "")): ?>
				  <?php if(acl_permiso($_SESSION['cargo'], array(1)) || (acl_permiso($_SESSION['cargo'], array(2)) && $esTutor) || acl_permiso($_SESSION['cargo'], array(8))): ?>
				  <?php if(!isset($tab1) && !isset($tab2) && !isset($tab3) && !isset($tab4) && !isset($tab5)) $tab6 = 1; ?>
				  <li <?php echo ($tab6) ? 'class="active"' : ''; ?>><a href="#intervenciones" role="tab" data-toggle="tab">Intervenciones</a></li>
				  <?php endif; ?>
				  <?php endif; ?>

				  <?php if ($config['mod_matriculacion']==1): ?>


				  <?php
				  	if (stristr($nivel_alumno, "E.S.O.")==TRUE) {
				  		$tabla_matriculas = "matriculas";
				  	}
				  	else{
				  		$tabla_matriculas = "matriculas_bach";
				  	}
				  	$mtr = mysqli_query($db_con,"select id from $tabla_matriculas where claveal = '$claveal'");
				  	if (mysqli_num_rows($mtr)>0){
				  	$id_mtr = mysqli_fetch_array($mtr);
				  	?>
				  	<li><a href="../matriculas/<?php echo $tabla_matriculas; ?>.php?id=<?php echo $id_mtr[0]; ?>" target="_blank">Matrícula</a></li>
				  	<?php } ?>





				  <?php endif; ?>
				</ul>

				<div class="tab-content">
				  <div class="tab-pane <?php echo ($tab1) ? 'active' : ''; ?>" id="asistencia">
				  <?php if (!($faltas == "" && $todos == "")) include("faltas.php"); ?>
				  <?php if (!($faltasd == "" && $todos == "")) include("faltasd.php"); ?>
				  </div>
				  <div class="tab-pane <?php echo ($tab2) ? 'active' : ''; ?>" id="convivencia">
				  <?php if (!($fechorias== "" and $todos == "")) include("fechorias.php"); ?>
				  </div>
				  <div class="tab-pane <?php echo ($tab3) ? 'active' : ''; ?>" id="evaluaciones">
				  <?php if (!($notas == "" and $todos == "")) include("notas.php"); ?>
				  </div>
				  <div class="tab-pane <?php echo ($tab4) ? 'active' : ''; ?>" id="tutoria">
				  <?php if (!($tutoria== "" and $todos == "")) include("tutoria.php"); ?>
				  </div>
				  <div class="tab-pane <?php echo ($tab5) ? 'active' : ''; ?>" id="horario">
				  <?php if (!($horarios== "" and $todos == "")) include("horarios.php"); ?>
				  </div>
				  <?php if(acl_permiso($_SESSION['cargo'], array(1)) || (acl_permiso($_SESSION['cargo'], array(2)) && $esTutor) || acl_permiso($_SESSION['cargo'], array(8))): ?>
				  <div class="tab-pane <?php echo ($tab6) ? 'active' : ''; ?>" id="intervenciones">
				  <?php if (!($act_tutoria== "" and $todos == ""))include("act_tutoria.php"); ?>
				  </div>
				  <?php endif; ?>
				 </div>

			</div>

		</div><!-- /.row -->

		<br>

		<div class="row hidden-print">

			<div class="col-sm-12">

				<a href="#" class="btn btn-primary" onclick="javascript:print();">Imprimir</a>
				<a href="cinforme.php" class="btn btn-default">Consultar otro informe</a>

			</div>

		</div>
		<?php else: ?>

		<h3>No hay información sobre el alumno/a en el curso seleccionado.</h3>

		<?php endif; ?>

	</div><!-- /.container -->


<?php include("../../pie.php");?>

</body>
</html>
