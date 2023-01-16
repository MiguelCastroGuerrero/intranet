<?php
ini_set("memory_limit","1024M");
require('../../../bootstrap.php');
require_once("../../../pdf/dompdf_config.inc.php");

define("DOMPDF_ENABLE_PHP", true);

if (isset($_GET['id'])) {
	$id = mysqli_real_escape_string($db_con, $_GET['id']);
	
	$result = mysqli_query($db_con, "SELECT apellidos, nombre, refuerzo.unidad, materia, texto, refuerzo.fecha, departamento FROM refuerzo, alma WHERE alma.claveal= refuerzo.alumno and id = '$id'");
}

if ($result) {
	$html .= '<html><body>';
	
	$html .= '
	<style type="text/css">
	html {
	  margin: 0 !important;
	}
	body {
	  font-family: Arial, Helvetica, sans-serif !important;
	  font-size: 11pt !important;
	  margin: 20mm 20mm 30mm 25mm !important;
	}
	</style>';
	
	$sonVarias = 0;
	while ($row = mysqli_fetch_array($result)) {
		
		$apellidos = $row['apellidos'];
		$nombre = $row['nombre'];
		$materia = $row['materia'];

		$sonVarias++;
		
		if ($sonVarias > 2) {
			$html .= '<div style="page-break-before: always;"></div>';
		}
		
		$html .= mb_convert_encoding($row['texto'], 'UTF-8', 'UTF-8');
	}
	
	$html .= '</body></html>';
	
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->render();
	if ($sonVarias > 2) {
		$dompdf->stream("Informe PRA de $nombre $apellidos ($materia).pdf", array("Attachment" => 0));
	}
	else {
		$departamento = $row['departamento'];
		$fecha = $row['fecha'];
		$dompdf->stream("Actas informes PRA $departamento - $fecha.pdf", array("Attachment" => 0));
	}
}
?>