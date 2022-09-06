<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1,7));

if (file_exists('config.php')) {
	include('config.php');
}

require("../../pdf/pdf_js.php");
//require("../pdf/mc_table.php");

class PDF_AutoPrint extends PDF_JavaScript
{
function AutoPrint($dialog=false)
{
    //Open the print dialog or start printing immediately on the standard printer
    $param=($dialog ? 'true' : 'false');
    $script="print($param);";
    $this->IncludeJS($script);
}

function AutoPrintToPrinter($server, $printer, $dialog=false)
{
    //Print on a shared printer (requires at least Acrobat 6)
    $script = "var pp = getPrintParams();";
    if($dialog)
        $script .= "pp.interactive = pp.constants.interactionLevel.full;";
    else
        $script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
    $script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
    $script .= "print(pp);";
    $this->IncludeJS($script);
}
}
define ( 'FPDF_FONTPATH', '../../pdf/font/' );
# creamos el nuevo objeto partiendo de la clase ampliada
$MiPDF = new PDF_AutoPrint();
$MiPDF->SetMargins ( 20, 20, 20 );
# ajustamos al 100% la visualizaciÃ³n
$MiPDF->SetDisplayMode ( 'fullpage' );
// Consulta  en curso. 
if (substr($curso, 0, 1) == '1') {
	$mas = ", colegio";
}

// include 'asignaturas.php';

//echo "select distinct id_matriculas from matriculas_temp, matriculas where id=id_matriculas order by curso".$mas.", letra_grupo, apellidos, nombre" ;
$result0 = mysqli_query($db_con, "select distinct id_matriculas from matriculas_temp, matriculas where id=id_matriculas order by curso".$mas.", letra_grupo, apellidos, nombre" );
while ($id_ar = mysqli_fetch_array($result0)) {
$id = "";
$id = $id_ar[0];
$result = mysqli_query($db_con, "select * from matriculas where id = '$id'");
if ($row = mysqli_fetch_array ( $result )) {
	$apellidos = "Apellidos del Alumno: ". $row['apellidos'];
	 $nombre= "Nombre: ".$row['nombre'];
	 $nacido= "Nacido en: ".$row['nacido'];
	 $nacimiento = cambia_fecha($row['nacimiento']);
	 $provincia= "Provincia de: ".$row['provincia'];
	 $fecha_nacimiento= "Fecha de Nacimiento: $nacimiento";
	 $domicilio= "Domicilio: ".$row['domicilio'];
	 $localidad= "Localidad: ".$row['localidad'];
	 $dni= "DNI del alumno: ".$row['dni'];
	 $padre= "Apellidos y nombre del Tutor legal 1: ".$row['padre'];
	 $pa = explode(", ", $row['padre']);
	 $papa = "$pa[1] $pa[0]";
	 $dnitutor= "DNI: ".$row['dnitutor'];
	 $madre= "Apellidos y nombre del Tutor legal 2: ".$row['madre'];
	 $dnitutor2= "DNI: ".$row['dnitutor2'];
	 $telefono1= "Teléfono Casa: ".$row['telefono1'];
	 $telefono2= "Teléfono Móvil: ".$row['telefono2'];
	 $telefonos="$telefono1\n   $telefono2";
	 $idioma = $row['idioma2'];
	 $religion = $row['religion'];
	 $itinerario = $row['itinerario'];
	 $optativas4 = $row['optativas4'];
	 $matematicas3 = $row['matematicas3'];
	 $ciencias4 = $row['ciencias4'];

	 if ($row['colegio'] == "Otro Centro") { $colegio= "Centro de procedencia:  ".$row['otrocolegio']; }else{	 $colegio= "Centro de procedencia:  ".$row['colegio']; }
	 $correo= "Correo electrónico de padre o madre: ".$row['correo'];

	 // Optativas y refuerzos
	 $n_curso = substr($curso, 0, 1);
	 $n_curso2 = $n_curso-1;

		for ($i=1;$i<9;$i++){
		$ni = $i-1;
		$n_o = 0;		
			${optativa.$i} = $row['optativa'.$i].". ".${opt.$n_curso}[$ni];
		} 

	for ($i=1;$i<9;$i++){
		$ni = $i-1;
		$ncr = $n_curso-1;
			${optativa2.$i} = $row['optativa2'.$i].". ".${opt.$ncr}[$ni];
	}

	 $observaciones= "OBSERVACIONES: ".$row['observaciones'];
	 $texto_exencion= "El alumno solicita la exención de la Asignatura Optativa";
	 $texto_bilinguismo= "El alumno solicita participar en el Programa de Bilinguismo";
	 $curso = $row['curso'];
	 $fecha_total = $row['fecha'];
	 $transporte = $row['transporte'];
	 $ruta_este = $row['ruta_este'];
	 $ruta_oeste = $row['ruta_oeste'];
	 $texto_transporte = "Transporte escolar: $ruta_este$ruta_oeste.";
	 $sexo = $row['sexo'];
	 if ($row['hermanos'] == '' or $row['hermanos'] == '0') { $hermanos = ""; } else{ $hermanos = $row['hermanos']; }
	 
	 $nacionalidad = $row['nacionalidad'];
	 $itinerario = $row['itinerario'];
	 $optativas4 = $row['optativas4'];
}
$fech = explode(" ",$fecha_total);
$fecha = $fech[0];
$titulo1 = "SOLICITUD DE MATRÍCULA EN ".$n_curso."º DE E.S.O.";
$an = substr($config['curso_actual'],0,4);
$an1 = $an+1;
$hoy = formatea_fecha(date('Y-m-d'));
$cuerpo3 = "En ".$config['centro_localidad'].", a $hoy
Firma del Padre/Madre/Representante legal D/Dª



Fdo. D/Dª ---------------------------------------------
que asegura la veracidad de los datos registrados en el formulario.
";
$datos_centro = "PROTECCIÓN DE DATOS.\n En cumplimiento de lo dispuesto en la Ley Orgánica 15/1999, de 13 de Diciembre, de Protección de Datos de Carácter Personal, el ".$config['centro_denominacion']." le informa que los datos personales obtenidos mediante la cumplimentación de este formulario y demás documentación que se adjunta van a ser incorporados, para su tratamiento, a nuestra base de datos, con la finalidad de recoger los datos personales y académicos del alumnado que cursa estudios en nuestro Centro, así como de las respectivas unidades familiares.\n De acuerdo con lo previsto en la Ley, puede ejercer los derechos de acceso, rectificación, cancelación y oposición dirigiendo un escrito a la Secretaría del Instituto en ".$config['centro_direccion'].", ".$config['centro_codpostal']." ".$config['centro_localidad'].", Málaga";

	# insertamos la primera pagina del documento
	$MiPDF->Addpage ();
	$MiPDF->SetFont ( 'Times', '', 10  );
	$MiPDF->SetTextColor ( 0, 0, 0 );
	$MiPDF->SetFillColor(230,230,230);
	
	
	// Formulario de matrícula

	#Cuerpo.
	$MiPDF->Image ( '../../img/encabezado2.jpg', 10, 10, 180, '', 'jpg' );
	$MiPDF->Ln ( 10 );
	$MiPDF->Multicell ( 0, 4, $titulo1, 0, 'C', 0 );
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(168,6,"DATOS PERSONALES DEL ALUMNO",1,0,'C',1);
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(112,8,$apellidos,1);

	$MiPDF->Cell(56,8,$nombre,1);
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(56,8,$nacido,1);
	$MiPDF->Cell(56,8,$provincia,1);
	$MiPDF->Cell(56,8,$fecha_nacimiento,1);
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(72,8,$domicilio,1);
	$MiPDF->Cell(40,8,$localidad,1);
	$MiPDF->Cell(56,8,$dni,1);
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(112,8,$padre,1);
	$MiPDF->Cell(56,8,$dnitutor,1);
	if (strlen($madre)>38) {
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(112,8,$madre,1);
	$MiPDF->Cell(56,8,$dnitutor2,1);
	}
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(90,8,$telefonos,1);
	$MiPDF->Cell(78,8,$colegio,1);
	if ($transporte=='1') {
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(168,8,$texto_transporte,1);	
	}
	if (strlen($correo)>38) {
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(168,8,$correo,1);	
	}
	$MiPDF->Ln ( 10 );
	if($n_curso < '3'){
	$MiPDF->Cell(168,6,"ENSEÑANZA DE RELIGIÓN O ALTERNATIVA",1,0,'L',1);
	}
	else{
	$MiPDF->Cell(168,6,"ENSEÑANZA DE RELIGIÓN O ALTERNATIVA",1,0,'L',1);
	}
	$MiPDF->Ln ( 6);
	
	if($n_curso < '3'){
	$MiPDF->Cell(168,8,$religion,0);
	}
	else{
	$MiPDF->Cell(168,8,$religion,0);
	}

	$MiPDF->Ln ( 8 );

	if($n_curso=='4'){
	$extra_it="";
	if(stristr($itinerario,"1")==TRUE){$extra_it="1 (".$ciencias4.")";}
	else{$extra_it=$itinerario." ";}
	//echo $ciencias4;
	if(strlen($optativas4)>1){$extra_it.=" - $optativas4";}	
	//if ($n_curso == '4') { $extra="4ESO (It. $itinerario".$extra_it.")";}
	
	$MiPDF->Cell(168,6,"ITINERARIO $extra_it.",1,0,'C',1);
	$MiPDF->Ln ( 6 );
		}
		else{
	$MiPDF->Cell(168,6,"ASIGNATURAS OPTATIVAS",1,0,'C',1);
	$MiPDF->Ln ( 6 );
		}
	

	// $MyPDF->FillColor();
	$MiPDF->Cell(84,8,$optativa1,0);
	$MiPDF->Cell(84,8,$optativa2,0);
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(84,8,$optativa3,0);
	$MiPDF->Cell(84,8,$optativa4,0);
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(84,8,$optativa5,0);
		if($n_curso < '4' AND $n_curso > '1'){
	$MiPDF->Cell(84,8,$optativa6,0);
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(84,8,$optativa7,0);
	$MiPDF->Cell(84,8,$optativa8,0);
	$MiPDF->Ln ( 5 );
		}


	if (substr($curso, 0, 1) == 2 or substr($curso, 0, 1) == 3 or substr($curso, 0, 1) == 4){
	$MiPDF->Ln ( 7 );
	$MiPDF->Cell(168,6,"ASIGNATURAS DE ".$n_curso2."º DE ESO",1,0,'C',1);
	$MiPDF->Ln ( 6 );
	$MiPDF->Cell(168,6,"ASIGNATURA OPTATIVA",1,0,'L',1);
	$MiPDF->Ln ( 6 );
	// $MyPDF->FillColor();
	$MiPDF->Cell(84,8,$optativa21,0);
	$MiPDF->Cell(84,8,$optativa22,0);
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(84,8,$optativa23,0);
	$MiPDF->Cell(84,8,$optativa24,0);
	$MiPDF->Ln ( 5 );
		if($n_curso>'2'){
	$MiPDF->Cell(84,8,$optativa25,0);
	$MiPDF->Cell(84,8,$optativa26,0);
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(84,8,$optativa27,0);
	$MiPDF->Ln ( 5 );
		}
	}

	else{
	$MiPDF->Ln ( 7 );		
	}
		
	if ($row[39]=='1') {
	$MiPDF->Ln ( 7 );	
	$MiPDF->Cell(168,5,$texto_exencion,1,0,'L',1);
	}
	if ($row[40]=='Si') {
		$MiPDF->Ln ( 7 );
		$MiPDF->Cell(168,5,$texto_bilinguismo,1,0,'L',1);
	}

	$MiPDF->Ln ( 8 );		
	}

   $MiPDF->AutoPrint(true);     
   $MiPDF->Output ();

?>