<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1, 2, 8));

require_once('../../pdf/class.ezpdf.php');
$pdf = new Cezpdf('a4');
$pdf->selectFont('../../pdf/fonts/Helvetica.afm');
$pdf->ezSetCmMargins(1,1,1.5,1.5);

if (isset($_POST['grupo_actual']) OR isset($_GET['grupo_actual'])) {
	$grupo_actual = $_POST['grupo_actual'];
	if(empty($grupo_actual)){ $grupo_actual[0] = $_GET['grupo_actual'];}
	$sql = "select distinct curso, grupo_actual from matriculas_bach where grupo_actual = '$grupo_actual[0]' and curso='$curso' order by curso, grupo_actual";
}
else{
	$sql = "select distinct curso, grupo_actual from matriculas_bach where grupo_actual != '' order by curso, grupo_actual";
}

$tot = mysqli_query($db_con, $sql);
while($total = mysqli_fetch_array($tot)){
# hasta aquÃ­ lo del pdf
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

		$sqldatos="SELECT concat(apellidos,', ',nombre),";
		if ($curso=="1BACH") {
		$sqldatos.="itinerario1, optativa1, idioma1, idioma2, ";
		$num_opc = 5;
		}
		else{
		$sqldatos.="itinerario2, optativa2, optativa2b1, optativa2b2, optativa2b3, optativa2b4, optativa2b5, optativa2b6, optativa2b7, optativa2b8, idioma1, opt_aut21, opt_aut22, opt_aut23, opt_aut24, opt_aut25, opt_aut26, opt_aut27, ";	
		$num_opc = 14;
		}
		$sqldatos .= "religion, bilinguismo FROM matriculas_bach WHERE curso = '$curso' and grupo_actual='".$grupo_actual[0]."' ORDER BY apellidos, nombre";
//echo $sqldatos;
$lista= mysqli_query($db_con, $sqldatos );
$nc=0;
unset($data);
while($datatmp = mysqli_fetch_array($lista)) { 
	$bil = "";
    if($datatmp['bilinguismo']=="Si"){$bil = " (Bil.)";}
	$religion = "";
	for ($i = 0; $i < $num_opc; $i++) {
		if ($datatmp[$i]=="0") {
			$datatmp[$i]="";
		}
	}

$nc+=1;

if ($curso=="2BACH") {
for ($i = 3; $i < 11; $i++) {
		if ($datatmp[$i]=="1") {
			$datatmp[$i]="X";
		}
		else{
			$datatmp[$i]="";
		}
		}

		$opt_2h="";

		for ($z = 12; $z < 20; $z++) {
		if ($datatmp[$z]=="1") {
			$opt_2h=$z-11;
		}
		}

		if (strstr($datatmp['religion'],"Cat")==TRUE) {
			$religion ="X";
		}	

	$opt="";
	$it_gen="";
	$num="";
	$opt.= "\nItinerarios de 2º Bachillerato: ";
	foreach ($it2 as $val) {
		$num++;
		$it_gen.="$num _ $val, ";
	}
	$opt.=$it_gen;
	$opt = substr($opt, 0, -2);
	$opt.=" ";	

	for ($i=1;$i<5;$i++) { 
		${n_opt.$i}="";
		${n_opt.$i}.= "\nOptativas del Itinerario $i: ";
		$num="";
		foreach (${opt2.$i} as $val) {
			$val = str_replace(" 1","",$val);
			$val = str_replace(" 2","",$val);
			$val = str_replace(" 3","",$val);
			$val = str_replace(" 4","",$val);
			$num++;
			$num_opt = $num;
			${n_opt.$i}.="$num_opt. $val, ";
		}
		${n_opt.$i} = substr(${n_opt.$i}, 0, -2);
		${n_opt.$i}.="; ";
		$opt.=${n_opt.$i};
	}
	$opt.= "\nOptativas generales de 2º Bachillerato (2 horas): ";
	$opt_gen="";
	$num="";
	foreach ($opt_aut2 as $val) {
		$num++;
		$opt_gen.="$num => $val, ";
	}
	$opt.=$opt_gen;
	$opt = substr($opt, 0, -2);
	$opt.=" ";	

	$opt = utf8_decode($opt);

	$optas = $datatmp[2];
	if (str_word_count($optas)>2) {
		$optas = iniciales($optas);
	}
	else{
		$optas = substr($optas, 0, 3);
	}	

	$data[] = array(
				'num'=>$nc,
				'nombre'=>utf8_decode($datatmp[0]).$bil,
				'c1'=>$religion,
				'c2'=>$datatmp['itinerario2'],
				'c3'=>$optas,
				'c4'=>$datatmp[3],
				'c5'=>$datatmp[4],
				'c6'=>$datatmp[5],
				'c7'=>$datatmp[6],
				'c8'=>$datatmp[7],
				'c9'=>$datatmp[8],
				'c10'=>$datatmp[9],
				'c11'=>$datatmp[10],
				'c12'=>$opt_2h,
				'c13'=>utf8_decode($datatmp['idioma1']),

				);
	$titles = array(
				'num'=>'<b>NC</b>',
				'nombre'=>'<b>Alumno</b>',
				'c1'=>'R.Cat.',
				'c2'=>'It2',
				'c3'=>'Opt2',
				'c4'=>'1',
				'c5'=>'2',
				'c6'=>'3',
				'c7'=>'4',
				'c8'=>'5',
				'c9'=>'6',
				'c10'=>'7',
				'c11'=>'8',
				'c12'=>'Opt_2H',
				'c13'=>'Idioma',
			);
}
if ($curso=="1BACH") {

		if (strstr($datatmp['religion'],"Cat")==TRUE) {
			$religion ="R. Cat.";
		}
		elseif (strstr($datatmp['religion'],"Isl")==TRUE) {
			$religion ="R. Isl.";
		}
		elseif (strstr($datatmp['religion'],"Eva")==TRUE) {
			$religion ="R. Evan.";
		}
		elseif (strstr($datatmp['religion'],"Valo")==TRUE) {
			$religion ="E. Ciud.";
		}
		

	$opt="";
	$it_gen="";
	$num="";

	$opt.= "\nItinerarios de 1º Bachillerato: ";
	foreach ($it1 as $val) {
		$num++;
		$it_gen.="$num => $val, ";
	}
	$opt.=$it_gen;
	$opt = substr($opt, 0, -2);
	$opt.=". \n";	

	for ($i=1;$i<5;$i++) { 
		${n_opt.$i}="";
		${n_opt.$i}.= "\nOptativas del Itinerario $i: ";
		$num="";
		foreach (${opt1.$i} as $key=>$val) {
			$val = str_replace(" 1","",$val);
			$val = str_replace(" 2","",$val);
			$val = str_replace(" 3","",$val);
			$val = str_replace(" 4","",$val);
			$num++;
			${n_opt.$i}.="$key => $val, ";
		}
		${n_opt.$i} = substr(${n_opt.$i}, 0, -2);
		${n_opt.$i}.="; ";
		$opt.=${n_opt.$i};
	}

	$opt = utf8_decode($opt);

	$optas = $datatmp[2];
	$optas = str_replace("1","",$optas);
	$optas = str_replace("2","",$optas);
	$optas = str_replace("3","",$optas);
	$optas = str_replace("4","",$optas);
	
	$data[] = array(
				'num'=>$nc,
				'nombre'=>utf8_decode($datatmp[0]).$bil,
				'c1'=>$religion,
				'c2'=>$datatmp[1],
				'c3'=>$optas,
				'c4'=>utf8_decode($datatmp[3]),
				'c5'=>utf8_decode($datatmp[4]),
				);
	$titles = array(
				'num'=>'<b>NC</b>',
				'nombre'=>'<b>Alumno</b>',
				'c1'=>utf8_decode('Religión'),
				'c2'=>'It1',
				'c3'=>'Opt1',
				'c4'=>'Idioma1',
				'c5'=>'Idioma2',
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
$pdf->ezText($opt, '10', $options);

$pdf->ezNewPage();
}
$pdf->ezStream();
?>