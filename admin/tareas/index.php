<?php
require('../../bootstrap.php');


$profesor = $_SESSION['profi'];
$cargo = $_SESSION['cargo'];

include("../../menu.php");
include("menu.php");
?>

<div class="container">
<div class="row">
<div class="page-header">
  <h2>Informes de Tareas <small> Expulsión o ausencia del Alumno</small></h2>
</div>
<br>

<div class="col-md-8 col-md-offset-2">

<?php
// Buscamos los grupos que tiene el Profesor, con su asignatura y nivel
	$SQLcurso = "select distinct grupo, materia, nivel from profesores where profesor = '$profesor'";
	//echo $SQLcurso;
  $esPT_o_REF = 0;
$resultcurso = mysqli_query($db_con, $SQLcurso);
	while($rowcurso = mysqli_fetch_array($resultcurso))
	{
	$unidad = $rowcurso[0];
	$asignatura = $rowcurso[1];

// Problema con asignaturas comunes de Bachillerato con distinto código
	if(strlen($rowcurso[2])>15){
		$rowcurso[2] = substr($rowcurso[2],0,15);
	}

	$asigna0 = "select codigo from asignaturas where nombre = '$asignatura' and curso like '$rowcurso[2]%' and abrev not like '%\_%'";
		// echo $asigna0."<br>";
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

	$hoy=date('Y-m-d');
	$nuevafecha = strtotime ( '-2 day' , strtotime ( $hoy ) ) ;
	$nuevafecha = date ( 'Y-m-d' , $nuevafecha );

// Buscamos los alumnos de esos grupos que tienen informes de Tutoría activos y además tienen esa asignatura en su el campo combasi
  if ($esPT_o_REF) {
    $query = "SELECT tareas_alumnos.ID, tareas_alumnos.CLAVEAL, tareas_alumnos.APELLIDOS, tareas_alumnos.NOMBRE, tareas_alumnos.unidad, alma.matriculas, tareas_alumnos.FECHA, tareas_alumnos.DURACION FROM tareas_alumnos, alma WHERE tareas_alumnos.claveal = alma.claveal and  date(tareas_alumnos.FECHA)>='$nuevafecha' and tareas_alumnos. unidad = '$unidad' ORDER BY tareas_alumnos.FECHA asc";
  }
  else {
    $query = "SELECT tareas_alumnos.ID, tareas_alumnos.CLAVEAL, tareas_alumnos.APELLIDOS, tareas_alumnos.NOMBRE, tareas_alumnos.unidad, alma.matriculas, tareas_alumnos.FECHA, tareas_alumnos.DURACION FROM tareas_alumnos, alma WHERE tareas_alumnos.claveal = alma.claveal and  date(tareas_alumnos.FECHA)>='$nuevafecha' and tareas_alumnos. unidad = '$unidad' and ($texto_asig2) ORDER BY tareas_alumnos.FECHA asc";
  }
	//echo "$query<br>";
	$result = mysqli_query($db_con, $query);
	$result0 = mysqli_query($db_con, "select tutor from FTUTORES where unidad = '$unidad'" );
	$row0 = mysqli_fetch_array ( $result0 );
	$tuti = $row0[0];



	if (mysqli_num_rows($result) < 1){ }
	else{
	$si_al.=1;
	echo "<form name='consulta' method='POST' action='tutoria.php'>";
//$num_informe = mysqli_num_rows($sql1);
echo "<p class='lead text-info'>$unidad <br /><small class='text-muted'>$n_asig</small></p>";
echo "<table align=center class='table'><tr class='active'>";
echo "<th>Alumno</th>
<th>Fecha Inicio</th>
<th></th>
</tr>";
$count = "";
	while($row = mysqli_fetch_array($result))
	{
	$nc_grupo = $row[1];

	$asig_bach = mysqli_query($db_con,"select distinct codigo from materias where nombre like (select distinct nombre from materias where codigo = '$codasi' limit 1) and grupo like '$unidad' and codigo not like '$codasi' and abrev not like '%\_%'");
		if (mysqli_num_rows($asig_bach)>0) {
			$as_bach=mysqli_fetch_array($asig_bach);
			$cod_asig_bach = $as_bach[0];
			$extra_asig = "or asignatura like '$cod_asig_bach'";

		}
		else{
			$extra_asig = "";
		}

	$sel = mysqli_query($db_con,"select alumnos from grupos where profesor = '$pr' and curso = '$unidad' and (asignatura = '$codasi' $extra_asig)");
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
// Comprobamos que el profesor no ha rellenado el informe de esa asignatura
$hay = "select * from tareas_profesor where id_alumno = '$row[0]' and asignatura = '$asignatura'";
$si = mysqli_query($db_con, $hay);
if (mysqli_num_rows($si) > 0)
		{
		echo "<tr><TD> $row[3] $row[2]</td>
   <TD colspan='1' nowrap style='vertical-align:middle'>$row[6] <span class='label label-success'>Informe ya rellenado</span></td>";
   echo "<TD>
			<a href='infocompleto.php?id=$row[0]&c_asig=$asignatura' class=' btn-mini'><i class='fas fa-search' title='Ver Informe'> </i></a>";
   echo "<a href='informar.php?id=$row[0]' class=''><i class='fas fa-pencil-alt fa-fw fa-lg' data-bs='tooltip' title='Redactar Informe'></i></a>";
   if (stristr($cargo,'1') == TRUE or ($tuti == $_SESSION['profi'])) {
   	echo "<a href='borrar_informe.php?id=$row[0]&del=1' class=' btn-mini' data-bb='confirm-delete'><i class='far fa-trash-alt' title='Borrar Informe' ></i></a>";
   }
			echo "</td>";
   }
   		else
		{
		$count = $count + 1;
		echo "<tr><TD>
	 $row[3] $row[2]</td>
   <TD>$row[6]</td>
   ";
	 echo "
	 <input type='hidden' name='profesor' value='$profesor'>";
			echo "
      <td>";
	  if (mysqli_num_rows($si) > 0 and $count < 1)
		{}
		else{
			echo "<a href='infocompleto.php?id=$row[0]&c_asig=$asignatura' class=' btn-mini'><i class='fas fa-search' title='Ver Informe'></i></a>";
		 if (stristr($cargo,'1') == TRUE or ($tuti == $_SESSION['profi'])) {
   	echo "&nbsp;<a href='borrar_informe.php?id=$row[0]&del=1' class=' btn-mini' data-bb='confirm-delete'><i class='far fa-trash-alt' title='Borrar Informe' ></i></a>";
   }
		}
	  if (mysqli_num_rows($si) > 0 and $count < 1)
		{}
		else{
echo "&nbsp;<a href='informar.php?id=$row[0]' class=' btn-mini'><i class='fas fa-pencil-alt' title='Redactar Informe'></i></a>";
			}
		}
	}
}
	 echo "</td>
	 </tr>
	 </table><br /></form><hr>";

		}
	}
}

if (strstr($si_al,"1")==FALSE) {
 			echo "<div class='alert alert-info' align='center'><p><i class='fas fa-check-square-o
'> </i> No hay Informes de Tareas activos para ti. </p></div>";
 		}
?>
</div>
</div>
</div>
<?php include("../../pie.php");?>
</body>
</html>
