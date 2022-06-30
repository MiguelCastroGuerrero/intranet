<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1, 2, 7, 8));

if (file_exists('config.php')) {
	include('config.php');
}

// Archivo con los arrays de las asigntauras optativas.
//// include 'asignaturas.php';

if (isset($_GET['curso'])) {$curso = $_GET['curso'];}elseif (isset($_POST['curso'])) {$curso = $_POST['curso'];}
if (isset($_GET['id'])) {$id = $_GET['id'];}elseif (isset($_POST['id'])) {$id = $_POST['id'];}
if (isset($_GET['consulta'])) {$consulta = $_GET['consulta'];}elseif (isset($_POST['consulta'])) {$consulta = $_POST['consulta'];}
if(isset($_POST['c_escolar'])){$c_escolar = $_POST['c_escolar'];}else{ $c_escolar=""; }


if (file_exists(INTRANET_DIRECTORY . '/config_datos.php')) {
	if (!empty($c_escolar) && ($c_escolar != $config['curso_actual'])) {
		$exp_c_escolar = explode("/", $c_escolar);
		$anio_escolar = $exp_c_escolar[0];
		$db_con = mysqli_connect($config['db_host_c'.$anio_escolar], $config['db_user_c'.$anio_escolar], $config['db_pass_c'.$anio_escolar], $config['db_name_c'.$anio_escolar]);
		mysqli_query($db_con,"SET NAMES 'utf8'");
	}
	if (empty($c_escolar)){
		$c_escolar = $config['curso_actual'];
	}
}
else {
	$c_escolar = $config['curso_actual'];
}



if (isset($_POST['listados'])) {
	foreach ($_POST as $key=>$val)
	{
		if (strlen($val)==1 and !(is_numeric($val))) {
			$cur_actual=$val;
		}
	}
	include("listado_total.php");
	exit();
}

if (isset($_POST['listado_total']) OR isset($_GET['listado_total'])) {
	include("listado_total.php");
	exit();
}

if (isset($_POST['listado_simple'])) {
	include("listado_simple.php");
	exit();
}

if (isset($_POST['imprimir'])) {
	mysqli_query($db_con, "drop table if exists matriculas_temp");
	mysqli_query($db_con, "CREATE TABLE  `matriculas_temp` (
 `id_matriculas` INT NOT NULL ,
INDEX (  `id_matriculas` )
)");
	foreach ($_POST as $key=>$val)
	{
		$tr = explode("-",$key);
		$id_submit = $tr[1];
		$col = $tr[0];
		if (is_numeric($id_submit)) {

			mysqli_query($db_con, "insert into matriculas_temp VALUES ('$id_submit')");
		}
	}
	include("imprimir.php");
	exit();
}

if (isset($_POST['caratulas'])) {
	mysqli_query($db_con, "drop table if exists matriculas_temp");
	mysqli_query($db_con, "CREATE TABLE  `matriculas_temp` (
 `id_matriculas` INT NOT NULL ,
INDEX (  `id_matriculas` )
)");
	foreach ($_POST as $key=>$val)
	{
		$tr = explode("-",$key);
		$id_submit = $tr[1];
		$col = $tr[0];
		if (is_numeric($id_submit)) {
			mysqli_query($db_con, "insert into matriculas_temp VALUES ('$id_submit')");
		}
	}
	include("caratulas.php");
	exit();
}



if (isset($_POST['cambios'])) {
	include("../../menu.php");
	include("menu.php");
	mysqli_query($db_con, "drop table if exists matriculas_temp");
	mysqli_query($db_con, "CREATE TABLE `matriculas_temp` (
 `id_matriculas` INT NOT NULL ,
INDEX (  `id_matriculas` )
)");
	foreach ($_POST as $key=>$val)
	{
		$tr = explode("-",$key);
		$id_submit = $tr[1];
		$col = $tr[0];
		if (is_numeric($id_submit)) {

			mysqli_query($db_con, "insert into matriculas_temp VALUES ('$id_submit')");
		}
	}

	$camb = mysqli_query($db_con, "select distinct id_matriculas from matriculas_temp, matriculas where id_matriculas=id order by apellidos, nombre");
	echo '<br><h3 align="center">Alumnos de <span style="color:#08c">'.$curso.'</span> con datos cambiados.</h3><br /><br />';
	echo "<div class='well well-large' style='width:650px;margin:auto;'>";
	while ($cam = mysqli_fetch_array($camb)) {
		$text_n="";
		$text_t="";
		$id_cambios = $cam[0];
		if ($curso == "1ESO") {
			if (date('n')==9) {
				$alma="alma_primaria";
			}
			else{
				$alma="alma";
			}			
		}
		else
			{
				$alma="alma";
			}
		$contr = mysqli_query($db_con, "select matriculas.apellidos, $alma.apellidos, matriculas.nombre, $alma.nombre, matriculas.domicilio, $alma.domicilio, matriculas.dni, $alma.dni, matriculas.padre, concat(primerapellidotutor,' ',segundoapellidotutor,', ',nombretutor), matriculas.dnitutor, $alma.dnitutor, matriculas.telefono1, $alma.telefono, matriculas.telefono2, $alma.telefonourgencia, $alma.claveal from matriculas, $alma where $alma.claveal=matriculas.claveal and id = '$id_cambios'");
		//$col_datos = array()
		$control = mysqli_fetch_array($contr);
		if (strlen($control[16])>0) {
			$text_n = "<p style='color:#08c'>$control[16]: $control[0], $control[2]</p>";
			for ($i = 0; $i < 18; $i++) {
				if ($i%2) {
					if ($i=="5" and strstr($control[$i], $control[$i-1])==TRUE) {}
					elseif ($i=="17") {}
					else{
						if ($control[$i]==$control[$i-1]) {}else{
							$text_t.= "<li><span class='text-error'>Séneca:</span> ".$control[$i]." ==> <span class='text-error'>Matrícula:</span> ".$control[$i-1]."</li>";
						}
					}
				}
			}
		}
			if(strlen($text_t)>0){
			echo $text_n.$text_t."<hr>";
		}
	}
	echo "</div>";
	mysqli_query($db_con, "drop table matriculas_temp");
	include("../../pie.php");
	echo '</body></html>';
	exit();
}

// Alumnos sin matricularse
if (isset($_POST['sin_matricula'])) {
	include("../../menu.php");
	include("menu.php");
	echo "<br>";
	
	if ($curso=="1ESO") {
		$cur_cole = "6";
		$cur_monterroso = substr($curso, 0, 1);
		$cur_monterroso2 = substr($curso, 0, 1)-1;
		$cole_nene = ", colegio";
		$cole_order = "colegio,";

		$query2 = "select distinct alma_primaria.apellidos, alma_primaria.nombre, alma_primaria.unidad, alma_primaria.telefono, alma_primaria.telefonourgencia, alma_primaria.fecha, alma_primaria.claveal, alma_primaria.colegio from alma_primaria, matriculas where alma_primaria.claveal=matriculas.claveal and (matriculas.confirmado = '0' or matriculas.confirmado is null) order by alma_primaria.colegio, apellidos, nombre";

		$camb = mysqli_query($db_con, "select distinct alma_primaria.apellidos, alma_primaria.nombre, alma_primaria.unidad, alma_primaria.telefono, alma_primaria.telefonourgencia, alma_primaria.fecha, alma_primaria.colegio, alma_primaria.claveal from alma_primaria where alma_primaria.claveal not in (select claveal from matriculas where curso like '1ESO') order by colegio, apellidos, nombre");

		echo '<h3 align="center">Alumnos de '.$curso.' sin matricular de Colegios de Primaria.</h3><br />';
		echo "<div class='well well-large' style='width:800px;margin:auto;'><ul class='unstyled'>";
		while ($cam = mysqli_fetch_array($camb)) {
			if(strlen($cam[6])>0){$cole = " (<em><b>$cam[6]</b></em>)";}else{$cole="";}
			echo "<li><i class='fa fa-user'></i> &nbsp;".$cam['claveal']." -- <span style='color:#08c'>$cam[0], $cam[1]</span> : $cam[3] - $cam[4] ==> $cam[5] $cole</li>";

		}
		echo "</ul></div><br />";
		
		$canf = mysqli_query($db_con, $query2);
		echo '<h3 align="center">Alumnos de '.$curso.' prematriculados sin confirmar.</h3><br />';
		echo "<div class='well well-large' style='width:800px;margin:auto;'><ul class='unstyled'>";
		while ($cam2 = mysqli_fetch_array($canf)) {
			if(strlen($cam2[7])>0){$cole = " (<em><b>$cam2[7]</b></em>)";}else{$cole="";}
			echo "<li><i class='fa fa-user'></i> &nbsp;".$cam2['claveal']." -- <span style='color:#08c'>$cam2[0], $cam2[1]</span>: $cam2[3] - $cam2[4] ==> $cam2[5] $cole</li>";

		}
		echo "</ul></div>";
	}
	else{
	$cur_monterroso = substr($curso, 0, 1);
	$cur_monterroso2 = substr($curso, 0, 1)-1;
	$cole_nene = "";
	$cole_order = "";
	$query2 = "select distinct alma.apellidos, alma.nombre, alma.unidad, alma.telefono, alma.telefonourgencia, alma.fecha, alma.claveal from alma, matriculas where alma.claveal=matriculas.claveal  and alma.curso like '$cur_monterroso2%' and alma.curso like '%E.S.O.' and (matriculas.confirmado = '0' or matriculas.confirmado is null) order by unidad, apellidos, nombre";

	$camb2 = mysqli_query($db_con, "select distinct alma.apellidos, alma.nombre, alma.unidad, alma.telefono, alma.telefonourgencia, alma.fecha, alma.claveal from alma where alma.curso like '$cur_monterroso2%' and alma.curso like '%E.S.O.' and alma.claveal not in (select claveal from matriculas where curso like '$cur_monterroso%' and curso like '%ESO') order by unidad, apellidos, nombre");

	echo '<h3 align="center">Alumnos de '.$curso.' sin matricular de nuestro Centro.</h3><br />';
	echo "<div class='well well-large' style='width:700px;margin:auto;'><ul class='unstyled'>";
	while ($cam2 = mysqli_fetch_array($camb2)) {
		if(strlen($cam[6])>0){$cole = " ($cam2[6])";}else{$cole="";}
		echo "<li><i class='fa fa-user'></i> &nbsp;".$cam2['claveal']." -- <span style='color:#08c'>$cam2[0], $cam2[1]</span> --> <strong style='color:#9d261d'>$cam2[2]</strong> : $cam2[3] - $cam2[4] ==> $cam2[5] $cole</li>";

	}
	echo "</ul></div><br />";

	$canf = mysqli_query($db_con, $query2);
	echo '<h3 align="center">Alumnos de '.$curso.' prematriculados sin confirmar.</h3><br />';
	echo "<div class='well well-large' style='width:700px;margin:auto;'><ul class='unstyled'>";
	while ($cam2 = mysqli_fetch_array($canf)) {
		echo "<li><i class='fa fa-user'></i> &nbsp;".$cam2['claveal']." -- <span style='color:#08c'>$cam2[0], $cam2[1]</span> --> <strong style='color:#9d261d'>$cam2[2]</strong> : $cam2[3] - $cam2[4] ==> $cam2[5]</li>";

		}
	}
	echo "</ul></div>";
	include("../../pie.php");
	echo '</body></html>';
	exit();
}

?>

<?php
include("../../menu.php");
include("./menu.php");

foreach($_POST as $key => $val)
{
	${$key} = $val;
}

foreach($_GET as $key_get => $val_get)
{
	${$key_get} = $val_get;
}
?>
<div class="container">

<div class="page-header">
<h2>Matriculación de alumnos <small> Alumnos/as matriculados en ESO</small></h2>
</div>
<br>

<?php
include 'filtro.php';
if (isset($_GET['borrar'])) {
	mysqli_query($db_con, "insert into matriculas_backup (select * from matriculas where id = '$id')");
	mysqli_query($db_con, "delete from matriculas where id='$id'");
	echo '<div align="center"><div class="alert alert-success alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
El alumno ha sido borrado de la tabla de matrículas. Se ha creado una copia de respaldo de us datos en la tabla matriculas_backup.
</div></div><br />' ;
}
if (isset($_GET['copia'])) {
	$conf = mysqli_query($db_con,"select confirmado from matriculas where id='$id' and confirmado='1'");
	if (mysqli_num_rows($conf)>0) { $confirma = 1; } else{ $confirma = 0; }

	mysqli_query($db_con, "delete from matriculas where id='$id'");
	mysqli_query($db_con, "insert into matriculas (select * from matriculas_backup where id = '$id')");
	mysqli_query($db_con, "update matriculas set promociona='0' where id = '$id'");
	mysqli_query($db_con, "update matriculas set confirmado='$confirma' where id='$id'");
	mysqli_query($db_con, "delete from matriculas_backup where id='$id'");
	echo '<div align="center"><div class="alert alert-success alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
Los datos originales de la matrícula del alumno han sido correctamente restaurados.
</div></div><br />' ;
}
if (isset($_GET['consulta']) or isset($_POST['consulta'])) {

	if ($curso) {$extra=" curso='$curso' ";}else{
		echo '<div align="center"><div class="alert alert-danger alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCIÓN:</h5>
No has seleccionado el Nivel. Así no podemos seguir...
</div></div>' ;
		exit();
	}
}

if (isset($curso)) {
	$extra=" curso='$curso' ";	
}

$n_curso = substr($curso, 0, 1);

if ($diversificacio=="Si") { $extra.=" and diversificacion = '1'";	}elseif ($diversificacio=="No"){ $extra.=" and diversificacion = '0'"; }
if ($exencio=="Si") { $extra.=" and exencion = '1'";	}elseif ($exencio=="No") { $extra.=" and exencion = '0'"; }
if ($promocion=="Promociona") { $extra.=" and promociona = '1'";	}elseif($promocion=="Repite"){$extra.=" and promociona = '2'";}

	if ($optativ) { $extra.=" and $optativ = '1'";}
	if ($optativ2) { $extra.=" and $optativ2 = '2'";}

if ($religio) { $extra.=" and religion = '$religio'";}
if ($letra_grup) { $extra.=" and letra_grupo = '$letra_grup'";}
if ($_POST['grupo_actua']) {

	$extra.=" and ( ";
	foreach ($_POST['grupo_actua'] as $grup_actua){
		if($grup_actua=="Ninguno"){$extra.=" grupo_actual = '' or";}
		else{
			$extra.=" grupo_actual = '$grup_actua' or";
		}
	}
	$extra = substr($extra,0,strlen($extra)-2);
	$extra.=")";

}
if ($grupo_actua_seg) { if($grupo_actua_seg=="Ninguno"){$extra.=" and grupo_actual = ''";} else{  $extra.=" and grupo_actual = '$grupo_actua_seg'";}}
if ($colegi) { $extra.=" and colegio = '$colegi'";}
if ($actividade) { $extra.=" and act1 = '$actividade'";}
if ($itinerari and $n_curso=='4') { 
	if(strlen($itinerari)>1){
		$it1 = substr($itinerari,0,1);
		$cienci = substr($itinerari,3,1);
		$extra.=" and itinerario = '$it1' and ciencias4 = '$cienci'";
	}
	else{$extra.=" and itinerario = '$itinerari'";}
}
if ($optativ4 and $n_curso=='4') { $extra.=" and optativas4 = '$optativ4'";}
if ($matematica3 and $n_curso=='3') { $extra.=" and matematicas3 = '$matematica3'";}
if ($ciencia4 and $n_curso=='4') { $extra.=" and ciencias4 = '$ciencias4'";}
if ($transport == "ruta_este") { $extra.=" and ruta_este != ''";}
if ($transport == "ruta_oeste") { $extra.=" and ruta_oeste != ''";}
if ($idioma2) { $extra.=" and idioma2 = '$idioma2'";}
if ($bilinguism == "No") { $extra.=" and bilinguismo = ''";}
if ($itinerario == "0") { $itinerario = "";	}
if (strlen($dn)>5) {$extra.=" and dni = '$dn'";}
if (strlen($apellid)>1) {$extra.=" and apellidos like '%$apellid%'";}
if (strlen($nombr)>1) {$extra.=" and nombre like '%$nombr%'";}
if (!($orden)) {
	$orden=" ";
	if (isset($_POST['op_orden'])) {
		$op_filtro= $_POST['op_orden'];
		if ($_POST['op_orden']=="optativas") {
			$orden.="optativa1, optativa2, optativa3, optativa4, optativa5, optativa6, optativa7, ";
		}
		elseif ($_POST['op_orden']=="actividades") {
			$orden.="act1, act2, act3, act4, ";
		}
		else{
			$orden.="$op_filtro desc, ";
		}
	}

	if ($curso=="1ESO") {
		// En Junio puede interesar ordenar por colegio
		if (date('m')>'05' and date('m')<'09'){
			$orden.="colegio, ";
		}
		else{
			$orden.="";
		}
	}

	include 'procesado.php';
	
	$sql = "select matriculas.id, matriculas.apellidos, matriculas.nombre, matriculas.curso, letra_grupo, colegio, bilinguismo, diversificacion, act1, confirmado, grupo_actual, observaciones, exencion, religion, itinerario, optativas4, promociona, claveal, ruta_este, ruta_oeste, revisado, foto, enfermedad, divorcio, matematicas3, ciencias4, analgesicos, idioma2 ";
	
	if ($curso=="1ESO"){$num_opt = $count_1;}elseif ($curso=="2ESO"){$num_opt = $count_2;}elseif ($curso=="3ESO"){$num_opt = $count_3;}else{$num_opt = $count_4;}

	for ($i=1;$i<$num_opt+1;$i++)
	{
		$sql.=", optativa$i";
	}
	$sql.=" from matriculas where ". $extra ." order by ". $orden ." apellidos, nombre, curso, grupo_actual";
	//echo $sql;
	$cons = mysqli_query($db_con, $sql);
	if(mysqli_num_rows($cons) < 1){
		echo '<div align="center"><div class="alert alert-warning alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCIÓN:</h5>
No hay alumnos que se ajusten a ese criterio. Prueba de nuevo.
</div></div><br />' ;
	}
	else{
		if ($curso) {
			?>
<h3 align=center><?php if($_POST['grupo_actua']){ 
	echo $curso." ";
	foreach ($_POST['grupo_actua'] as $grup_actua){
		echo $grup_actua." ";
	}
} else{ echo $curso;}?></h3>
<br />
<form action="consultas.php?curso=<?php echo $curso;?>&consulta=1"
	name="form1" method="post">
<table class="table table-striped table-condensed" align="center"
	style="width: auto">
	<thead>
		<th colspan="2"></th>
		<th>Nombre</th>
		<th>Curso</th>
		<th>Gr1</th>
		<th>Gr2</th>
		<?php
		if ($curso=="1ESO") {
			echo '<th>Colegio</th>';
		}
		echo '<th>Rel.</th>';
		echo '<th>Transprt</th>';
		if ($n_curso>2) {
			echo '<th>Bil.</th>';		
		}
		if ($n_curso==1 or $n_curso==4) {
			echo '<th>Ex.</th>';
		}
		if ($n_curso==2 or $n_curso==3) {
			echo '<th>Div.</th>';
		}
		if ($n_curso=="4") {
			echo '<th>Itin.</th>';
			echo '<th>Opt. Mod.</th>';
		}

		if ($n_curso==1) {
			echo '<th>2º Id.</th>';
		}
		echo "<th>Opt1</th><th>Opt2</th>";

		?>

		<th class="hidden-print">Obs.</th>
		<?php
		if ($n_curso>1) {
			echo '<th class="hidden-print">SI |NO </th>';
		}

		// Cambios producidos en la matrícula
		echo '<th class="hidden-print">Camb.</th>';
		// Hay copia de seguridad porque se han modificado datos y se guarda copia original de 1ª matriculación
		echo '<th class="hidden-print">Copia</th>';
		// Eliminar matrícula
		echo '<th class="hidden-print">Borrar</th>';
		?>
		<!-- Problemas de convivencia y comportamiento -->
		<th class="hidden-print">Conv.</th>
		<!-- Datos diversos: observaciones, enfermedades, foto, etc. -->
		<th class="hidden-print">Otros</th>
	</thead>
	<tbody>
	<?php
	
	while($consul = mysqli_fetch_array($cons)){
		$backup="";
		$respaldo='1';
		$id = $consul['id'];
		$apellidos = $consul['apellidos'];
		$nombre= $consul['nombre'];
		$letra_grupo = $consul['letra_grupo'];
		$colegio=str_ireplace("C.E.I.P.","",$consul['colegio']);
		$bilinguismo = $consul['bilinguismo'];
		$diversificacion = $consul['diversificacion'];
		$idioma2 = $consul['idioma2'];
		$confirmado = $consul['confirmado'];
		$grupo_actual = $consul['grupo_actual'];
		$observaciones = $consul['observaciones'];
		$exencion = $consul['exencion'];
		$religion = $consul['religion'];
		$itinerario = $consul['itinerario'];
		$optativas4 = $consul['optativas4'];
		$promociona = $consul['promociona'];
		$promocion = $promociona;
		$claveal = $consul['claveal'];
		$ruta_este = $consul['ruta_este'];
		$ruta_oeste = $consul['ruta_oeste'];
		$revisado = $consul['revisado'];
		$foto = $consul['foto'];
		$enf = $consul['enfermedad'];
		$divorcio = $consul['divorcio'];
		$matematicas3 = $consul['matematicas3'];
		$ciencias4 = $consul['ciencias4'];
		$analgesicos = $consul['analgesicos'];
		$back = mysqli_query($db_con, "select id from matriculas_backup where id = '$id'");
		if (mysqli_num_rows($back)>0) {
			$respaldo = '1';
			$backup="<a href='consultas.php?copia=1&id=$id&curso=$curso&consulta=1'><i class='fa fa-sync-alt' data-bs='tooltip' title='Restaurar datos originales de la matrícula del alumno '> </i></a>";
		}

		if ($curso=='4ESO') {
			$back4 = mysqli_query($db_con, "select id from matriculas_bach_backup where claveal = '$claveal'");
			if (mysqli_num_rows($back4)>0) {
				$id4 = mysqli_fetch_array($back4);
				$respaldo = '1';
				$backup="<a href='consultas_bach.php?copia=1&id=$id4[0]&id_4=$id&curso=$curso&consulta=1'><i class='fa fa-sync-alt text-warning' rel='Tooltip' title='Restaurar datos originales de la matrícula de Bachillerato'> </i> <span class=text-warning>(B)</span></a>";
			}
		}

		
		for ($i=1;$i<$num_opt+1;$i++)
		{
			${optativa.$i} = $consul['optativa'.$i];
		}

		// Problemas de Convivencia
		$n_fechorias="";
		$fechorias = mysqli_query($db_con, "select * from Fechoria where claveal='".$claveal."'");
		$n_fechorias = mysqli_num_rows($fechorias);
		//$fechori="16 --> 1000";
		if (!(isset($fechori)) or $fechori=="") {
			$fechori1="0";
			$fechori2="1000";
		}
		else{
			if ($fechori=="Sin problemas") {
				$fechori1="0";
				$fechori2="1";
			}
			else{
				$tr_fech = explode(" --> ",$fechori);
				$fechori1=$tr_fech[0];
				$fechori2=$tr_fech[1];
			}
		}
		if ($n_fechorias >= $fechori1 and $n_fechorias < $fechori2) {
			$num_al+=1;
			echo '<tr>

	<td><input value="1" name="confirmado-'. $id .'" type="checkbox"';
			if ($confirmado=="1") { echo " checked";}
			echo ' onClick="submit()"/></td><td>'.$num_al.'</td>
	<td><a href="matriculas.php?id='. $id .'&c_escolar='. $c_escolar .'" target="_blank">'.$apellidos.', '.$nombre.'</a></td>
	<td>'.$curso.'</td>
	<td>'.$letra_grupo.'</td>
	<td><input name="grupo_actual-'. $id .'" type="text" class="form-control input-sm" style="width:35px" value="'. $grupo_actual .'" /></td>';
			if ($curso=="1ESO") {
				echo '<td>';
				$clg = mysqli_query($db_con,"select * from transito_datos where claveal = '$claveal'");
				if (mysqli_num_rows($clg)>0) {	echo "<a href='informe_transito.php?claveal=$claveal' target='_blank' data-bs='tooltip' title='Alumno de Primaria con Informe de Tránsito' class='text-info'>$colegio</a>";}else{ echo $colegio;}
				echo '</td>';
			}
			$color_rel = "";
			if (strstr($religion,"Cat")==TRUE) {
				$color_rel = " style='background-color:#FFFF99;'";
			}
			if (strstr($religion,"Valores")==TRUE) {
				$color_rel = " style='background-color:#cdecab'";
			}
			if (strstr($religion,"Rel")==TRUE) { $text_rel = substr($religion,9,3);}else{ $text_rel = substr($religion,0,3);}
			echo '<td '.$color_rel.'>'.$text_rel.'</td>';
			$trans = "";
			if($ruta_este){$trans = substr($ruta_este, 0, 10).".";}
			if($ruta_oeste){$trans = substr($ruta_oeste, 0, 10).".";}
			echo '<td> '.$trans.'</td>';

			$an_bd = substr($config['curso_fin'],0,4);
			if ($n_curso>"2") {
				$bl="";
				$bl0 = mysqli_query($db_con,"select * from matriculas_".$an_bd." where claveal = '$claveal' and bilinguismo = 'Si' and claveal not like ''");
				if (mysqli_num_rows($bl0)>0 and $n_curso>2) { 
					$bl = '1'; 
				}
				echo '<td><input name="bilinguismo-'. $id .'" type="checkbox" value="Si"';
				if($bilinguismo=="Si"){ echo " checked";} elseif ($bl == '1') { echo " checked";}
				echo ' /></td>';
			}

			if ($n_curso=="1" or $n_curso=="4") {
			 	if ($exencion=="0") {$exencion="";}
			 	echo '<td><input name="exencion-'. $id .'" type="checkbox" value="1"';
			 	if($exencion=="1"){echo " checked";}
			 	echo ' /></td>';
			}
			
			if ($n_curso=="1") {
			 	echo '<td>'.$idioma2.'</td>';
			}

			if ($n_curso=="2" or $n_curso=="3") {
				echo '<td><input name="diversificacion-'. $id .'" type="checkbox" value="1"';
				if($diversificacion=="1"){echo " checked";}
				echo ' /></td>';
			}
			if ($n_curso=="4") {
				if ($itinerario == '0'){$itinerario="";}
				if ($itinerario == '1') {$it = $itinerario." (".$ciencias4.")";}else{$it=$itinerario;}
				echo '<td>'.$it.'</td>';
			}


			if ($n_curso=="4") {
				$wrd1 = explode(" ", $optativas4);
					$num_w = count($wrd1);
					if ($num_w>1) {
						$opt44 = "";
						$opt44 = iniciales($optativas4);;					
					}
					else{
						$opt44 = strtoupper(substr($optativas4,0,3));
						}

				echo '<td>'.$opt44.'</td>';
			}

			echo "<td align='center' style='background-color:#efdefd;'>";
			$num_opt = count(${opt.$n_curso});
			$i=0;
				foreach (${opt.$n_cur} as $key=>$val) {
				$i++;

				if (${optativa.$i} == '0' or ${optativa.$i} == '') {${optativa.$i}=""; $acr1="";}

				if (${optativa.$i}=="1") {
					$a_opt1 = iniciales($val);
				}
			}
			
			echo $a_opt1."</td>";

			echo "<td align='center'>";
			$num_opt = count(${opt.$n_curso});
			$i=0;
				foreach (${opt.$n_cur} as $key=>$val) {
				$i++;
				if (${optativa.$i} == '0' or ${optativa.$i} == '') {${optativa.$i}=""; $acr2="";}
				if (${optativa.$i}=="2") {
					$a_opt2 = iniciales($val);
				}
			}
			
			echo $a_opt2."</td>";

			echo '<td class="hidden-print">';
			if ($curso == "1ESO") {$alma="alma_primaria";}else{$alma="alma";}
			$contr = mysqli_query($db_con, "select matriculas.apellidos, $alma.apellidos, matriculas.nombre, $alma.nombre, matriculas.domicilio, $alma.domicilio, matriculas.dni, $alma.dni, matriculas.padre, concat(primerapellidotutor,' ',segundoapellidotutor,', ',nombretutor), matriculas.dnitutor, $alma.dnitutor, matriculas.telefono1, $alma.telefono, matriculas.telefono2, $alma.telefonourgencia from matriculas, $alma where $alma.claveal=matriculas.claveal and id = '$id'");
			$control = mysqli_fetch_array($contr);

			for ($i = 0; $i < 16; $i++) {
				if ($i%2) {
					if ($i=="5" and strstr($control[$i], $control[$i-1])==TRUE) {}
					else{
						$text_contr="";
						if ($control[$i]==$control[$i-1]) {$icon="";}else{
							if ($control[$i-1] != 0) {
								$icon="fa fa- fa-info-circle";
								$text_contr.= $control[$i]." --> ".$control[$i-1]."; ";
							}
						}
					}
				}
				//echo "$control[$i] --> ";
			}
			echo "<i class='$icon' data-bs='tooltip' title='$text_contr'> </i>&nbsp;";

			if ($observaciones) { echo "<i class='fa fa-bookmark' data-bs='tooltip' title='$observaciones' > </i>";}
			
			echo '</td>';

			// Curso actual
			$c_a = mysqli_query($db_con, "select curso from alma where alma.claveal='".$claveal."'");
			$cur_a = mysqli_fetch_array($c_a);
			$curs_ant = substr($cur_a[0],0,1);

			// Promocionan o no
			if (date('m')=='09' OR ($curs_ant == '4' and date('m')>6)){
				$pro_n = mysqli_query($db_con, "select estadomatricula from alma where alma.claveal='".$claveal."'");
				$pro_nt = mysqli_fetch_array($pro_n);
				$promo_f = $pro_nt[0];
				$promociona="";
				if ($promo_f=="Repite") { $promociona="2"; }elseif($promo_f=="Obtiene Título" or $promo_f=="Promociona"){ $promociona="1"; }else{ $promociona=""; }
				}

				$rp_cur="";
				if ($promociona == "1" and $n_curso==$curs_ant) {
					$rp_cur = "<i class='fa fa-exclamation-circle text-danger' data-bs='tooltip' title='El alumno ha promocionado y su matrícula debe ser restaurada'> </i>";
				}

				if ($n_curso>1) {
				echo "<td style='background-color:#efeefd' class='hidden-print' nowrap>";

					$val_notas="";
					$not = mysqli_query($db_con, "select notas3, notas4 from notas, alma where alma.claveal1=notas.claveal and alma.claveal='".$claveal."'");
					$nota = mysqli_fetch_array($not);
					$tr_not = explode(";", $nota[0]);

					$pro_n = mysqli_query($db_con, "select estadomatricula from alma where alma.claveal='".$claveal."'");
					$pro_nt = mysqli_fetch_array($pro_n);
					$promo_f = $pro_nt[0];

					if ($promo_f=="Repite") { $promociona="2"; }elseif($promo_f=="Obtiene Título" or $promo_f=="Promociona"){ $promociona="1"; }else{ $promociona=""; }
					
					
					if (date('m')>'05' and date('m')<'09' and $curs_ant < 4){
						foreach ($tr_not as $val_asig) {
							$tr_notas = explode(":", $val_asig);
							foreach ($tr_notas as $key_nota=>$val_nota) {
								if($key_nota == "1" and ($val_nota<'347' and $val_nota !=="339" and $val_nota !=="") or $val_nota == '397' ){
									$val_notas=$val_notas+1;
								}
							}
						}
						for ($i=1;$i<3;$i++){
							echo '<input type="radio" name = "promociona-'. $id .'" value="'.$i.'" ';
								if($promociona == 1 and $i == 1){
									echo " checked";
								}
							echo " />&nbsp;";
						}
						if ($val_notas > 0) {
								echo "<span class='badge' data-bs='tooltip' title='Materias suspensas'> $val_notas</span>";		
							}
					}
					
					elseif (date('m')=='09' OR ($curs_ant == '4' and date('m')>6)){

						$tr_not2 = explode(";", $nota[1]);
						foreach ($tr_not2 as $val_asig) {
							$tr_notas = explode(":", $val_asig);
							foreach ($tr_notas as $key_nota=>$val_nota) {
								if($key_nota == "1" and ($val_nota<'347' and $val_nota !=="339" and $val_nota !=="") or $val_nota == '397' ){
									$val_notas=$val_notas+1;
								}
							}
						}
						for ($i=1;$i<3;$i++){
							echo '<input type="radio" name = "promociona-'. $id .'" value="'.$i.'" ';
								if($promociona == $i){
									echo " checked";
								}
							echo " />&nbsp;";
								}
							if ($val_notas > 0) {
								echo "<span class='badge' data-bs='tooltip' title='Materias suspensas'> $val_notas</span>";		
							}
					}
					
				echo "</td>";
			}
			echo '<td class="hidden-print text-center"><input name="revisado-'. $id .'" type="checkbox" value="1" onClick="submit()"';
			if($revisado=="1"){echo " checked";}
			echo ' /></td>';
			echo "<td class='hidden-print'>";
			if ($respaldo=='1') {
				echo $backup." ".$rp_cur;
			}
			echo "</td>";
			echo "<td class='hidden-print'>";
			echo "<a href='consultas.php?borrar=1&id=$id&curso=$curso&consulta=1' data-bb='confirm-delete'><i class='fa fa-trash-alt' data-bs='tooltip' title='Eliminar alumno de la tabla'> </i></a>";
			echo "</td>";

			echo "<td class='hidden-print'>";
			$disr = mysqli_query($db_con,"select * from transito_datos where claveal = '$claveal' and (tipo='disruptivo' and dato='2')");
			if (mysqli_num_rows($disr)>0) {	echo "<a href='informe_transito.php?claveal=$claveal' target='_blank'><span class='label label-info' data-bs='tooltip' title='Alumno disruptivo de Primaria con Problemas de Convivencia'>Disrup.</span></a>";}
			$disr1 = mysqli_query($db_con,"select * from transito_datos where claveal = '$claveal' and (tipo='integra' and dato='4')");
			if (mysqli_num_rows($disr1)>0) {echo "<br><a href='informe_transito.php?claveal=$claveal' target='_blank'><span class='label label-warning' data-bs='tooltip' title='Viene de Primaria con Problemas de Integración en el Aula'>Integra</span></a>";}
			// Problemas de Convivencia
			if($n_fechorias >= 15){ echo "<a href='../fechorias/fechorias.php?claveal=$claveal&submit1=1' target='blank'><span class='badge badge-important'>$n_fechorias</span></a>";}
			elseif($n_fechorias > 4 and $n_fechorias < 15){ echo "<a href='../fechorias/fechorias.php?claveal=$claveal&submit1=1' target='blank'><span class='badge badge-warning'>$n_fechorias</span></a>";}
			elseif($n_fechorias < 5 and $n_fechorias > 0){ echo "<a href='../fechorias/fechorias.php?claveal=$claveal&submit1=1' target='blank'><span class='badge badge-info'>$n_fechorias</span></a>";}
			// Fin de Convivencia.
			echo "</td>";
			echo "<td class='hidden-print' nowrap>";
			if($foto <> 1){ echo '<span class="fa fa-camera" style="color: orange;" data-bs="tooltip" title="No permite publicar su foto."></span>&nbsp;';}
			if($analgesicos <> 1){ echo '<span class="fa fa-tablets" style="color: blue;" data-bs="tooltip" title="No permite analgésicos."></span>&nbsp;';}
			if(!empty($enf)){ echo '<span class="fa fa-medkit" style="color: red;" data-bs="tooltip" title="'.$enf.'"></span>&nbsp;';}
			if(!empty($divorcio)){
				if ($divorcio=="Guardia y Custodia compartida por Madre y Padre") {echo '<span class="fa fa-group" style="color: orange;" data-bs="tooltip" title="'.$divorcio.'"></span>';}
				elseif($divorcio=="Guardia y Custodia de la Madre") {echo '<span class="fa fa-female" style="color: orange;" data-bs="tooltip" title="'.$divorcio.'"></span>';}
				elseif($divorcio=="Guardia y Custodia del Padre") {echo '<span class="fa fa-male" style="color: orange;" data-bs="tooltip" title="'.$divorcio.'"></span>';}
			}
			echo "</td>";
			echo '
	</tr>';	
		}
	}
	echo "</table>";
	echo "<div align='center'>
<input type='hidden' name='extra' value='$extra' />";

	// Control del envío de datos

	echo "<input type='submit' name='enviar' value='Enviar datos' class='btn btn-primary hidden-print' onclick='confirmacion2()' /><br>";

	echo "<br><input type='submit' name='imprimir' value='Imprimir'  class='btn btn-success hidden-print' formtarget='_blank' />&nbsp;&nbsp;
		<input type='submit' name='caratulas' value='Imprimir Carátulas' class='btn btn-success hidden-print' formtarget='_blank' />&nbsp;&nbsp;
		<input type='submit' name='cambios' value='Ver cambios en datos' class='btn btn-warning hidden-print' formtarget='_blank' />&nbsp;&nbsp;
		<input type='submit' name='sin_matricula' value='Alumnos sin matricular' class='btn btn-danger hidden-print' formtarget='_blank' />";

	if(count($grupo_actua)=='1'){
		echo "<input type='hidden' name='grupo_actual' value='$grupo_actua' />&nbsp;&nbsp;
		<input type='submit' name='listados' value='Listado en PDF' class='btn btn-info hidden-print' formtarget='_blank' />";} 
	else
		{ 
		echo "&nbsp;&nbsp;<input type='submit' name='listado_total' value='Listado PDF total' class='btn btn-info hidden-print' formtarget='_blank' />&nbsp;&nbsp;
			<input type='submit' name='listado_simple' value='Listado Simple' class='btn btn-info hidden-print' formtarget='_blank' />";
		}
		echo "</div></form>";
		?>
		<?php
			
		if ($curso) {

			if ($curso=="1ESO" OR $curso=="2ESO" OR $curso=="3ESO"){
				$exen = mysqli_query($db_con, "select exencion from matriculas where $extra and exencion ='1'");
				$num_exen = mysqli_num_rows($exen);

				if ($curso=="1ESO"){$num_acti = $count_a1;}elseif($curso=="2ESO"){$num_acti = $count_a2;} elseif($curso=="3ESO"){$num_acti = $count_a3;}
				for ($i=1;$i<$num_acti+1;$i++){
					${acti.$i} = mysqli_query($db_con, "select act1 from matriculas where $extra and act1 = '$i'");
					${num_act.$i} = mysqli_num_rows(${acti.$i});
					//echo "select act1 from matriculas where $extra and act1 = '$i': ".${num_act.$i}."<br>";
				}
			}
			$rel = mysqli_query($db_con, "select religion from matriculas where $extra and religion like '%Católica%'");

			$num_rel = mysqli_num_rows($rel);

			if ($curso=="4ESO") {
				for ($i=1;$i<$num_opt+1;$i++){
				${opta.$i} = mysqli_query($db_con, "select optativa1 from matriculas where $extra and optativa1 = '$i' and exencion ='0'");
				${num_opta.$i} = mysqli_num_rows(${opta.$i});
				}
			}
			else{
				for ($i=1;$i<$num_opt+1;$i++){
				${opta.$i} = mysqli_query($db_con, "select optativa$i from matriculas where $extra and optativa$i = '1' and exencion ='0'");
				${num_opta.$i} = mysqli_num_rows(${opta.$i});
			}
			}
			

			if ($curso=="2ESO" OR $curso=="3ESO"){
				$diver = mysqli_query($db_con, "select diversificacion from matriculas where $extra and diversificacion = '1'");
				$num_diver = mysqli_num_rows($diver);
			}
			$promo = mysqli_query($db_con, "select promociona from matriculas where $extra and promociona = '1'");
			$num_promo = mysqli_num_rows($promo);

			//$an_bd_ant = $an_bd-1;
			//$repit = mysqli_query($db_con, "select * from matriculas_bach, ".$db.$an_bd.".alma where ".$db.$an_bd.".alma.claveal = matriculas_bach.claveal and matriculas_bach.curso = '$curso' and ".$db.$an_bd.".alma.unidad like '$n_curso%'");
			//$num_repit = mysqli_num_rows($repit);
			?>
		<br />
		<table class="table table-striped table-bordered" align="center"
			style="width: auto">
			<tr>
			<?php
			echo "<th>Religión</th>";
			if ($curso=="1ESO" OR $curso=="2ESO"){
				echo "<th>Exención</th>";
			}
			if ($curso=="3ESO" OR $curso=="2ESO"){
				echo "<th>Diversificación</th>";
			}

			for ($i=1;$i<$num_opt+1;$i++){
				echo "<th>Optativa$i</th>";
			}
			if ($curso=="1ESO"){
				$num_acti = "7";
				for ($i=1;$i<$num_acti+1;$i++){
					echo "<th>Act$i</th>";
				}
			}
			if ($curso=="2ESO"){
				$num_acti = "5";
				for ($i=1;$i<$num_acti+1;$i++){
					echo "<th>Act$i</th>";
				}
			}
			if ($curso=="3ESO"){
				$num_acti = "6";
				for ($i=1;$i<$num_acti+1;$i++){
					echo "<th>Act$i</th>";
				}
			}
			echo "<th>Promociona</th>";
			//if ($curso=="2ESO" OR $curso=="3ESO" OR $curso=="4ESO"){
			//echo "<th>Repite</th>";
			//}
			?>
			</tr>
			<tr>
			<?php
			echo "<td>$num_rel</td>";
			if ($curso=="1ESO" OR $curso=="2ESO"){
				echo "<td>$num_exen</td>";
			}
			if ($curso=="3ESO" OR $curso=="2ESO"){
				echo "<td>$num_diver</td>";
			}
			//if ($curso=="3ESO"){$num_opt = "7";}elseif ($curso=="2ESO"){$num_opt = "3";}elseif ($curso=="1ESO"){$num_opt = "4";}elseif ($curso=="4ESO"){$num_opt = "5";}
			for ($i=1;$i<$num_opt+1;$i++){
				echo "<td>${num_opta.$i}</td>";
			}
			
			if ($curso=="1ESO"){
				$num_acti = "7";
				for ($i=1;$i<$num_acti+1;$i++){
					echo "<td>${num_act.$i}</td>";
				}
			}

			if ($curso=="2ESO"){
				$num_acti = "5";
				for ($i=1;$i<$num_acti+1;$i++){
					echo "<td>${num_act.$i}</td>";
				}
			}
			
			if ($curso=="3ESO"){
				$num_acti = "6";
				for ($i=1;$i<$num_acti+1;$i++){
					echo "<td>${num_act.$i}</td>";
				}
			}

		echo "<td>$num_promo</td>";

		//if ($curso=="2ESO" OR $curso=="3ESO" OR $curso=="4ESO"){
		//echo "<td>$num_repit</td>";
		//	}
		?>
			</tr>
		</table>
		<?php
	}
	?>

		<br />
		<table class="table table-striped table-bordered hidden-print"
			align="center" style="width: auto">
			<tr>
				<td><?php
				if ($curso=="4ESO") {

					for ($i=1;$i<$num_opt-1;$i++){
						$nombre_optativa = "";
						$nom_opt.= "<span style='font-weight:bold;color:#9d261d;'>Itinerario $i </span>";
						foreach (${it4.$i} as $nombre_opt => $valor){
							$n_it++;
							if ($n_it > 1):
								$nombre_optativa=$nombre_optativa+1;
								if ($nombre_opt>0) {
									$acr1="";
									$a_opt1 = iniciales($valor);
									$wrd1 = explode(" ", $valor);
									$num_w = count($wrd1);
									if ($num_w>1) {
										$acr1 = "";
										$acr1 = $a_opt1;					
									}
									else{
										$acr1 = strtoupper(substr($valor,0,3));
										}
									$acr3 = strtoupper(substr($valor,0,3));
									$nom_opt.="<span style='color:#08c;'>".$acr1."</span> = ".$valor."; ";
								}
						endif;
						//echo substr($nom_opt,0,-2);
						
					}
					$nom_opt.= "<br>";
				}
				}
				else{
					foreach (${opt.$n_curso} as $nombre_opt => $valor){
						$nombre_optativa=$nombre_opt+1;						
						$nom_opt.="<span style='color:#08c;'>Opt".$nombre_optativa."</span> = ".$valor."; ";
					}
				}
				echo substr($nom_opt,0,-2);
				?></td>
			</tr>
		</table>
		<?php
		if ($n_curso<4){
			echo '<table class="table table-striped table-bordered hidden-print" align="center" style="width:auto"><tr>
<td>';
			foreach (${a.$n_curso} as $nombre_a => $valora){
				$nombre_act=$nombre_a+1;
				$nom_a.="<span style='color:#08c;'>Act ".$nombre_act."</span> = ".$valora."; ";
			}
			echo substr($nom_a,0,-2).'</td></tr></table>';
		}
}
	}
	}
	?>
		</div>
	</div>
		<script language="javascript">
		 if (document.form2.curso.value=="1ESO"){ 
			 document.form2.itinerari.disabled = true; 
			 document.form2.diversificacio.disabled = true;
			 document.form2.promocion.disabled = true;
			 document.form2.actividade.disabled = false;
			 document.form2.exencio.disabled = true;
			}
		 if (document.form2.curso.value=="2ESO"){ 
			 document.form2.itinerari.disabled = true; 
			 document.form2.promocion.disabled = false;
			 document.form2.diversificacio.disabled = false;
			 document.form2.actividade.disabled = false;
			 document.form2.exencio.disabled = true;
			 document.form2.idioma2.disabled = true;
			}
		 if (document.form2.curso.value=="3ESO"){ 
			 document.form2.itinerari.disabled = true; 
			 document.form2.matematica3.disabled = false;
			 document.form2.actividade.disabled = false;
			 document.form2.exencio.disabled = true;
			 document.form2.diversificacio.disabled = false;
			 document.form2.promocion.disabled = false;
			 document.form2.idioma2.disabled = true;
			}
		 if (document.form2.curso.value=="4ESO"){ 
			 document.form2.actividade.disabled = true;
			 document.form2.exencio.disabled = true;
			 document.form2.itinerari.disabled = false; 
			 document.form2.matematica4.disabled = false;
			 document.form2.diversificacio.disabled = false;
			 document.form2.promocion.disabled = false;  
			 document.form2.idioma2.disabled = true;
			}
  </script>
  <?php
  // Control del envío de datos

  if (($mes_submit>5 and $mes_submit<9)) {
  	?>
		<script type="text/javascript">
function confirmacion2() {
	var answer = confirm("ATENCIÓN\n Estás a punto de procesar los datos de todos los alumnos de este Nivel tomando como referencia las calificaciones de la EVALUACIÖN ORDINARIA. Los alumnos que cumplen con los criterios de Promoción propios de su Nivel aparecen marcados en la columna como <<SI/NO>>.\n Si estás seguro de lo que haces pulsa Aceptar; de lo contrario pulsa Cancelar.")
	if (answer){
return true;
	}
	else{
return false;
	}
}
</script>
<?php
  }
  elseif ($mes_submit=="9") {
  	?>
		<script type="text/javascript">
function confirmacion2() {
	var answer = confirm("ATENCIÓN\n Estás a punto de procesar los datos de todos los alumnos de este Nivel tomando como referencia las calificaciones de la EVALUACIÖN EXTRAORDINARIA. Todos los alumnos han sido marcados en la columna <<SI/NO>> de acuerdo a los datos de promoción registrados en Séneca.\n Por motivos de seguridad, se va acrear una copia de respaldo de los datos originales de la matrícula de aquellos alumnos que NO promocionan. Estos datos pueden ser recuperados en todo momento pulsando el botón <<Restaurar>>.\n Si estás seguro de lo que haces pulsa Aceptar; de lo contrario pulsa Cancelar.")
	if (answer){
return true;
	}
	else{
return false;
	}
}
</script>
		<?php
  	  }
  
  ?>

		<?php include("../../pie.php"); ?>

		</body>
		</html>
