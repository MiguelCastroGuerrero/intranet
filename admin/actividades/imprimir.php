<?php
require('../../bootstrap.php');

if (file_exists("config.php")) {
  include("config.php");
}

$tutor = $_SESSION['profi'];

if (isset($_GET['id'])) {$id = limpiarInput($_GET['id'], 'numeric');}elseif (isset($_POST['id'])) {$id = limpiarInput($_POST['id'], 'numeric');}else{$id="";}
if (isset($_GET['eliminar'])) {$id = limpiarInput($_GET['eliminar'], 'numeric');}elseif (isset($_POST['eliminar'])) {$eliminar = limpiarInput($_POST['eliminar'], 'numeric');}else{$eliminar="";}
if (isset($_GET['enviar'])) {$enviar = $_GET['enviar'];}elseif (isset($_POST['enviar'])) {$enviar = $_POST['enviar'];}else{$enviar="";}
if (isset($_GET['crear'])) {$crear = $_GET['crear'];}elseif (isset($_POST['crear'])) {$crear = $_POST['crear'];}else{$crear="";}
if (isset($_GET['buscar'])) {$buscar = limpiarInput($_GET['buscar'], 'alphanumericspecial');}elseif (isset($_POST['buscar'])) {$buscar = limpiarInput($_POST['buscar'], 'alphanumericspecial');}else{$buscar="";}

if (isset($_GET['calendario'])) {$calendario = limpiarInput($_GET['calendario'], 'alphanumericspecial');}elseif (isset($_POST['calendario'])) {$calendario = limpiarInput($_POST['calendario'], 'alphanumericspecial');}else{$calendario="";}
if (isset($_GET['act_calendario'])) {$act_calendario = limpiarInput($_GET['act_calendario'], 'alphanumericspecial');}elseif (isset($_POST['act_calendario'])) {$act_calendario = limpiarInput($_POST['act_calendario'], 'alphanumericspecial');}else{$act_calendario="";}
if (isset($_GET['confirmado'])) {$confirmado = limpiarInput($_GET['confirmado'], 'alphanumericspecial');}elseif (isset($_POST['confirmado'])) {$confirmado = limpiarInput($_POST['confirmado'], 'alphanumericspecial');}else{$confirmado="";}
if (isset($_GET['detalles'])) {$detalles = limpiarInput($_GET['detalles'], 'alphanumericspecial');}elseif (isset($_POST['detalles'])) {$detalles = limpiarInput($_POST['detalles'], 'alphanumericspecial');}else{$detalles="";}
if (isset($_GET['lugar'])) {$lugar = limpiarInput($_GET['lugar'], 'alphanumericspecial');}elseif (isset($_POST['lugar'])) {$lugar = limpiarInput($_POST['lugar'], 'alphanumericspecial');}else{$lugar="";}
if (isset($_GET['fecha'])) {$fecha = limpiarInput($_GET['fecha'], 'alphanumericspecial');}elseif (isset($_POST['fecha'])) {$fecha = limpiarInput($_POST['fecha'], 'alphanumericspecial');}else{$fecha="";}
if (isset($_GET['fechafin'])) {$fechafin = limpiarInput($_GET['fechafin'], 'alphanumericspecial');}elseif (isset($_POST['fechafin'])) {$fechafin = limpiarInput($_POST['fechafin'], 'alphanumericspecial');}else{$fechafin="";}
if (isset($_GET['horario'])) {$horario = limpiarInput($_GET['horario'], 'alphanumericspecial');}elseif (isset($_POST['horario'])) {$horario = limpiarInput($_POST['horario'], 'alphanumericspecial');}else{$horario="";}
if (isset($_GET['horariofin'])) {$horariofin = limpiarInput($_GET['horariofin'], 'alphanumericspecial');}elseif (isset($_POST['horariofin'])) {$horariofin = limpiarInput($_POST['horariofin'], 'alphanumericspecial');}else{$horariofin="";}
if (isset($_GET['profesor'])) {$profesor = limpiarInput($_GET['profesor'], 'alphanumericspecial');}elseif (isset($_POST['profesor'])) {$profesor = limpiarInput($_POST['profesor'], 'alphanumericspecial');}else{$profesor="";}
if (isset($_GET['actividad'])) {$actividad = limpiarInput($_GET['actividad'], 'alphanumericspecial');}elseif (isset($_POST['actividad'])) {$actividad = limpiarInput($_POST['actividad'], 'alphanumericspecial');}else{$actividad="";}
if (isset($_GET['descripcion'])) {$descripcion = limpiarInput($_GET['descripcion'], 'alphanumericspecial');}elseif (isset($_POST['descripcion'])) {$descripcion = limpiarInput($_POST['descripcion'], 'alphanumericspecial');}else{$descripcion="";}
if (isset($_GET['observaciones'])) {$observaciones = limpiarInput($_GET['observaciones'], 'alphanumericspecial');}elseif (isset($_POST['observaciones'])) {$observaciones = limpiarInput($_POST['observaciones'], 'alphanumericspecial');}else{$observaciones="";}

if($fecha == $fechafin){
$fecha_act = $fecha;
}
else{
$fecha_act = $fecha.' - '.$fechafin;
}

$horario = substr($horario,0,-3);
$horariofin = substr($horariofin,0,-3);
if($horario == $horariofin){
$horario_act = $horario;
}
else{
$horario_act = $horario.' - '.$horariofin;
}

// PDF
$fecha2 = date('Y-m-d');
$hoy = formatea_fecha($fecha);

include("../../pdf/fpdf.php");
define('FPDF_FONTPATH','../../pdf/font/');

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
		$this->Cell(80,5,'Consejería de Educación y Deporte',0,1);
		$this->Cell(75);
		$this->SetTextColor(53, 110, 59);
		$this->SetFont('Noto Sans HK','',7);
		$this->Cell(80,5,mb_strtoupper($config['centro_denominacion']),0,1);
		$this->SetTextColor(255, 255, 255);
	}
	function Footer() {
		global $config;

		$this->SetTextColor(53, 110, 59);
		$this->Image( '../../img/pie.jpg', 0, 250, 25, '', 'jpg' );
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


$MiPDF->SetMargins(25,20,20);
$MiPDF->SetDisplayMode('fullpage');


  $fecha1 = explode("-",$fecha);
  $dia = $fecha[0];
  $mes = $fecha[1];
  $ano = $fecha[2];

// Borramos registros anteriores
 mysqli_query($db_con,"delete from actividadalumno where cod_actividad='$id'");

  foreach($_POST as $key => $value)
  {
//  echo "$key --> $value<br>";
if(is_numeric(trim($key))){

$alumnos0 = "SELECT nombre, apellidos, padre, domicilio, codpostal, localidad, provinciaresidencia, dnitutor, unidad, matriculas, telefono, telefonourgencia FROM alma WHERE claveal = '$value'";
$alumnos1 = mysqli_query($db_con, $alumnos0);
while($alumno = mysqli_fetch_array($alumnos1))
{
	mysqli_query($db_con, "INSERT INTO actividadalumno (claveal,cod_actividad) VALUES ('".$value."','".$id."')");
	# insertamos la primera pagina del documento
	$MiPDF->Addpage();

	$autorizacion = "D./Dña. ".$alumno['padre']." con D.N.I ".$alumno['dnitutor'].", como representante legal de ".$alumno['nombre']." ".$alumno['apellidos'].", alumno/a de la unidad ".$alumno['unidad'].", asume la responsabilidad de que su hijo/a participe en la siguiente Actividad Complementaria y Extraescolar e igualmente autoriza a los profesores/as responsables a tomar cuantas medidas sean necesarias para conseguir un desarrollo adecuado de la actividad programada. El abajo firmante también autoriza de forma expresa e inequívoca la difusión de imágenes/vídeos en la web y redes sociales del Centro con fines didácticos y/o publicitarios.";

	$alergias = "Al mismo tiempo indico que mi hijo/a:
	__ Necesita tratamiento médico o medicación específica.
	__ Es alérgico a algún tipo de comidas.

	Observaciones:
	";

	// INFORMACION DE LA CARTA
	$MiPDF->SetY(45);
	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Cell(75, 5, 'Fecha:  '.date('d.m.Y'), 0, 0, 'L', 0 );
	$MiPDF->Cell(75, 5, $alumno['padre'], 0, 1, 'L', 0 );
	$MiPDF->Cell(75, 12, 'Ref.:     Act/'.$id, 0, 0, 'L', 0);
	$MiPDF->Cell(75, 5, $alumno['domicilio'], 0, 1, 'L', 0 );
	$MiPDF->Cell(75, 0, '', 0, 0, 'L', 0);
	$MiPDF->Cell(75, 5, $alumno['codpostal'].' '.$alumno['localidad'].', '.mb_strtoupper($alumno['provinciaresidencia'], 'UTF-8'), 0, 1, 'L', 0 );
	$MiPDF->Cell(0, 12, 'Asunto: '.$actividad, 0, 1, 'L', 0);
	$MiPDF->Ln(3);


	$MiPDF->Multicell(0, 5, $autorizacion, 0, 'L', 0);
	$MiPDF->Ln(3);

	#Cuerpo.
	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->Cell(30, 5, 'Lugar: ', 0, 0, 'L');

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Cell(130, 5, $lugar, 0, 1, 'L');

	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->Cell(30, 5, 'Fecha: ', 0, 0, 'L');

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Cell(130, 5, $fecha_act, 0, 1, 'L');

	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->Cell(30, 5, 'Horario: ', 0, 0, 'L');

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Cell(130, 5, $horario_act, 0, 1, 'L');

	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->Cell(30, 5, 'Actividad: ', 0, 0, 'L');

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Cell(130, 5, $actividad, 0, 1, 'L');

	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->Cell(30, 5, 'Descripción: ', 0, 0, 'L');

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->SetY($MiPDF->GetY()+1);
	$MiPDF->SetX($MiPDF->GetX()+30);
	$MiPDF->MultiCell(130, 5, $descripcion, 0, 'L' , 0);

	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->Cell(30, 8, 'Observaciones: ', 0, 0, 'L');

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->SetY($MiPDF->GetY()+1);
	$MiPDF->SetX($MiPDF->GetX()+30);
	$MiPDF->MultiCell(130, 5, $observaciones, 0, 'L' , 0);

	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->Cell(30, 8, 'Profesor/es: ', 0, 0, 'L');

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->SetY($MiPDF->GetY()+1.5);
	$MiPDF->SetX($MiPDF->GetX()+30);
	$MiPDF->MultiCell(130, 5, $profesor, 0, 'L' , 0);
	$MiPDF->Ln(3);

	if ($_SERVER['SERVER_NAME'] == 'institutomediterraneo.es') {
		$MiPDF->SetFont('Noto Sans HK', 'B', 8);
		$MiPDF->MultiCell(0, 4, 'NOTA: El alumno/a debe llevar una copia de la tarjeta sanitaria y una copia del pasaporte COVID, si lo tienen.', 0, 'L', 0);
		$MiPDF->Ln(3);
	}

	// EJEMPLAR PARA EL PROFESOR
	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$txt_acuse = "D./Dña. ".$alumno['padre']." con D.N.I ".$alumno['dnitutor'].", como representante legal de ".$alumno['nombre']." ".$alumno['apellidos'].", alumno/a de la unidad ".$alumno['unidad'].", autoriza a su hijo/a a participar en la actividad $actividad con referencia Act/".$id.".";

	$MiPDF->Line(20, $MiPDF->GetY(), 190, $MiPDF->GetY());
	$MiPDF->Ln(3);

	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->Multicell(0, 5, 'RECORTE POR LA LÍNEA Y ENTREGUE ESTE DOCUMENTO AL PROFESOR RESPONSABLE', 0, 'L', 0 );
	$MiPDF->Ln(3);

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Multicell(0, 5, $txt_acuse, 0, 'L', 0 );
	$MiPDF->Ln(3);

	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->Cell(65, 8, 'Teléfonos de contacto con la familia:', 0, 0, 'L');
	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Cell(100, 8, $alumno['telefono'] . ' / ' . $alumno['telefonourgencia'], 0, 1, 'L');

	$MiPDF->SetFont('Noto Sans HK', 'B', 10);
	$MiPDF->MultiCell(0, 8, 'Información médica (marque con una X):', 0, 'L', 0);

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Cell(10, 8, '____', 0, 0, 'L');
	$MiPDF->Cell(150, 8, ' Necesita tratamiento médico o medicación específica.', 0, 1, 'L');

	$MiPDF->Cell(10, 8, '____', 0, 0, 'L');
	$MiPDF->Cell(150, 8, ' Es alérgico a algún tipo de comida.', 0, 1, 'L');

	$MiPDF->SetFont('Noto Sans HK', '', 8);
	$MiPDF->MultiCell(0, 4, 'En el caso de haber marcado alguna opción, anote en el reverso del documento las circunstancias que concurren.', 0, 'L', 0);
	$MiPDF->MultiCell(0, 4, 'Si fuese necesario por presentar síntomas, autoriza a que el profesor/a acompañe a su hijo/a a un Centro de Salud para ser sometido a una prueba de diagnóstico.', 0, 'L', 0);

	$MiPDF->Ln(3);

	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Cell (90, 5, '', 0, 0, 'C', 0 );
	$MiPDF->Cell (55, 5, 'Representante legal', 0, 1, 'C', 0 );
	$MiPDF->Cell (55, 20, '', 0, 0, 'C', 0 );
	$MiPDF->Cell (55, 20, '', 0, 1, 'C', 0 );
	$MiPDF->SetFont('Noto Sans HK', '', 10);
	$MiPDF->Cell (90, 5, '', 0, 0, 'C', 0 );
	$MiPDF->Cell (55, 0, 'Fdo. '.$alumno['padre'], 0, 1, 'C', 0 );



	}
}
}
$MiPDF->Output();

