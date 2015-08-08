<?php
require('../../bootstrap.php');


if(!(stristr($_SESSION['cargo'],'1') == TRUE))
{
header('Location:'.'http://'.$config['dominio'].'/intranet/salir.php');
exit;	
}


include("../../menu.php");
?>
<br />
<div class="container">
<div class="row">
<div class="page-header">
  <h2>Administraci�n <small> D�as festivos y vacaciones</small></h2>
</div>
<br />
<?php
 
// Borramos datos
mysqli_query($db_con, "truncate table festivos");	

// Importamos los datos del fichero CSV en la tab�a alma.
$handle = fopen ($_FILES['archivo']['tmp_name'] , "r" ) or die('
<div align="center"><div class="alert alert-success alert-block fade in" align="left">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
No se ha podido abrir el fichero de importaci�n<br> Aseg�rate de que su formato es correcto y no est� vac�o.
			</div></div><br />	'); 
while (($data1 = fgetcsv($handle, 1000, "|")) !== FALSE) 
{
$tr = explode("/",trim($data1[0]));
$fecha="$tr[2]-$tr[1]-$tr[0]";
$datos1 = "INSERT INTO festivos ( `fecha` , `nombre` , `ambito` , `docentes` ) VALUES (\"". $fecha . "\",\"". trim($data1[1]) . "\",\"". trim($data1[2]) . "\",\"". trim($data1[3]) . "\")";
mysqli_query($db_con, $datos1);
}
fclose($handle);
$borrarvacios = "delete from festivos where date(fecha) = '0000-00-00'";
 mysqli_query($db_con, $borrarvacios);
 if (mysqli_affected_rows($db_con) > '0') {
?>
 	<div align="center"><div class="alert alert-success alert-block fade in" align="left">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
	 Los datos se han importado correctamente.
			</div></div><br /> 
			<div align="center"><a href="../index.php" class="btn btn-primary" />Volver
a Administraci�n</a></div>
			<?php
			}
?>
</div>
</div>
    <?php 
include("../../pie.php");
?>
</body>
</html>