<?php
require('../../bootstrap.php');

if(isset($_GET['id']) && !empty($_GET['id'])) {
	$id = $_GET['id'];
}
else {
	header('Location:'.'index.php');
}

require("../../pdf/mc_table.php");

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
		$this->Image( '../../img/pie.jpg', 0, 165, 24, '', 'jpg' );
	}
}

$MiPDF = new GranPDF('L', 'mm', 'A4');
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

$MiPDF->SetMargins(25, 20, 20);
$MiPDF->SetDisplayMode('fullpage');

$titulo = "Informe de tareas por ausencia del alumno";
$cuerpo = "";


$MiPDF->Addpage();

$MiPDF->SetFont('Noto Sans HK', 'B', 10);
$MiPDF->Multicell(0, 5, mb_strtoupper($titulo, 'UTF-8'), 0, 'C', 0 );
$MiPDF->Ln(5);


$MiPDF->SetFont('Noto Sans HK', '', 10);


// INFORMACION DEL ALUMNO
$result = mysqli_query($db_con, "SELECT apellidos, nombre, tareas_alumnos.unidad, tareas_alumnos.id, tutor, fecha, duracion, claveal FROM tareas_alumnos, FTUTORES WHERE FTUTORES.unidad = tareas_alumnos.unidad and tareas_alumnos.id='$id'");

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$MiPDF->SetFont('Noto Sans HK', 'B', 10);
$MiPDF->Cell(25, 5, 'Alumno/a: ', 0, 0, 'L', 0);
$MiPDF->SetFont('Noto Sans HK', '', 10);
$MiPDF->Cell(80, 5, $row['apellidos'].', '.$row['nombre'], 0, 0, 'L', 0 );

$MiPDF->SetFont('Noto Sans HK', 'B', 10);
$MiPDF->Cell(40, 5, 'Fecha de la ausencia: ', 0, 0, 'L', 0);
$MiPDF->SetFont('Noto Sans HK', '', 10);
$MiPDF->Cell(55, 5, strftime('%e de %B de %Y',strtotime($row['fecha'])), 0, 0, 'L', 0 );

$MiPDF->SetFont('Noto Sans HK', 'B', 10);
$MiPDF->Cell(15, 5, 'Días: ', 0, 0, 'L', 0);
$MiPDF->SetFont('Noto Sans HK', '', 10);
$MiPDF->Cell(15, 5, $row['duracion'], 0, 1, 'L', 0 );

$MiPDF->Ln(2);

$MiPDF->SetFont('Noto Sans HK', 'B', 10);
$MiPDF->Cell(20, 5, 'Unidad: ', 0, 0, 'L', 0);
$MiPDF->SetFont('Noto Sans HK', '', 10);
$MiPDF->Cell(85, 5, $row['unidad'], 0, 0, 'L', 0 );

$MiPDF->SetFont('Noto Sans HK', 'B', 10);
$MiPDF->Cell(20, 5, 'Tutor/a: ', 0, 0, 'L', 0);
$MiPDF->SetFont('Noto Sans HK', '', 10);
$MiPDF->Cell(40, 5, mb_convert_case($row['tutor'], MB_CASE_TITLE, "UTF-8"), 0, 1, 'L', 0 );

$MiPDF->Ln(5);

mysqli_free_result($result);


// TAREAS

$MiPDF->SetWidths(array(70, 65, 120));
$MiPDF->SetFont('Noto Sans HK', 'B', 10);
$MiPDF->SetTextColor(255, 255, 255);
$MiPDF->SetFillColor(61, 61, 61);

$MiPDF->Row(array('Asignatura / Materia', 'Profesor/a', 'Tarea'), 0, 5.5);	

$result = mysqli_query($db_con, "SELECT asignatura, tarea, confirmado, profesor FROM tareas_profesor WHERE id_alumno='$id'");

$MiPDF->SetTextColor(0, 0, 0);
$MiPDF->SetFont('Noto Sans HK', '', 10);

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$MiPDF->Row(array($row['asignatura'], $row['profesor'], $row['tarea']), 1, 5.5);	
}

mysqli_free_result($result);


// SALIDA

$MiPDF->Output();

