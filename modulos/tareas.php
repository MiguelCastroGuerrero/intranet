<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed'); ?>

<div class='alert alert-danger'>
<p class="lead"><i class='far fa-bell'> </i> Informes de tareas activos</p>
<br />
<?php

$resultcurs = mysqli_query($db_con, $SQLcurso);
while($rowcurs = mysqli_fetch_array($resultcurs))
{
	$n_tareas = $n_tareas+1;
	$curso = $rowcurs[0];
	$unidad_t = $curso;
	$asignatura = $rowcurs[1];
	$esPT_o_REF = 0;

	// Problema con asignaturas comunes de Bachillerato con distinto código
	if(strlen($rowcurs[2])>15){
		$rowcurs[2] = substr($rowcurs[2],0,15);
	}

	$asigna0 = "select codigo from asignaturas where nombre = '$asignatura' and curso like '$rowcurs[2]%' and abrev not like '%\_%'";
	$asigna1 = mysqli_query($db_con, $asigna0);

	if(mysqli_num_rows($asigna1)>1){
	$texto_asig2="";
	while($asigna2 = mysqli_fetch_array($asigna1)){
		$codasi = $asigna2[0];
		$texto_asig2.=" combasi like '%$asigna2[0]:%' or";
		$c_asig2.=" asignatura = '$asigna2[0]' or";
		if ($asigna2[0] == '21' || $asigna2[0] == '136') $esPT_o_REF = 1;
	}
	$texto_asig2=substr($texto_asig2,0,-3);
	$c_asig2=substr($c_asig2,0,-3);
	}
	else{
		$asigna2 = mysqli_fetch_array($asigna1);
		$codasi = $asigna2[0];
		$texto_asig2=" combasi like '%$asigna2[0]:%'";
		$c_asig2=" asignatura = '$asigna2[0]'";
		if ($asigna2[0] == '21' || $asigna2[0] == '136') $esPT_o_REF = 1;
	}

	if($c_asig2){

	$hoy = date('Y-m-d');
	$nuevafecha = strtotime ( '-2 day' , strtotime ( $hoy ) ) ;
	$nuevafecha = date ( 'Y-m-d' , $nuevafecha );

	if ($esPT_o_REF) {
		$query = "SELECT tareas_alumnos.ID, tareas_alumnos.CLAVEAL, tareas_alumnos.APELLIDOS, tareas_alumnos.NOMBRE, tareas_alumnos.unidad, alma.matriculas, tareas_alumnos.FECHA, tareas_alumnos.DURACION FROM tareas_alumnos, alma WHERE tareas_alumnos.claveal = alma.claveal and date(tareas_alumnos.FECHA)>='$nuevafecha' and tareas_alumnos.unidad = '$unidad_t' ORDER BY tareas_alumnos.FECHA asc";
	}
	else {
		$query = "SELECT tareas_alumnos.ID, tareas_alumnos.CLAVEAL, tareas_alumnos.APELLIDOS, tareas_alumnos.NOMBRE, tareas_alumnos.unidad, alma.matriculas, tareas_alumnos.FECHA, tareas_alumnos.DURACION FROM tareas_alumnos, alma WHERE tareas_alumnos.claveal = alma.claveal and date(tareas_alumnos.FECHA)>='$nuevafecha' and tareas_alumnos.unidad = '$unidad_t' and ($texto_asig2) ORDER BY tareas_alumnos.FECHA asc";
	}
	$result = mysqli_query($db_con, $query);
	if (mysqli_num_rows($result) > 0)
	{
		$n_t=1;
		while($row1 = mysqli_fetch_array($result))
		{
			$id = $row1[0];
			$nc_grupo = $row1['CLAVEAL'];
			$sel = mysqli_query($db_con,"select alumnos from grupos where profesor = '$pr' and curso = '$unidad_t' and asignatura = '$codasi'");
			$hay_grupo = mysqli_num_rows($sel);
			if ($hay_grupo>0) {
				$sel_al = mysqli_fetch_array($sel);
				$al_sel = explode(",",$sel_al[0]);
				$hay_al="";
				foreach($al_sel as $num_al){
					if ($num_al == $nc_grupo) {
						$hay_al = "1";;
					}
				}
			}

			if ($hay_al=="1" or $hay_grupo<1) {

				$hay = "select * from tareas_profesor where id_alumno = '$row1[0]'  and asignatura = '$asignatura'";
				$si = mysqli_query($db_con, $hay);
				if (mysqli_num_rows($si) > 0)
				{ }
				else
				{
					$fechac = explode("-",$row1[6]);
					echo "<p>$fechac[2]-$fechac[1]-$fechac[0] <a class='alert-link' data-toggle='modal' href='#tarea$n_tareas'> $row1[3] $row1[2]</a> -- $curso $row[6]  &nbsp;&nbsp;&nbsp;&nbsp;
	<span class=' pull-right'>
	<a href='./admin/tareas/infocompleto.php?id=$row1[0]' class='alert-link' data-bs='tooltip' title='Ver informe'><span class='fas fa-search fa-fw fa-lg'></span></a>
	<a href='./admin/tareas/informar.php?id=$row1[0]' class='alert-link' data-bs='tooltip' title='Rellenar'><span class='fas fa-pencil-alt fa-fw fa-lg'></span></a>
	</span>
	</p>";
					?>
<div class="modal fade" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true"
	id="tarea<?php echo $n_tareas;?>">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title" style="color: #333;">Informe de Tareas<br><small><?php echo "$row1[3] $row1[2]";?></small></h4>
</div>
<div class="modal-body">
<?php
$alumno=mysqli_query($db_con, "SELECT APELLIDOS,NOMBRE,tareas_alumnos.unidad,tareas_alumnos.GRUPO,tutor, FECHA, duracion, claveal FROM tareas_alumnos, FTUTORES WHERE FTUTORES.unidad = tareas_alumnos.unidad and FTUTORES.grupo = tareas_alumnos.grupo and tareas_alumnos.ID='$id'");
$dalumno = mysqli_fetch_array($alumno);
$claveal=$dalumno[7];
$datos=mysqli_query($db_con, "SELECT asignatura, tarea FROM tareas_profesor WHERE id_alumno='$id'");
if(mysqli_num_rows($datos) > 0)
{
	while($informe = mysqli_fetch_array($datos))
	{
		echo "<p style='color:#08c'>".$informe[0].". <span style='color:#555'>".$informe[1]."</span></p>";
	}
}
else
{
	echo '<p style="color:#08c"> Los Profesores no han rellenado aún su Informe de tareas</p>';
}
?></div>
<div class="modal-footer"><a href="#" class="btn btn-primary"
	data-dismiss="modal">Cerrar</a></div>
</div>
</div>
</div>
<?php
					}
				}
			}
		}
	}
}
if ($n_t==1) {
	echo "<br>";
}
?></div>
