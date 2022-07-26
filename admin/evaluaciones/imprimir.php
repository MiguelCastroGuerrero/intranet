<?php
require('../../bootstrap.php');
require('inc_evaluaciones.php');

function obtener_nombre_profesor_desde_idea($db_con, $idea) {
	$result = mysqli_query($db_con, "SELECT profesor FROM c_profes WHERE idea = '$idea' LIMIT 1") or die (mysqli_error($db_con));
	$row = mysqli_fetch_array($result);
	$nomprofesor = $row['profesor'];
	return $nomprofesor;
}

if (file_exists('config.php')) {
	include('config.php');
}

if (isset($_GET['id'])) $id = $_GET['id'];

if (!$id) {
	die ("<h1>FORBIDDEN</h1>");
}

require_once("../../pdf/dompdf_config.inc.php"); 

// OBTENEMOS LOS DATOS DEL ACTA
$result = mysqli_query($db_con, "SELECT unidad, evaluacion, fecha, texto_acta, asistentes FROM evaluaciones_actas WHERE id = ".$id);

if (mysqli_num_rows($result)) {
	$row = mysqli_fetch_array($result);

	// OBTENEMOS EL TUTOR DEL GRUPO
	$result_tutor = mysqli_query($db_con, "SELECT tutor FROM FTUTORES WHERE unidad = '".$row['unidad']."' LIMIT 1");
	$row_tutor = mysqli_fetch_array($result_tutor);
	$tutor = nomprofesor($row_tutor['tutor']);

	// SOLO EQUIPO DIRECTIVO O EL TUTOR DE LA UNIDAD PUEDE BLOQUEAR LA EDICIÓN
	if (stristr($_SESSION['cargo'],'1') == true || (stristr($_SESSION['cargo'],'2') == true && $tutor == $pr)) {
		// BLOQUEAMOS LA EDICIÓN
		mysqli_query($db_con, "UPDATE evaluaciones_actas SET impresion = 1 WHERE id = ".$id);
	}

	// COMPROBAMOS SI ES UN PMAR
	$esPMAR = (stristr($row['unidad'], ' (PMAR)') == true) ? 1 : 0;
	if ($esPMAR) {
		$row['unidad'] = str_ireplace(' (PMAR)', '', $row['unidad']);
	}

	// OBTENEMOS EL NIVEL EDUCATIVO DE LA UNIDAD
	$result_curso = mysqli_query($db_con, "SELECT cursos.nomcurso FROM unidades JOIN cursos ON unidades.idcurso = cursos.idcurso WHERE unidades.nomunidad = '".$row['unidad']."'");
	$row_curso = mysqli_fetch_array($result_curso);
	$curso = $row_curso['nomcurso'];

	$unidad = ($esPMAR) ? $row['unidad'].' (PMAR)' : $row['unidad'];
	$evaluacion = $row['evaluacion'];
	$texto_acta = $row['texto_acta'];
	$asistentes = unserialize($row['asistentes']);
	$ausentes = array();
	$fecha = $row['fecha'];
	$fecha_alt = strftime('%d/%m/%Y', strtotime($row['fecha']));
	$dia = strftime('%e', strtotime($row['fecha']));
	$mes = strftime('%B', strtotime($row['fecha']));
	$anio = strftime('%Y', strtotime($row['fecha']));

	// OBTENEMOS PROFESORES AUSENTES
	$result_equipoeducativo = mysqli_query($db_con, "SELECT DISTINCT profesores.profesor, c_profes.idea FROM profesores JOIN c_profes ON profesores.profesor = c_profes.profesor WHERE grupo = '".$row['unidad']."' ORDER BY profesor ASC");
	while ($row_equipoeducativo = mysqli_fetch_array($result_equipoeducativo)) {
		if (! in_array($row_equipoeducativo['idea'], $asistentes)) {
			$ausentes[] = $row_equipoeducativo['idea'];
		}
	}

	// DATOS PARA LA CONSTRUCCIÓN DE TABLAS
	$numero_asistentes = count($asistentes);
	if ($numero_asistentes) {

		$tabla_asistentes = '<p><br></p>

		<h4>Profesores asistentes</h4>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Nombre y apellidos</th>
					<th>Firma</th>
					<th>Nombre y apellidos</th>
					<th>Firma</th>
				</tr>
			</thead>
			<tbody>';

		for ($i = 0; $i < $numero_asistentes; $i+=2) {

			$profesor_1 = (isset($asistentes[$i])) ? obtener_nombre_profesor_desde_idea($db_con, nomprofesor($asistentes[$i])) : '';
			$profesor_2 = (isset($asistentes[$i+1])) ? obtener_nombre_profesor_desde_idea($db_con, nomprofesor($asistentes[$i+1])) : '';

			$tabla_asistentes .= '
					<tr>
						<td>'.$profesor_1.'</td>
						<td><br></td>
						<td>'.$profesor_2.'</td>
						<td><br></td>
					</tr>';
		}

		$tabla_asistentes .= '
			</tbody>
		</table>';

	}
	

	$numero_ausentes = count($ausentes);

	if ($numero_ausentes) {

		$tabla_ausentes = '<p><br></p>

		<h4>Profesores ausentes</h4>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Nombre y apellidos</th>
					<th>Firma</th>
					<th>Nombre y apellidos</th>
					<th>Firma</th>
				</tr>
			</thead>
			<tbody>';

		for ($i = 0; $i < $numero_ausentes; $i+=2) {

			$profesor_1 = (isset($ausentes[$i])) ? obtener_nombre_profesor_desde_idea($db_con, nomprofesor($ausentes[$i])) : '';
			$profesor_2 = (isset($ausentes[$i+1])) ? obtener_nombre_profesor_desde_idea($db_con, nomprofesor($ausentes[$i+1])) : '';

			$tabla_ausentes .= '
					<tr>
						<td>'.$profesor_1.'</td>
						<td><br></td>
						<td>'.$profesor_2.'</td>
						<td><br></td>
					</tr>';
		}

		$tabla_ausentes .= '
			</tbody>
		</table>';
	}

	$texto_acta = '
	<header>
		<style type="text/css">
		body {
			font-size: 9.5pt;
			margin: 10mm 10mm 20mm 10mm; 
		}
		#footer {
			position: fixed;
		left: 0;
			right: 0;
			bottom: 0;
			color: #aaa;
			font-size: 0.9em;
			text-align: right;
		}
		.page-number:before {
		content: counter(page);
		}

		h1 {
			font-size: 16pt;
		}
		h1 > small {
			font-size: 13pt;
			font-weight: normal;
			color: #666;
		}

		.texto-consejeria {
			font-size: 8.5pt;
			font-weight: bold;
			color: rgb(0, 109, 38);
		}

		.table-no-border td {
			border: 0;
			padding: 2mm 0;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 5mm 
		}
		table th {
			background-color: #333;
			color: #fff;
			padding: 2mm 2mm;
		}
		table td {
			border: 1px solid #666;
			padding: 2mm 2mm;

		}
	</style>
	</header>
	<body>
		<table class="table-no-border">
			<tr>
				<td width="50%"><img src="../../img/encabezado.png" alt=""></td>
				<td class="texto-consejeria" width="50%">Consejería de Desarrollo Educativo y Formación Profesional<br>
				'.$config['centro_denominacion'].'</td>
			</tr>
		</table>
		
		<br>

		<h1>Acta de evaluación <small>Curso escolar '.$config['curso_actual'].'</small></h1>

		<table class="table-no-border">
			<tr>
				<td><strong>Unidad:</strong> '.$unidad.' ('.$curso.')</td>
				<td><strong>Evaluación:</strong> '.$evaluaciones[$evaluacion].'</td>
			</tr>
			<tr>
				<td><strong>Tutor/a:</strong> '.$tutor.'</td>
				<td><strong>Fecha:</strong> '.$fecha_alt.'</td>
				
			</tr>
		</table>

		<br>
		<br>

		'.$texto_acta.'

		'.$tabla_asistentes.'
		
		'.$tabla_ausentes.'

		<p><br></p>

		En '.$config['centro_localidad'].' a '.$dia.' de '.$mes.' de '.$anio.'.
	</body>
	<div id="footer">
	  Página <span class="page-number"></span>
	</div>';
	$html = mb_convert_encoding($texto_acta, 'UTF-8', 'UTF-8');
	
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->render();
	$dompdf->stream("Acta de evaluación $evaluacion - $unidad.pdf", array("Attachment" => 0));
}
?>