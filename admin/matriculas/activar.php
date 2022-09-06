<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1));

if (file_exists('config.php')) {
  include('config.php');
}

include("../../menu.php");
include("menu.php");
?>
<div class="container">

<div class="page-header" align="center">
  <h2>Matriculación de Alumnos <small> Activación de la matriculación</small></h2>
</div>
<br />
<div class="row">
<div class="col-sm-8 col-sm-offset-2">

<?php 
mysqli_query($db_con,"create table matriculas_".substr($config['matriculas']['fecha_fin'],0,4)." select * from matriculas");
mysqli_query($db_con,"create table matriculas_bach_".substr($config['matriculas']['fecha_fin'],0,4)." select * from matriculas_bach");
mysqli_query($db_con,"create table matriculas_bach_backup_".substr($config['matriculas']['fecha_fin'],0,4)." select * from matriculas_bach_backup");
mysqli_query($db_con,"create table matriculas_backup_".substr($config['matriculas']['fecha_fin'],0,4)." select * from matriculas_backup");
mysqli_query($db_con,"truncate table matriculas");
mysqli_query($db_con,"truncate table matriculas_backup");
mysqli_query($db_con,"truncate table matriculas_bach");
mysqli_query($db_con,"truncate table matriculas_bach_backup");
?>

<div class="alert alert-success">Las tablas de las matriculas en la base de datos han sido vaciadas para proceder a la matriculación en este curso escolar. Una copia de seguridad de las tablas del curso anterior ha sido creada en la base de datos. </div>

</div>

</div>

</div>


	<?php include("../../pie.php"); ?>

</body>
</html>

