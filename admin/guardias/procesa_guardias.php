<?php
require('../../bootstrap.php');


function texto_turno($turno) {
	
	switch ($turno) {
		case 1 : $texto_turno = 'Hora completa'; break;
		case 2 : $texto_turno = 'Primera mitad de la hora'; break;
		case 3 : $texto_turno = 'Segunda mitad de la hora'; break;
	}
	
	return $texto_turno;
}


if (! isset($_POST['submit'])) {
	exit('No direct script access allowed');
}

// VARIABLES FORMULARIO
if (isset($_POST['profesor'])) $profesor = mysqli_real_escape_string($db_con, $_POST['profesor']);
if (isset($_POST['ausente'])) $ausente = mysqli_real_escape_string($db_con, $_POST['ausente']);
if (isset($_POST['fecha_guardia'])) $fecha_guardia = mysqli_real_escape_string($db_con, $_POST['fecha_guardia']);
if (isset($_POST['hora_guardia'])) $hora = mysqli_real_escape_string($db_con, $_POST['hora_guardia']);
if (isset($_POST['turno_guardia'])) $turno = mysqli_real_escape_string($db_con, $_POST['turno_guardia']);

// COMPROBACIÓN DEL FORMULARIO

// Campos vacíos
if (empty($_POST['profesor']) || empty($_POST['ausente']) || empty($_POST['fecha_guardia']) || empty($_POST['hora_guardia']) || empty($_POST['turno_guardia']) || (isset($_POST['diasdif']) && $_POST['diasdif'] > 2)) {

	$msg_error = "<h4>Error al procesar el formulario</h4>\n<ul>";

	if (empty($_POST['profesor'])) $msg_error .= "<li>Profesor/a de guardia - No has seleccionado el profesor que va a realizar la guardia.</li>\n";
	if (empty($_POST['ausente'])) $msg_error .= "<li>Profesor/a ausente - No has seleccionado el profesor que va a ausentarse.</li>\n";
	if (empty($_POST['fecha_guardia'])) $msg_error .= "<li>Fecha de la guardia - No has seleccionado la fecha de la guardia.</li>\n";
	if (empty($_POST['hora_guardia'])) $msg_error .= "<li>Hora de la guardia - No has seleccionado la hora de la guardia.</li>\n";
	if (empty($_POST['turno_guardia'])) $msg_error .= "<li>Turno de la guardia - No has seleccionado el turno de la guardia.</li>\n";
	if (isset($_POST['diasdif']) && $_POST['diasdif'] > 2) $msg_error .= "<li>Plazo de registro - Estás intentando registrar una guardia con dos días o más de diferencia respecto a la fecha de la guardia, y eso no es posible. Si por motivo justificado necesitas hacerlo, ponte en contacto con algún miembro del Equipo directivo.</li>\n";

	$msg_error .= "</ul>\n";
}
else {
	// Fechas
	$exp_fecha = explode('-', $fecha_guardia);
	$dia = $exp_fecha[0];
	$mes = $exp_fecha[1];
	$anno = $exp_fecha[2];
	$fechasql = $anno.'-'.$mes.'-'.$dia;
	$diasem = date('w', mktime(0, 0, 0, $mes, $dia, $anno));
	$fechahoy = date('Y-m-d H:i:s');
	
	// Comprobamos si el usuario está actualizando los datos o comprobando la validez del formulario.
	if (isset($_POST['accion'])) {
		
		if ($_POST['accion'] == 'actualizar') {
			if (isset($_POST['id'])) $id = mysqli_real_escape_string($db_con, $_POST['id']);
			
			$result = mysqli_query($db_con, "UPDATE guardias SET profesor = '$profesor', fecha = '".$fechahoy."', turno = $turno WHERE id = '".$id."'");
			if (! $result) $msg_error = "Ha ocurrido un error al actualizar los datos. Error: ".mysqli_error($db_con);
			else $msg_success = "Los datos de la guardia han sido registrados correctamente.\n";
		}
		
		if ($_POST['accion'] == 'asignar') {
			if (isset($_POST['id'])) $id = mysqli_real_escape_string($db_con, $_POST['id']);
			
			$result = mysqli_query($db_con, "INSERT INTO guardias (profesor, profe_aula, dia, hora, fecha, fecha_guardia, turno) VALUES ('$profesor', '$ausente', '$diasem', '$hora', '$fechahoy', '$fechasql', '$turno')");
			if (! $result) $msg_error = "Ha ocurrido un error al insertar los datos. Error: ".mysqli_error($db_con);
			else $msg_success = "Los datos de la guardia han sido registrados correctamente.\n";
		}
		
	}
	else {
		
		$result = mysqli_query($db_con, "SELECT id, profesor, profe_aula, hora, fecha, fecha_guardia, turno FROM guardias WHERE dia = '$diasem' AND hora = '$hora' AND fecha_guardia = '$fechasql' AND profe_aula = '$ausente'");

		$result_ya = mysqli_query($db_con, "SELECT * FROM guardias WHERE dia = '$diasem' AND hora = '$hora' AND fecha_guardia = '$fechasql' AND profesor = '$profesor'");
		
		if (mysqli_num_rows($result_ya)>0) {
			$msg_error = "<h4>Guardia registrada</h4>\n<p>No es posible registrar la sustitución de 2 guardias en la misma hora.</p>\n";
		}
		elseif ($num_reg = mysqli_num_rows($result)) {
		
			$row = mysqli_fetch_array($result);
			
			// Ya ha registrado la guardia a este profesor en el mismo turno
			if (nomprofesor($row['profesor']) == nomprofesor($profesor) && $row['turno'] == $turno) {
				
				$msg_error = "<h4>Guardia registrada</h4>\n<p>Ya ha sido registrada esta guardia.</p>\n";
				
			}
			
			// Ya ha registrado la guardia a este profesor pero en otro turno, se le da la opción de modificar el turno.
			elseif (nomprofesor($row['profesor']) == nomprofesor($profesor) && $row['turno'] != $turno) {
				
				$msg_warning = "<h4>Guardia registrada</h4>\n<p><strong>".nomprofesor($row['profe_aula'])."</strong> ya ha sido sustituido por <strong>".nomprofesor($row['profesor'])."</strong> el <strong>".strftime("%A, %e de %B de %Y", strtotime($fecha_guardia))."</strong> a <strong>".$row['hora']."ª hora</strong> en la ".texto_turno($row['turno']).". ¿Quieres cambiar el turno?</p>\n";
				
				$datos_registrados = array(
					'id' => $row['id'],
					'fecha' => $row['fecha'],
					'fecha_guardia' => $row['fecha_guardia'],
					'profesor' => nomprofesor($row['profesor']),
					'ausente' => nomprofesor($row['profe_aula']),
					'hora' => $row['hora'],
					'turno' => $row['turno'],
				);
				
				$actualizar_turno = 1;
				
			}
			
			// Ya ha registrado la guardia a otro profesor y la hora completa
			elseif (nomprofesor($row['profesor']) != nomprofesor($profesor) && $row['turno'] == 1) {
				$msg_error = "<h4>Guardia registrada</h4>\n<p><strong>".nomprofesor($row['profe_aula'])."</strong> ya ha sido sustituido por <strong>".nomprofesor($row['profesor'])."</strong> el <strong>".strftime("%A, %e de %B de %Y", strtotime($fecha_guardia))."</strong> a <strong>".$row['hora']."ª hora</strong>.</p>\n";
			}
			
			// Ya ha registrado la guardia a otro profesor pero no la hora completa
			elseif (nomprofesor($row['profesor']) != nomprofesor($profesor) && $row['turno'] != 1) {
				
				// Si hay varios registros significa que están asignados ambos turnos
				if ($num_reg > 1) {
				
					$msg_error = "<h4>Guardia registrada</h4>\n<p><strong>".nomprofesor($row['profe_aula'])."</strong> ya ha sido sustituido por <strong>".nomprofesor($row['profesor'])."</strong> el <strong>".strftime("%A, %e de %B de %Y", strtotime($fecha_guardia))."</strong> a <strong>".$row['hora']."ª hora</strong> en la ".texto_turno($row['turno']).".</p>\n";
					
					$row = mysqli_fetch_array($result); // Obtenemos el segundo registro
					
					$msg_error .= "<p><strong>".nomprofesor($row['profe_aula'])."</strong> ya ha sido sustituido por <strong>".nomprofesor($row['profesor'])."</strong> el <strong>".strftime("%A, %e de %B de %Y", strtotime($fecha_guardia))."</strong> a <strong>".$row['hora']."ª hora</strong> en la ".texto_turno($row['turno']).".</p>\n";

					
				}
				// En otro caso, ofrecemos la opción de registrar el turno disponible
				else {
					
					$turno = ($row['turno'] == 2) ? 3 : 2;
					
					$msg_warning = "<h4>Guardia registrada</h4>\n<p>Ya ha sido registrada esta guardia en este turno. Queda sin asignar el turno de la ".texto_turno($turno).". ¿Quieres asignar la guardia en el turno disponible?</p>\n";
				
					$datos_registrados = array(
						'id' => $row['id'],
						'fecha' => $row['fecha'],
						'fecha_guardia' => $row['fecha_guardia'],
						'profesor' => nomprofesor($row['profesor']),
						'ausente' => nomprofesor($row['profe_aula']),
						'hora' => $row['hora'],
						'turno' => $row['turno']
					);
					
					$asignar_turno = 1;
					
				}
				
			}
			
		}


		
		// No se han encontrado registros, por lo que insertamos en la base de datos los datos
		else {
			
			// Comprobamos si el profesor ausente ha registrado la ausencia
			$result = mysqli_query($db_con, "SELECT * FROM ausencias WHERE profesor = '$ausente' AND inicio <= '$fechasql' AND fin >= '$fechasql'");
			
			if (mysqli_num_rows($result)) {
				
				$row = mysqli_fetch_array($result);
				$horas = $row['horas'];
				$horas_array = explode(",",$row['horas']);
				if ($horas != 0 && $horas != '' && !in_array($hora, $horas_array)) {
					$horas_array[] = $hora;
					asort($horas_array);
					$horas_string = implode(',', $horas_array);
					//$horas = $horas.$hora;
					mysqli_query($db_con, "UPDATE ausencias SET horas = '$horas_string' WHERE id = '".$row['id']."'");
				}
			}
			else {
				mysqli_query($db_con, "INSERT INTO ausencias (profesor, inicio, fin, horas, tareas, ahora, archivo, Observaciones) VALUES ('$ausente', '$fechasql', '$fechasql', '$hora', '', '$fechahoy', '', NULL)");
			}
			
			// Insertamos en la base de datos de guardias
			$result = mysqli_query($db_con, "INSERT INTO guardias (profesor, profe_aula, dia, hora, fecha, fecha_guardia, turno) VALUES ('$profesor', '$ausente', '$diasem', '$hora', '$fechahoy', '$fechasql', '$turno')");
			if (! $result) $msg_error = "Ha ocurrido un error al insertar los datos. Error: ".mysqli_error($db_con);
			else $msg_success = "Los datos de la guardia han sido registrados correctamente.\n";
		}
	}

}


include("../../menu.php");
include("menu.php");
?>

<div class="container">

	<div class="page-header">
		<h2>Guardias de aula <small>Registrar guardia</small></h2>
	</div>

	<div class="row">

		<div class="col-sm-12">

			<?php if (isset($msg_error)): ?>
			<div class="alert alert-danger">
				<?php echo $msg_error; ?>
			</div>

			<a class="btn btn-primary" href="javascript:history.go(-1);">Volver atrás</a>
			<?php else: ?>
			
			<?php if (isset($msg_warning)): ?>
			<div class="alert alert-warning">
				<?php echo $msg_warning; ?>
			</div>
			<?php endif; ?>
			
			<?php if (isset($msg_success)): ?>
			<div class="alert alert-success">
				<?php echo $msg_success; ?>
			</div>
			<?php endif; ?>

			<h3>Información de la guardia</h3>

			<table class="table table-bordered table-striped">
				<?php if (isset($datos_registrados)): ?>
				<thead>
					<tr>
						<th class="col-sm-2">&nbsp;</th>
						<th>Guardia propuesta</th>
						<th>Guardia registrada</th>
					</tr>
				</thead>
				<?php endif; ?>
				<tbody>
					<tr>
						<th>Fecha de registro</th>
						<td><span class="text-success"><?php echo strftime("%e de %B de %Y a las %Rh", strtotime($fechahoy)); ?></span></td>
						<?php if (isset($datos_registrados)): ?>
						<td><span class="text-warning"><?php echo strftime("%e de %B de %Y a las %Rh", strtotime($datos_registrados['fecha'])); ?></span></td>
						<?php endif; ?>
					</tr>
					<tr>
						<th>Fecha de ausencia</th>
						<td><span class="text-success"><?php echo strftime("%A, %e de %B de %Y", strtotime($fecha_guardia)); ?></span></td>
						<?php if (isset($datos_registrados)): ?>
						<td><span class="text-warning"><?php echo strftime("%A, %e de %B de %Y", strtotime($datos_registrados['fecha_guardia'])); ?></span></td>
						<?php endif; ?>
					</tr>
					<tr>
						<th>Profesor de guardia</th>
						<td><span class="text-success"><?php echo $profesor; ?></span></td>
						<?php if (isset($datos_registrados)): ?>
						<td><span class="text-warning"><?php echo $datos_registrados['profesor']; ?></span></td>
						<?php endif; ?>
					</tr>
					<tr>
						<th>Profesor ausente</th>
						<td><span class="text-success"><?php echo $ausente; ?></span></td>
						<?php if (isset($datos_registrados)): ?>
						<td><span class="text-warning"><?php echo $datos_registrados['ausente']; ?></span></td>
						<?php endif; ?>
					</tr>
					<tr>
						<th>Hora</th>
						<td><span class="text-success"><?php echo $hora; ?>ª hora</span></td>
						<?php if (isset($datos_registrados)): ?>
						<td><span class="text-warning"><?php echo $datos_registrados['hora']; ?>ª hora</span></td>
						<?php endif; ?>
					</tr>
					<tr>
						<th>Turno</th>
						<td><span class="text-success"><?php echo texto_turno($turno); ?></span></td>
						<?php if (isset($datos_registrados)): ?>
						<td><span class="text-warning"><?php echo texto_turno($datos_registrados['turno']); ?></span></td>
						<?php endif; ?>
					</tr>
				</tbody>
			</table>
			
			<?php if (isset($datos_registrados)): ?>
			<div class="text-center">
				<form action="procesa_guardias.php" method="post">
					<input type="hidden" name="profesor" value="<?php echo $profesor; ?>">
					<input type="hidden" name="ausente" value="<?php echo $ausente; ?>">
					<input type="hidden" name="fecha_guardia" value="<?php echo $fecha_guardia; ?>">
					<input type="hidden" name="hora_guardia" value="<?php echo $hora; ?>">
					<input type="hidden" name="turno_guardia" value="<?php echo $turno; ?>">
					<input type="hidden" name="id" value="<?php echo $datos_registrados['id']; ?>">
					<?php if (isset($actualizar_turno) && $actualizar_turno == 1): ?>
					<input type="hidden" name="accion" value="actualizar">
					<button type="submit" class="btn btn-primary" name="submit">Actualizar datos</button>
					<?php endif; ?>
					<?php if (isset($asignar_turno) && $asignar_turno == 1): ?>
					<input type="hidden" name="accion" value="asignar">
					<button type="submit" class="btn btn-primary" name="submit">Asignar turno</button>
					<?php endif; ?>
					
					<a class="btn btn-default" href="javascript:history.go(-1);">Volver</a>
				</form>
			</div>
			
			<?php else: ?>
			
			<div class="text-center">
				<?php if (acl_permiso($_SESSION['cargo'], array(1))): ?>
				<a class="btn btn-primary" href="index_admin.php">Volver</a>
				<?php else: ?>
				<a class="btn btn-primary" href="index.php?diasem=<?php echo $diasem; ?>&hora=<?php echo $hora; ?>">Volver</a>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			
			<?php endif; // MSG_ERROR ?>
			
		</div>

	</div>

</div>
</div>
</div>

	<?php include("../../pie.php"); ?>

</body>
</html>
