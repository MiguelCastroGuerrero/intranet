<?php
require('../../../bootstrap.php');

//PROCESAR DATOS ENVIADOS
if (isset($_POST['enviar_datos'])) {
	$enviado = 1;
	$id_informe = $_POST['id_informe'];
	mysqli_query($db_con,"delete from informe_pendientes_alumnos where id_informe = '".$id_informe."' and claveal='".$_POST['claveal']."'");
	foreach ($_POST as $clave => $val) {
		unset($observaciones);
		if (!empty($val) and $clave !== 'enviar_datos' and $clave !== 'grupo' and $clave !== 'id_informe') {	
		$tr_clave = explode("-", $clave);
		$claveal = $tr_clave[0];
		$id_contenido = $tr_clave[2];
			if ($id_contenido == 0) {
				$observaciones = $val;
			}

			if ($claveal !== "claveal") {
				$result = mysqli_query($db_con,"INSERT into informe_pendientes_alumnos (id_informe,id_contenido,claveal,actividades) VALUES ('$id_informe','$id_contenido','$claveal','$observaciones')");

				if (! $result) {
					$msg_error = "No se ha podido guardar algún elemento del informe. O bien has borrado todos los datos de un alumno e informe";
				}
				else{
					$msg_acierto = "Se han actualizado correctamente los datos del informe. Puedes volver a la página de evaluación de pendientes para continuar.";
				}
			}
		
		}
	}
	$id_informe = $_POST['id_informe'];
}

// COMPROBAMOS SI SE HA SELECCIONADO LA UNIDAD
if (isset($_GET['curso_pendiente'])) {	$curso_pendiente = $_GET['curso_pendiente'];}
if (isset($_GET['id_informe'])) {	$id_informe = $_GET['id_informe'];}
if (isset($_GET['claveal'])) {	$claveal = $_GET['claveal'];}

$materias = mysqli_query($db_con,"select codigo, nombre from asignaturas where curso like '$curso_pendiente%' and nombre = (select asignatura from informe_pendientes where id_informe = '$id_informe' and plantilla='1') and abrev not like '%\_%'");

$materia_informe = mysqli_fetch_array($materias);
$codigo = $materia_informe['codigo'];
$asignatura = $materia_informe['nombre'];

include("../../../menu.php");
include("menu.php");
?>
	
	<div class="container">
		
		<div class="page-header">
			<h2>Informe individual para alumnos con materias pendientes <small> <br>Informe de los alumnos</small></h2>
		</div>

		<?php if(isset($msg_error) && $msg_error): ?>
		<div class="alert alert-danger alert-fadeout">
			<?php echo $msg_error; ?>
		</div>
		<?php endif; ?>
		<?php if(isset($msg_acierto) && $msg_acierto): ?>
		<div class="alert alert-success alert-fadeout">
			<?php echo $msg_acierto; ?>
		</div>
		<?php endif; ?>

		<?php if($enviado <> 1) { ?>
		<div class="row">
			
			<div class="col-sm-12">
				<p class="help-block">Marca las casillas de los contenidos (<em>UNIDADES</em>) que el alumno debe preparar para la evaluación. <br>Si necesitas personalizar las actividades y tareas de un alumno, puedes añadir información única para él en el campo de <em>observaciones individuales</em>.</p>
				<br>
				<table class="table table-striped table-bordered table-condensed">
					<thead>
						<tr>
							<th>Alumno</th>
							<th>Unidades</th>
							<th>Observaciones individuales</th>
						</tr>
					</thead>
					<tbody>
						<form action="alumnado_pendientes.php" method="post">
						<?php 
						

						$alumno = mysqli_query($db_con,"select claveal, claveal1, unidad, curso, concat(nombre,' ',apellidos) as nombre_alumno from alma where claveal = '".$claveal."'");
						while($alumnos = mysqli_fetch_array($alumno)):

								$al_reg = mysqli_query($db_con,"select * from informe_pendientes_alumnos where claveal = '".$alumnos['claveal']."'");
								while($reg = mysqli_fetch_array($al_reg)){
								$reg_informe = $reg['id_informe'];
								$reg_contenido = $reg['id_contenido'];
								$reg_observaciones = $reg['actividades'];
								$reg_datos = $alumnos['claveal']."-".$reg_informe."-".$reg_contenido;
								$ya_datos[] = $reg_datos;
								}
								?>
							<tr>
								<td class="text-danger" style="width:260px; vertical-align:middle; font-weight:bold; "><?php echo $alumnos['nombre_alumno']; ?></td>
								<td><div class="form-group"><?php 
								$tema = mysqli_query($db_con,"select id_contenido, unidad, titulo from informe_pendientes_contenidos where id_informe = '".$id_informe."' order by id_contenido");
								while($temas = mysqli_fetch_array($tema)) { 
									$sel = "";
									if (in_array($alumnos['claveal']."-".$id_informe."-".$temas['id_contenido'], $ya_datos)) {
										$sel = 'checked';
									}
									else{
										$sel = '';
									}
									?>
									<div class="checkbox">
										<label class="checkbox-inline" for="checkbox_1">
									  	<input type="checkbox" name="<?php echo $alumnos['claveal']."-".$id_informe."-".$temas['id_contenido']; ?>" value="1" id="checkbox_1" <?php echo $sel; ?>>
									  	<?php echo $temas['unidad']." (<b>".$temas['titulo']."</b>)"; ?>	  
									  </label>
									</div>

								<?php } ?>	
								</div>								
								</td>
								<td>
									<?php
									unset($actividad);
									$al_act = mysqli_query($db_con,"select actividades from informe_pendientes_alumnos where claveal = '".$alumnos['claveal']."' and id_informe = '$id_informe' and id_contenido='0'");
									if (mysqli_num_rows($al_act)>0) {
										$actividad = mysqli_fetch_array($al_act);
									}

									?>
									<textarea class="form-control" name="<?php echo $alumnos['claveal']."-".$id_informe."-0";?>" rows="8"><?php echo $actividad['actividades'];?></textarea></td>
							</tr>
						
							<?php endwhile; ?>
							<tr><td colspan="3">  
								<input type="hidden" name="id_informe" value="<?php echo $id_informe;?>" >
								<input type="hidden" name="claveal" value="<?php echo $claveal;?>" >
								<input type="submit" class="btn btn-primary" name="enviar_datos" value="Guardar los informes" ></td></tr>
						</form>
					</tbody>
				</table>

			</div><!-- /.col-sm-12-->

		</div><!-- /.row -->
		
	<?php } ?>

	</div>

	<?php include("../../../pie.php"); ?>

</body>
</html>
