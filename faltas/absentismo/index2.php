<?php
require('../../bootstrap.php');


include("../../menu.php");
include("../menu.php");

if (isset($_GET['mes'])) {$mes = $_GET['mes'];}elseif (isset($_POST['mes'])) {$mes = $_POST['mes'];}else{$mes="";}
if (isset($_GET['claveal'])) {$claveal = $_GET['claveal'];}elseif (isset($_POST['claveal'])) {$claveal = $_POST['claveal'];}else{$claveal="";}
if (isset($_GET['del'])) {$del = $_GET['del'];}elseif (isset($_POST['del'])) {$del = $_POST['del'];}else{$del="";}
if (isset($_GET['inf'])) {$inf = $_GET['inf'];}elseif (isset($_POST['inf'])) {$inf = $_POST['inf'];}else{$inf="";}
if (isset($_GET['texto'])) {$texto = $_GET['texto'];}elseif (isset($_POST['texto'])) {$texto = $_POST['texto'];}else{$texto="";}
if (isset($_GET['texto2'])) {$texto2 = $_GET['texto2'];}elseif (isset($_POST['texto2'])) {$texto2 = $_POST['texto2'];}else{$texto2="";}
$mas2="";
?>
<?php
if (strstr($_SESSION['cargo'],'8')==TRUE) {
	$mas="";
	$titulo="Departamento de orientación  ";
	$upd=" orientacion='$texto' ";
}
if (strstr($_SESSION['cargo'],'2')==TRUE and strstr($_SESSION['cargo'],'8')==FALSE) {
	$tut=$_SESSION['profi'];
	$tutor=mysqli_query($db_con, "select unidad from FTUTORES where tutor='$tut'");
	$d_tutor=mysqli_fetch_array($tutor);
	$mas=" and absentismo.unidad='$d_tutor[0]' and tutoria like '' ";
	$mas2=" and tutoria like '' ";
	$titulo="Tutor: $d_tutor[0]";
	$upd=" tutoria='$texto' ";
}
if (strstr($_SESSION['cargo'],'1')==TRUE) {
	$mas="";
	$titulo="Jefatura de Estudios ";
	$upd=" jefatura='$texto', serv_sociales='$texto2' ";
}
?>
<div class="container">
<div class="row">
<div class="page-header">
  <h2>Faltas de Asistencia <small> Alumnos absentistas</small></h2>
</div>
<br />
<?php
// Borramos alumnos
if ($del=='1') {
	mysqli_query($db_con, "delete from absentismo where claveal = '$claveal' and mes = '$mes'");
	echo '<div align="center""><div class="alert alert-warning alert-block fade in" align="left">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
Los datos del alumno han sido borrados de la Base de datos.
			</div></div><br />';
}
// Procesamos datos si se ha dado al botón 
if (isset($_POST['submit'])) {
mysqli_query($db_con, "update absentismo set $upd where claveal='$claveal' and mes='$mes'")	;
// echo "update absentismo set $upd where claveal='$claveal' and mes='$mes'";
echo '<div align="center""><div class="alert alert-success alert-block fade in" align="left">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
Los datos de los alumnos absentistas se han actualizado.
			</div></div><br />';
	}

                    if($mes=='Septiembre'){$mes='09';}
                    if($mes=='Octubre'){$mes='10';}
                    if($mes=='Noviembre'){$mes='11';}
                    if($mes=='Diciembre'){$mes='12';}
                    if($mes=='Enero'){$mes='01';}
                    if($mes=='Febrero'){$mes='02';}
                    if($mes=='Marzo'){$mes='03';}
                    if($mes=='Abril'){$mes='04';}
                    if($mes=='Mayo'){$mes='05';}
                    if($mes=='Junio'){$mes='06';}
// Vamos a rellenar informe

if ($_GET['inf']=="1") {
$al=mysqli_query($db_con, "SELECT distinct apellidos, nombre, absentismo.unidad, matriculas, numero, jefatura, orientacion, tutoria, serv_sociales FROM absentismo, alma WHERE alma.claveal = absentismo.claveal and absentismo.claveal='$claveal' and mes='$mes' $mas2");
if (mysqli_num_rows($al)>0) {
$datos=mysqli_fetch_array($al);
if (strstr($_SESSION['cargo'],'1')==TRUE) {$obs=$datos[5];$obs2=$datos[8];$obs3=$datos[6];$obs4=$datos[7];}elseif (strstr($_SESSION['cargo'],'8')==TRUE){$obs=$datos[6];$obs2=$datos[8];$obs3=$datos[6];$obs4=$datos[7];$obs5=$datos[5];}else {$obs=$datos[7];}
?>

<?php
echo  "<table class='table' style='width:auto' align=center><tr><th align='center'> ALUMNO </th><th align='center'> CURSO </th>
<th align='center'> MES </th><th align='center'> Nº FALTAS </th></tr>
<tr class='h4'><td align='center'>$datos[0], $datos[1]</td><td id='' align='center'>$datos[2]</td><td id='' align='center'>$mes</td><td id='' align='center'>$datos[4]</td></tr></table><br />";
?>

	<?php $result = mysqli_query($db_con, "SELECT correo FROM control WHERE claveal='$claveal' LIMIT 1"); ?>
		<?php $row2 = mysqli_fetch_array($result); ?>
		<?php mysqli_free_result($result); ?>

		<?php $result = mysqli_query($db_con, "select distinct alma.claveal, alma.DNI, alma.fecha, alma.domicilio, alma.telefono, alma.padre, alma.matriculas, telefonourgencia, paisnacimiento, correo, nacionalidad, edad, curso, alma.unidad, numeroexpediente from alma where alma.claveal= '$claveal'"); ?>

		<?php if ($row = mysqli_fetch_array($result)):
		$nivel_alumno = $row['curso'];
		$tut = mysqli_query($db_con,"SELECT tutor FROM FTUTORES WHERE unidad = '".$row['unidad']."'");
		$tuto = mysqli_fetch_array($tut);
		$tr_tutor = explode(", ",$tuto['tutor']);
		$tutor = $tr_tutor[1]." ".$tr_tutor[0];
		?>
		<!-- SCAFFOLDING -->
		<div class="well col-sm-10 col-sm-offset-1">
		<div class="row">

			<!-- COLUMNA IZQUIERDA -->
			<div class="col-sm-2 text-center hidden-xs">
				<?php if ($foto = obtener_foto_alumno($claveal)): ?>
				<img class="img-thumbnail" src="../../xml/fotos/<?php echo $foto; ?>" style="width: 100px !important;" alt="<?php echo $apellido.', '.$nombrepil; ?>">
				<?php else: ?>
				<h2><span class="img-thumbnail far fa-user fa-fw fa-3x" style="width: 120px !important;"></span></h2>
				<?php endif; ?>

			</div><!-- /.col-sm-2 -->


			<!-- COLUMNA DERECHA -->
			<div class="col-sm-10">

				<div class="row">

					<div class="col-sm-6">

						<dl class="dl-horizontal">
						  <dt>DNI / Pasaporte</dt>
						  <dd><?php echo ($row['DNI'] != "") ? $row['DNI']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Fecha de nacimiento</dt>
						  <dd><?php echo ($row['fecha'] != "") ? $row['fecha']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Edad</dt>
						  <dd><?php echo ($row['edad'] != "") ? $row['edad'].' años': '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Domicilio</dt>
						  <dd><?php echo ($row['domicilio'] != "") ? $row['domicilio']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Nacionalidad</dt>
						  <dd><?php echo ($row['nacionalidad'] != "") ? $row['nacionalidad']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Teléfono</dt>
						  <dd><?php echo ($row['telefono'] != "") ? '<a href="tel:'.$row['telefono'].'">'.$row['telefono'].'</a>': '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Teléfono urgencias</dt>
						  <dd><?php echo ($row['telefonourgencia'] != "") ? '<a href="tel:'.$row['telefonourgencia'].'">'.$row['telefonourgencia'].'</a>': '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Correo electrónico</dt>
							<?php
							if ($row['correo'] != "") {
								$correo = '<a href="mailto:'.$row['correo'].'">'.$row['correo'].'</a>';
							}
							elseif($row2['correo'] != "") {
								$correo = '<a href="mailto:'.$row2['correo'].'">'.$row2['correo'].'</a>';
							}
							else {
								$correo = '<span class="text-muted">Sin registrar</span>';
							}
							?>
						  <dd><?php echo $correo ?></dd>
						  <dt>Representante legal</dt>
						  <dd><?php echo ($row['padre'] != "") ? $row['padre']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
						</dl>

					</div><!-- /.col-sm-6 -->

					<div class="col-sm-6">

						<dl class="dl-horizontal">
						  <dt><abbr data-bs="tooltip" title="Número de Identificación Escolar">N.I.E.</abbr></dt>
						  <dd><?php echo ($row['claveal'] != "") ? $row['claveal']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Nº Expediente</dt>
						  <dd><?php echo ($row['numeroexpediente'] != "") ? $row['numeroexpediente']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Año académico</dt>
						  <dd><?php echo $c_escolar; ?></dd>
						  <dt>Curso</dt>
						  <dd><?php echo ($row['curso'] != "") ? $row['curso']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Unidad</dt>
						  <dd><?php echo ($row['unidad'] != "") ? $row['unidad']: '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Tutor</dt>
						  <dd><?php echo ($tutor != "") ? mb_convert_case($tutor, MB_CASE_TITLE, 'UTF-8'): '<span class="text-muted">Sin registrar</span>'; ?></dd>
						  <dt>Repetidor/a</dt>
						  <dd><?php echo ($row['matriculas'] > 1) ? 'Sí': 'No'; ?></dd>
							<?php if (isset($config['convivencia']['puntos']['habilitado']) && $config['convivencia']['puntos']['habilitado']): ?>
							<dt>Puntos</dt>
							<dd><?php echo sistemaPuntos($row['claveal']); ?></dd>
							<?php endif; ?>
						</dl>

					</div><!-- /.col-sm-6 -->

				</div><!-- /.row -->

			</div><!-- /.col-sm-10 -->

		</div><!-- /.row -->
		</div><!-- /.well -->

		</div><!-- /.row -->

		<br>

		<?php else: ?>

		<h3>No hay información sobre el alumno/a seleccionado.</h3>

		<?php endif; ?>

<?php
echo "<div class='row'><div class='col-sm-8 col-sm-offset-2'>
";	

echo "<form enctype='multipart/form-data' action='index2.php' method='post'>";
?>
<input name="claveal" type="hidden" value="<?php echo $claveal;?>">
<input name="mes" type="hidden" value="<?php echo $mes;?>">
<div class="form-group"><label>Observaciones</label>
<textarea name="texto" title="Informe de Alumno absentista." class="form-control" rows="12"><?php echo $obs;?></textarea></div>
<?php
if (strstr($_SESSION['cargo'],'1')==TRUE OR strstr($_SESSION['cargo'],'8')==TRUE) {
if (strstr($_SESSION['cargo'],'8')==TRUE) { ?>
<div class="form-group"><label>Informe de Jefatura</label>
<textarea name="texto2" title="Informe de Alumno absentista." class="form-control" rows="12" readonly><?php echo $obs5;?></textarea></div>
<?php	
}
?>
<div class="form-group"><label>Informe de Servicios Sociales</label>
<textarea name="texto2" title="Informe de Alumno absentista." class="form-control" rows="12"><?php echo $obs2;?></textarea></div>

<div class="form-group"><label>Informe del Tutor</label>
<textarea name="" title="Informe de Alumno absentista." class="form-control" rows="12" readonly><?php echo $obs4;?></textarea></div>
<?php 
if (strstr($_SESSION['cargo'],'8')==FALSE) { ?>
<div class="form-group"><label>Informe de Orientación</label>
<textarea name="" title="Informe de Alumno absentista." class="form-control" rows="12" readonly><?php echo $obs3;?></textarea></div>
<?php
}
}
?>
<input type="submit" name="submit" value="Enviar Informe" class="btn btn-primary">
<?php
echo "</form>";
echo "<hr>";
}
echo "</div></div>";
}
?>
<div class="row">
<div class="col-sm-10 col-sm-offset-1">
<br />
<legend align="center">Alumnos con informes de absentismo pendiente <br /><span class="text-info"><?php echo  $titulo;?></span> </legend><br />
<?php

$SQL0 = "SELECT absentismo.CLAVEAL, apellidos, nombre, absentismo.unidad, matriculas, numero, mes, jefatura, orientacion, tutoria, serv_sociales, alma.fecha  FROM absentismo, alma WHERE alma.claveal = absentismo.claveal and mes='$mes' $mas  order by unidad";

$result0 = mysqli_query($db_con, $SQL0);
  if (mysqli_num_rows($result0)>0) {
echo  "<center><table class='table table-striped table-bordered' style='width:auto'>\n";
        echo "<tr><th align='center' colspan=2>ALUMNO</th><th align='center'>CURSO</th>
        <th align='center'>MES</th><th align='center'>Nº FALTAS</th>";

        if (strstr($_SESSION['cargo'],'1')==TRUE OR strstr($_SESSION['cargo'],'8')==TRUE) {
        	echo "<th>Jef.</th><th>Orienta.</th><th>Tut.</th><th>S. Soc.</th><th class='no_imprimir'></th>";
        }
		echo "</tr>";
 while  ($row0 = mysqli_fetch_array($result0)){
 	$claveal=$row0[0];
 	$mes=$row0[6];
 	$numero=$row0[5];
 	$unidad=$row0[3];
 	$nombre=$row0[2];
 	$apellidos=$row0[1];
 	$jefatura=$row0[7];
 	$orientacion=$row0[8];
 	$tutoria=$row0[9];
 	$s_sociales=$row0[10];

 	$nacim = cambia_fecha($row0[11]);
	$nacimien = date("Y-m-d",strtotime($nacim."+ 16 year"));
	$edad = strtotime($nacimien);
	$fecha_hoy = strtotime("now");

 if ($edad > $fecha_hoy) {

 	if (strlen($jefatura)>0) {$chj=" checked ";}else{$chj="";}if(strlen($orientacion)>0) {$cho=" checked ";}else{$cho="";}if (strlen($tutoria)>0) {$cht=" checked ";}else{$cht="";} if (strlen($s_sociales)>0) {$chs=" checked ";}else{$chs="";}
	echo "<tr><td  align='left'>";
	if ($foto = obtener_foto_alumno($claveal)) {
    	echo '<img class="img-thumbnail" src="../../xml/fotos/'.$foto.'" style="width: 45px !important;" alt="">';
	}
	else {
		echo '<span class="img-thumbnail far fa-user fa-fw fa-2x" style="width: 45px !important;"></span>';
	}
	echo "</td>";
	echo "<td>$apellidos, $nombre</td><td>$unidad</td><td>$mes</td><td>$numero</td>";
        if (strstr($_SESSION['cargo'],'1')==TRUE OR strstr($_SESSION['cargo'],'8')==TRUE) {
		echo "<td><input type='checkbox' disabled $chj></td><td><input type='checkbox' disabled $cho></td><td><input type='checkbox' disabled $cht></td><td><input type='checkbox' disabled $chs></td>";
        }
		echo "<td align='center' class='no_imprimir'><a href='index2.php?claveal=$claveal&mes=$mes&inf=1'> <i class='fas fa-pencil-alt'> </i></a>";
		if (strstr($_SESSION['cargo'],'1')==TRUE) {
				echo "<a href='index2.php?claveal=$claveal&mes=$mes&del=1' data-bb='confirm-delete'> <i class='far fa-trash-alt'> </i></a>";
		}

	echo "</td>";
	
	echo "</tr>";
	}
}





	echo "</table>";
	echo "<div class='no_imprimir'><br /><input type='button' value='Imprimir todos' name='boton2' class='btn btn-primary' onclick='window.location=\"imprimir.php?mes=".$mes."\"' /></div>";   
	}
else
{
	echo '<div align="center""><div class="alert alert-warning alert-block fade in" align="left">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
Parece que no hay alumnos absentistas registrados en ese mes. Si te has equivocado, vueve atr&aacute;s e int&eacute;ntalo de nuevo.			</div></div>';
}
  ?>
</div>
</div>
</div>
</div>

	<?php include("../../pie.php"); ?>
	
</body>
</html>
