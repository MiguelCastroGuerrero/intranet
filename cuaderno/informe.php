<?php
require('../bootstrap.php');

$dia = $_GET['dia'];
$hora = $_GET['hora'];
foreach($_GET as $key => $val)
{
  ${$key} = $val;
}

$unidad = $curso;

$alumnos_unidad = array();
$result_alumnos_unidad = mysqli_query($db_con, "SELECT claveal FROM alma WHERE unidad = '$unidad' AND combasi LIKE '%$asignatura%' ORDER BY apellidos ASC, nombre ASC");
$total_alumnos_unidad = mysqli_num_rows($result_alumnos_unidad);
while ($row_alumnos_unidad = mysqli_fetch_array($result_alumnos_unidad)) {
  array_push($alumnos_unidad, $row_alumnos_unidad['claveal']);
}

$key_claveal = array_search($claveal, $alumnos_unidad);

if((isset($_GET['anterior']) && ($_GET['anterior'] == 1)) && ($key_claveal > 0)) {
  $claveal = $alumnos_unidad[$key_claveal - 1];
}

if((isset($_GET['siguiente']) && ($_GET['siguiente'] == 1)) && ($key_claveal < ($total_alumnos_unidad - 1))){
  $claveal = $alumnos_unidad[$key_claveal + 1];
}

if (! empty($claveal)) {
  $key_claveal = array_search($claveal, $alumnos_unidad);
  $alum = mysqli_query($db_con, "SELECT claveal, unidad, nombre, apellidos FROM alma WHERE claveal = '$claveal' LIMIT 1");
  $alumno = mysqli_fetch_array($alum);
  $claveal = $alumno[0];
  $unidad = $alumno[1];
  $nombre = $alumno[2];
  $apellidos = $alumno[3];
  $curso = $unidad;
}
else {
  die ('Error al obtener el NIE del alumno.');
}

include("../menu.php");
include("menu.php");
?>
<div class="container">
<div class="row">
<?php
echo "<div class='page-header'>";
$n_profe = explode(", ",$pr);
$nombre_profe = "$n_profe[1] $n_profe[0]";
echo "<h2 class='no_imprimir'>Cuaderno de Notas&nbsp;&nbsp;<small> Informes personales</small></h2>";
echo "</div>";
echo '<div align="center">';
?>
<div class="col-sm-8 col-sm-offset-2">
<?php
echo "<h3><span class='label label-info' style='padding:8px'>$curso -- $nom_asig </span></h3><br>";
echo "<br /><div class='well'><table style='width:100%'><tr><td style='text-align:center;width:90%'><h4 class='text-info'>";

if ($key_claveal > 0) {
  $mens_ant = "informe.php?profesor=$profesor&claveal=$claveal&curso=$curso&asignatura=$asignatura&nombre=$nombre&apellidos=$apellidos&nom_asig=$nom_asig&dia=$dia&hora=$hora&anterior=1";
  echo '<a class="btn btn-primary btn-sm" href="'.$mens_ant.'"><i class="fas fa-chevron-left"></i> Anterior</a>';
}
else {
   echo '<a class="btn btn-primary btn-sm disabled" href="#"><i class="fas fa-chevron-left"></i> Anterior</a>';
}

echo "&nbsp;&nbsp; $nombre $apellidos &nbsp;&nbsp;"; 

if ($key_claveal < ($total_alumnos_unidad - 1)) {
  $mens_sig = "informe.php?profesor=$profesor&claveal=$claveal&curso=$curso&asignatura=$asignatura&nombre=$nombre&apellidos=$apellidos&nom_asig=$nom_asig&dia=$dia&hora=$hora&siguiente=1";
  echo '<a class="btn btn-primary btn-sm" href="'.$mens_sig.'">Siguiente <i class="fas fa-chevron-right"></i></a>';
}
else {
  echo '<a class="btn btn-primary btn-sm disabled" href="#">Siguiente <i class="fas fa-chevron-right"></i></a>';
}

echo "</h4></td><td style='text-align:right'>";

if ($foto = obtener_foto_alumno($claveal)) {
  echo '<img class="img-thumbnail" src="../xml/fotos/'.$foto.'" style="width: 64px !important;" alt="">';
}
else {
  echo '<span class="img-thumbnail far fa-user fa-fw fa-3x" style="width: 64px !important;"></span>';
}	
echo "</td></tr></table>";
    

echo "</div>"; 

?>
<div class="tabbable" style="margin-bottom: 18px;">
<ul class="nav nav-tabs">
<li class="active"><a href="#tab1" data-toggle="tab">Notas del alumno</a></li>
<li><a href="#tab2" data-toggle="tab">Datos generales</a></li>
<li><a href="#tab3" data-toggle="tab">Datos académicos </a></li>
</ul>
<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
<div class="tab-pane fade in active" id="tab1">
<br>
<?php  
// Procesamos los datosxxxx
$datos1 = "select distinct fecha, nombre, nota from datos, notas_cuaderno where  notas_cuaderno.id = datos.id and profesor = '$profesor' and curso like '%$curso%,' and claveal = '$claveal' and asignatura = '$asignatura' order by orden";
$datos0 = mysqli_query($db_con, $datos1);
	if (mysqli_num_rows($datos0) > 0) {
		?>
    <h4 class='text-info'>
 Notas en la Columnas</h4><br />
    <?php
echo "<table align='center' class='table table-striped' style='width:auto'>\n"; 
echo "<tr><th>Fecha</td><th>Columna</td><th>Datos</td>";
		while($datos = mysqli_fetch_array($datos0))
		{
		echo "<tr><td class='text-info' nowrap>".cambia_fecha($datos[0])."</td><td>$datos[1]</td><td align='left' class='text-success'> <strong>$datos[2]</strong></td></tr>";
		}
echo "</table>";
		}
		else	
		{
echo '<br /><div align="center"><div class="alert alert-danger alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCIÓN:</h5>
El alumno no tiene notas registradas en el cuaderno.
</div></div>';		
		}
?>
</div>

<div class="tab-pane fade in" id="tab2">
<br>
<?php		
   	include("informes/datos.php");
	echo '<hr style="width:400px;">';   
?>
    </div>
    
<div class="tab-pane fade in" id="tab3">
<br>
<div align="left">
<?php	
include("informes/faltas.php");
echo '<hr style="width:400px;">';
include("informes/fechorias.php");
echo '<hr style="width:400px;">';
include("informes/notas.php");
?>
</div>
<br />
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<?php 
include("../pie.php");
?>
  </body>
</html>

