<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1, 2, 7, 8));

if (file_exists('config.php')) {
	include('config.php');
}

if (isset($_GET['curso'])) {$curso = $_GET['curso'];}elseif (isset($_POST['curso'])) {$curso = $_POST['curso'];}else{$curso="";}
if (isset($_GET['id'])) {$id = $_GET['id'];}elseif (isset($_POST['id'])) {$id = $_POST['id'];}else{$id="";}
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

// Control de tablas
$bck =mysqli_query($db_con,"select divorcio from matriculas_backup");
if (mysqli_num_rows($bck)>0) {}else{
	mysqli_query($db_con,"ALTER TABLE `matriculas_bach_backup` ADD `enfermedad` VARCHAR(254) NULL , ADD `otraenfermedad` VARCHAR(254) NULL , ADD `foto` TINYINT(1) NOT NULL , ADD `divorcio` VARCHAR(64) NULL , ADD `bilinguismo` CHAR(2) NULL , ADD `religion1b` VARCHAR(64) NULL");
}

if (isset($_POST['listados'])) {
foreach ($_POST as $key=>$val)
	{
		if (strlen($val)==1 and !(is_numeric($val))) {
			$cur_actual=$val;
		}
	}
	include("listados_bach.php");
	exit();
}

if (isset($_POST['listado_simple'])) {
	include("listado_simple_bach.php");
	exit();
}

if (isset($_POST['listado_total']) OR isset($_GET['listado_total'])) {
	include("listado_total_bach.php");
	exit();
}

// Impresión de listas
if (isset($_POST['imprimir'])) {	

	mysqli_query($db_con, "drop table if exists matriculas_bach_temp");
	mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS  `matriculas_bach_temp` (
 `id_matriculas` INT NOT NULL ,
INDEX (  `id_matriculas` )
)");

	foreach ($_POST as $key=>$val)
	{
		//echo "$key => $val<br>";
		$tr = explode("-",$key);
		$id_submit = $tr[1];
		$col = $tr[0];
		if (is_numeric($id_submit)) {
			mysqli_query($db_con, "insert into matriculas_bach_temp VALUES ('$id_submit')");
		}
	}
	include("imprimir_bach.php");
	mysqli_query($db_con, "drop table if exists matriculas_bach_temp");
	exit();
}

// Carátulas
if (isset($_POST['caratulas'])) {
	mysqli_query($db_con, "drop table if exists matriculas_bach_temp");
	mysqli_query($db_con, "CREATE TABLE  `matriculas_bach_temp` (
 `id_matriculas` INT NOT NULL ,
INDEX (  `id_matriculas` )
)");
	foreach ($_POST as $key=>$val)
	{
		$tr = explode("-",$key);
		$id_submit = $tr[1];
		$col = $tr[0];
		if (is_numeric($id_submit)) {
			mysqli_query($db_con, "insert into matriculas_bach_temp VALUES ('$id_submit')");
		}
	}
	include("caratulas_bach.php");	
	exit();
}

// Cambios en la matrícula
if (isset($_POST['cambios'])) {
	include("../../menu.php");
	include("menu.php");
	echo "<br>";
	mysqli_query($db_con, "drop table if exists matriculas_bach_temp");
	mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `matriculas_bach_temp` (
 `id_matriculas` INT NOT NULL ,
INDEX (  `id_matriculas` )
)");
	foreach ($_POST as $key=>$val)
	{
		$tr = explode("-",$key);
		$id_submit = $tr[1];
		$col = $tr[0];
		if (is_numeric($id_submit)) {
			mysqli_query($db_con, "insert into matriculas_bach_temp VALUES ('$id_submit')");
		}
	}

	$camb = mysqli_query($db_con, "select distinct id_matriculas from matriculas_bach_temp, matriculas_bach where id_matriculas=id order by apellidos, nombre");
	echo '<h3 align="center">Alumnos de <span style="color:#08c">'.$curso.'</span> con datos cambiados.</h3><br /><br />';
	echo "<div class='well well-large' style='width:650px;margin:auto;'>";
	while ($cam = mysqli_fetch_array($camb)) {
		$id_cambios = $cam[0];
		if ($curso == "1BACH") {
			//$c_clave = mysqli_query($db_con, "select * from alma_primera, matriculas_bach where alma_primera.claveal = matriculas_bach.claveal and id = '$id_cambios'");
			$c_clave = mysqli_query($db_con, "select * from alma, matriculas_bach where alma.claveal = matriculas_bach.claveal and id = '$id_cambios'");
			if(mysqli_num_rows($c_clave)>0){
				if (date('n')=="9") {
						$alma="alma_primera";
					}
					else{
						$alma="alma";
					}	
			}
			else{
				$alma="alma_secundaria";
			}
		}
		else{
			$alma="alma";
		}
		$contr = mysqli_query($db_con, "select matriculas_bach.apellidos, $alma.apellidos, matriculas_bach.nombre, $alma.nombre, matriculas_bach.domicilio, $alma.domicilio, matriculas_bach.dni, $alma.dni, matriculas_bach.padre, concat(primerapellidotutor,' ',segundoapellidotutor,', ',nombretutor), matriculas_bach.dnitutor, $alma.dnitutor, matriculas_bach.telefono1, $alma.telefono, matriculas_bach.telefono2, $alma.telefonourgencia, $alma.claveal from matriculas_bach, $alma where $alma.claveal=matriculas_bach.claveal and id = '$id_cambios'");
		$text_n="";
		$text_t="";
		//$col_datos = array()
		$control = mysqli_fetch_array($contr);
		if (strlen($control[16])>0) {
		$text_n= "<p style='color:#08c'>$control[16]: $control[0], $control[2]</p>";
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
	mysqli_query($db_con, "drop table matriculas_bach_temp");
	include("../../pie.php");
	echo '</body></html>';
	exit();
}


// Alumnos sin matrícula
if (isset($_POST['sin_matricula'])) {
	include("../../menu.php");
	include("menu.php");
	echo "<br>";
	if ($curso=="1BACH") {
		// Nuestro centro
		$hay_tabla = mysqli_query($db_con,"select * from alma");
		if (mysqli_num_rows($hay_tabla)>0) {

		$camb = mysqli_query($db_con, "select distinct apellidos, nombre, unidad, telefono, telefonourgencia, fecha, claveal from alma where claveal not in (select claveal from matriculas_bach) and curso like '4%' order by unidad, apellidos, nombre");

		echo '<h3 align="center">Alumnos del Centro de 4º de ESO sin matricular.</h3><br />';
		echo "<div class='well well-large' style='width:800px;margin:auto;'><ul class='unstyled'>";
		while ($cam = mysqli_fetch_array($camb)) {
			echo "<li><i class='fa fa-user'></i> &nbsp;$cam[7] <span style='color:#08c'>$cam[0], $cam[1]</span> --> <strong style='color:#9d261d'>$cam[2]</strong> : $cam[3] - $cam[4] ==> $cam[5] </li>";

		}
		echo "</ul></div><br />";
		}


		// Otros centros adscritos
		$hay_tabla = mysqli_query($db_con,"select * from alma_secundaria");
		if (mysqli_num_rows($hay_tabla)>0) {

		$camb = mysqli_query($db_con, "select distinct apellidos, nombre, unidad, telefono, telefonourgencia, fecha, colegio, claveal from alma_secundaria where claveal not in (select claveal from matriculas_bach) and curso like '4%' order by colegio, unidad, apellidos, nombre");

		echo '<h3 align="center">Alumnos de 4º de ESO sin matricular de IES adscritos.</h3><br />';
		echo "<div class='well well-large' style='width:800px;margin:auto;'><ul class='unstyled'>";
		while ($cam = mysqli_fetch_array($camb)) {
			if(strlen($cam[6])>0){$cole = " ($cam[6])";}else{$cole="";}
			echo "<li><i class='fa fa-user'></i> <span style='color:#08c'>$cam[0], $cam[1]</span> --> <strong style='color:#9d261d'>$cam[2]</strong> : $cam[3] - $cam[4] ==> $cam[5] $cole</li>";
			}
		echo "</ul></div><br />";
		}
		$canf = mysqli_query($db_con, "select distinct matriculas_bach.apellidos, matriculas_bach.nombre, matriculas_bach.curso, matriculas_bach.telefono1, matriculas_bach.telefono2, matriculas_bach.nacimiento, alma.unidad from matriculas_bach, alma where matriculas_bach.claveal=alma.claveal and (confirmado = '0' or confirmado is null) and matriculas_bach.curso like '1BACH' order by matriculas_bach.apellidos, matriculas_bach.nombre");

	echo '<h3 align="center">Alumnos de '.$curso.' prematriculados sin confirmar.</h3><br />';
			echo "<div class='well well-large' style='width:800px;margin:auto;'><ul class='unstyled'>";
	while ($cam2 = mysqli_fetch_array($canf)) {
				
			echo "<li><i class='fa fa-user'></i> &nbsp;<span style='color:#08c'>$cam2[0], $cam2[1]</span> --> <strong style='color:#9d261d'>$cam2[6]</strong> : $cam2[3] - $cam2[4] ==> $cam2[5]</li>";
		
	}
	}
	
	

	if ($curso=="2BACH") {

	$camb = mysqli_query($db_con, "select distinct apellidos, nombre, unidad, telefono, telefonourgencia, fecha, claveal from alma where claveal not in (select claveal from matriculas_bach) and curso like '1º de Bach%' order by unidad, apellidos, nombre");
	echo '<h3 align="center">Alumnos de '.$curso.' sin matricular.</h3><br />';
			echo "<div class='well well-large' style='width:800px;margin:auto;'><ul class='unstyled'>";
	while ($cam = mysqli_fetch_array($camb)) {
				
			echo "<li><i class='far fa-user'></i> &nbsp; <span style='color:#08c'>$cam[0], $cam[1]</span> --> <strong style='color:#9d261d'>$cam[2]</strong> : $cam[3] - $cam[4] ==> $cam[5]</li>";
	}	

echo "</ul></div><br />";

	$canf = mysqli_query($db_con, "select distinct matriculas_bach.apellidos, matriculas_bach.nombre, matriculas_bach.curso, matriculas_bach.telefono1, matriculas_bach.telefono2, matriculas_bach.nacimiento, alma.unidad from matriculas_bach, alma where matriculas_bach.claveal=alma.claveal and (confirmado = '0' or confirmado is null) and matriculas_bach.curso like '2BACH' order by matriculas_bach.apellidos, matriculas_bach.nombre");
	echo '<h3 align="center">Alumnos de '.$curso.' prematriculados sin confirmar.</h3><br />';
			echo "<div class='well well-large' style='width:800px;margin:auto;'><ul class='unstyled'>";
	while ($cam2 = mysqli_fetch_array($canf)) {
				
			echo "<li><i class='fa fa-user'></i> &nbsp;<span style='color:#08c'>$cam2[0], $cam2[1]</span> --> <strong style='color:#9d261d'>$cam2[6]</strong> : $cam2[3] - $cam2[4] ==> $cam2[5]</li>";
		
	}
}
echo "</ul></div>";
include("../../pie.php");
echo '</body></html>';
	exit();
}


?>
<script>
function confirmacion() {
	var answer = confirm("ATENCIÓN:\n ¿Estás seguro de que quieres borrar los datos? Esta acción es irreversible. Para borrarlo, pulsa Aceptar; de lo contrario, pulsa Cancelar.")
	if (answer){
return true;
	}
	else{
return false;
	}
}
</script>
<?php
include("../../menu.php");
include("menu.php");
 	
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
	  <h2>Matriculación de alumnos <small> Alumnos/as matriculados en Bachillerato</small></h2>
	</div>


<?php
// Asignaturas y Modalidades
//include 'asignaturas_bach.php';

$n_curso = substr($curso, 0, 1);
include 'filtro_bach.php';

if (isset($_GET['borrar'])) {
	mysqli_query($db_con, "insert into matriculas_bach_backup (select * from matriculas_bach where id = '$id')");
	mysqli_query($db_con, "delete from matriculas_bach where id='$id'");
	echo '<div align="center"><div class="alert alert-success alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
El alumno ha sido borrado de la tabla de matrículas. Se ha creado una copia de respaldo de us datos en la tabla matriculas_bach_backup.
</div></div><br />' ;
}
if (isset($_GET['copia'])) {
	
	if ($curso=="4ESO") {
	$conf = mysqli_query($db_con,"select confirmado from matriculas where id='$id_4' and confirmado='1'");
	if (mysqli_num_rows($conf)>0) { $confirma = 1; } else{ $confirma = 0; }

	mysqli_query($db_con, "delete from matriculas where id='$id_4'");
	$curso="1BACH";
	}
	else{
	$conf = mysqli_query($db_con,"select confirmado from matriculas_bach where id='$id' and confirmado='1'");
	if (mysqli_num_rows($conf)>0) { $confirma = 1; } else{ $confirma = 0; }	

	mysqli_query($db_con, "delete from matriculas_bach where id='$id'");
	}
	mysqli_query($db_con, "insert into matriculas_bach(select * from matriculas_bach_backup where id = '$id')");

	mysqli_query($db_con, "update matriculas_bach set confirmado='$confirma' where id='$id'");

	mysqli_query($db_con, "delete from matriculas_bach_backup where id='$id'");

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

if ($optativ) { 
	if ($curso=='1BACH') {
		$extra.=" and optativa".$n_curso." = '".$optativ."'";
	}
	else{
			$it_op = substr($optativ, -1);
			foreach (${opt2.$it_op} as $key => $value) {
				$num_opt++;
				if ($optativ==$key) {
					$n_op = $num_opt;
					$extra.=" and itinerario2='$it_op' and optativa2b".$n_op." = '1'";
					}
				}			
			}	
		}


if ($opt_2h) { $extra.=" and opt_aut2".$opt_2h." = '1'";}


if ($optativ2) { $extra.=" and optativa2 = '".$optativ2."'";}
	
	
if ($optativ121) { $extra.=" and optativa121 = '".$optativ121."'";}

	
if ($itinerari) { $extra.=" and itinerario$n_curso = '$itinerari'";}
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
//if ($grupo_actua) { if($grupo_actua=="Ninguno"){$extra.=" and grupo_actual = ''";} else{  $extra.=" and grupo_actual = '$grupo_actua'";}}
if ($colegi) { $extra.=" and colegio = '$colegi'";}
if ($transport == "ruta_este") { $extra.=" and ruta_este != ''";}
if ($transport == "ruta_oeste") { $extra.=" and ruta_oeste != ''";}
if (strlen($dn)>5) {$extra.=" and dni = '$dn'";}
if (strlen($apellid)>2) {$extra.=" and apellidos like '%$apellid%'";}
if (strlen($nombr)>1) {$extra.=" and nombre like '%$nombr%'";}

if ($promocion=="SI") { $extra.=" and promociona = '1'";}
if ($promocion=="NO") { $extra.=" and promociona = '2'";}

if ($bilinguism=="Si") { $extra.=" and bilinguismo = 'Si'";}
if ($bilinguism=="No") { $extra.=" and bilinguismo = ''";}



if (!($orden)) {
	$orden=" ";
		if (isset($_POST['op_orden'])) {
			$op_filtro= $_POST['op_orden'];
			if ($_POST['op_orden']=="opt_orden") {
				$orden.="optativa1, optativa2, ";
			}
		else{
				$orden.="$op_filtro desc, ";
			}
		}
		
		
	if ($curso=="1BACH") {
		// En Junio puede interesar ordenar por colegio
		if (date('m')>'05' and date('m')<'09'){
			$orden.="colegio, ";
		}
		else{
			$orden.="";
		}
	}

	include 'procesado_bach.php';

	$sql = "select id, apellidos, nombre, curso, letra_grupo, colegio, otrocolegio, confirmado, grupo_actual, observaciones, religion, itinerario1, itinerario2, promociona, claveal, ruta_este, ruta_oeste, revisado, optativa1, optativa2, idioma1, idioma2, optativa151, optativa152, optativa121 ";

	if ($curso=="2BACH"){
		for ($i=1;$i<10;$i++)
		{
			$sql.=", optativa2b$i";
		}
		for ($i=1;$i<9;$i++){
			$sql.=", opt_aut2$i";
		}
	}
	$sql.=", repite, foto, enfermedad, bilinguismo, divorcio, analgesicos, parcial from matriculas_bach where ". $extra ." order by ". $orden ." curso,  grupo_actual, apellidos, nombre ";
	 //echo $sql;
	$cons = mysqli_query($db_con, $sql);

	$n_cons = mysqli_num_rows($cons);
	if($n_cons=="0"){
	}
	else{
		if ($curso) {
			//$n_cons = mysqli_num_rows($cons);
			?>
<h3 align=center><?php if($_POST['grupo_actua']){ 
	echo $curso." ";
	foreach ($_POST['grupo_actua'] as $grup_actua){
		echo $grup_actua." ";
	}
} else{ echo $n_curso."º de Bachillerato";}?></h3>
<br />
<form action="consultas_bach.php?curso=<?php echo $curso;?>&consulta=1"
	name="form1" method="post">
<table class="table table-striped table-condensed" align="center"
	style="width: auto">
	<tr>
		<th colspan="2"></th>
		<th>Nombre</th>
		<th>Curso</th>
		<th>Gr1</th>
		<th>Gr2</th>
		<?php
		echo '<th>Centro</th>';
		echo '<th>Rel.</th>';
		echo '<th>Transporte</th>';
		echo '<th>Bil.</th>';
		echo '<th>Mod.</th>';
		if ($n_curso>1) {
		echo "<th>Opt_2h</th>";
			for ($i = 1; $i < 3; $i++) {
				echo '<th>O'.$i.'</th>';
			}
		}
		else{
		echo "<th>Opt1B</th>";	
		echo "<th>Opt1B51</th>";
		echo "<th>Opt1B52</th>";
		echo "<th>Opt1B21</th>";
		}
		?>
		<?php
		echo '<th class="hidden-print" style="align:center">Sí NO</th>';
		?>
		<!-- Se han producido cambios en la matrícula -->
		<th class="hidden-print">Camb.</th>
		<!-- Problemas de convivencia y comportamiento -->
		<th class="hidden-print">Conv.</th>
		<!-- Datos diversos: observaciones, enfermedades, foto, etc. -->
		<th class="hidden-print">Otros</th>
	</tr>
	<?php
	while($datos_ya = mysqli_fetch_object($cons)){

		$backup="";
		$respaldo='1';
		$naci = explode("-",$datos_ya->nacimiento);
		$nacimiento = "$naci[2]-$naci[1]-$naci[0]";
		$apellidos = $datos_ya->apellidos; $id = $datos_ya->id; $nombre = $datos_ya->nombre; $nacido = $datos_ya->nacimiento; $provincia = $datos_ya->provincia; $domicilio = $datos_ya->domicilio; $localidad = $datos_ya->localidad; $dni = $datos_ya->dni; $padre = $datos_ya->padre; $dnitutor = $datos_ya->dnitutor; $madre = $datos_ya->madre; $dnitutor2 = $datos_ya->dnitutor2; $telefono1 = $datos_ya->telefono1; $telefono2 = $datos_ya->telefono2; $colegio = $datos_ya->colegio; $correo = $datos_ya->correo; $otrocolegio = $datos_ya->otrocolegio; $letra_grupo = $datos_ya->letra_grupo; $religion = $datos_ya->religion; $observaciones = $datos_ya->observaciones; $promociona = $datos_ya->promociona; $transporte = $datos_ya->transporte; $ruta_este = $datos_ya->ruta_este; $ruta_oeste = $datos_ya->ruta_oeste; $sexo = $datos_ya->sexo; $hermanos = $datos_ya->hermanos; $nacionalidad = $datos_ya->nacionalidad; $claveal = $datos_ya->claveal; $curso = $datos_ya->curso;  $itinerario1 = $datos_ya->itinerario1; $itinerario2 = $datos_ya->itinerario2; $optativa1 = $datos_ya->optativa1; $optativa2 = $datos_ya->optativa2; $optativa2b1 = $datos_ya->optativa2b1; $optativa2b2 = $datos_ya->optativa2b2; $optativa2b3 = $datos_ya->optativa2b3; $optativa2b4 = $datos_ya->optativa2b4; $optativa2b5 = $datos_ya->optativa2b5; $optativa2b6 = $datos_ya->optativa2b6; $optativa2b7 = $datos_ya->optativa2b7; $optativa2b8 = $datos_ya->optativa2b8; $optativa2b9 = $datos_ya->optativa2b9; $opt_aut21 = $datos_ya->opt_aut21; $opt_aut22 = $datos_ya->opt_aut22; $opt_aut23 = $datos_ya->opt_aut23; $opt_aut24 = $datos_ya->opt_aut24; $opt_aut25 = $datos_ya->opt_aut25; $opt_aut26 = $datos_ya->opt_aut26; $opt_aut27 = $datos_ya->opt_aut27; $repetidor = $datos_ya->repite;$revisado = $datos_ya->revisado; $confirmado = $datos_ya->confirmado; $grupo_actual = $datos_ya->grupo_actual; $idioma1 = $datos_ya->idioma1; $idioma2 = $datos_ya->idioma2; $foto = $datos_ya->foto; $enf = $datos_ya->enfermedad;  $bilinguismo = $datos_ya->bilinguismo;  $divorcio = $datos_ya->divorcio; $analgesicos = $datos_ya->analgesicos; $parcial = $datos_ya->parcial; $optativa151 = $datos_ya->optativa151; $optativa152 = $datos_ya->optativa152; $optativa121 = $datos_ya->optativa121;
		$back = mysqli_query($db_con, "select id from matriculas_bach_backup where id = '$id'");

if (mysqli_num_rows($back)>0) {
	$respaldo = '1';
	$backup="<a href='consultas_bach.php?copia=1&id=$id&curso=$curso&consulta=1'><i class='fa fa-sync-alt' data-bs='tooltip' title='Restaurar datos originales de la matrícula del alumno '> </i></a>";
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
	<td><a href="matriculas_bach.php?id='. $id .'&c_escolar='. $c_escolar .'" target="_blank">'.$apellidos.', '.$nombre.'</a></td>

	<td>'.$curso.'</td>
	<td>'.$letra_grupo.'</td>
	<td><input name="grupo_actual-'. $id .'" type="text" class="form-control input-sm" style="width:35px" value="'. $grupo_actual .'" /></td>';
		echo '<td>'. $colegio .'</td>';
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
		$an_bd = substr($config['curso_inicio'],0,4);
		$bl="";
		$extra_bil="";
		if ($curso=='2BACH') {
		$bl0 = mysqli_query($db_con,"select * from matriculas_bach_".$an_bd." where claveal = '$claveal' and bilinguismo = 'Si' and claveal not like ''");
		if (mysqli_num_rows($bl0)>0) { 
			$bl = '1';
		}
		}
		echo '<td> <input name="bilinguismo-'. $id .'" type="checkbox" value="Si"';
		if($bilinguismo=="Si"){ echo " checked";} elseif ($bl == '1') { echo " checked";}
		echo ' />';
		if ($bilinguismo=="Si" and $bl != 1 and $curso=='2BACH') {
			$extra_bil = "<i class='fas fa-question text-warning' data-bs='tooltip' title='El alumno no aparece como bilingue en la matrícula del curso anterior'> </i>";
		}
		echo $extra_bil.'</td>';

		echo '<td>'.${itinerario.$n_curso}.'</td>';
		//if ($optativa1 == '0') {${optativa.$i}="";}

		if ($n_curso>1) {
			for ($i = 1; $i < ($count_2b2+1); $i++) {
				if (${opt_aut2.$i}=='1') {
					echo '<td>'.$i.'</td>';
				}
			}	

			for ($i = 1; $i < 10; $i++) {
				if (${optativa2b.$i}=='1') {
					echo '<td>'.$i.'</td>';
				}
			}
			for ($i = 1; $i < 10; $i++) {
				if (${optativa2b.$i}=='2') {
					echo '<td>'.$i.'</td>';
				}
			}
		}
		else{
			echo '<td>'.$optativa1.'</td>';
			echo '<td>'.substr($optativa151,0,3).'</td>';
			echo '<td>'.substr($optativa152,0,3).'</td>';
			echo '<td>'.substr($optativa121,0,3).'</td>';
		}

		// Curso actual
			$c_a = mysqli_query($db_con, "select curso from alma where alma.claveal='".$claveal."'");
			$cur_a = mysqli_fetch_array($c_a);
			$curs_ant = substr($cur_a[0],0,1);

		// Promocionan o no en Septiembre
		$pro_n = mysqli_query($db_con, "select estadomatricula from alma where alma.claveal='".$claveal."'");
			$pro_nt = mysqli_fetch_array($pro_n);
			$promo_f = $pro_nt[0];
			$promociona="";
			if ($promo_f=="Repite") { $promociona="2"; }elseif($promo_f=="Obtiene Título" or $promo_f=="Promociona"){ $promociona="1"; }else{ $promociona=""; }

			$rp_cur="";
			if ($promociona == "1" and $n_curso==$curs_ant) {
				$rp_cur = "<i class='far fa-exclamation-circle text-danger' data-bs='tooltip' title='El alumno ha promocionado y su matrícula debe ser restaurada'> </i>";
			}

		if ($n_curso) {
			$val_notas="";
			echo "<td class='hidden-print' style='background-color:#efeefd;' nowrap>";
				
				$not = mysqli_query($db_con, "select notas3, notas4 from notas, alma where alma.claveal1=notas.claveal and alma.claveal=".$claveal."");
				$nota = mysqli_fetch_array($not);
				$nota[0] = substr($nota[0],0,strlen($nota[0])-1);
				$nota[1] = substr($nota[1],0,strlen($nota[1])-1);

				if (empty($nota[1]) and strlen($nota[0])>4){
					$val_notas="";				
					$tr_not = explode(";", $nota[0]);
					$num_ord="";
					foreach ($tr_not as $val_asig) {
						$tr_notas = explode(":", $val_asig);
						foreach ($tr_notas as $key_nota=>$val_nota) {
							$num_ord+=1;
						if ($n_curso == "1") {
							$rp = mysqli_query($db_con,"select  * from matriculas_bach_backup where claveal=".$claveal." and curso='2BACH'");
							if (mysqli_num_rows($rp)>0) {
								if($key_nota == "1" and $val_nota<'427' or $val_nota =="439" or $val_nota ==""){
								$val_notas=$val_notas+1;
							}
							}
							else{
								if($key_nota == "1" and ($val_nota<'347' or $val_nota == '397')  and $val_nota !=="" and $val_nota !=="339"){
								$val_notas=$val_notas+1;
							}
							}						
							}
						if ($n_curso == "2") {
							if($key_nota == "1" and ($val_nota<'427' or $val_nota =="439") and $val_nota !==""){
								$val_notas=$val_notas+1;
							}
							}
						}
					}
				}
				elseif (strlen($nota[1])>4){
					$val_notas="";
					if (empty($nota[1])) {}
					else{					
						$tr_not2 = explode(";", $nota[1]);
						$num_ord1="";
						foreach ($tr_not2 as $val_asig) {
							$tr_notas = explode(":", $val_asig);
							foreach ($tr_notas as $key_nota=>$val_nota) {
								$num_ord1+=1;
							if ($n_curso == "1") {
								if($key_nota == "1" and ($val_nota<'347' or $val_nota == '397')  and $val_nota !=="" and $val_nota !=="339"){
									$val_notas++;
								}						
								}
							if ($n_curso == "2") {
								if($key_nota == "1" and ($val_nota<'427' or $val_nota =="439") and $val_nota !==""){
									$val_notas++;
								}
								}
							}
						}				
					}
				}
				
				if (date('m')=='09' OR ($curs_ant == '4' and date('m')>6)){
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
				elseif (date('m')>'05' and date('m')<'09'){
					for ($i=1;$i<3;$i++){
						echo '<input type="radio" name = "promociona-'. $id .'" value="'.$i.'" ';
							if($promociona == 1 and $i == 1){
								echo " checked";
						}
						echo " />&nbsp;";
					}
					if ($val_notas > 0) {
						echo "<span class='badge badge-info' data-bs='tooltip' title='Materias suspensas'> $val_notas</span>";		
					}		
				}
			}
			if($parcial == 1){ echo '<span class="far fa-times-circle" style="color: blue;" data-bs="tooltip" title="Matrícula parcial."></span>&nbsp;';}

			echo "</td>";

		echo '<td class="hidden-print text-center"><input name="revisado-'. $id .'" type="checkbox" value="1" onClick="submit()"';
		if($revisado=="1"){echo " checked";}
		echo ' /></td>';
			echo "<td class='hidden-print'>";
// Problemas de Convivencia
if($n_fechorias >= 15){ echo "<a href='../fechorias/fechorias.php?claveal=$claveal&submit1=1'><span class='badge badge-important' target='blank'>$n_fechorias</span></a>";}
elseif($n_fechorias > 4 and $n_fechorias < 15){ echo "<a href='../fechorias/fechorias.php?claveal=$claveal&submit1=1' target='blank'><span class='badge badge-warning'>$n_fechorias</span></a>";}
elseif($n_fechorias < 5 and $n_fechorias > 0){ echo "<a href='../fechorias/fechorias.php?claveal=$claveal&submit1=1' target='blank'><span class='badge badge-info'>$n_fechorias</span></a>";}
// Fin de Convivencia.
echo "</td>";

		echo '<td class="hidden-print" style="text-align:right" nowrap>';
		$contr = mysqli_query($db_con, "select matriculas_bach.apellidos, alma.apellidos, matriculas_bach.nombre, alma.nombre, matriculas_bach.domicilio, alma.domicilio, matriculas_bach.dni, alma.dni, matriculas_bach.padre, concat(primerapellidotutor,' ',segundoapellidotutor,', ',nombretutor), matriculas_bach.dnitutor, alma.dnitutor, matriculas_bach.telefono1, alma.telefono, matriculas_bach.telefono2, alma.telefonourgencia from matriculas_bach, alma where alma.claveal=matriculas_bach.claveal and id = '$id'");
		$control = mysqli_fetch_array($contr);

		$text_contr="";
		for ($i = 0; $i < 16; $i++) {
			if ($i%2) {
				if ($i=="5" and strstr($control[$i], $control[$i-1])==TRUE) {}
		else{
		if ($control[$i]==$control[$i-1]) {$icon="";}else{	
			if ($control[$i-1] != 0) {
						$icon="fa fa-info-circle";
						$text_contr.= $control[$i]." --> ".$control[$i-1]."; ";					
				}	
		}
	}
			}
			//echo "$control[$i] --> ";
		}
		echo "<i class='$icon' data-bs='tooltip' title='".$text_contr."'> </i>&nbsp;";
		

		if ($observaciones) { echo "<i class='fa fa-bookmark' data-bs='tooltip' title='$observaciones' > </i>&nbsp;";}

		if ($respaldo=='1') {
			echo $backup."&nbsp;".$rp_cur;
		}

if($foto <> 1){ echo '<span class="fa fa-camera" style="color: orange;" data-bs="tooltip" title="No permite publicar su foto."></span>&nbsp;';}
if($analgesicos <> 1){ echo '<span class="fa fa-tablets" style="color: blue;" data-bs="tooltip" title="No permite analgésicos."></span>&nbsp;';}
if(!empty($enf)){ echo '<span class="fa fa-medkit" style="color: red;" data-bs="tooltip" title="'.$enf.'"></span>&nbsp;';}
if(!empty($divorcio)){ 
	if ($divorcio=="Guardia y Custodia compartida por Madre y Padre") {echo '<span class="fa fa-group" style="color: orange;" data-bs="tooltip" title="'.$divorcio.'"></span>';}
	elseif($divorcio=="Guardia y Custodia de la Madre") {echo '<span class="fa fa-female" style="color: orange;" data-bs="tooltip" title="'.$divorcio.'"></span>';}
	elseif($divorcio=="Guardia y Custodia del Padre") {echo '<span class="fa fa-male" style="color: orange;" data-bs="tooltip" title="'.$divorcio.'"></span>';}
	}
	echo "<a href='consultas_bach.php?borrar=1&id=$id&curso=$curso&consulta=1' data-bb='confirm-delete'><i class='fa fa-trash-alt' data-bs='tooltip' title='Eliminar alumno de la tabla'> </i></a>&nbsp;";
echo "</td>";
echo '</tr>';	
	}
	}
	echo "</table>";
	echo "<div align='center'><br />
<input type='hidden' name='extra' value='$extra' />";
		  
		  // Control del envío de datos
	echo "<input type='submit' name='enviar' value='Enviar datos' class='btn btn-primary hdden-print' onclick='confirmacion2()' /><br>";

	
echo "<br>
<input type='submit' name='imprimir' value='Imprimir'  class='btn btn-success hidden-print' formtarget='_blank' />&nbsp;&nbsp;
<input type='submit' name='caratulas' value='Imprimir Carátulas' class='btn btn-success hidden-print' formtarget='_blank' />&nbsp;&nbsp;
<input type='submit' name='cambios' value='Ver cambios en datos' class='btn btn-warning hidden-print' formtarget='_blank' />&nbsp;&nbsp;
<input type='submit' name='sin_matricula' value='Alumnos sin matricular' class='btn btn-danger hidden-print' formtarget='_blank' />";
	if(count($grupo_actua)=='1'){ echo "
	<input type='hidden' name='grupo_actual' value='$grupo_actua' />&nbsp;&nbsp;
	<input type='submit' name='listados' value='Listado en PDF' class='btn btn-info hidden-print' formtarget='_blank' />";} 
	else{ 
	echo "&nbsp;&nbsp;
	<input type='submit' name='listado_total' value='Listado PDF total' class='btn btn-info hidden-print' formtarget='_blank' />
	&nbsp;&nbsp;
	<input type='submit' name='listado_simple' value='Listado Simple' class='btn btn-info hdden-print' formtarget='_blank' />";
	}
	echo "</div></form>";
	?>
	<?php
	if ($curso) {

		$rel = mysqli_query($db_con, "select religion from matriculas_bach where $extra and religion like '%Católica%'");
		$num_rel = mysqli_num_rows($rel);
		//echo $num_rel;
		if ($curso=="2BACH"){		
		for ($i=1;$i<10;$i++){
			${optativ.$i} = mysqli_query($db_con, "select optativa2b$i from matriculas_bach where $extra and optativa2b$i = '1'");
			${optativb.$i} = mysqli_num_rows(${optativ.$i});
		}
		}
		$promo = mysqli_query($db_con, "select promociona from matriculas_bach where $extra and promociona = '1'");
		$num_promo = mysqli_num_rows($promo);
		$an_bd = substr($config['curso_actual'],0,4);
		$repit = mysqli_query($db_con, "select promociona from matriculas_bach where $extra and promociona = '2'");
		$num_repit = mysqli_num_rows($repit);
		
		$it_11 = mysqli_query($db_con, "select itinerario1 from matriculas_bach where $extra and itinerario1 = '1'");
		$num_it11 = mysqli_num_rows($it_11);
		$it_12 = mysqli_query($db_con, "select itinerario1 from matriculas_bach where $extra and itinerario1 = '2'");
		$num_it12 = mysqli_num_rows($it_12);
		$it_13 = mysqli_query($db_con, "select itinerario1 from matriculas_bach where $extra and itinerario1 = '3'");
		$num_it13 = mysqli_num_rows($it_13);
		$it_14 = mysqli_query($db_con, "select itinerario1 from matriculas_bach where $extra and itinerario1 = '4'");
		$num_it14 = mysqli_num_rows($it_14);
		$it_15 = mysqli_query($db_con, "select itinerario1 from matriculas_bach where $extra and itinerario1 = '5'");
		$num_it15 = mysqli_num_rows($it_15);

		$it_21 = mysqli_query($db_con, "select itinerario2 from matriculas_bach where $extra and itinerario1 = '1'");
		$num_it21 = mysqli_num_rows($it_21);
		$it_22 = mysqli_query($db_con, "select itinerario2 from matriculas_bach where $extra and itinerario2 = '2'");
		$num_it22 = mysqli_num_rows($it_22);
		$it_23 = mysqli_query($db_con, "select itinerario2 from matriculas_bach where $extra and itinerario2 = '3'");
		$num_it23 = mysqli_num_rows($it_23);
		$it_24 = mysqli_query($db_con, "select itinerario2 from matriculas_bach where $extra and itinerario2 = '4'");
		$num_it24 = mysqli_num_rows($it_24);
		?>
	<br />
	<table class="table table-striped table-bordered" align="center"
		style="width: auto">
		<tr>
		<?php
		echo "<th>Religión</th>";

		//echo "<th>Optativa$n_curso</th>";

		echo "<th>Promociona</th>";
		echo "<th>Repite</th>";
		if ($curso=="1BACH"){
		//echo "<th>3/4</th>";
		}
		
		echo "<th>Itinerario1</th>";
		echo "<th>Itinerario2</th>";
		echo "<th>Itinerario3</th>";
		echo "<th>Itinerario4</th>";
		echo "<th>Itinerario5</th>";

		if ($curso=="2BACH"){
		for ($i=1;$i<10;$i++){
			echo "<td>Opt.$i</td>";
		}
		}
		?>
		</tr>
		<tr>
		<?php
		echo "<td>$num_rel</td>";
		echo "<td>$num_promo</td>";
		echo "<td>$num_repit</td>";
		
		if ($curso=="1BACH"){
		echo "<td>$num_it11</td>";
		echo "<td>$num_it12</td>";
		echo "<td>$num_it13</td>";
		echo "<td>$num_it14</td>";
		echo "<td>$num_it15</td>";
		}
		else{
		echo "<td>$num_it21</td>";
		echo "<td>$num_it22</td>";
		echo "<td>$num_it23</td>";
		echo "<td>$num_it24</td>";
		}
		

		if ($curso=="2BACH"){
		for ($i=1;$i<10;$i++){
			echo "<td>${optativb.$i}</td>";
	}
	}
	
	?>
		</tr>
	</table>
	<?php
		}
		?>

	<br />

	<table class="table table-striped table-bordered hidden-print"
		align="center" style="width:1000px;">
		<tr>			
			<?php
			for ($i=1; $i < 3; $i++) { 
				if ($curso==$i."BACH"){
					for ($z=1; $z < 6; $z++) { 
						$num="";
						echo "<td><h4 class='text-info'>Itinerario $z:<br>";
						echo "<small>".${it.$i}[$z]."</small></h5>";
						foreach (${opt.$i.$z} as $clave=>$valor){
							$num++;
							echo "<span class='text-danger'>".$num.": </span>".$valor."<br>";
						}	
						echo "</td>";			
					}	
				}
			}		
			?>			
		</tr>
	</table>

<?php if ($curso=="2BACH"){ ?>
		<table class="table table-striped table-bordered hidden-print"
		align="center" style="width:1000px;">
		<tr>			
			<?php				
						$num="";
						echo "<td><h4 class='text-info'>Optativas de 2 horas de 2º de Bachillerato</h4>";
						foreach ($opt_aut2 as $clave=>$valor){
							$num++;
							echo "<span class='text-danger'>Optativa ".$num.": </span>".$valor."; ";
						}	
						echo "</td>";			
			?>			
		</tr>
	</table>
<?php } ?>

	<?php
			}
		}
	}
?>
	</div>
	
</div>
</div>
	
	<?php include("../../pie.php"); ?>
  <?php
  // Control del envío de datos

  if (($mes_submit>5 and $mes_submit<9)) {
?>
 <script type="text/javascript">
function confirmacion2() {
	var answer = confirm("ATENCIÓN\n Estás a punto de procesar los datos de todos los alumnos de este Nivel tomando como referencia las calificaciones de la EVALUACIÖN ORDINARIA. Los alumnos que cumplen con los criterios de Promoción propios de su Nivel han sido marcados en la columna <<SI-NO-3/4>>.\n El resto de los alumnos serán procesados tras la Evaluación Extraordinaria de Septiembre.\n Si estás seguro de lo que haces pulsa Aceptar; de lo contrario pulsa Cancelar.")
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
	var answer = confirm("ATENCIÓN\n Estás a punto de procesar los datos de todos los alumnos de este Nivel tomando como referencia las calificaciones de la EVALUACIÖN EXTRAORDINARIA. Todos los alumnos han sido marcados en la columna <<SI/NO/PIL>> de acuerdo a los criterios regulares de promoción.\n ES MUY IMPORTANTE que marques con un SÍ aquellos alumnos que promocionan a 1º de Bachillerato por decisión del Equipo Educativo a pesar de que no cumplen con los criterios regulares de promoción.\n Por motivos de seguridad, se va acrear una copia de respaldo de los datos originales de la matrícula de aquellos alumnos que NO promocionan. Estos datos pueden ser recuperados en todo momento pulsando el botón <<Restaurar>>.\n Si estás seguro de lo que haces pulsa Aceptar; de lo contrario pulsa Cancelar.")
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
 <script language="javascript">
		 if (document.form2.curso.value=="1BACH"){ 
			 document.form2.idiom1.disabled = true;
			 document.form2.idiom2.disabled = true; 
			}
		 if (document.form2.curso.value=="2BACH"){ 

			}
  </script>
</body>
</html>
