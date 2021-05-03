<?php
require('../../bootstrap.php');

if (file_exists('config.php')) {
	include('config.php');
}

acl_acceso($_SESSION['cargo'], array(1, 2, 8));

// COMPROBAMOS SI ES EL TUTOR, SI NO, ES DEL EQ. DIRECTIVO U ORIENTADOR
if (stristr($_SESSION['cargo'],'2') == TRUE) {
	
	$_SESSION['mod_tutoria']['tutor']  = $_SESSION['mod_tutoria']['tutor'];
	$_SESSION['mod_tutoria']['unidad'] = $_SESSION['mod_tutoria']['unidad'];
	
}
else {

	if(isset($_POST['tutor'])) {
		$exp_tutor = explode('==>', $_POST['tutor']);
		$_SESSION['mod_tutoria']['tutor'] = trim($exp_tutor[0]);
		$_SESSION['mod_tutoria']['unidad'] = trim($exp_tutor[1]);
	}
	else{
		if (!isset($_SESSION['mod_tutoria'])) {
			header('Location:'.'tutores.php');
		}
	}
	
}

	$unidad = $_SESSION['mod_tutoria']['unidad'];


include("../../menu.php");
include("menu.php");
?>

<div class="container">
	
	<div class="page-header">
		<h2> Tutoría de <?php echo $_SESSION['mod_tutoria']['unidad']; ?> <small>Datos importantes de la Matrícula</small></h2>
	</div>
	

	<div class="row">
	
		<div class="col-sm-12">
<?php

$SQL = "select distinct claveal, apellidos, nombre, curso from alma where unidad = '$unidad' order BY apellidos, nombre";
$result = mysqli_query($db_con, $SQL);

	echo "<table class='table table-bordered table-striped table-vcentered'>";
	echo "<thead><tr>
			<th></th>
			<th>Alumno/a</th>
	        <th>Enfermedades</th>
	        <th>Situación Familiar</th>
	        <th>Autorización fotos</th>
	        <th>Analgésicos</th>";
	echo "</tr></thead><tbody>";

while($row = mysqli_fetch_array($result))
{
	if (stristr($row['curso'],"Bach")==TRUE) {
		$tabla_mtr = 'matriculas_bach';
	}
	else{
		$tabla_mtr = 'matriculas';
	}

	$mtr = mysqli_query($db_con,"select enfermedad, otraenfermedad, divorcio, foto, id, analgesicos from $tabla_mtr where claveal = '".$row['claveal']."'");
	$dato_mtr = mysqli_fetch_array($mtr);
	if (strlen($dato_mtr['otraenfermedad'])>1) {
		$enfermo = $dato_mtr['enfermedad'].": ".$dato_mtr['otraenfermedad'];
	}
	else{
		$enfermo = $dato_mtr['enfermedad'];	
	}
	$claveal = $row['claveal'];
	$alumno = $row['apellidos'].", ".$row['nombre'];
	$divorcio = $dato_mtr['divorcio'];
	if ($dato_mtr['foto']==1) {
		$aut_foto = "";
	}
	else{
		$aut_foto = "NO";
	}
	
	$id = $dato_mtr['id'];

	if ($dato_mtr['analgesicos']==0) {
		$analgesicos = "NO";
	}


	if (strlen($enfermo)>0 or strlen($divorcio)>0 or strlen($aut_foto)>0) {
			
	if ($foto = obtener_foto_alumno($claveal)) {
		$foto_alumno = "<img src='../../xml/fotos/$foto' width='55' class=\"img-thumbnail\" />";
	}
	else {
		$foto_alumno = "<span class=\"far fa-user fa-3x fa-fw\"></span>";
	}
	
	echo "<tr><td>$foto_alumno</td>";
	echo '<td><a href="../matriculas/'.$tabla_mtr.'.php?id='. $id.'" target="_blank">'.$alumno.'</a></td>';
	echo "<td>$enfermo</td>
		<td>$divorcio</td>
		<td>$aut_foto</td>
		<td>$analgesicos</td><tr>";
		
		} 
	}
	echo "</tbody></table>\n";
?> 

<br />
</div>
</div>
</div>

<?php include("../../pie.php"); ?>

	<script>
	jQuery.extend( jQuery.fn.dataTableExt.oSort, {
		"latin-pre": function ( data ) {
			var a = 'a';
			var e = 'e';
			var i = 'i';
			var o = 'o';
			var u = 'u';
			var c = 'c';
			var special_letters = {
				"Á": a, "á": a, "Ã": a, "ã": a, "À": a, "à": a,
				"É": e, "é": e, "Ê": e, "ê": e,
				"Í": i, "í": i, "Î": i, "î": i,
				"Ó": o, "ó": o, "Õ": o, "õ": o, "Ô": o, "ô": o,
				"Ú": u, "ú": u, "Ü": u, "ü": u,
				"ç": c, "Ç": c
			};
			for (var val in special_letters)
			   data = data.split(val).join(special_letters[val]).toLowerCase();
			return data;
		},
		"latin-asc": function ( a, b ) {
			return ((a < b) ? -1 : ((a > b) ? 1 : 0));
		},
		"latin-desc": function ( a, b ) {
			return ((a < b) ? 1 : ((a > b) ? -1 : 0));
		}
	} );

	</script>

</body>
</html>