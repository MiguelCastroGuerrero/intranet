<?php
require('../../bootstrap.php');
require("../../pdf/fpdf.php");
define('FPDF_FONTPATH', '../../pdf/font/');

acl_acceso($_SESSION['cargo'], array(1, 'c'));


# creamos la clase extendida de fpdf.php
class GranPDF extends FPDF {
	function Header() {
		global $config;

		$this->SetTextColor(0, 122, 61);
		$this->Image( '../../img/encabezado.jpg',25,14,53,'','jpg');
		$this->SetFont('ErasDemiBT','B',10);
		$this->SetY(15);
		$this->Cell(75);
		$this->Cell(80,5,'Consejería de Desarrollo Educativo y Formación Profesional',0,1);
		$this->SetFont('ErasMDBT','I',10);
		$this->Cell(75);
		$this->Cell(80,5,$config['centro_denominacion'],0,1);
		$this->SetTextColor(255, 255, 255);
	}
	function Footer() {
		global $config;
		
		$this->SetTextColor(0, 122, 61);
		$this->Image( '../../img/pie.jpg', 0, 245, 25, '', 'jpg' );
		$this->SetY(275);
		$this->SetFont('ErasMDBT','',8);
		$this->Cell(75);
		$this->Cell(80,4,$config['centro_direccion'].'. '.$config['centro_codpostal'].', '.$config['centro_localidad'].' ('.$config['centro_provincia'] .')',0,1);
		$this->Cell(75);
		$this->Cell(80,4,'Telf: '.$config['centro_telefono'].' '.(($config['centro_fax']) ? '   Fax: '.$config['centro_fax'] : ''),0,1);
		$this->Cell(75);
		$this->Cell(80,4,'Correo-e: '.$config['centro_email'],0,1);
		$this->SetTextColor(255, 255, 255);
	}
}

# creamos el nuevo objeto partiendo de la clase
$MiPDF=new GranPDF('P','mm','A4');
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
# creamos el nuevo objeto partiendo de la clase ampliada
$MiPDF->SetMargins ( 25, 20, 20 );
# ajustamos al 100% la visualizaciÃ³n
$MiPDF->SetDisplayMode ( 'fullpage' );
$hoy= date ('d-m-Y',time());
$tutor="Jefatura de Estudios";
$titulo1 = "COMUNICACIÓN DE AMONESTACIÓN ESCRITA";

if(isset($_POST['impreso'])){

	$impreso=$_POST['impreso'];
	$hola=$_POST['hola'];

	$j=0;
	foreach ($_POST as $ide => $valor) {
		if(($ide != 'impreso') and (!empty( $valor))){
		
			for($i=0; $i <= count($valor)-1; $i++){
			$j+=1; //echo $valor[$i];
			$al=mysqli_query($db_con, "select apellidos,nombre,curso from morosos where id='$valor[$i]'") or die ("error al localizar alumno");
			//echo "select apellidos,nombre,curso from morosos where id='$valor[$i]'";
			while($alu=mysqli_fetch_array($al)){

				$nombre=$alu[1];
				$apellido=$alu[0];
				$curso=$alu[2];
				// echo $nombre.'-'.$apellido;



				// aquí generamos el pdf con todas las amonestaciones
				$nombre=$nombre;
				$apellido=$apellido;

				$cuerpo1 = "Muy Señor/Sra. mío/a:

Pongo en su conocimiento que con  fecha $hoy su hijo/a $nombre $apellido alumno del grupo $curso ha sido amonestado/a por \"Retraso injustificado en la devolución de material a la Biblioteca del Centro\"";
				$cuerpo2 = "Asimismo, le comunico que, según contempla el Plan de Convivencia del Centro, regulado por el Decreto 327/2010 de 13 de Julio por el que se aprueba el Reglamento Orgánico de los Institutos de Educación Secundaria, de reincidir su hijo/a en este tipo de conductas contrarias a las normas de convivencia del Centro podría imponérsele otra medida de corrección que podría llegar a ser la suspensión del derecho de asistencia al Centro.";
				$cuerpo3 = "----------------------------------------------------------------------------------------------------------------------------------------------

En ".$config['centro_localidad'].", a _________________________________
Firmado: El Padre/Madre/Representante legal:



D./Dña _____________________________________________________________________
D.N.I ___________________________";
				$cuerpo4 = "
----------------------------------------------------------------------------------------------------------------------------------------------

COMUNICACIÓN DE AMONESTACIÓN ESCRITA

	El alumno/a $nombre $apellido del grupo $curso, ha sido amonestado/a con fecha $hoy con falta grave, recibiendo la notificación mediante comunicación escrita de la misma para entregarla al padre/madre/representante legal.

                                           Firma del alumno/a:

";

				# insertamos la primera pagina del documento
				$MiPDF->Addpage ();
				#### Cabecera con dirección
				$MiPDF->SetFont('Noto Sans HK', '', 10);
				$MiPDF->SetTextColor ( 0, 0, 0 );
				$MiPDF->SetTextColor ( 0, 0, 0 );
				$MiPDF->Text ( 128, 35, $config['centro_denominacion'] );
				$MiPDF->Text ( 128, 39, $config['centro_direccion'] );
				$MiPDF->Text ( 128, 43, $config['centro_codpostal'] . " (" . $config['centro_localidad'] . ")" );
				$MiPDF->Text ( 128, 47, "Tlfno. " . $config['centro_telefono']);
				#Cuerpo.
				$MiPDF->Ln ( 45 );
				$MiPDF->SetFont('Noto Sans HK', 'B', 10);
				$MiPDF->Multicell ( 0, 4, $titulo1, 0, 'C', 0 );
				$MiPDF->SetFont('Noto Sans HK', '', 10);
				$MiPDF->Ln ( 4 );
				$MiPDF->Multicell ( 0, 4, $cuerpo1, 0, 'J', 0 );
				$MiPDF->Ln ( 3 );
				$MiPDF->Multicell ( 0, 4, $cuerpo2, 0, 'J', 0 );
				$MiPDF->Ln ( 6 );
				$MiPDF->Multicell ( 0, 4, 'En ' . $config['centro_localidad'] . ', a ' . $hoy, 0, 'C', 0 );
				$MiPDF->Ln ( 20 );
				$MiPDF->Multicell ( 0, 4, $tutor, 0, 'C', 0 );
				$MiPDF->Ln ( 5 );
				$MiPDF->Multicell ( 0, 4, $cuerpo3, 0, 'J', 0 );
				$MiPDF->Ln ( 5 );
				$MiPDF->Multicell ( 0, 4, $cuerpo4, 0, 'J', 0 );
			}
			}
			$MiPDF->Output ();
	 }
	}
}
