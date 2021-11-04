<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed'); 

// Procesamos los datos
// Borramos datos en casillas de verificaciÃ³n visibles
 	$contr = mysqli_query($db_con, "select id from notas_cuaderno where profesor = '$profesor' and Tipo like 'Casilla%' and oculto = '0' and asignatura='".$_POST['asignatura']."' and curso = '".$_POST['curso']."'");
  	while($control_veri = mysqli_fetch_array($contr)){
  		$borra_veri = "delete from datos  WHERE datos.id = '$control_veri[0]'";
		$borra1 = mysqli_query($db_con, $borra_veri);
  	}
 
  foreach ($_POST as $key => $val) {
  	// echo "$key --> $val<br />";
  	$trozos = explode("-",$key);
  	$id = $trozos[0];
  	$claveal = $trozos[1];
  	
	// Duplicados
  	$dupli = mysqli_query($db_con, "select * from datos where id = '$id' and claveal = '$claveal'");
	$duplic = mysqli_fetch_array($dupli);

	// Condiciones para procesar los datos
  	if (is_numeric($claveal) and is_numeric($id)) {

		if($val==""){
		$borra = "delete from datos WHERE datos.id = '$id' AND datos.claveal = '$claveal'";
		$borra0 = mysqli_query($db_con, $borra);
		}
  		elseif(strlen($duplic[1])>0){
		$actualiza = "UPDATE datos SET nota = '$val' WHERE datos.id = '$id' AND datos.claveal = '$claveal'";
		//echo $actualiza."<br />";
		$actua0 = mysqli_query($db_con, $actualiza);
		}		
		else{
  		$insert = "insert into datos (id, claveal, nota, ponderacion) values ('$id','$claveal','$val','1')";
  		$insert0 = mysqli_query($db_con, $insert);	
		//echo $insert."<br />";
  		}
		}
  }  
mysqli_select_db($db_con, $db);
echo '<div align="center"><div class="alert alert-success alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
Los datos han sido registrados en el Cuaderno.          
</div></div><br />';
?>

