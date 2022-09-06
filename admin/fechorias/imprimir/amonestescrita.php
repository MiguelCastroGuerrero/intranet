<?php
require('../../../bootstrap.php');

if (file_exists('../config.php')) {
	include('../config.php');
}

// Consulta  en curso.

if(!($_POST['id'])){$id = $_GET['id'];}else{$id = $_POST['id'];}
if(!($_POST['claveal'])){$claveal = $_GET['claveal'];}else{$claveal = $_POST['claveal'];}

$actualizar = "UPDATE  Fechoria SET  recibido =  '1' WHERE  Fechoria.id = '$id'";
mysqli_query($db_con, $actualizar );
$result = mysqli_query($db_con, "select alma.apellidos, alma.nombre, alma.unidad, Fechoria.fecha, Fechoria.notas, Fechoria.asunto, Fechoria.informa,
  Fechoria.grave, Fechoria.medida, listafechorias.medidas2, Fechoria.expulsion, Fechoria.tutoria, Fechoria.claveal, alma.padre, alma.domicilio, alma.localidad, alma.codpostal, alma.provinciaresidencia, tutor, Fechoria.id from Fechoria, alma, listafechorias, FTUTORES where FTUTORES.unidad = alma.unidad and  Fechoria.claveal = alma.claveal and listafechorias.fechoria = Fechoria.asunto  and Fechoria.id = '$id' order by Fechoria.fecha DESC" ) or die (mysqli_error($db_con));

if ($row = mysqli_fetch_array ( $result )) {
	$apellidos = $row[0];
	$nombre = $row[1];
	$unidad = $row[2];
	$fecha = $row[3];
	$notas = $row[4];
	$asunto = $row[5];
	$informa = $row[6];
	$grave = $row[7];
	$medida = $row[8];
	$medidas2 = $row[9];
	$expulsion = $row[10];
	$tutoria = $row[11];
	$claveal = $row[12];
	$padre = $row[13];
	$direccion = $row[14];
	$localidad = $row[15];
	$codpostal = $row[16];
	$provincia = $row[17];
	$tutor = $row[18];
}
$tr_tut = explode(", ", $tutor);
$tutor = "$tr_tut[1] $tr_tut[0]";
$fecha2 = date('Y-m-d');
$hoy = strftime("%d.%m.%Y", strtotime($fecha));

require("../../../pdf/fpdf.php");

# creamos la clase extendida de fpdf.php
class GranPDF extends FPDF {
	function SetFontSpacing($size) {
		$size = ($size / 100);
	    if($this->FontSpacingPt==$size)
	        return;
	    $this->FontSpacingPt = $size;
	    $this->FontSpacing = $size/$this->k;
	    if ($this->page>0)
	        $this->_out(sprintf('BT %.3f Tc ET', $size));
	}

	function Header() {
		global $config;

		$this->SetTextColor(48, 46, 43);
		$this->SetFontSpacing(-10);
		$this->SetY(14);
		$this->SetFont('Noto Sans HK Bold','',16);
		$this->Cell(80,5,'Junta de Andalucía',0,1);
		$this->SetY(15);
		$this->Cell(75);
		$this->SetFontSpacing(0);
		$this->SetFont('Noto Sans HK','',10);
		$this->Cell(80,5,'Consejería de Desarrollo Educativo y Formación Profesional',0,1);
		$this->Cell(75);
		$this->SetTextColor(53, 110, 59);
		$this->SetFont('Noto Sans HK','',8);
		$this->Cell(80,5,mb_strtoupper($config['centro_denominacion']),0,1);
		$this->SetTextColor(255, 255, 255);
	}
	function Footer() {
		global $config;

		$this->SetTextColor(53, 110, 59);
		$this->Image( '../../../img/pie.jpg', 0, 250, 25, '', 'jpg' );
		$this->SetY(275);
		$this->SetFont('Noto Sans HK','',7);
		$this->Cell(75);
		$this->Cell(80,4,$config['centro_direccion'].'. '.$config['centro_codpostal'].', '.$config['centro_localidad'].' ('.$config['centro_provincia'] .')',0,1);
		$this->Cell(75);
		$this->Cell(80,4,'Telf: '.$config['centro_telefono'].' '.(($config['centro_fax']) ? '   Fax: '.$config['centro_fax'] : ''),0,1);
		$this->Cell(75);
		$this->Cell(80,4,'Correo-e: '.$config['centro_email'],0,1);
		$this->SetTextColor(255, 255, 255);
	}
}


# creamos el nuevo objeto partiendo de la clase ampliada
$A4="A4";
$MiPDF = new GranPDF ( 'P', 'mm', $A4 );
$MiPDF->AddFont('Noto Sans HK Bold','','NotoSansHK-Bold.php');
$MiPDF->AddFont('Noto Sans HK Bold','B','NotoSansHK-Bold.php');
$MiPDF->AddFont('Noto Sans HK','','NotoSansHK-Regular.php');
$MiPDF->AddFont('Noto Sans HK','B','NotoSansHK-Bold.php');

$MiPDF->AddFont('NewsGotT','','NewsGotT.php');
$MiPDF->AddFont('NewsGotT','B','NewsGotTb.php');
$MiPDF->AddFont('ErasDemiBT','','ErasDemiBT.php');
$MiPDF->AddFont('ErasDemiBT','B','ErasDemiBT.php');
$MiPDF->AddFont('ErasMDBT','','ErasMDBT.php');
$MiPDF->AddFont('ErasMDBT','I','ErasMDBT.php');

$MiPDF->SetMargins (25, 20, 20);
$MiPDF->SetDisplayMode ( 'fullpage' );

$titulo = "Comunicación de amonestación escrita";
$cuerpo = "Muy Srs. nuestros:

Pongo en su conocimiento que con fecha ".strftime("%e de %B de %Y", strtotime($fecha))." su hijo/a $nombre $apellidos alumno del grupo $unidad ha sido amonestado/a por \"$asunto\"";
if (isset($config['convivencia']['mostrar_descripcion']) && $config['convivencia']['mostrar_descripcion'] && ! empty($notas)) {
	$cuerpo .= " por el siguiente motivo: ".$notas;
}
else {
	$cuerpo .= ".";
}
$cuerpo .= "

Asimismo, le comunico que, según contempla el Plan de Convivencia del Centro, regulado por el Decreto 327/2010 de 13 de Julio por el que se aprueba el Reglamento Orgánico de los Institutos de Educación Secundaria, de reincidir su hijo/a en este tipo de conductas contrarias a las normas de convivencia del Centro podría imponérsele otra medida de corrección que podría llegar a ser la suspensión del derecho de asistencia al Centro.

En ".$config['centro_localidad'].", a ".strftime("%e de %B de %Y", strtotime($fecha)).".";


for($i = 0; $i < 1; $i ++) {
	# insertamos la primera pagina del documento
	$MiPDF->Addpage ();

	// INFORMACION DE LA CARTA
	$MiPDF->SetY(45);
	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Cell(75, 5, 'Fecha:  '.$hoy, 0, 0, 'L', 0 );
	$MiPDF->Cell(75, 5, $padre, 0, 1, 'L', 0 );
	$MiPDF->Cell(75, 12, 'Ref.:     Fec/'.$row['id'], 0, 0, 'L', 0 );
	$MiPDF->Cell(75, 5, $direccion, 0, 1, 'L', 0 );
	$MiPDF->Cell(75, 0, '', 0, 0, 'L', 0 );
	$MiPDF->Cell(75, 5, $codpostal.' '.mb_strtoupper($provincia, 'UTF-8'), 0, 1, 'L', 0 );
	$MiPDF->Cell(0, 12, 'Asunto: '.$titulo, 0, 1, 'L', 0 );
	$MiPDF->Ln(10);

	// CUERPO DE LA CARTA
	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->Multicell(0, 5, mb_strtoupper($titulo, 'UTF-8'), 0, 'C', 0 );
	$MiPDF->Ln(5);

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Multicell( 0, 5, $cuerpo, 0, 'L', 0 );
	$MiPDF->Ln(10);

	//FIRMAS
	$MiPDF->Cell (90, 5, 'Representante legal', 0, 0, 'C', 0 );
	$MiPDF->Cell (55, 5, 'Tutor/a', 0, 1, 'C', 0 );
	$MiPDF->Cell (55, 20, '', 0, 0, 'C', 0 );
	$MiPDF->Cell (55, 20, '', 0, 1, 'C', 0 );
	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Cell (90, 5, 'Fdo. '.$padre, 0, 0, 'C', 0 );
	$MiPDF->Cell (55, 5, 'Fdo. '.mb_convert_case($tutor, MB_CASE_TITLE, "UTF-8"), 0, 1, 'C', 0 );


	// RECIBI
	$txt_recibi = "D./Dña. $nombre $apellidos, alumno/a del grupo $unidad, he recibido la $titulo con referencia Fec/".$row['id']." registrado el ".strftime("%e de %B de %Y", strtotime($fecha)).".";

	$MiPDF->Ln(8);

	$MiPDF->Line(20, $MiPDF->GetY(), 190, $MiPDF->GetY());
	$MiPDF->Ln(3);

	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->Multicell(0, 5, 'RECIBÍ', 0, 'C', 0 );
	$MiPDF->Ln(3);

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Multicell(0, 5, $txt_recibi, 0, 'L', 0 );
	$MiPDF->Ln(15);
	$MiPDF->Cell (55, 25, '', 0, 0, 'L', 0 );
	$MiPDF->Cell (55, 15, 'Fdo. '.$nombre.' '.$apellidos, 0, 0, 'L', 0 );

}

$MiPDF->Output();

