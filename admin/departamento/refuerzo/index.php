<?php
require('../../../bootstrap.php');

if (isset($_POST['departamento'])) {
		$departamento = mysqli_real_escape_string($db_con, $_POST['departamento']);
		$titulo = 'Departamento de '.$departamento;
}
else {
	$departamento = $dpto;
	$titulo = 'Departamento de '.$departamento;
}
	$profesor = mysqli_real_escape_string($db_con, $_SESSION['profi']);
	
	if (isset($_POST['id_acta'])) { $id_acta = $_POST['id_acta'];} elseif (isset($_GET['id_acta'])) { $id_acta = $_GET['id_acta'];} else{ $id_acta = "";}
	if (isset($_POST['grupo'])) { $grupo = mysqli_real_escape_string($db_con, $_POST['grupo']);} else{ $grupo = '';}
	if (isset($_POST['materia_codigo'])) { $materia_codigo = mysqli_real_escape_string($db_con,$_POST['materia_codigo']);} else{ $materia_codigo = '';}
	if (isset($_POST['alumno_claveal'])) { $alumno_claveal = mysqli_real_escape_string($db_con, $_POST['alumno_claveal']);} else{ $alumno_claveal = '';}
	if (isset($_POST['curso'])) { $curso = mysqli_real_escape_string($db_con, $_POST['curso']);} else{ $curso="";}
	if (isset($_POST['texto_acta'])) { $texto_acta = mysqli_real_escape_string($db_con, $_POST['texto_acta']);} else{ $texto_acta="";}

	if (!empty($materia_codigo)) {
		$tr_materia = explode(";", $materia_codigo);
		$materia = $tr_materia[0];
		$codigo = $tr_materia[1];
	}
	if (!empty($alumno_claveal)) {
		$tr_alumno = explode(";", $alumno_claveal);
		$alumno = $tr_alumno[0];
		$claveal = $tr_alumno[1];
		$apel_nombre = explode(", ", $alumno);
		$nombre_alumno = $apel_nombre[1]." ".$apel_nombre[0];
	}

	$fecha_hoy = date('Y-m-d');	

	$registrado = "";

// REGISTRAMOS EL ACTA

if (isset($_POST['guardar'])) {
	if (!empty($alumno) and !empty($curso) and !empty($materia) and !empty($grupo) and !empty($texto_acta)) {
		$result = mysqli_query($db_con, "INSERT INTO refuerzo (alumno, unidad, materia, departamento, profesor, texto, fecha, curso) VALUES ('$claveal', '$grupo', '$materia', '$departamento', '$profesor', '$texto_acta', '$fecha_hoy', '$curso')");	
		if (! $result) $msg_error = "Ha ocurrido un error al registrar el acta. Error: ".mysqli_error($db_con);
		else $msg_success = "El documento ha sido registrado correctamente";
		$ya_hay = mysqli_query($db_con,"select * from refuerzo where profesor = '$profesor' and alumno = '$claveal' and materia = '$materia'");
		if (mysqli_num_rows($ya_hay)) { 
			$ya_texto = mysqli_fetch_array($ya_hay);
			$texto_acta = $ya_texto['texto'];
			$id_acta = $ya_texto['id'];
			$registrado = 1;
		}
		else{
			$id_acta="";
		}
	}
	else{
		echo "Error de inserción";
	}
}

// ACTUALIZAMOS EL ACTA
if (isset($_POST['actualizar'])) {
	$result = mysqli_query($db_con, "UPDATE refuerzo SET texto = '$texto_acta', fecha = '$fecha_hoy' WHERE id = $id_acta");
	echo "UPDATE refuerzo SET texto = '$texto_acta', fecha = '$fecha_hoy' WHERE id = $id_acta";
	if (! $result) $msg_error = "Ha ocurrido un error al actualizar el documento. Error: ".mysqli_error($db_con);
	else $msg_success = "El documento ha sido actualizado correctamente";
}

// ELIMINAR ACTA
if (isset($_GET['eliminar_id'])) {

	$eliminar_id = mysqli_real_escape_string($db_con, $_GET['eliminar_id']);

	$result = mysqli_query($db_con, "DELETE FROM refuerzo WHERE id = $eliminar_id");
	if (! $result) $msg_error = "Ha ocurrido un error al eliminar el acta. Error: ".mysqli_error($db_con);
	else $msg_success = "El documento ha sido eliminado correctamente.";
}

// Datos de los alumnos
if (isset($_POST['alumno_claveal'])) {
		$grupo = mysqli_real_escape_string($db_con, $_POST['grupo']);
		
		$materia_codigo = mysqli_real_escape_string($db_con, $_POST['materia_codigo']);
		$tr_materia = explode(";", $materia_codigo);
		$materia = $tr_materia[0];
		$codigo = $tr_materia[1];

		$alumno_claveal = mysqli_real_escape_string($db_con, $_POST['alumno_claveal']);
		$tr_alumno = explode(";", $alumno_claveal);
		$alumno = $tr_alumno[0];
		$claveal = $tr_alumno[1];
		$apel_nombre = explode(", ", $alumno);
		$nombre_alumno = $apel_nombre[1]." ".$apel_nombre[0];

		$fecha = $_POST['fecha'];

		$curso = $_POST['curso'];

		$ya_hay = mysqli_query($db_con,"select * from refuerzo where profesor = '$profesor' and alumno = '$claveal' and materia = '$materia'");
		if (mysqli_num_rows($ya_hay)) { 
			$ya_texto = mysqli_fetch_array($ya_hay);
			$texto_acta = $ya_texto['texto'];
			$id_acta = $ya_texto['id'];
			$registrado = 1;
		}
		else{
			$id_acta="";
		}
	}

// URI módulo
$uri = 'index.php';

include ("../../../menu.php");
include("../menu.php");
include ("menu.php");


$profesor = $_SESSION['profi'];
?>
<div class="container">

	<form method="post" action="index.php">

		<div class="page-header">
			<h2>Programas de Refuerzo de Aprendizaje <small>Registrar informe</small></h2>

			<h3><?php echo $departamento; ?></h3>

		</div><!-- /.page-header -->

		<?php if (isset($msg_error)): ?>
		<div class="alert alert-danger">
			<?php echo $msg_error; ?>
		</div>
		<?php endif; ?>

		<?php if (isset($msg_alerta)): ?>
		<div class="alert alert-warning">
			<?php echo $msg_alerta; ?>
		</div>
		<?php endif; ?>

		<?php if (isset($msg_success)): ?>
		<div class="alert alert-success">
			<?php echo $msg_success; ?>
		</div>
		<?php endif; ?>

		<div class="row">

			<div class="col-md-8">

			<?php
			// RECOLECTAMOS DATOS PARA RELLENAR EL ACTA
			if ( isset($_GET['edit_id'])) {
				$id_acta = $_GET['edit_id'];
				$result = mysqli_query($db_con, "SELECT id, alumno, unidad, materia, departamento, profesor, texto, fecha, curso FROM refuerzo WHERE id = '$id_acta'");
				while ( $row = mysqli_fetch_row($result)) {							
					$alumno = $row[1];
					$al = mysqli_query($db_con, "select apellidos, nombre from alma where claveal = '$alumno'");
					$alum = mysqli_fetch_array($al);
					$alumno_claveal = "$alum[0], $alum[1];$alumno";
					$nombre_alumno = $alum[1]." ".$alum[0];
					$grupo = $row[2];
					$materia = $row[3];
					$cod = mysqli_query($db_con, "select codigo from asignaturas where nombre = '$materia' and abrev not like '%\_%' limit 1");
					$codi = mysqli_fetch_array($cod);
					$codigo = $codi[0];
					$materia_codigo = "$materia;$codigo";
					$departamento = $row[4];
					$profeor = $row[5];
					$texto = $row[6];
					$fecha = $row[7];
					$curso = $row[8];
					$texto_acta = $row[6];
					$registrado=1;
				}
			}
			?>

			<legend class="text-muted">Nuevo informe</legend>
					
				<div class="well">
			
					<fieldset>

						<div class="row">

							<div class="col-sm-2">

								<select class="form-control text-info" id="grupo" name="grupo" onchange="submit()">
									<option value=""></option>
										<?php $result = mysqli_query($db_con, "SELECT DISTINCT grupo FROM profesores WHERE profesor like '$profesor' ORDER BY grupo ASC"); ?>
										<?php while ($row = mysqli_fetch_array($result)): 
											if (!isset($grupo)) { $grupo = $row['grupo'];}
										?>
										<option value="<?php echo $row['grupo']; ?>"<?php echo (isset($grupo) && $grupo == $row['grupo']) ? ' selected' : ''; ?>><?php echo $row['grupo']; ?></option>
										<?php endwhile; ?>
								</select>

							</div><!-- /.col-sm-4 -->


							<div class="col-sm-5">

								<select class="form-control text-info" id="materia_codigo" name="materia_codigo" onchange="submit()">
									<option value=""></option>
										<?php $result = mysqli_query($db_con, "SELECT distinct materia, nivel FROM profesores WHERE profesor like '$profesor' and grupo = '$grupo'"); ?>
										<?php while ($row = mysqli_fetch_array($result)): 
											$curso = $row[1];
										?>
										<?php $result1 = mysqli_query($db_con, "SELECT distinct codigo FROM asignaturas WHERE nombre like '$row[0]' and curso = '$row[1]' and abrev not like '%\_%' limit 1"); 
											$cod = mysqli_fetch_row($result1);
											$codigo = $cod[0];
										?>	
										<option value="<?php echo $row['materia'].";".$codigo; ?>"<?php echo (isset($materia_codigo) && $materia_codigo == $row['materia'].";".$codigo) ? ' selected' : ''; ?>><?php echo $row['materia']." - ".$curso; ?></option>
										<?php endwhile; ?>
								</select>
								<input type="hidden" name="curso" value="<?php echo $curso; ?>">
							</div><!-- /.col-sm-4 -->

							<div class="col-sm-5">
								<select class="form-control text-info" id="alumno_claveal" name="alumno_claveal" onchange="submit()">
									<option value=""></option>
									<optgroup label="Alumnos del grupo">
										<?php $result = mysqli_query($db_con, "SELECT CONCAT(apellidos, ',',' ', nombre) as alumno, claveal FROM alma WHERE unidad like '$grupo' and combasi like '%$codigo%' ORDER BY apellidos, nombre ASC"); ?>
										<?php while ($row = mysqli_fetch_array($result)):?>
										<option value="<?php echo $row['alumno'].";".$row['claveal']; ?>"<?php echo (isset($alumno_claveal) && $alumno_claveal == $row['alumno'].";".$row['claveal']) ? ' selected' : ''; ?>><?php echo $row['alumno']; ?></option>
										<?php endwhile; ?>
									</optgroup>
								</select>

							</div><!-- /.col-sm-4 -->

						</div><!-- /.row -->

<?php
if (!empty($grupo) and !empty($curso)) {
	$grupo_curso = '<b>'.$grupo.'</b> (<em>'.$curso.'</em>)';
}
else{
	$grupo_curso = "";
}

$ntrimestre = trimestreActual();
switch($ntrimestre) {
	case 1: $trimestre = "Primer trimestre"; break;
	case 2: $trimestre = "Segundo trimestre"; break;
	case 3: $trimestre = "Tercer trimestre"; break;
	case 4: default: $trimestre = "";
}

$html_textarea = '<p><span style="font-size: 18pt;"><strong>INFORME DE SEGUIMIENTO DE ATENCI&Oacute;N A LA DIVERSIDAD</strong></span></p>
<table style="border-collapse: collapse; width: 100%; border-color: #7E8C8D; border-style: solid;" border="1">
<tbody>
<tr>
<td style="width: 9.75414%; background-color: #ecf0f1;"><strong>ALUMNO/A</strong></td>
<td style="width: 90.2459%;">'.$nombre_alumno.'</td>
</tr>
<tr>
<td style="width: 9.75414%; background-color: #ecf0f1;"><strong>ASIGNATURA</strong></td>
<td style="width: 90.2459%;">'.$materia.'</td>
</tr>
<tr>
<td style="width: 9.75414%; background-color: #ecf0f1;"><strong>CURSO</strong></td>
<td style="width: 90.2459%;">'.$grupo_curso.'</td>
</tr>
<tr>
<td style="width: 9.75414%; background-color: #ecf0f1;"><strong>TRIMESTRE</strong></td>
<td style="width: 90.2459%;">'.$trimestre.'</td>
</tr>
</tbody>
</table>
<p><span style="font-size: 10pt;"><br />Marque con una </span><strong style="font-size: 10pt;">X</strong><span style="font-size: 10pt;"> seg&uacute;n proceda:</span></p>
<table style="border-collapse: collapse; width: 100%; border-color: #7E8C8D; border-style: solid;" border="1">
<tbody>
<tr>
<td style="width: 6.11972%; text-align: center;">&nbsp;</td>
<td style="width: 93.8803%; background-color: #ecf0f1;"><strong>PRr:</strong> Alumnado que no ha promocionado - REPETIDORES</td>
</tr>
<tr>
<td style="width: 6.11972%; text-align: center;">&nbsp;</td>
<td style="width: 93.8803%; background-color: #ecf0f1;"><strong>PRp:</strong> Alumnado que ha promocionado y tiene alguna materia pendiente - PENDIENTES</td>
</tr>
<tr>
<td style="width: 6.11972%; text-align: center;">&nbsp;</td>
<td style="width: 93.8803%; background-color: #ecf0f1;"><strong>PRd: </strong>Que a juicio del docente considera que presenta dificultades de aprendizaje que justifica su inclusi&oacute;n.</td>
</tr>
<tr>
<td style="width: 6.11972%; text-align: center;">&nbsp;</td>
<td style="width: 93.8803%; background-color: #ecf0f1;"><strong>PA: </strong>Programas de Ampliaci&oacute;n</td>
</tr>
</tbody>
</table>
<p>&nbsp;</p>
<table style="border-collapse: collapse; width: 100%; border-color: #95a5a6; border-style: solid; height: 330.25px;" border="1">
<tbody>
<tr style="height: 33.5938px;">
<td style="width: 100.053%; background-color: #236fa1; text-align: center; height: 33.5938px;" colspan="2"><span style="color: #ffffff; font-size: 14pt;"><strong>INFORMACI&Oacute;N PERI&Oacute;DICA SOBRE EL DESARROLLO DEL PRA</strong></span></td>
</tr>
<tr style="height: 52.2812px;">
<td style="width: 100.053%; background-color: #c2e0f4; text-align: center; height: 52.2812px;" colspan="2"><strong><span style="font-size: 14pt;"><span style="font-size: 12pt;">EVOLUCI&Oacute;N DEL ALUMNO/A EN EL PROGRAMA</span><br /></span></strong><span style="font-size: 14pt;"><span style="font-size: 10pt;">Marque con una <strong>X</strong> seg&uacute;n proceda:</span></span></td>
</tr>
<tr style="height: 244.375px;">
<td style="width: 50%; height: 244.375px;">
<table style="border-collapse: collapse; width: 100.088%; height: 179.125px;" border="1">
<tbody>
<tr style="height: 22.3906px;">
<td style="width: 9.56196%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 90.382%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Evoluciona positivamente en el programa</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 9.56196%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 90.382%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Realiza las tareas de clase</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 9.56196%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 90.382%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Est&aacute; motivado hacia el aprendizaje</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 9.56196%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 90.382%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Trae las tareas realizadas de casa</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 9.56196%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 90.382%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Es participativo en clase</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 9.56196%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 90.382%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Se comporta adecuadamente en clase</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 9.56196%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 90.382%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Se relaciona correctamente con sus compa&ntilde;eros</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 9.56196%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 90.382%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Otros <em>(especificar)</em>:&nbsp;</td>
</tr>
</tbody>
</table>
</td>
<td style="width: 50.0535%; height: 244.375px;">
<table style="border-collapse: collapse; width: 100.141%; height: 179.125px;" border="1">
<tbody>
<tr style="height: 22.3906px;">
<td style="width: 10.4542%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 89.5441%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Evoluciona negativamente</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 10.4542%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 89.5441%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">No realiza las tareas de clase</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 10.4542%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 89.5441%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Muestra desmotivaci&oacute;n a la hora de aprender</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 10.4542%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 89.5441%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">No trae las tareas realizadas de casa</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 10.4542%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 89.5441%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">No participa en clase</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 10.4542%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 89.5441%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Suele comportarse mal en clase</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 10.4542%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 89.5441%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">Tiene problemas de habilidades sociales con sus compa&ntilde;eros/as</td>
</tr>
<tr style="height: 22.3906px;">
<td style="width: 10.4542%; height: 22.3906px; text-align: center; border: 1px solid #95a5a6;">&nbsp;</td>
<td style="width: 89.5441%; height: 22.3906px; background-color: #ecf0f1; border: 1px solid #95a5a6;">&nbsp;</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<p>&nbsp;</p>';
?>

					<hr>
						<div class="form-group">
							<textarea class="form-control" id="texto_acta" name="texto_acta" rows="20" required><?php echo (isset($texto_acta) and ($registrado==1)) ? $texto_acta : $html_textarea; ?></textarea>
						</div>

					</fieldset>
					<?php if (empty($id_acta)): ?>
					<button class="btn btn-primary" id="guardar" name="guardar">Registrar acta</button>
					<?php else: ?>
					<input type="hidden" name="id_acta" value="<?php echo $id_acta; ?>">
					<button class="btn btn-primary" id="actualizar" name="actualizar" >Actualizar acta</button>
					<a class="btn btn-default" href="<?php echo $uri; ?>">Registrar nueva acta</a>
					<?php endif; ?>
				</div>

			</div>

		

			<div class="col-md-4">

				<?php $result = mysqli_query($db_con, "SELECT id, apellidos, nombre, refuerzo.fecha, refuerzo.materia, refuerzo.unidad FROM refuerzo, alma WHERE alma.claveal = refuerzo.alumno and profesor = '$profesor' ORDER BY apellidos, nombre DESC"); ?>
				<?php if (mysqli_num_rows($result)): ?>
				<legend class="text-muted">Informes registrados</legend>
				<table class="table table-bordered table-hover table-striped">
					<thead>
						<th>Alumno</th>
						<th>Opciones</th>
					</thead>
					<tbody>
						<?php while ($row = mysqli_fetch_array($result)): ?>
						<tr>
							<td>
								<?php echo $row['apellidos'].", ".$row['nombre']; ?><br />
								<small class="text-muted"><?php echo "<em>".$row['materia']."</em> (".$row['unidad'].")"; ?></small>
							</td>
							<td>
								<a href="<?php echo $uri; ?>?edit_id=<?php echo $row['id']; ?>" data-bs="tooltip" title="Editar documento"><span class="far fa-edit fa-fw fa-lg"></span></a>
								<a href="pdf.php?id=<?php echo $row['id']; ?>&amp;imprimir=1" target="_blank" data-bs="tooltip" title="Imprimir"><span class="fas fa-print fa-fw fa-lg"></span></a>
								<a href="<?php echo $uri; ?>?eliminar_id=<?php echo $row['id']; ?>" data-bs="tooltip" title="Eliminar documento" data-bb="confirm-delete"><span class="far fa-trash-alt fa-fw fa-lg"></span></a>
							</td>
						</tr>
						<?php endwhile; ?>
					</tbody>
				</table>

				<?php else: ?>
				<p class="lead text-muted text-center">No se ha registrado ningún informe.</p>
				<?php endif; ?>

			</div>

		</div><!-- /.row -->

	</form>
</div>

<?php include("../../../pie.php"); ?>

<script>

	$(document).ready(function() {

		// EDITOR DE TEXTO
		tinymce.init({
			selector: 'textarea#texto_acta',
			language: 'es',
			height: 500,
			<?php if ($bloquea_campos): ?>
			readonly: 1,
			<?php endif; ?>
			plugins: 'print preview fullpage paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars',
			imagetools_cors_hosts: ['picsum.photos'],
			menubar: 'file edit view insert format tools table help',
			toolbar: 'undo redo | bold italic underline strikethrough | fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap | fullscreen  preview save print | insertfile image media template link anchor | ltr rtl',
			toolbar_sticky: true,
			autosave_ask_before_unload: true,
			autosave_interval: "30s",
			autosave_prefix: "{path}{query}-{id}-",
			autosave_restore_when_empty: false,
			autosave_retention: "2m",
			image_advtab: true,
			
			/* enable title field in the Image dialog*/
			image_title: true,
			/* enable automatic uploads of images represented by blob or data URIs*/
			automatic_uploads: true,
			/*
			URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
			images_upload_url: 'postAcceptor.php',
			here we add custom filepicker only to Image dialog
			*/
			file_picker_types: 'image',
			/* and here's our custom image picker*/
			file_picker_callback: function (cb, value, meta) {
			var input = document.createElement('input');
			input.setAttribute('type', 'file');
			input.setAttribute('accept', 'image/*');

			/*
			  Note: In modern browsers input[type="file"] is functional without
			  even adding it to the DOM, but that might not be the case in some older
			  or quirky browsers like IE, so you might want to add it to the DOM
			  just in case, and visually hide it. And do not forget do remove it
			  once you do not need it anymore.
			*/

			input.onchange = function () {
			  var file = this.files[0];

			  var reader = new FileReader();
			  reader.onload = function () {
			    /*
			      Note: Now we need to register the blob in TinyMCEs image blob
			      registry. In the next release this part hopefully won't be
			      necessary, as we are looking to handle it internally.
			    */
			    var id = 'blobid' + (new Date()).getTime();
			    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
			    var base64 = reader.result.split(',')[1];
			    var blobInfo = blobCache.create(id, file, base64);
			    blobCache.add(blobInfo);

			    /* call the callback and populate the Title field with the file name */
			    cb(blobInfo.blobUri(), { title: file.name });
			  };
			  reader.readAsDataURL(file);
			};

			input.click();
			}
		});

	});
	</script>

</body>
</html>
