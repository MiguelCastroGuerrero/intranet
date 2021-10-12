<?php
require('../../../bootstrap.php');

// CAMBIOS EN LA TABLA
$obs = mysqli_query($db_con, "select observaciones from informe_pendientes");
if(mysqli_num_rows($obs)>0){}else{mysqli_query($db_con, "ALTER TABLE `informe_pendientes` CHANGE `fecha` `observaciones` TEXT NULL DEFAULT NULL");}

// BORRAR INFORMES
if (isset($_GET['borrar']) AND $_GET['borrar'] == 1) {
	$result = mysqli_query($db_con,"delete from informe_pendientes where id_informe= '".$_GET['id_informe']."'");
	$result2 = mysqli_query($db_con,"delete from informe_pendientes_contenidos where id_informe= '".$_GET['id_informe']."'");
	if (! $result) {
		$msg_error = "No se ha podido eliminar el informe. Error: ".mysqli_error($db_con);
		}
}

// COMPROBAMOS SI SE ENÍA INFORME EXISTENTE
if (isset($_GET['id_informe'])) {
	$id_informe = $_GET['id_informe'];
	$edicion  = mysqli_fetch_array(mysqli_query($db_con, "select * from informe_pendientes where id_informe = '$id_informe'"));
	$edita = 1;
	$curso = limpiarInput($edicion['curso'], 'alphanumericspecial');
	$materia = limpiarInput($edicion['asignatura'], 'alphanumericspecial');
	$observaciones = $edicion['observaciones'];	
}

$extra_dep1="";
$extra_prof1="";

// Si es jefe de departamento
	$extra_dep1 = " OR ";
	$extra_prof1 = " OR ";
	$dep1 = mysqli_query($db_con, "SELECT distinct materia, nivel from profesores where profesor in (select distinct nombre from departamentos where departamento like '".$_SESSION['dpt']."')");
	while ($row_dep1 = mysqli_fetch_array($dep1)) {
		$extra_dep1.=" (asignatura like '$row_dep1[0]' and curso = '$row_dep1[1]') OR";
		$extra_prof1.=" (materia like '$row_dep1[0]' and nivel = '$row_dep1[1]') OR";
	}
	$extra_dep1 = substr($extra_dep1,0,-3);
	$extra_prof1 = substr($extra_prof1,0,-3);


// OBTENEMOS LOS NIVELES DONDE IMPARTE MATERIA EL PROFESOR
$cursos = array();
$result = mysqli_query($db_con, "SELECT DISTINCT `nivel` FROM `profesores` WHERE `profesor` = '".$_SESSION['profi']."' $extra_prof1 order by nivel ASC");
while ($row = mysqli_fetch_array($result)) {
	if(stristr($row['nivel'],'4')==FALSE){
	array_push($cursos, $row['nivel']);
		}
}

// COMPROBAMOS SI SE HA SELECCIONADO EL CURSO
if (isset($_POST['curso']) && in_array($_POST['curso'], $cursos)) {
	$curso = limpiarInput($_POST['curso'], 'alphanumericspecial');
}

// OBTENEMOS LAS MATERIAS QUE IMPARTE EL PROFESOR
$materias = array();
if (isset($curso)) {
	$result = mysqli_query($db_con, "SELECT distinct `materia`, `nivel` FROM `profesores` WHERE (`profesor` = '".$_SESSION['profi']."' $extra_prof1) AND `nivel` = '".$curso."'");
}
else {
	$result = mysqli_query($db_con, "SELECT distinct `materia`, `nivel` FROM `profesores` WHERE (`profesor` = '".$_SESSION['profi']."' $extra_prof1)");
}
while ($row = mysqli_fetch_array($result)) {
	array_push($materias, $row['materia']);
}


// COMPROBAMOS SI SE HA SELECCIONADO LA MATERIA
if (isset($_POST['asignatura']) && in_array($_POST['asignatura'], $materias)) {
	$materia = limpiarInput($_POST['asignatura'], 'alphanumericspecial');
}

//
if (!empty($_POST['curso']) && !empty($_POST['asignatura']) and $_POST['crear_informe']!="Actualizar informe") {
	$observa = mysqli_query($db_con, "select observaciones from informe_pendientes where asignatura='".$_POST['asignatura']."' and curso='".$_POST['curso']."'");
	if(mysqli_num_rows($observa)>0){
		$ya_obs = mysqli_fetch_array($observa);
		$observaciones = $ya_obs[0];
	}
}

// CREAR INFORME
if (isset($_POST['crear_informe'])) {

	// Variables
	$profesor = $_SESSION['ide'];
	$curso = limpiarInput($_POST['curso'], 'alphanumericspecial');
	$materia = limpiarInput($_POST['asignatura'], 'alphanumericspecial');
	$observaciones = $_POST['observaciones'];

	$hay_informe  = mysqli_query($db_con, "select * from informe_pendientes where asignatura = '$materia' and curso = '$curso'");
	if (mysqli_num_rows($hay_informe)>0) {
		$id_inf = mysqli_fetch_array($hay_informe);
		$id_informe = $id_inf['id_informe'];
		$result = mysqli_query($db_con, "update `informe_pendientes` set observaciones='".$observaciones."' where id_informe = '".$id_informe."'");
		if (! $result) {
			$msg_error = "No se ha podido actualizar el informe. Error: ".mysqli_error($db_con);
		}
		else {
			$msg_success = "Se ha actualizado el informe correctamente.";
		}
	}	
	else{
		$result = mysqli_query($db_con, "INSERT INTO `informe_pendientes` (`profesor`, `asignatura`,  `observaciones`, `curso`) VALUES ('".$profesor."', '".$materia."', '".$observaciones."', '".$curso."')");
		if (! $result) {
			$msg_error = "No se ha podido crear el informe. Error: ".mysqli_error($db_con);
		}
		else {
			$id_informe = mysqli_insert_id($db_con);
			header('Location:'.'contenidos.php?id_informe='.$id_informe);
			exit();
		}
	}
}

include("../../../menu.php");
include("menu.php");
?>
	
	<div class="container">
		
		<div class="page-header">
			<h2>Informe individual para alumnos con materias pendientes <small> <br>Mis informes</small></h2>
		</div>

		<?php if(isset($msg_error) && $msg_error): ?>
		<div class="alert alert-danger alert-fadeout">
			<?php echo $msg_error; ?>
		</div>
		<?php endif; ?>
		<?php if(isset($msg_success) && $msg_success): ?>
		<div class="alert alert-success alert-fadeout">
			<?php echo $msg_success; ?>
		</div>
		<?php endif; ?>

		<div class="row">

			<div class="col-sm-7">
				
				<?php 
				
				// OBTENEMOS LAS UNIDADES DONDE IMPARTE MATERIA EL PROFESOR
				$informes = array();
				$result = mysqli_query($db_con, "SELECT `id_informe`, `asignatura`, `curso`, `observaciones` FROM `informe_pendientes` WHERE profesor like '".$_SESSION['ide']."' $extra_dep1 ORDER BY curso ASC");
				while ($row = mysqli_fetch_array($result)) {
					$informe = array(
						'id_informe' => $row['id_informe'],
						'asignatura' => $row['asignatura'],
						'curso' => $row['curso'],
						'observaciones' => $row['observaciones']
					);

					array_push($informes, $informe);

					unset($informe);
				}

				
				?>
				<legend>Mis informes</legend>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Asignatura</th>
							<th>Curso</th>
							<th ></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($informes as $informe): ?>
						<tr>
							<td><?php echo $extra_mod; ?> &nbsp;<a href="index.php?id_informe=<?php echo $informe['id_informe']; ?>"  data-bs="tooltip" title="Editar los datos de este informe."  style="text-decoration: none;"><?php echo $informe['asignatura']; ?></a></td>
							<td><?php echo $informe['curso']; ?></td>
							<td class="pull-right" nowrap="nowrap">
								
								<a href="//<?php echo $config['dominio']; ?>/intranet/admin/informes/pendientes/contenidos.php?id_informe=<?php echo $informe['id_informe']; ?>" data-bs="tooltip" title="Modificar los contenidos y actividades de este informe."><span class="text-info fas fa-edit fa-fw fa-lg"></span></a>&nbsp;
								
								<a href="//<?php echo $config['dominio']; ?>/intranet/admin/informes/pendientes/index.php?borrar=1&id_informe=<?php echo $informe['id_informe']; ?>" data-bb="confirm-delete" data-bs="tooltip" title="Borrar este informe"><span class="text-danger far fa-trash-alt fa-fw fa-lg"></span></a>
								
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

			</div><!-- /.col-sm-12 -->
			
			<div class="col-sm-5">
				
				<legend>Nuevo informe</legend>
				
				<div class="well">
					
					<form action="index.php" method="post">

						<fieldset>

							<div class="form-group">
								<label for="curso">Curso</label>
								<select id="curso" name="curso" class="form-control" onchange="submit()" required>
									<option value=""></option>
									<?php foreach ($cursos as $curso_cmp): ?>
									<option value="<?php echo $curso_cmp; ?>" <?php echo ($curso_cmp == $curso) ? 'selected': ''; ?>><?php echo $curso_cmp; ?></option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="form-group">
								<label for="asignatura">Asignatura</label>
								<select id="asignatura" name="asignatura" class="form-control" onchange="submit()" required>
									<option value=""></option>
									<?php if (isset($curso) OR $edita==1): ?>
									<?php foreach ($materias as $materia_cmp): ?>
									<option value="<?php echo $materia_cmp; ?>" <?php echo ($materia_cmp == $materia) ? 'selected': ''; ?>><?php echo $materia_cmp; ?></option>
									<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>

								<div class="form-group">
									<label for="observaciones">Observaciones</label>
										<textarea class="form-control" id="observaciones" name="observaciones" rows="8" placeholder="Utiliza este espacio para precisar detalles como las fechas de entrega de actividades, exámenes, etc." ><?php if($edita==1 OR isset($observaciones)){ echo $observaciones;} ?></textarea>
								</div>

							<br>

							<div class="form-group">
								<input type="submit" class="btn btn-primary" value="<?php if($edita==1){ echo 'Actualizar informe';} else{ echo 'Crear informe'; }?>" name="crear_informe">
							</div>

						</fieldset>

					</form>


				</div><!-- /.well -->

			</div><!-- /.col-sm-6 -->

		</div><!-- /.row -->
		


	</div>

	<?php include("../../../pie.php"); ?>

	<script>
	$(function() {
		// DATETIMEPICKERS
		$('.datetimepicker1').datetimepicker({
			language: 'es',
			pickTime: true,
			inline: true,
            sideBySide: true
		});
	});
	</script>

</body>
</html>