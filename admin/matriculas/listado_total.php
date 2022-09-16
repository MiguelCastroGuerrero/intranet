<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1, 2, 8));

if (file_exists('config.php')) {
	include('config.php');
}

if (isset($_POST['grupo_actual']) OR isset($_GET['grupo_actual'])) {
	$grupo_actual = $_POST['grupo_actual'];
	if(empty($grupo_actual)){ $grupo_actual[0] = $_GET['grupo_actual'];}
	$sql = "select distinct curso, grupo_actual from matriculas where grupo_actual = '$grupo_actual[0]' and curso='$curso' order by curso, grupo_actual";
}
else{
	$sql = "select distinct curso, grupo_actual from matriculas where grupo_actual != '' order by curso, grupo_actual";
}

require_once('../../pdf/class.ezpdf.php');

$pdf = new Cezpdf('a4');
$pdf->selectFont('../../pdf/fonts/Helvetica.afm');
$pdf->ezSetCmMargins(1,1,1.5,1.5);
$tot = mysqli_query($db_con, $sql);

while($total = mysqli_fetch_array($tot)){
	
$options_center = array(
				'justification' => 'center'
			);
$options_right = array(
				'justification' => 'right'
			);
$options_left = array(
				'justification' => 'left'
					);
	$curso = $total[0];
	$grupo_actual = $total[1];
	
$sqldatos="SELECT concat(apellidos,', ',nombre), exencion, optativa1, optativa2, optativa3, optativa4, optativa5, optativa6, optativa7, optativa8, optativa9, act1, religion, diversificacion, matematicas3, bilinguismo, itinerario, optativas4, ciencias4, idioma2 FROM matriculas WHERE curso = '$curso' and grupo_actual='".$grupo_actual."' ORDER BY apellidos, nombre";
//echo $sqldatos;

$div3=mysqli_query($db_con,"SELECT diversificacion FROM matriculas WHERE curso = '$curso' and grupo_actual='".$grupo_actual."' and diversificacion='1'");
if (mysqli_num_rows($div3)>0) {
	$div_3 = $grupo_actual;
}		
	
$lista= mysqli_query($db_con, $sqldatos );
$nc=0;
unset($data);
while($datatmp = mysqli_fetch_array($lista)) { 


$bil = "";
if($datatmp['bilinguismo']=="Si"){$bil = " (Bil.)";}
        
$religion = "";
	
$idioma2 = $datatmp['idioma2'];
	
if ($curso=="3ESO") {
	// Diversificación
if ($datatmp['diversificacion']=="1") {
			$datatmp['diversificacion']="X";
		}
		else{
			$datatmp['diversificacion']="";
		}
}
else {
if ($curso=="2ESO") {
	// Diversificación
	if ($datatmp['diversificacion']=="1") {
			$datatmp['diversificacion']="X";
		}
		else{
			$datatmp['diversificacion']="";
		}
}	
}

$op1="";
$op2="";
	
	for ($i = 2; $i < 11; $i++) {
		
		if ($datatmp[$i]=="1") {
			$op1=$i-1;
		}
		elseif ($datatmp[$i]=="2") {
			$op2=$i-1;
		}
		else{
			$datatmp[$i]="";
		}
	}
	
	
$nc+=1;

if (strstr($datatmp['religion'],"Cat")==TRUE) {
	$religion ="X";
}

if ($curso=="3ESO") {
	$num="";
	$opt = "\nOptativas:
	";
	$num="";
	foreach ($opt3 as $val) {
		$num++;
		$opt.="$num => $val, ";
	}
	$opt = substr($opt, 0, -2);

	if ($div_3 == $grupo_actual) {
			$data[] = array(
				'num'=>$nc,
				'nombre'=>utf8_decode($datatmp[0]),
				'c9'=>$religion,
				'c2'=>$op1,
				'c11'=>$datatmp['diversificacion'],				
				);
	$titles = array(
				'num'=>'<b>NC</b>',
				'nombre'=>'<b>Alumno</b>',
				'c9'=>'Rel. Cat.',
				'c2'=>'Opt1',
				'c11'=>'Divers.',
			);
	}
	else{
			$data[] = array(
				'num'=>$nc,
				'nombre'=>utf8_decode($datatmp[0]).$bil,
				'c10'=>$religion,
				'c2'=>$op1,
				);
			$titles = array(
				'num'=>'<b>NC</b>',
				'nombre'=>'<b>Alumno</b>',
				'c10'=>'Rel. Cat.',
				'c2'=>'Optativa',
			);
	}
}

if ($curso=="2ESO") {

	$opt = "\nOptativas:
	";
	$num="";
	foreach ($opt2 as $val) {
		$num++;
		$opt.="$num => $val, ";
	}
	$opt = substr($opt, 0, -2);

	if ($div_3 == $grupo_actual) {
				$data[] = array(
				'num'=>$nc,
				'nombre'=>utf8_decode($datatmp[0]),
				'c1'=>$religion,
				'c2'=>$op1,
				'c3'=>$datatmp['diversificacion'],
				);

	$titles = array(
				'num'=>'<b>NC</b>',
				'nombre'=>'<b>Alumno</b>',
				'c1'=>'Rel. Cat.',
				'c2'=>'Optativa',
				'c3'=>'Divers.',
			);
	}
	else{
			$data[] = array(
				'num'=>$nc,
				'nombre'=>utf8_decode($datatmp[0]),
				'c1'=>$religion,
				'c2'=>$op1,
				);

	$titles = array(
				'num'=>'<b>NC</b>',
				'nombre'=>'<b>Alumno</b>',
				'c1'=>'Rel. Cat.',
				'c2'=>'Optativa',
			);
	}

}


if ($curso=="1ESO") {

	$opt = "\nOptativas:
	";
	$num="";
	foreach ($opt1 as $val) {
		$num++;
		$opt.="$num => $val, ";
	}
	$opt = substr($opt, 0, -2);


	if ($datatmp['exencion']==1) {
		$exencion = 'X';
	}
	else{
		$exencion = '';
	}
	
	$data[] = array(
				'num'=>$nc,
				'nombre'=>utf8_decode($datatmp[0]),
				'c1'=>$religion,
				'c2'=>$op1,
				'c3'=>utf8_decode($idioma2),
				'c4'=>$exencion,
				);

	$titles = array(
				'num'=>'<b>NC</b>',
				'nombre'=>'<b>Alumno</b>',
				'c1'=>'Rel. Cat.',
				'c2'=>'Optativa',
				'c3'=>utf8_decode('2º Idioma'),
				'c4'=>utf8_decode('Exención'),
			);
}


if ($curso=="4ESO") {
	$opt="";
	for ($i=1;$i<4;$i++) { 
		${n_opt.$i}="";
		${n_opt.$i}.= "\n\nOptativas Modalidad Itinerario $i (".${it4.$i}[0]." -> ".${it4.$i}[1]."): ";
		$num="";
		foreach (${it4.$i} as $val) {
			$num++;
			if ($num>2) {
				$num_opt = $num-2;
				${n_opt.$i}.="$num_opt => $val, ";
			}			
		}
		${n_opt.$i} = substr(${n_opt.$i}, 0, -2);
		${n_opt.$i}.="; ";
		$opt.=${n_opt.$i};
	}
	$opt.=" ";
	$opt.= "\n\nOptativas generales de 4 ESO: ";
$num="";
	foreach ($opt4 as $val) {
		$num++;
		$opt_gen.="$num => $val, ";
	}
	$opt.=$opt_gen;
	$opt = substr($opt, 0, -2);
	$opt.=" ";

if ($datatmp['itinerario']==1) {
	$extra_itin="(".$datatmp['ciencias4'].")";
}
else{$extra_itin="";}

$opt4="";
$opt4 = iniciales($datatmp['optativas4']);

if ($datatmp['exencion']==1) {
	$exencion = 'X';
}
else{
	$exencion = '';
}
$data[] = array(
				'num'=>$nc,
				'nombre'=>utf8_decode($datatmp[0].$bil),
				'c8'=>$religion,
				'It.'=>$datatmp['itinerario'].$extra_itin,
				'c2'=>$op1,
				'c7'=>$opt4,
				'c9'=>$exencion,
				);

	$titles = array(
				'num'=>'<b>NC</b>',
				'nombre'=>'<b>Alumno</b>',
				'c8'=>'Rel. Cat.',
				'It.'=>'Itiner.',
				'c2'=>'OptGen',
				'c7'=>'OptMod',
				'c9'=>'Ex.',
			);
		}
	}


$options = array(
				'textCol' => array(0.2,0.2,0.2),
				'innerLineThickness'=>0.5,
				'outerLineThickness'=>0.7,
				'showLines'=> 2,
				'shadeCol'=>array(0.9,0.9,0.9),
				'xOrientation'=>'center',
				'width'=>500
			);

$txttit = "Lista del Grupo ".$curso."-".$grupo_actual."\n";
$txttit.= utf8_decode($config['centro_denominacion']).". Curso ".$config['curso_actual'].".\n";
	
$pdf->ezText($txttit, 13,$options_center);
$pdf->ezTable($data, $titles, '', $options);
$pdf->ezText(utf8_decode($opt), '10', $options);
$pdf->ezText("\n", 10);
$pdf->ezText("<b>Fecha:</b> ".date("d/m/Y"), 10,$options_right);
	
$pdf->ezNewPage();
}
$pdf->ezStream();
?>
