<?php
require('../../../bootstrap.php');

// COMPROBAMOS EL ID DEL INFORME
if (isset($_GET['id_informe']) && intval($_GET['id_informe'])) {
	$id_informe = limpiarInput($_GET['id_informe'], 'numeric');
}
else {
	header('Location:'.'index.php');
	exit();
}

// BORRAR CONTENIDOS
if (isset($_GET['borrar']) AND $_GET['borrar'] == 1) {
	$result = mysqli_query($db_con,"delete from informe_extraordinaria_contenidos where id_informe= '".$_GET['id_informe']."' and id_contenido= '".$_GET['id_contenido']."'");
	if (! $result) {
		$msg_error = "No se ha podido eliminar el elemento del informe. Error: ".mysqli_error($db_con);
		}
		else{
		$msg_acierto = "El elemento del informe ha sido borrado correctamente.";	
		}
}

// OBTENEMOS LOS DATOS DEL INFORME SELECCIONADO
$result = mysqli_query($db_con, "SELECT `profesor`, `asignatura`, `unidad`, `curso`, `fecha`, `modalidad`, `plantilla`  FROM `informe_extraordinaria` WHERE `id_informe` = '".$id_informe."' LIMIT 1");
$datos_informe = mysqli_fetch_array($result);


// OBTENEMOS LOS CONTENIDOS DEL INFORME SELECCIONADO
$contenidos_informe = array();
$result = mysqli_query($db_con, "SELECT `id_contenido`, `unidad`, `titulo`, `contenidos`, `actividades` FROM `informe_extraordinaria_contenidos` WHERE `id_informe` = '".$id_informe."' ORDER BY `id_contenido` ASC");
	
	if (!mysqli_num_rows($result)>0) {
		$curso_corto = substr($datos_informe['curso'], 0, 19);
		$result_cont = mysqli_query($db_con, "SELECT `id_contenido`, `unidad`, `titulo`, `contenidos`, `actividades` FROM `informe_extraordinaria_contenidos` WHERE `id_informe` in (select id_informe from informe_extraordinaria where asignatura = '".$datos_informe['asignatura']."' and curso like '".$curso_corto."%' and plantilla = '1') ORDER BY `id_contenido` ASC");
		$plantilla = 1;
	}


while ($row = mysqli_fetch_array($result)) {
	$contenido = array(
		'id_contenido' => $row['id_contenido'],
		'unidad' => $row['unidad'],
		'curso' => $row['curso'],
		'titulo' => $row['titulo'],
		'contenidos' => $row['contenidos'],
		'actividades' => $row['actividades']
	);

	array_push($contenidos_informe, $contenido);

	unset($contenido);
}


// CREAR CONTENIDO
if (isset($_POST['crear_contenido'])) {

	// Variables
	$unidad = limpiarInput($_POST['unidad'], 'alphanumericspecial');
	$titulo = limpiarInput($_POST['titulo'], 'alphanumericspecial');
	$contenidos = $_POST['contenidos'];
	$actividades = $_POST['actividades'];

	$result = mysqli_query($db_con, "INSERT INTO `informe_extraordinaria_contenidos` (`id_informe`, `unidad`, `titulo`, `contenidos`, `actividades`) VALUES ('".$id_informe."', '".$unidad."', '".$titulo."', '".$contenidos."', '".$actividades."')");
	if (! $result) {
		$msg_error = "No se ha podido añadir contenido a este informe. Error: ".mysqli_error($db_con);
	}
	else {
		header('Location:'.'contenidos.php?id_informe='.$id_informe);
		exit();
	}

}


// CREAR CONTENIDO
if (isset($_POST['actualizar_contenido'])) {

	// Variables
	$unidad = limpiarInput($_POST['unidad'], 'alphanumericspecial');
	$titulo = limpiarInput($_POST['titulo'], 'alphanumericspecial');
	$contenidos = $_POST['contenidos'];
	$actividades = $_POST['actividades'];
	$id_informe = limpiarInput($_POST['id_informe'], 'alphanumericspecial');
	$id_contenido = limpiarInput($_POST['id_contenido'], 'alphanumericspecial');


	$result = mysqli_query($db_con, "update `informe_extraordinaria_contenidos` set unidad= '".$unidad."', titulo= '".$titulo."', contenidos= '".$contenidos."', actividades= '".$actividades."' where id_informe='".$id_informe."' and id_contenido='".$id_contenido."'");
	if (! $result) {
		$msg_error = "No se ha podido añadir contenido a este informe. Error: ".mysqli_error($db_con);
	}
	else {
		header('Location:'.'contenidos.php?id_informe='.$id_informe);
		exit();
	}

}


if (isset($_GET['editar']) AND isset($_GET['id_contenido'])) {
	$result = mysqli_query($db_con, "select * from informe_extraordinaria_contenidos where id_informe = '".$_GET['id_informe']."' and id_contenido = '".$_GET['id_contenido']."'");
	if ($result) {
		$edicion = mysqli_fetch_array($result);
		$editando = 1;
	}
}

include("../../../menu.php");
include("menu.php");
?>
	
	<div class="container page-header">
		<h2>Informe individual de evaluación extraordinaria  <small><br><?php echo $datos_informe['asignatura']; ?><br><?php echo $datos_informe['unidad']; ?></small></h2>
	</div>

	<div class="container-fluid">

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

		<div class="row">

			<div class="col-sm-8">

				<table class="table table-striped">
					<thead>
						<tr>
							<th style="width:20%;">Unidad y título</th>
							<th style="width:40%;">Contenidos</th>
							<th style="width:40%;">Actividades</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php if($plantilla !== 1){ ?>
						<?php foreach ($contenidos_informe as $contenido_informe): ?>
						<tr>
							<td><strong><?php echo $contenido_informe['unidad']; ?></strong><br><?php echo $contenido_informe['titulo']; ?></td>
							<td><?php echo $contenido_informe['contenidos']; ?></td>
							<td><?php echo $contenido_informe['actividades']; ?></td>
							<td>
								<a href="//<?php echo $config['dominio']; ?>/intranet/admin/informes/extraordinaria/contenidos.php?editar=1&id_informe=<?php echo $id_informe; ?>&id_contenido=<?php echo $contenido_informe['id_contenido']; ?>"><span class="far fa-edit fa-fw fa-lg"></span></a>
								<br><br>
								<a href="//<?php echo $config['dominio']; ?>/intranet/admin/informes/extraordinaria/contenidos.php?borrar=1&id_informe=<?php echo $id_informe; ?>&id_contenido=<?php echo $contenido_informe['id_contenido']; ?>" data-bb='confirm-delete'><span class="far fa-trash-alt fa-fw fa-lg"></span></a>
							</td>
						</tr>
						<?php endforeach; ?>
						<?php } else{ ?>
							<?php while ($contenido_informe = mysqli_fetch_array($result_cont)): 
								mysqli_query($db_con,"insert into informe_extraordinaria_contenidos (id_informe,unidad, titulo, contenidos,actividades,id_contenido) VALUES ('".$id_informe."','".$contenido_informe['unidad']."','".$contenido_informe['titulo']."','".$contenido_informe['contenidos']."','".$contenido_informe['actividades']."','".$contenido_informe['id_contenido']."')");
							?>
							<tr>
								<td><strong><?php echo $contenido_informe['unidad']; ?></strong><br><?php echo $contenido_informe['titulo']; ?></td>
								<td><?php echo $contenido_informe['contenidos']; ?></td>
								<td><?php echo $contenido_informe['actividades']; ?></td>
								<td>
									<a href="//<?php echo $config['dominio']; ?>/intranet/admin/informes/extraordinaria/contenidos.php?editar=1&id_informe=<?php echo $id_informe; ?>&id_contenido=<?php echo $contenido_informe['id_contenido']; ?>"><span class="far fa-edit fa-fw fa-lg"></span></a>
									<br><br>
									<a href="//<?php echo $config['dominio']; ?>/intranet/admin/informes/extraordinaria/contenidos.php?borrar=1&id_informe=<?php echo $id_informe; ?>&id_contenido=<?php echo $contenido_informe['id_contenido']; ?>" data-bb='confirm-delete'><span class="far fa-trash-alt fa-fw fa-lg"></span></a>
								</td>
							</tr>
						<?php endwhile; ?>
						<?php } ?>
					</tbody>
				</table>

			</div><!-- /.col-sm-7 -->
			
			<div class="col-sm-4">
				
				<div class="well">
					
					<form action="" method="post">

						<fieldset>

							<legend>Añadir contenido</legend>

							<div class="form-group">
								<label for="unidad">Unidad</label>
								<input type="text" class="form-control" id="unidad" name="unidad" <?php if(!empty($edicion['unidad'])) {echo " value='".$edicion['unidad']."'"; } else{ echo ' placeholder="Unidad"';} ?> maxlength="60" required>
							</div>

							<div class="form-group">
								<label for="titulo">Título</label>
								<input type="text" class="form-control" id="titulo" name="titulo" <?php if(!empty($edicion['titulo'])) {echo " value='".$edicion['titulo']."'"; } else{ echo ' placeholder="Título"';} ?> maxlength="60" required>
							</div>

							<div class="form-group">
								<label for="contenidos">Contenidos de la unidad</label>
								<textarea id="contenidos" name="contenidos" class="form-control" rows="8" required placeholder="Utiliza la etiqueta <br> al final de una frase o palabra para producir un salto de línea (o sea, punto y aparte). De ese modo el texto se vuelve más ordenado y legible."><?php if(!empty($edicion['contenidos'])) { echo $edicion['contenidos']; } else{ echo '';} ?></textarea>
							</div>

							<div class="form-group">
								<label for="actividades">Actividades propuestas</label>
								<textarea id="actividades" name="actividades" class="form-control" rows="8" required placeholder="Puedes también insertar otras etiquetas para dar formatro al texto. Por ejemplo, <b>palabra</b> produce texto en negrita; <u>palabra</u>, texto subrayado; <em>palabra</em>, texto en cursiva."><?php if(!empty($edicion['actividades'])) {echo $edicion['actividades']; } else{ echo '';} ?></textarea>
							</div>

							<div class="form-group">
								<input type="hidden" class="form-control" id="id_informe" name="id_informe" value="<?php echo $edicion['id_informe'];?>" maxlength="60">
								<input type="hidden" class="form-control" id="id_contenido" name="id_contenido" value="<?php echo $edicion['id_contenido'];?>" maxlength="60">
								<?php if ($editando==1) {
								?>
								<button type="submit" class="btn btn-danger" name="actualizar_contenido" >Actualizar contenido</button>
								<button type="submit" class="btn btn-primary" name="crear_contenido" >Añadir contenido</button>
								<?php
								}
								else{
								?>
								<button type="submit" class="btn btn-primary" name="crear_contenido" >Añadir contenido</button>
								<?php
								}
								?>								
							</div>

						</fieldset>

					</form>


				</div><!-- /.well -->

			</div><!-- /.col-sm-5 -->

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