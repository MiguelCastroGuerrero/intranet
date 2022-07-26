<?php
require('../../bootstrap.php');


if ($_POST['pdf'] == 1) {
	
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
	
	$titulo = "Listado de alumnos con asignaturas pendientes";
	
	$grupos=substr($_POST['grupos'],0,-1);
	$tr_gr = explode(";",$grupos);
	
	foreach($tr_gr as  $valor) {
		$MiPDF->Addpage();
		
		$MiPDF->SetFont('Noto Sans HK', 'B', 10);
		$MiPDF->Multicell(0, 5, mb_strtoupper($titulo, 'UTF-8'), 0, 'C', 0 );
		$MiPDF->Ln(5);
		
		
		$MiPDF->SetFont('Noto Sans HK', '', 10);
		
		
		// INFORMACION
		$result = mysqli_query($db_con, "SELECT DISTINCT nombre, curso FROM asignaturas WHERE codigo='$valor' ORDER BY nombre ASC");
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$MiPDF->SetFont('Noto Sans HK', 'B', 10);
		$MiPDF->Cell(25, 5, 'Asignatura: ', 0, 0, 'L', 0);
		$MiPDF->SetFont('Noto Sans HK', '', 10);
		$MiPDF->Cell(100, 5, $row['nombre'].' ('.$row['curso'].')', 0, 1, 'L', 0 );
		
		$MiPDF->Ln(5);
		
		mysqli_free_result($result);
		
		
		// INFORME
		
		$MiPDF->SetWidths(array(20, 90, 30, 105));
		$MiPDF->SetFont('Noto Sans HK', 'B', 10);
		$MiPDF->SetTextColor(255, 255, 255);
		$MiPDF->SetFillColor(61, 61, 61);
		
		$MiPDF->Row(array('Unidad', 'Alumno/a', 'Asignatura', 'Observaciones'), 0, 6);	
		
		$result = mysqli_query($db_con, "SELECT DISTINCT alma.unidad, CONCAT(alma.apellidos, ', ', alma.nombre) AS alumno, alma.matriculas, asignaturas.abrev FROM pendientes, asignaturas, alma WHERE asignaturas.codigo = pendientes.codigo AND alma.claveal = pendientes.claveal AND alma.unidad NOT LIKE '%p-%' AND asignaturas.codigo = '$valor' and alma.unidad not like '1%' AND abrev LIKE  '%\_%' ORDER BY alma.curso, alma.unidad, alma.apellidos, alma.nombre");
		
		$MiPDF->SetTextColor(0, 0, 0);
		$MiPDF->SetFont('Noto Sans HK', '', 10);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		
			$observaciones = ($row['matriculas']>1) ? 'Repetidor/a' : '';
			
			$MiPDF->Row(array($row['unidad'], $row['alumno'], $row['abrev'], $observaciones), 1, 6);	
		}
		
		mysqli_free_result($result);
	}
	
	
	// SALIDA
	
	$MiPDF->Output();
	exit();
}
else{
	include "../../menu.php";

	include("../informes/pendientes/menu.php");
	
	foreach($_POST["select"] as  $val) {
		$grupos.=$val.";";
	}
	
	echo '
<div class="container">

	<div class="page-header">
	  <h2 style="display: inline;">Alumnos y Grupos <small>Lista de Alumnos con asignaturas pendientes</small></h2>';
	 echo "<form class=\"pull-right\" action='lista_pendientes.php' method='post'>";	
	 echo "<input type='hidden' name='grupos' value='".$grupos."' />";
	 echo "<input type='hidden' name='pdf' value='1' />";
	 echo "<button class='btn btn-primary' name='submit10' type='submit' formtarget='_blank'><i class='fas fa-print fa-fw'></i> Imprimir</button>";
	 echo "</form>";
	echo '	  
	</div>

	<div class="row">
	<div class="col-sm-8 col-sm-offset-2">';

foreach($_POST["select"] as  $valor) {
	$asig = mysqli_query($db_con,"select distinct nombre, curso from asignaturas where codigo = '$valor' order by nombre");
	$asignatur = mysqli_fetch_row($asig);
	$asignatura = $asignatur[0];
	$curso = $asignatur[1];
echo '<br><legend class="text-info" align="center"><strong>'.$asignatura.' ('.$curso.')</strong></legend><hr />';	
echo "<table class='table table-striped' align='center'><thead><th>Grupo</th><th>Alumno</th><th>Asignatura</th></thead><tbody>";

$sql = 'SELECT distinct alma.apellidos, alma.nombre, alma.unidad, asignaturas.nombre, asignaturas.abrev, alma.curso, alma.matriculas,  pendientes.claveal, alma.matriculas
FROM pendientes, asignaturas, alma
WHERE asignaturas.codigo = pendientes.codigo
AND alma.claveal = pendientes.claveal
AND alma.unidad NOT LIKE  "%p-%" 
AND asignaturas.codigo =  "'.$valor.'" and alma.unidad not like "1%"
AND abrev LIKE  "%\_%"
ORDER BY alma.curso, alma.unidad, alma.apellidos, alma.nombre';
		//echo $sql."<br><br>";
		$Recordset1 = mysqli_query($db_con, $sql) or die(mysqli_error($db_con));  #crea la consulata;
		while ($salida = mysqli_fetch_array($Recordset1)){
		$val_nivel=substr($pendi[5],0,1);
		$c_unidad = substr($salida[2],0,1);
		$c_curso = substr($salida[4],-2,1);
		if ($salida[8]>1) {
			$rep = "(Rep.)";
		}
		else{
			$rep='';
		}
		$n1+=1;	
		echo "<tr><td>$salida[2]</td><td nowrap><a href='//".$config['dominio']."/intranet/admin/informes/index.php?claveal=$salida[7]&todos=Ver Informe Completo del Alumno'>$salida[0], $salida[1]</a> <span class='text-warning'>$rep</span></td><td>$salida[4] </td></tr>";

		}
//}

		echo "</tbody></table>";

	}
}

?>
</div>
</div>
</div>

	<?php include("../../pie.php"); ?>
</body>
</html>
