<?php
require('../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1));

function limpiar_string($string)
{
	return trim(htmlspecialchars($string, ENT_QUOTES,'UTF-8'));
}

if (isset($_POST['btnGuardar'])) {
	
	$limiteDiasEvento	= limpiar_string($_POST['limiteDiasEvento']);
	$prefExamenes	= limpiar_string($_POST['prefExamenes']);
	$prefActividades	= limpiar_string($_POST['prefActividades']);
	$prefUltimoTrimestre	= limpiar_string($_POST['prefUltimoTrimestre']);

	// CREACIÓN DEL ARCHIVO DE CONFIGURACIÓN
	if($file = fopen('config.php', 'w+'))
	{
		fwrite($file, "<?php \r\n");
		
		fwrite($file, "\r\n// CONFIGURACIÓN MÓDULO DE CALENDARIO\r\n");
		fwrite($file, "\$config['calendario']['limiteDiasEvento']\t= $limiteDiasEvento;\r\n");
		fwrite($file, "\$config['calendario']['prefExamenes']\t= $prefExamenes;\r\n");
		fwrite($file, "\$config['calendario']['prefActividades']\t= $prefActividades;\r\n");
		fwrite($file, "\$config['calendario']['prefUltimoTrimestre']\t= $prefUltimoTrimestre;\r\n");


		
		fwrite($file, "\r\n\r\n// Fin del archivo de configuración");
		
		fclose($file);
		
		$msg_success = "Las preferencias han sido guardadas correctamente.";
	}
	
}

if (file_exists('config.php')) {
	include('config.php');
}


include("../menu.php");
include("menu.php");
?>

<div class="container">

	<div class="page-header">
		<h2>Calendario <small>Preferencias</small></h2>
	</div>
	
	<!-- MENSAJES -->
	<?php if (isset($msg_error)): ?>
	<div class="alert alert-danger alert-fadeout">
		<?php echo $msg_error; ?>
	</div>
	<?php endif; ?>
	
	<?php if (isset($msg_success)): ?>
	<div class="alert alert-success alert-fadeout">
		<?php echo $msg_success; ?>
	</div>
	<?php endif; ?>


	<div class="row">

		<div class="col-sm-12">
			
			<form class="form-horizontal" method="post" action="preferencias.php">
				
				<div class="well">
					
					<fieldset>
						<legend>Preferencias</legend>
						
						<div class="form-group">
							<label for="limiteDiasEvento" class="col-sm-8 control-label">Permitir que los profesores registren actividades extraescolares 7 días antes de que se realice la actividad</label>
							<div class="col-sm-3">
								<select class="form-control" id="limiteDiasEvento" name="limiteDiasEvento">
									<option value="0" <?php echo (isset($config['calendario']['limiteDiasEvento']) && $config['calendario']['limiteDiasEvento'] == 0) ? 'selected' : ''; ?>>Prohibir</option>
									<option value="1" <?php echo (isset($config['calendario']['limiteDiasEvento']) && $config['calendario']['limiteDiasEvento'] == 1) ? 'selected' : ''; ?>>Permitir</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="prefExamenes" class="col-sm-8 control-label">Permitir el registro de más de 1 Actividad de grupo (Examen, Control, etc.) en el mismo día</label>
							<div class="col-sm-3">
								<select class="form-control" id="prefExamenes" name="prefExamenes">	
									<option value="1" <?php echo (isset($config['calendario']['prefExamenes']) && $config['calendario']['prefExamenes'] == 1) ? 'selected' : ''; ?>>Permitir</option>
									<option value="0" <?php echo (isset($config['calendario']['prefExamenes']) && $config['calendario']['prefExamenes'] == 0) ? 'selected' : ''; ?>>Prohibir</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="prefActividades" class="col-sm-8 control-label">Permitir el registro de más de 1 Actividad de grupo (Examen, Control, etc.) cuando este ya tiene registrada 1 Actividad Complementaria/Extraescolar para ese mismo día</label>
							<div class="col-sm-3">
								<select class="form-control" id="prefActividades" name="prefActividades">
									<option value="1" <?php echo (isset($config['calendario']['prefActividades']) && $config['calendario']['prefActividades'] == 1) ? 'selected' : ''; ?>>Permitir</option>
									<option value="0" <?php echo (isset($config['calendario']['prefActividades']) && $config['calendario']['prefActividades'] == 0) ? 'selected' : ''; ?>>Prohibir</option>									
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="prefUltimoTrimestre" class="col-sm-8 control-label">Permitir el registro de Actividades Complementarias/Extraescolares durante el tercer trimestre</label>
							<div class="col-sm-3">
								<select class="form-control" id="prefUltimoTrimestre" name="prefUltimoTrimestre">
									<option value="1" <?php echo (isset($config['calendario']['prefUltimoTrimestre']) && $config['calendario']['prefUltimoTrimestre'] == 1) ? 'selected' : ''; ?>>Permitir</option>
									<option value="0" <?php echo (isset($config['calendario']['prefUltimoTrimestre']) && $config['calendario']['prefUltimoTrimestre'] == 0) ? 'selected' : ''; ?>>Prohibir</option>									
								</select>
							</div>
						</div>
						
					</fieldset>
					
				</div>
				
				<button type="submit" class="btn btn-primary" name="btnGuardar">Guardar cambios</button>
				<?php if (isset($_GET['esAdmin']) && $_GET['esAdmin'] == 1): ?>
				<a href="../../../xml/index.php" class="btn btn-default">Volver</a>
				<?php else: ?>
				<a href="index.php" class="btn btn-default">Volver</a>
				<?php endif; ?>
			
			</form>
		
		</table>

		</div>

	</div>

</div>

<?php include("../../pie.php"); ?>

</body>
</html>
