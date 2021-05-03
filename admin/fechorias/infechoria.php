<?php
require('../../bootstrap.php');

if (file_exists('config.php')) {
	include('config.php');
}

include("../../menu.php");
include("menu.php");
?>
<div class="container">

	<div class="page-header">
		<h2 style="display: inline;">Problemas de convivencia <small>Registro de un Problema de Convivencia</small></h2>

		<!-- Button trigger modal -->
		<a href="#"class="btn btn-default btn-sm pull-right hidden-print" data-toggle="modal" data-target="#modalAyuda">
			<span class="fas fa-question fa-lg"></span>
		</a>

		<!-- Modal -->
		<div class="modal fade" id="modalAyuda" tabindex="-1" role="dialog" aria-labelledby="modal_ayuda_titulo" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
						<h4 class="modal-title" id="modal_ayuda_titulo">Instrucciones de uso</h4>
					</div>
					<div class="modal-body">
						<p>El registro de un Problema de Convivencia comienza con la selección de la
						<em><strong>fecha</strong></em> en que sucedió. <br>Continúa con la selección de la
						<em><strong>Unidad o Grupo de alumnos</strong></em> dentro del cual se encuentra el
						autor del problema. El Grupo no es un campo obligatorio, simplemente facilita la
						búsqueda al reducir la lista de alumnos.</p>
						<p>El campo <em><strong>Alumno/a</strong></em> presenta al principio la lista de todos
						los alumnos del Centro ordenada alfabéticamente. Si elegimos un Grupo aparecerán los
						alumnos de ese Grupo. Tanto en la lista total como en la lista de un Grupo podemos
						seleccionar uno o varios alumnos. Como se señala en el texto de ayuda del formulario,
						se pueden seleccionar múltiples alumnos mediante el uso de la tecla <kbd>CTRL</kbd> +
						click sobre los distintos elementos; si queremos seleccionar a todo el Grupo, hacemos
						click sobre el primero de la lista y, manteniendo presionada la tecla Mayúsculas
						(<kbd>SHIFT</kbd>), seleccionamos el último de la lista.</p>
						<p>El segundo bloque de campos del formulario comienza con la elección de la
						<em><strong>Gravedad</strong></em> del Problema que vamos a registrar. La Gravedad
						puede ser: Leve, Grave y Muy Grave. Cada categoría va asociada a un conjunto de
						<em><strong>Conductas Negativas</strong></em> que aparecen en el ROF (Reglamento de
						Organización y Funcionamiento) del Centro y que puede ser editado por parte de los
						Administradores de la Intranet (Administración de la Intranet --> A principio de
						Curso --> Modificar ROF). Al cargar una de las categorías, el desplegable muestra las
						Conductas Negativas propias de esa categoría. Seleccionamos una Conducta y aparecerán
						al mismo tiempo la <em><strong>Medida Adoptada</strong></em> administrativamente (si
						procede según el ROF) y las <em><strong>Medidas Complementarias</strong></em> que deben
						tomarse (según el ROF). Si el alumno ha sido <em><strong>expulsado del Aula</strong></em>,
						debe marcarse la opción correspondiente.</p>
						<p>En el campo <em><strong>Observaciones</strong></em> describimos el acontecimiento que
						hemos tipificado. La descripción debe ser precisa y completa, de tal modo que tanto el
						Tutor como el Jefe de Estudios como los propios Padres del alumno puedan hacerse una
						idea ajustada de lo sucedido.</p>
						<p>El <em><strong>Profesor</strong></em> que informa del Problema coincide con el Profesor
						que ha abierto la sesión de la Intranet, excepto en el caso de los miembros del Equipo
						Directivo que pueden elegir entre la lista de todos los Profesores.</p>
						<p>El botón <em><strong>Registrar</strong></em> envía los datos del formulario y completa
						el proceso de registro.</p>
						<p>Hay que tener en cuenta algunos detalles que suceden al registrar un Problema
						de Convivencia:</p>
						<ul>
							<li>El Tutor recibe un mensaje en la Página principal cuando se
							registra un Problema Grave o Muy Grave de alguno de sus alumnos. El mensaje ofrece
							datos sobre el problema e indica el procedimiento a seguir. El Jefe de Estudios
							también ve los Problemas que se van registrando en el momento de producirse.</li>
							<li>Si el problema es Leve, el sistema registra un nuevo problema
							Grave por reiteración: cada 5 problemas Leves se crea un Problema Grave de forma
							automática. El Tutor y Jefe de Estudios reciben una notificación.</li>
							<li>Los Problemas de Convivencia caducan según el tiempo
							especificado en el ROF. Los valores por defecto de la aplicación son los siguientes:
							30 días para los Leves y Graves; 60 días para los Muy Graves.</li>
							<li>Se puede editar el Problema registrado en los dos días
							siguientes a la fecha en la que sucedió. Posteriormente, la edición queda bloqueada.</li>
						</ul>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Entendido</button>
					</div>
				</div>
			</div>
		</div>

	</div>

<div class="row"><?php
//variables();
if ( $_POST['unidad']) {$unidad = $_POST['unidad'];}
if (isset($_GET['id'])) { $id = $_GET['id'];}elseif (isset($_POST['id'])) { $id = $_POST['id'];}
if (isset($_GET['nombre'])) { $nombre = $_GET['nombre'];}elseif (isset($_POST['nombre'])) { $nombre = $_POST['nombre'];}
if (isset($_GET['claveal'])) { $claveal = $_GET['claveal'];}elseif (isset($_POST['claveal'])) { $claveal = $_POST['claveal'];}

$notas = $_POST['notas']; $grave = $_POST['grave']; $asunto = $_POST['asunto'];$fecha = $_POST['fecha'];$informa = $_POST['informa']; $medidaescr = $_POST['medidaescr']; $medida = $_POST['medida']; $expulsionaula = $_POST['expulsionaula']; $confirmado = $_POST['confirmado'];
if ($expulsionaula == ''){ $expulsionaula = 0;}
if ($_POST['grave']=="muy grave" and ($_POST['asunto']=="" or isset($id)) and $_POST['submit1']=="" and $_POST['submit2']=="" and stristr($_SESSION['cargo'],'1') == FALSE) {
?>
<div id="muy_grave" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-danger">Mensaje de Jefatura de Estudios:</h4>
      </div>
      <div class="modal-body">
        <p>Has seleccionado un tipo de problema <b>Muy grave</b>, y este tipo de problema debe ser confirmado por la Jefatura de Estudios. <br> Una vez hayas terminado de registrar el problema, ponte en contacto cuanto antes con el <b>Jefe de Estudios</b> para confirmar los detalles y completar el proceso.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
 <?php
}
?>

<?php
if (isset($_POST['submit1']) or isset($_POST['submit2']))
{
	include("fechoria25.php");
}

// Si se envian datos desde el campo de búsqueda de alumnos, se separa claveal para procesarlo.
if ($_GET['seleccionado']=="1") {
	$claveal=$_GET['nombre'];
	//$nombrel=$claveal;
	$ng_al0=mysqli_query($db_con, "select unidad, apellidos, nombre from alma where claveal = '$claveal'");
	$ng_al=mysqli_fetch_array($ng_al0);
	$unidad=$ng_al[0];
	$nombre_al=$ng_al[1].", ".$ng_al[2];
}
if ($_GET['id'] or $_POST['id']) {
	$result = mysqli_query($db_con, "select alma.apellidos, alma.nombre, alma.unidad, alma.claveal, Fechoria.fecha, Fechoria.notas, Fechoria.asunto, Fechoria.informa, Fechoria.grave, Fechoria.medida, listafechorias.medidas2, Fechoria.expulsion, Fechoria.tutoria, Fechoria.inicio, Fechoria.fin, aula_conv, inicio_aula, fin_aula, Fechoria.horas, expulsionaula, Fechoria.adjunto, Fechoria.confirmado from Fechoria, alma, listafechorias where Fechoria.claveal = alma.claveal and listafechorias.fechoria = Fechoria.asunto  and Fechoria.id = '$id' order by Fechoria.fecha DESC");

	if ($row = mysqli_fetch_array($result))
	{

		$nombre_al = "$row[0], $row[1]";
		$unidad = $row[2];
		$fecha = $row[4];
		$notas = $row[5];

		$informa = $row[7];
		if ($asunto or $grave) {}else{
			$grave = $row[8];
			$asunto = $row[6];
		}
		$expulsionaula = $row[19];
		$medida = $row[9];
		$medidas2 = $row[10];
		$expulsion = $row[11];
		$tutoria = $row[12];
		$inicio = $row[13];
		$fin = $row[14];
		$convivencia = $row[15];
		$inicio_aula = $row[16];
		$fin_aula = $row[17];
		$horas = $row[18];
		$adjunto = $row[20];
		$confirmado = $row[21];
	}
}
?>
<?php
if ($grave=="muy grave" and isset($id) and stristr($_SESSION['cargo'],'1') == TRUE and $confirmado <> 1) {
?>
<div id="muy_grave_dir" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-danger">ATENCIÓN JEFATURA DE ESTUDIOS:</h4>
      </div>
      <div class="modal-body">
				<?php
				if (isset($config['convivencia']['convivencia_seneca']) && $config['convivencia']['convivencia_seneca'] == 1) {
					$titulo_gravedad = 'gravemente contraria';
				}
				else {
					$titulo_gravedad = 'muy grave';
				}
				?>
        <p>Los miembros del Equipo Directivo deben pulsar en el botón de '<b>Actualizar datos</b>' para completar el proceso de registro de un problema de conducta calificado de '<b><?php echo $titulo_gravedad; ?></b>'. Al pulsar sobre el botón se enviarán un SMS o email a los padres y finalizará el proceso.</p>
				<?php if (isset($titulo_gravedad)) unset($titulo_gravedad); ?>
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
 <?php
}
?>
<form method="post" action="" name="Cursos" enctype="multipart/form-data">
<fieldset>

<div class="col-sm-6">

<div class="well">
<div class="form-group" id="datetimepicker1"><label for="fecha">Fecha</label>
<div class="input-group"><input name="fecha" type="text"
	class="form-control" data-date-format="DD-MM-YYYY" id="fecha"
	value="<?php if($fecha == "") { echo date('d-m-Y'); } else { echo $fecha;}?>"
	required> <span class="input-group-addon"><i class="far fa-calendar"></i></span>
</div>
</div>
<div class="form-group"><label for="unidad">Unidad</label> <select
	class="form-control" id="unidad" name="unidad" onchange="submit()">

	<option><?php echo $unidad;?></option>

	<?php unidad();?>
</select></div>
<label for="nombre">Alumno/a</label> <?php echo $nombre;
if ((isset($nombre)) and isset($unidad) and !(is_array($nombre)))
{

	//echo "<OPTION value='$claveal' selected>$nombre_al</OPTION>";

	echo '<select class="form-control" id="nombre" name="nombre" required>';
	$alumnos = mysqli_query($db_con, "SELECT distinct APELLIDOS, NOMBRE, claveal FROM alma WHERE unidad = '$unidad' order by APELLIDOS, NOMBRE asc");

	while($falumno = mysqli_fetch_array($alumnos))
	{

		if ($nombre==$falumno[2]){
			$sel = " selected ";
		}
		else{
			$sel="";
		}

		echo "<OPTION value='$falumno[2]'  $sel>$falumno[0], $falumno[1]</OPTION>";

	}
	?>
	</select>
	<?php
}
else{

	?> <select class="form-control" id="nombre" name="nombre[]"
	multiple='multiple' style='height: 450px;' required>
	<?php
	if ($unidad) {
		$uni = " WHERE unidad like '$unidad%'";
	}
	else{
		$uni="";
	}
	$alumnos = mysqli_query($db_con, " SELECT distinct APELLIDOS, NOMBRE, claveal FROM alma $uni order by APELLIDOS asc");
	while($falumno = mysqli_fetch_array($alumnos))
	{
		$sel="";
		if (is_array($nombre)) {
			foreach($nombre as $n_alumno){

				if ($n_alumno==$falumno[2]){
					$sel = " selected ";
				}
			}
		}

		echo "<OPTION value='$falumno[2]'  $sel>$falumno[0], $falumno[1]</OPTION>";

	}
?>
</select>
<p class="help-block">Puedes seleccionar varios alumnos manteniendo
presionada la tecla <kbd>Ctrl</kbd> mientras haces clic con el ratón
sobre los mismos. <br>Si has seleccionado un Grupo y quieres registrar un Problema a todos los alumnos del Grupo, marca con el ratón el primer alumno y, mientras mantienes pulsada la tecla <kbd>Mayúsculas (Shift)</kbd>, marca el último de los alumnos para seleccionarlos a todos.</p>
<?php
}
?>


</div>
</div>
<div class="col-sm-6">
<div class="well">



<div class="form-group"><label for="grave"> Gravedad</label> <select
	class="form-control" id="grave" name="grave" onchange="submit()" required>
	<?php if (isset($config['convivencia']['convivencia_seneca']) && $config['convivencia']['convivencia_seneca'] == 1): ?>
	<option></option>
	<?php
	$result_gravedad = mysqli_query($db_con, "SELECT DISTINCT `tipo` FROM `listafechorias`");
	while($row_gravedad = mysqli_fetch_array($result_gravedad))
	{
		switch ($row_gravedad[0]) {
			case 'leve':
				$titulo_gravedad = "Otras conductas contrarias no incluidas en el Decreto 327/2010 o en el Decreto 328/2010";
				break;
			case 'grave':
				$titulo_gravedad = "Conductas contrarias (Decreto 327/2010 (art.34) o Decreto 328/2010 (art.33))";
				break;
			case 'muy grave':
				$titulo_gravedad = "Conductas graves (Decreto 327/2010 (art.37) o Decreto 328/2010 (art.36))";
				break;
		}
		echo '<option value="'.$row_gravedad[0].'"'.(($row_gravedad[0] == $grave) ? ' selected' : '').'>'.$titulo_gravedad.'</option>';
	}
	?>
	<?php else: ?>
	<option><?php echo $grave; ?></option>
	<?php tipo(); ?>
	<?php endif; ?>
</select></div>

<div class="form-group"><label for="asunto">Conducta negativa</label>
	<select class="form-control" id="asunto" name="asunto" onchange="submit()" required>
		<option></option>
		<?php
		$result_fechorias = mysqli_query($db_con, "SELECT fechoria FROM listafechorias WHERE tipo = '$grave' ORDER BY fechoria ASC");
		while($row_fechoria = mysqli_fetch_array($result_fechorias))
		{
			echo '<option value="'.$row_fechoria[0].'"'.(($row_fechoria[0] == $asunto) ? ' selected' : '').'>'.$row_fechoria[0].'</option>';
		}
		?>
	</select>
</div>

<div class="form-group"><label class="medida">Medida Adoptada</label> <?php

$tipo = "select distinct medidas from listafechorias where fechoria = '$asunto'";
$tipo1 = mysqli_query($db_con, $tipo);
while($tipo2 = mysqli_fetch_array($tipo1))
{
	if($tipo2[0] == "Amonestación escrita")
	{
		$medidaescr = $tipo2[0];
		echo '<input type="hidden" id="medida" name="medida" value="'.$tipo2[0].'">';
	}
	else
	{
		echo '<input  type="hidden"id="medida" name="medida" value="'.$tipo2[0].'">';
	}
}

?> <input type="text" value="<?php echo $medidaescr;?>" readonly
	class="form-control" /></div>

<div class="form-group"><label for="medidas">Medidas complementarias que
deben tomarse</label> <textarea class="form-control" id="medidas"
	name="medidas" rows="7" disabled><?php if($medidas){ echo $medidad; }else{  medida2($asunto);} ?></textarea>
</div>

<?php
if($grave == 'grave' or $grave == 'muy grave'){
	?>
<div class="checkbox"><label> <input type="checkbox" id="expulsionaula"
	name="expulsionaula" value="1"
	<?php  if ($expulsionaula == "1") { echo " checked ";}?>> El alumno ha
sido <u>expulsado</u> del aula </label></div>

	<?php
}
?>

<div class="form-group"><label for="notas">Descripción:</label> <textarea
	class="form-control" id="notas" name="notas" rows="7"
	placeholder="Describe aquí los detalles del incidente..." required><?php echo $notas; ?></textarea>
</div>

<div class="form-group">
	<label for="adjunto">Adjuntar archivo en formato imagen o PDF <span class="text-muted">(opcional)</span>:</label>
	<?php if (isset($id) && $id && isset($adjunto) && ! empty($adjunto)): ?>
	<ul class="list-group">
		<li class="list-group-item" style="word-wrap: break-word;">
			<a href="//<?php echo $config['dominio']; ?>/intranet/lib/obtenerAdjunto.php?mod=convivencia&file=<?php echo $adjunto; ?>" target="_blank"><?php echo $adjunto; ?></a>
		</li>
	</ul>
	<?php endif; ?>

	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo php_directive_value_to_bytes('upload_max_filesize'); ?>">
	<input type="file" id="adjunto" name="adjunto" accept="image/*,application/pdf">
	<p class="help-block"><small>Tamaño máximo: <?php echo ini_get('upload_max_filesize'); ?>b.</small></p>
</div>
<br>

<?php
if ($id && (stristr($_SESSION['cargo'],'1') == TRUE OR stristr($_SESSION['cargo'],'b') == TRUE)) {
	?>

<div class="form-group"><label for="informa">Profesor</label> <select
	class="form-control" id="informa" name="informa">
	<?php
	if ($id) {
		echo "<OPTION>".$informa."</OPTION>";
	}

	$profe = mysqli_query($db_con, " SELECT distinct prof FROM horw order by prof asc");
	while($filaprofe = mysqli_fetch_array($profe)) {
		echo"<OPTION>$filaprofe[0]</OPTION>";
	}
	?>
</select></div>
	<?php
}
else{
	if(stristr($_SESSION['cargo'],'1') == TRUE OR stristr($_SESSION['cargo'],'b') == TRUE){
		?>
<div class="form-group"><label class="informa">Profesor</label> <select
	class="form-control" id="informa" name="informa">
	<?php
	if ($id) {
		echo '<OPTION value="'.$informa.'">'.nomprofesor($informa).'</OPTION>';
	}
	else{
		echo '<OPTION value="'.$_SESSION['profi'].'">'.nomprofesor($_SESSION['profi']).'</OPTION>';
	}
	$profe = mysqli_query($db_con, " SELECT distinct prof FROM horw order by prof asc");
	while($filaprofe = mysqli_fetch_array($profe)) {
		echo '<OPTION value="'.$filaprofe[0].'">'.nomprofesor($filaprofe[0]).'</OPTION>';
	}
	?>
</select></div>
	<?php
	}
	else{
		?> <input type="hidden" id="informa" name="informa"
	value="<?php echo $_SESSION['profi'];?>"> <?php
	}

}

?></div>


<hr />
<?php
if(stristr($_SESSION['cargo'],'1') == TRUE and isset($id) and $grave=="muy grave"){
	echo '<input type="hidden" name="id" value="'.$id.'">';
	echo '<input type="hidden" name="claveal" value="'.$claveal.'">';
	echo '<input type="hidden" name="confirmado" value="1">';
	echo '<input name = "submit1" type="submit" value="Actualizar datos" class="btn btn-warning btn-lg">';
}
elseif (isset($id)) {
	echo '<input type="hidden" name="id" value="'.$id.'">';
	echo '<input type="hidden" name="claveal" value="'.$claveal.'">';
	echo '<input name = "submit2" type="submit" value="Actualizar datos" class="btn btn-warning btn-lg">';
}
else{
	if(stristr($_SESSION['cargo'],'1') == TRUE and $grave=="muy grave"){
	echo '<input type="hidden" name="confirmado" value="1">';
	}
	echo '<input name=submit1 type=submit value="Registrar" class="btn btn-primary btn-lg">';
}
?></div>
</fieldset>
</form>
</div>
</div>

	<?php include("../../pie.php"); ?>

<script>
	$(function ()
	{
		$('#muy_grave').modal('show')
	});

	$(function ()
	{
		$('#muy_grave_dir').modal('show')
	});

	$(function ()
	{
		$('#datetimepicker1').datetimepicker({
			language: 'es',
			pickTime: false
		})
	});
	</script>

</body>
</html>
