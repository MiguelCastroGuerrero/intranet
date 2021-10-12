<?php
ini_set("memory_limit","1024M");
require('../../../bootstrap.php');
require_once("../../../pdf/dompdf_config.inc.php");

define("DOMPDF_ENABLE_PHP", true);

	$claveal = mysqli_real_escape_string($db_con, $_GET['claveal']);


	$html="<html><head>
	<meta charset='UTF-8'>";
	$html.="<style>
		html {
		 font-family: sans-serif;
		 font-size:16px;
		 -webkit-text-size-adjust: 100%;
		 -ms-text-size-adjust: 100%;
		}
	  	.table {
	    border-collapse: collapse !important;
	  	width: auto;
	  	}
	  	.table td
	  	{
	    background-color: #fff !important;
	    padding:15px 12px;
	    vertical-align:top;
	  	}
	  	.table th {
	    background-color: #ccc !important;
	    padding:8px -3px;
	    margin:0px;
	  	}
	  	.table-bordered td, th {
	    border: 1px solid #999 !important;
	  	}
	  	hr {
	  	border: 1px solid #ddd !important;
	  	}
	</style>";
	
	$html .= "
	<body>";

	$html.="<h2 align='center'><u>Informe de evaluación de alumnos con materias pendientes</u></h2>";

	$alum = mysqli_fetch_array(mysqli_query($db_con,"select concat(nombre,' ',apellidos) as nombre_al, curso, unidad from alma where claveal='".$claveal."'"));

	$html.="<br><h2 align='center'>".$alum['nombre_al']." <small>(".$alum['unidad'].")</small></h2><br>";


	$asigna_pend = mysqli_query($db_con,"select distinct codigo from pendientes where claveal='$claveal'");
	while($nom_asig = mysqli_fetch_array($asigna_pend)){
			
		$asignat = mysqli_fetch_array(mysqli_query($db_con,"select distinct nombre, curso from asignaturas where codigo='$nom_asig[0]' and abrev not like '%\_%' limit 1"));
			$asignatura_pend = $asignat['nombre'];
			$curso_pend = $asignat['curso'];
							
		$n_inf = mysqli_query($db_con,"select id_informe from informe_pendientes where asignatura like '$asignatura_pend' and curso like '$curso_pend'");
		while($id_inf = mysqli_fetch_array($n_inf)){
			$extra_id.=" id_informe = '".$id_inf['id_informe']."' OR";
		}
	}
	$extra_id = substr($extra_id,0,-3);

	$materias = mysqli_query($db_con, "SELECT distinct informe_pendientes.asignatura, informe_pendientes.id_informe, informe_pendientes.observaciones, informe_pendientes.curso FROM informe_pendientes WHERE $extra_id");
	$num_informes = mysqli_num_rows($materias);
	while($materia_curso = mysqli_fetch_array($materias)){

		$n_inf++;

		$id_informe = $materia_curso['id_informe'];
		$curso_pend = $materia_curso['curso'];
		$observaciones = $materia_curso['observaciones'];

		$html.="<br><h3>".$materia_curso['asignatura']."<small> (".$curso_pend.")</small></h3><br>";
		
		$html.="
			<table class='table table-bordered'>
			<thead>
			<tr>
			<th>Contenidos</th><th>Actividades</th>
			</tr>
			</thead>
			<tbody>
			";
		
		$content = mysqli_query($db_con,"select unidad, titulo, contenidos, actividades from informe_pendientes_contenidos where id_informe = ".$id_informe." order by id_contenido");
			
		while($datos = mysqli_fetch_array($content)){
			$html.="<tr><td><u><b>".$datos['unidad']."</b>: ".$datos['titulo']."</u><br><br>".$datos['contenidos']."</td><td>".$datos['actividades']."</td></tr>";
			}
		
		
	$html.="</tbody>
	</table>";

	$html.="
	<table class='table table-bordered' style='width:100%'>
	<thead>
	<tr>
	<th>Observaciones generales</th>
	</tr>
	</thead>
	<tbody>
	";
	$html.="<tr><td>".$observaciones."</td></tr>";

	$html.="</tbody>
	</table>";
	
	$html .= '<div style="page-break-before: always !important;"></div>';

	}

	$html .= '</body></html>';
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->render();
	
	$dompdf->stream("Informe de ".$alum['nombre_al'].".pdf", array("Attachment" => 0));

	
?>