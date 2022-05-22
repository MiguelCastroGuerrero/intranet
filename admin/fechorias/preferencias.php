<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1));

function limpiar_string($string)
{
	return trim(htmlspecialchars($string, ENT_QUOTES,'UTF-8'));
}


if (isset($_POST['btnGuardar'])) {
	include('config.php');

	$prefConvivenciaSeneca	= $_POST['prefConvivenciaSeneca'];
	$prefNotificacionPadres	= limpiar_string($_POST['prefNotificacionPadres']);
	$prefNotificacionLevesPadres = limpiar_string($_POST['prefNotificacionLevesPadres']);
	$prefMostrarDescripcion	= limpiar_string($_POST['prefMostrarDescripcion']);
	$prefListadosDireccion	= limpiar_string($_POST['prefListadosDireccion'], 'numeric');
	$prefCompromisoConvivencia	= $_POST['prefCompromisoConvivencia'];
	$prefPuntosHabilitado	= $_POST['prefPuntosHabilitado'];
	$prefPuntosMaximo	= $_POST['prefPuntosMaximo'];
	$prefPuntosTotal	= $_POST['prefPuntosTotal'];
	$prefPuntosRestaLeves	= $_POST['prefPuntosRestaLeves'];
	$prefPuntosRestaGraves	= $_POST['prefPuntosRestaGraves'];
	$prefPuntosRestaMGraves	= $_POST['prefPuntosRestaMGraves'];
	$prefPuntosRecuperaConvivencia	= $_POST['prefPuntosRecuperaConvivencia'];
	$prefPuntosRecuperaSemana	= $_POST['prefPuntosRecuperaSemana'];

	$cambioTablaConvivencia = 0;
	if (isset($config['convivencia']['convivencia_seneca']) && ($config['convivencia']['convivencia_seneca'] != $prefConvivenciaSeneca)) {
		$cambioTablaConvivencia = 1;
	}

	// CREACIÓN DEL ARCHIVO DE CONFIGURACIÓN
	if($file = fopen('config.php', 'w+'))
	{
		fwrite($file, "<?php \r\n");

		fwrite($file, "\r\n// CONFIGURACIÓN MÓDULO DE PROBLEMAS DE CONVIVENCIA\r\n");
		fwrite($file, "\$config['convivencia']['convivencia_seneca']\t= $prefConvivenciaSeneca;\r\n");
		fwrite($file, "\$config['convivencia']['notificaciones_padres']\t= $prefNotificacionPadres;\r\n");
		fwrite($file, "\$config['convivencia']['notificaciones_padres_leves']\t= $prefNotificacionLevesPadres;\r\n");
		fwrite($file, "\$config['convivencia']['mostrar_descripcion']\t= $prefMostrarDescripcion;\r\n");
		fwrite($file, "\$config['convivencia']['listados_direccion']\t= $prefListadosDireccion;\r\n");
		fwrite($file, "\$config['convivencia']['compromiso_convivencia']\t= $prefCompromisoConvivencia;\r\n");
		fwrite($file, "\r\n// CONFIGURACIÓN SISTEMA POR PUNTOS\r\n");
		fwrite($file, "\$config['convivencia']['puntos']['habilitado']\t= $prefPuntosHabilitado;\r\n");
		fwrite($file, "\$config['convivencia']['puntos']['maximo']\t= $prefPuntosMaximo;\r\n");
		fwrite($file, "\$config['convivencia']['puntos']['total']\t= $prefPuntosTotal;\r\n");
		fwrite($file, "\$config['convivencia']['puntos']['resta_leves']\t= $prefPuntosRestaLeves;\r\n");
		fwrite($file, "\$config['convivencia']['puntos']['resta_graves']\t= $prefPuntosRestaGraves;\r\n");
		fwrite($file, "\$config['convivencia']['puntos']['resta_mgraves']\t= $prefPuntosRestaMGraves;\r\n");
		fwrite($file, "\$config['convivencia']['puntos']['recupera_convivencia']\t= $prefPuntosRecuperaConvivencia;\r\n");
		fwrite($file, "\$config['convivencia']['puntos']['recupera_semana']\t= $prefPuntosRecuperaSemana;\r\n");

		fwrite($file, "\r\n\r\n// Fin del archivo de configuración");

		fclose($file);

		$msg_success = "Las preferencias han sido guardadas correctamente.";
	}

}

if (file_exists('config.php')) {
	include('config.php');

	if (isset($cambioTablaConvivencia) && $cambioTablaConvivencia == 1) {
		if (isset($config['convivencia']['convivencia_seneca']) && $config['convivencia']['convivencia_seneca'] == 1) {
			mysqli_query($db_con, "RENAME TABLE `listafechorias` TO `listafechorias_intranet`");
			mysqli_query($db_con, "RENAME TABLE `listafechorias_seneca` TO `listafechorias`");
		}
		else {
			mysqli_query($db_con, "RENAME TABLE `listafechorias` TO `listafechorias_seneca`");
			mysqli_query($db_con, "RENAME TABLE `listafechorias_intranet` TO `listafechorias`");
		}
	}
	unset($cambioTablaConvivencia);

}

include("../../menu.php");
include("menu.php");
?>

<div class="container">

	<div class="page-header">
		<h2>Problemas de convivencia <small>Preferencias</small></h2>
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
							<label for="prefConvivenciaSeneca" class="col-sm-4 control-label">Usar Problemas de Convivencia por defecto de Séneca</label>
							<div class="col-sm-3">
								<select class="form-control" id="prefConvivenciaSeneca" name="prefConvivenciaSeneca">
									<option value="0" <?php echo (isset($config['convivencia']['convivencia_seneca']) && $config['convivencia']['convivencia_seneca'] == 0) ? 'selected' : ''; ?>>Deshabilitado</option>
									<option value="1" <?php echo (isset($config['convivencia']['convivencia_seneca']) && $config['convivencia']['convivencia_seneca'] == 1) ? 'selected' : ''; ?>>Habilitado</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="prefNotificacionPadres" class="col-sm-4 control-label">Enviar notificación de problemas a padres vía SMS o email</label>
							<div class="col-sm-3">
								<select class="form-control" id="prefNotificacionPadres" name="prefNotificacionPadres">
									<option value="1" <?php echo (isset($config['convivencia']['notificaciones_padres']) && $config['convivencia']['notificaciones_padres'] == 1) ? 'selected' : ''; ?>>Habilitado</option>
									<option value="0" <?php echo (isset($config['convivencia']['notificaciones_padres']) && $config['convivencia']['notificaciones_padres'] == 0) ? 'selected' : ''; ?>>Deshabilitado</option>
								</select>
							</div>
						</div>

						<?php if (isset($config['convivencia']['notificaciones_padres']) && $config['convivencia']['notificaciones_padres'] == 1): ?>

						<div class="form-group">
							<label for="prefNotificacionLevesPadres" class="col-sm-4 control-label">Enviar notificación de problemas leves a padres vía SMS o email</label>
							<div class="col-sm-3">
								<select class="form-control" id="prefNotificacionLevesPadres" name="prefNotificacionLevesPadres">
									<option value="0" <?php echo (isset($config['convivencia']['notificaciones_padres_leves']) && $config['convivencia']['notificaciones_padres_leves'] == 0) ? 'selected' : ''; ?>>Deshabilitado</option>
									<option value="1" <?php echo (isset($config['convivencia']['notificaciones_padres_leves']) && $config['convivencia']['notificaciones_padres_leves'] == 1) ? 'selected' : ''; ?>>Habilitado</option>
								</select>
							</div>
						</div>

						<?php endif; ?>

						<div class="form-group">
							<label for="prefMostrarDescripcion" class="col-sm-4 control-label">Descripción del problema en impresión de partes</label>
							<div class="col-sm-3">
								<select class="form-control" id="prefMostrarDescripcion" name="prefMostrarDescripcion">
									<option value="0" <?php echo (isset($config['convivencia']['mostrar_descripcion']) && $config['convivencia']['mostrar_descripcion'] == 0) ? 'selected' : ''; ?>>Deshabilitado</option>
									<option value="1" <?php echo (isset($config['convivencia']['mostrar_descripcion']) && $config['convivencia']['mostrar_descripcion'] == 1) ? 'selected' : ''; ?>>Habilitado</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="prefListadosDireccion" class="col-sm-4 control-label">Listados de problemas visibles solo para Dirección</label>
							<div class="col-sm-3">
								<select class="form-control" id="prefListadosDireccion" name="prefListadosDireccion">
									<option value="0" <?php echo (isset($config['convivencia']['listados_direccion']) && $config['convivencia']['listados_direccion'] == 0) ? 'selected' : ''; ?>>Deshabilitado</option>
									<option value="1" <?php echo (isset($config['convivencia']['listados_direccion']) && $config['convivencia']['listados_direccion'] == 1) ? 'selected' : ''; ?>>Habilitado</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="prefCompromisoConvivencia" class="col-sm-4 control-label">Compromiso de convivencia</label>
							<div class="col-sm-3">
								<select class="form-control" id="prefCompromisoConvivencia" name="prefCompromisoConvivencia">
									<option value="0" <?php echo (isset($config['convivencia']['compromiso_convivencia']) && $config['convivencia']['compromiso_convivencia'] == 0) ? 'selected' : ''; ?>>Deshabilitado</option>
									<option value="1" <?php echo (isset($config['convivencia']['compromiso_convivencia']) && $config['convivencia']['compromiso_convivencia'] == 1) ? 'selected' : ''; ?>>Habilitado</option>
								</select>
							</div>
							<div class="col-sm-5">
								<p class="help-block">Esta opción permite a jefatura de estudios marcar aquellos alumnos que tienen un compromiso de buena conducta. El profesorado verá al pasar el listado de faltas la etiqueta <span class="label label-info">CC</span> en los alumnos con compromiso de convivencia.</p>
							</div>
						</div>

					</fieldset>

					<fieldset>
						<legend>Sistema por puntos</legend>

						<div class="form-group">
							<label for="prefPuntosHabilitado" class="col-sm-4 control-label">Usar sistema por puntos</label>
							<div class="col-sm-3">
								<select class="form-control" id="prefPuntosHabilitado" name="prefPuntosHabilitado">
									<option value="0" <?php echo (isset($config['convivencia']['puntos']['habilitado']) && $config['convivencia']['puntos']['habilitado'] == 0) ? 'selected' : ''; ?>>Deshabilitado</option>
									<option value="1" <?php echo (isset($config['convivencia']['puntos']['habilitado']) && $config['convivencia']['puntos']['habilitado'] == 1) ? 'selected' : ''; ?>>Habilitado</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="prefPuntosMaximo" class="col-sm-4 control-label">Puntos máximos que puede acumular</label>
							<div class="col-sm-3">
								<input type="number" class="form-control" id="prefPuntosMaximo" name="prefPuntosMaximo" min="0" max="100" value="<?php echo (isset($config['convivencia']['puntos']['maximo'])) ? $config['convivencia']['puntos']['maximo'] : 15; ?>">
							</div>
						</div>

						<div class="form-group">
							<label for="prefPuntosTotal" class="col-sm-4 control-label">Puntos a comienzo de curso o tras expulsión del Centro</label>
							<div class="col-sm-3">
								<input type="number" class="form-control" id="prefPuntosTotal" name="prefPuntosTotal" min="0" max="100" value="<?php echo (isset($config['convivencia']['puntos']['total'])) ? $config['convivencia']['puntos']['total'] : 8; ?>">
							</div>
						</div>

						<div class="form-group">
							<label for="prefPuntosRestaLeves" class="col-sm-4 control-label">Puntos que resta por cada parte leve</label>
							<div class="col-sm-3">
								<input type="number" class="form-control" id="prefPuntosRestaLeves" name="prefPuntosRestaLeves" min="0" max="100" value="<?php echo (isset($config['convivencia']['puntos']['resta_leves'])) ? $config['convivencia']['puntos']['resta_leves'] : 2; ?>">
							</div>
						</div>

						<div class="form-group">
							<label for="prefPuntosRestaGraves" class="col-sm-4 control-label">Puntos que resta por cada parte grave</label>
							<div class="col-sm-3">
								<input type="number" class="form-control" id="prefPuntosRestaGraves" name="prefPuntosRestaGraves" min="0" max="100" value="<?php echo (isset($config['convivencia']['puntos']['resta_graves'])) ? $config['convivencia']['puntos']['resta_graves'] : 4; ?>">
							</div>
						</div>

						<div class="form-group">
							<label for="prefPuntosRestaMGraves" class="col-sm-4 control-label">Puntos que resta por cada parte muy grave</label>
							<div class="col-sm-3">
								<input type="number" class="form-control" id="prefPuntosRestaMGraves" name="prefPuntosRestaMGraves" min="0" max="100" value="<?php echo (isset($config['convivencia']['puntos']['resta_mgraves'])) ? $config['convivencia']['puntos']['resta_mgraves'] : 6; ?>">
							</div>
						</div>

						<div class="form-group">
							<label for="prefPuntosRecuperaConvivencia" class="col-sm-4 control-label">Puntos que recupera si asiste y trabaja en el Aula de Convivencia</label>
							<div class="col-sm-3">
								<input type="number" class="form-control" id="prefPuntosRecuperaConvivencia" name="prefPuntosRecuperaConvivencia" min="0" max="100" value="<?php echo (isset($config['convivencia']['puntos']['recupera_convivencia'])) ? $config['convivencia']['puntos']['recupera_convivencia'] : 2; ?>">
							</div>
						</div>

						<div class="form-group">
							<label for="prefPuntosRecuperaSemana" class="col-sm-4 control-label">Puntos que recupera por cada semana que no registra un problema</label>
							<div class="col-sm-3">
								<input type="number" class="form-control" id="prefPuntosRecuperaSemana" name="prefPuntosRecuperaSemana" min="0.00" max="100.00" step=".01" value="<?php echo (isset($config['convivencia']['puntos']['recupera_semana'])) ? $config['convivencia']['puntos']['recupera_semana'] : '0.15'; ?>">
							</div>
						</div>

					</fieldset>

				</div>

				<button type="submit" class="btn btn-primary" name="btnGuardar">Guardar cambios</button>
				<?php if (isset($_GET['esAdmin']) && $_GET['esAdmin'] == 1): ?>
				<a href="../../../xml/index.php" class="btn btn-default">Volver</a>
				<?php else: ?>
				<a href="lfechorias.php" class="btn btn-default">Volver</a>
				<?php endif; ?>

			</form>

		</table>

		</div>

	</div>

</div>

<?php include("../../pie.php"); ?>

</body>
</html>
