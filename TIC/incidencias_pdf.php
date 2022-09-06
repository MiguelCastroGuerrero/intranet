<?php
require('../bootstrap.php');
require('inc_variables.php');

if (file_exists('config.php')) {
	include('config.php');
}

$get_order = $_GET['order'];

switch ($get_order) {
	case 'fecha' : $order = 'ORDER BY `fecha` DESC'; break;
	case 'estado' : $order = 'ORDER BY `estado` ASC, `fecha` DESC'; break;
	default : $order = 'ORDER BY `fecha` DESC'; break;
}

require("../pdf/mc_table.php");

class GranPDF extends PDF_MC_Table {
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
		$this->MultiCell(170,5,'Consejería de Desarrollo Educativo y Formación Profesional',0,'R',0);
		$this->Ln(15);

	}
	function Footer() {
		global $config;

		$this->SetTextColor(53, 110, 59);
		$this->Image( '../img/pie.jpg', 0, 165, 24, '', 'jpg' );
	}
}

$pdf = new GranPDF('L', 'mm', 'A4');
$pdf->AddFont('Noto Sans HK Bold','','NotoSansHK-Bold.php');
$pdf->AddFont('Noto Sans HK Bold','B','NotoSansHK-Bold.php');
$pdf->AddFont('Noto Sans HK','','NotoSansHK-Regular.php');
$pdf->AddFont('Noto Sans HK','B','NotoSansHK-Bold.php');

$pdf->AddFont('NewsGotT','','NewsGotT.php');
$pdf->AddFont('NewsGotT','B','NewsGotTb.php');
$pdf->AddFont('ErasDemiBT','','ErasDemiBT.php');
$pdf->AddFont('ErasDemiBT','B','ErasDemiBT.php');
$pdf->AddFont('ErasMDBT','','ErasMDBT.php');
$pdf->AddFont('ErasMDBT','I','ErasMDBT.php');

$pdf->SetMargins(25, 20, 20);
$pdf->SetDisplayMode('fullpage');

$titulo = "Listado de incidencias TIC - Curso ".$config['curso_actual'];


$pdf->Addpage();

$pdf->SetFont('Noto Sans HK', 'B', 10);
$pdf->Multicell(0, 5, mb_strtoupper($titulo, 'UTF-8'), 0, 'C', 0 );
$pdf->Ln(5);

$pdf->SetFont('Noto Sans HK', '', 10);

$pdf->Multicell(0, 5, 'Fecha: '.date('d/m/Y'), 0, 'L', 0 );

$pdf->Ln(5);
				
if (mysqli_num_rows($result)) {

}
$pdf->SetWidths(array(25, 40, 110, 55, 20));
$pdf->SetFont('Noto Sans HK', '', 10);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFillColor(61, 61, 61);

$pdf->Row(array('Fecha', 'Dependencia', 'Incidencia', 'Solicitante', 'Estado'), 0, 6);

$result = mysqli_query($db_con, "SELECT `id`, `fecha`, `solicitante`, `dependencia`, `problema`, `descripcion`, `estado`, `numincidencia`, `resolucion` FROM `incidencias_tic` WHERE `fecha` = '".$config['curso_inicio']."' $order");

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Noto Sans HK', '', 10);

while ($row = mysqli_fetch_array($result)) {
	switch ($row['estado']) {
		case 1: $estado = 'En curso'; break;
		case 2: $estado = 'Abierta'; break;
		case 3: $estado = 'Cerrada'; break;
		case 4: $estado = 'Cancelada'; break;
	}

	$array_tipoproblema = obtener_problema_por_id_asunto($row['problema'], $tipos_incidencia);
	$tipoproblema = $array_tipoproblema['problema'];

	$pdf->Row(array($row['fecha'], $row['dependencia'], $tipoproblema.' - '.$row['descripcion'], obtener_nombre_profesor_por_idea($row['solicitante']), $estado), 1, 6);	
}

mysqli_free_result($result);


// SALIDA

$pdf->Output();
