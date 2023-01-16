<?php
require('../../../bootstrap.php');

if (isset($_GET['curso_escolar'])) $curso_escolar = $_GET['curso_escolar'];

if (isset($_GET['evaluacion'])) 
    $evaluacion_seleccionada = $_GET['evaluacion'];
    //else $evaluacion_seleccionada = '1ev';
else { 
    switch (trimestreActual()) { //seleccionamos por defecto la evaluación anterior que ya está finalizada
        case 1: $evaluacion_seleccionada = 'evi'; break;
        case 2: $evaluacion_seleccionada = '1ev'; break;
        case 3: $evaluacion_seleccionada = '2ev'; break;
        case 4: $evaluacion_seleccionada = 'ord'; break;
    }
}
//echo 'EVALUACION seleccionada: '.$evaluacion_seleccionada;
if (file_exists(INTRANET_DIRECTORY . '/config_datos.php')) {
	if (isset($curso_escolar) && ($curso_escolar != $config['curso_actual'])) {
		$exp_curso = explode("/", $curso_escolar);
		$anio_escolar = $exp_curso[0];
		
		$db_con = mysqli_connect($config['db_host_c'.$anio_escolar], $config['db_user_c'.$anio_escolar], $config['db_pass_c'.$anio_escolar], $config['db_name_c'.$anio_escolar]);
		mysqli_query($db_con,"SET NAMES 'utf8'");
	}
	else {
		$curso_escolar = $config['curso_actual'];
	}
}
else {
	$curso_escolar = $config['curso_actual'];
}



// Si el usuario desea eliminar los datos y recalcular
if (isset($_GET['recalcular']) && $_GET['recalcular']) {
	mysqli_query($db_con, "DROP TABLE `informe_evaluaciones_pendientes_".$evaluacion_seleccionada."`;");
}

// Inicializamos variables
$evaluaciones = array('evi' => 'notas0', '1ev' => 'notas1', '2ev' => 'notas2', 'ord' => 'notas3', 'ext' => 'notas4');
$evaluacionSinNotas = 1;
$resultados_evaluaciones = array();

// Consultamos la evaluación seleccionada
$result = mysqli_query($db_con, "SELECT * FROM notas WHERE ".$evaluaciones[$evaluacion_seleccionada]." IS NOT NULL");
$existenNotas = mysqli_num_rows($result);
mysqli_free_result($result);

if ($existenNotas) {
	
	// Comprobamos si se ha creado el informe
	$result_informe = mysqli_query($db_con, "SELECT `idcurso`, `idunidad`, `total_alumnos`, `asignaturas` FROM `informe_evaluaciones_pendientes_".$evaluacion_seleccionada."`");
	if (mysqli_num_rows($result_informe)) {
		$i = 0;
		while ($row_resultados_evaluaciones = mysqli_fetch_array($result_informe, MYSQLI_ASSOC)) {

			$result_nomcurso = mysqli_query($db_con, "SELECT `nomcurso` FROM `cursos` WHERE `idcurso` = '".$row_resultados_evaluaciones['idcurso']."' LIMIT 1");
			$row_result_nomcurso = mysqli_fetch_array($result_nomcurso, MYSQLI_ASSOC);

			$result_nomunidad = mysqli_query($db_con, "SELECT `nomunidad` FROM `unidades` WHERE `idunidad` = '".$row_resultados_evaluaciones['idunidad']."' LIMIT 1");
			$row_result_nomunidad = mysqli_fetch_array($result_nomunidad, MYSQLI_ASSOC);

			$resultados_evaluaciones[$i]['idcurso'] = $row_resultados_evaluaciones['idcurso'];
			$resultados_evaluaciones[$i]['nomcurso'] = $row_result_nomcurso['nomcurso'];
			$resultados_evaluaciones[$i]['idunidad'] = $row_resultados_evaluaciones['idunidad'];
			$resultados_evaluaciones[$i]['nomunidad'] = $row_result_nomunidad['nomunidad'];
			$resultados_evaluaciones[$i]['total_alumnos'] = $row_resultados_evaluaciones['total_alumnos'];
			$resultados_evaluaciones[$i]['asignaturas'] = unserialize($row_resultados_evaluaciones['asignaturas']);
			$i++;
		}
		unset($i);
	}
	else {
		// Creamos las tablas necesarias para el funcionamiento del módulo
		mysqli_query($db_con, "CREATE TABLE `informe_evaluaciones_pendientes_".$evaluacion_seleccionada."` (
		  `idcurso` int(12) NOT NULL,
		  `idunidad` int(12) NOT NULL,
		  `total_alumnos` tinyint(4) NOT NULL,
		  `asignaturas` text NOT NULL,
		  PRIMARY KEY(`idcurso`,`idunidad`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
		
		// Obtenemos las unidades del centro
		if ($evaluacion_seleccionada == 'evi') {
			$result_unidades = mysqli_query($db_con, "SELECT cursos.idcurso, cursos.nomcurso, unidades.idunidad, unidades.nomunidad FROM unidades JOIN cursos ON unidades.idcurso = cursos.idcurso WHERE cursos.nomcurso NOT LIKE '1%' AND (cursos.nomcurso LIKE '%E.S.O.%' OR cursos.nomcurso LIKE '%Bachillerato%') ORDER BY SUBSTR(cursos.nomcurso, 6) ASC, unidades.nomunidad ASC");
		}
		else {
			$result_unidades = mysqli_query($db_con, "SELECT cursos.idcurso, cursos.nomcurso, unidades.idunidad, unidades.nomunidad FROM unidades JOIN cursos ON unidades.idcurso = cursos.idcurso WHERE cursos.nomcurso NOT LIKE '1%' ORDER BY SUBSTR(cursos.nomcurso, 6) ASC, unidades.nomunidad ASC");
		}
		
		$row_unidades = array();
		while ($row = mysqli_fetch_array($result_unidades, MYSQLI_ASSOC)) $row_unidades[] = $row;
		
		foreach ($row_unidades as $unidades) {
			
			$idcurso = $unidades['idcurso'];
			$curso = $unidades['nomcurso'];
			
			$idunidad = $unidades['idunidad'];
			$unidad = $unidades['nomunidad'];
			
			// Obtenemos las asignaturas de la unidad
			$result_asignaturas_unidad = mysqli_query($db_con, "SELECT DISTINCT codigo, nombre, abrev FROM materias WHERE curso = '".$unidades['nomcurso']."' AND abrev LIKE '%\_%' ORDER BY codigo ASC");
			
			$row_asignaturas_unidad = array();
			while ($row = mysqli_fetch_array($result_asignaturas_unidad, MYSQLI_ASSOC)) $row_asignaturas_unidad[] = $row;
			
			$columna = 0;
			foreach ($row_asignaturas_unidad as $asignatura) {
				$unidades['asignaturas'][$columna]['codigo'] = $asignatura['codigo'];
				$unidades['asignaturas'][$columna]['nombre'] = $asignatura['nombre'];
				$unidades['asignaturas'][$columna]['abrev'] = $asignatura['abrev'];
				
				$unidades['asignaturas'][$columna]['matriculados'] = 0;
				$unidades['asignaturas'][$columna]['aprobados'] = 0;
				$unidades['asignaturas'][$columna]['suspensos'] = 0;
				
				// Obtenemos el número de alumnos
				$result_alumnos_unidad = mysqli_query($db_con, "SELECT alma.claveal1, notas.".$evaluaciones[$evaluacion_seleccionada]." FROM alma JOIN notas ON alma.claveal1 = notas.claveal WHERE alma.unidad = '".$unidades['nomunidad']."' AND alma.curso = '".$unidades['nomcurso']."'");
				$unidades['total_alumnos'] = mysqli_num_rows($result_alumnos_unidad);
				
				// Obtenemos las notas de los alumnos
				$row_alumnos_unidad = array();
				while ($row = mysqli_fetch_array($result_alumnos_unidad, MYSQLI_ASSOC)) $row_alumnos_unidad[] = $row;
				
				foreach ($row_alumnos_unidad as $alumno) {

					$alumno[$evaluaciones[$evaluacion_seleccionada]] = rtrim($alumno[$evaluaciones[$evaluacion_seleccionada]], ';');
					
					$exp_asignaturas = explode(';', $alumno[$evaluaciones[$evaluacion_seleccionada]]);
					
					foreach ($exp_asignaturas as $asignatura_alumno) {
						
						$exp_notas_asignatura = explode(':', trim($asignatura_alumno));
						$idasignatura = trim($exp_notas_asignatura[0]);
						$idcalificacion = trim($exp_notas_asignatura[1]);
						
						if ($idasignatura == $asignatura['codigo']) {
							$unidades['asignaturas'][$columna]['matriculados']++;
							
							if ($idcalificacion != '') {
								$result_calificacion = mysqli_query($db_con, "SELECT abreviatura FROM calificaciones WHERE codigo = '".$idcalificacion."'");
								$row_calificacion = mysqli_fetch_array($result_calificacion);
								if (! intval($row_calificacion['abreviatura']) || $row_calificacion['abreviatura'] < 5) {
									$unidades['asignaturas'][$columna]['suspensos']++;
								}
								else {
									$unidades['asignaturas'][$columna]['aprobados']++;
								}
							}
						}
					}
				}
				
				if (!$unidades['asignaturas'][$columna]['matriculados']) {
					$unidades['asignaturas'][$columna]['matriculados'] = -1;
					$unidades['asignaturas'][$columna]['aprobados'] = -1;
					$unidades['asignaturas'][$columna]['suspensos'] = -1;
				}
				
				$columna++;
			}
			
			// Añadimos a la base de datos
			mysqli_query($db_con, "INSERT INTO `informe_evaluaciones_pendientes_".$evaluacion_seleccionada."` (`idcurso`, `idunidad`, `total_alumnos`, `asignaturas`) VALUES ('".$idcurso."', '".$idunidad."', '".$unidades['total_alumnos']."', '".serialize($unidades['asignaturas'])."');");
			
			// Añadimos la información al array
			array_push($resultados_evaluaciones, $unidades);
		}
		
		mysqli_free_result($result_cursos);
	}
	
	$evaluacionSinNotas = 0;
}

include("../../../menu.php");
include("menu.php");
?>	
	<style type="text/css">
	td.active {
		background-color: #ececec !important;
	}
	
	.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
		padding: 6px 4px !important;
	}
	</style>
	
	<div class="container-fluid">
		
		<div class="page-header">
			<h2>Informes de evaluaciones <small>Estadísticas por alumnos con pendientes</small></h2>
		</div>
		
		<div class="row">
			<div class="col-sm-10">
				<?php if (file_exists(INTRANET_DIRECTORY . '/config_datos.php')): ?>
				<form class="col-sm-2 pull-left" method="get" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<div class="form-group">
					  <input type="hidden" name="evaluacion" value="<?php echo $_GET['evaluacion']; ?>">
					  <select class="form-control" id="curso_escolar" name="curso_escolar" onchange="submit()">
					  	<?php $exp_curso = explode("/", $config['curso_actual']); ?>
					  	<?php for($i = 0; $i < 5; $i++): ?>
					  	<?php $anio_escolar = $exp_curso[0] - $i; ?>
					  	<?php $anio_escolar_sig = substr(($exp_curso[0] - $i + 1), 2, 2); ?>
					  	<?php if($i == 0 || (isset($config['db_host_c'.$anio_escolar]) && $config['db_host_c'.$anio_escolar] != "")): ?>
					  	<option value="<?php echo $anio_escolar.'/'.$anio_escolar_sig; ?>"<?php echo (isset($curso_escolar) && $curso_escolar == $anio_escolar.'/'.$anio_escolar_sig) ? ' selected' : ''; ?>><?php echo $anio_escolar.'/'.$anio_escolar_sig; ?></option>
					  	<?php endif; ?>
					  	<?php endfor; ?>
					  </select>
					</div>
				</form>
				<?php endif; ?>
				
				<ul class="nav nav-pills">
				    <li<?php echo ($evaluacion_seleccionada == 'evi') ? ' class="active"' : ''; ?>>
                        <a href="?evaluacion=evi<?php echo (isset($curso_escolar)) ? '&curso_escolar='.$curso_escolar : ''; ?>">Evaluación Inicial</a>
                    </li>
				    <li<?php echo ($evaluacion_seleccionada == '1ev') ? ' class="active"' : ''; ?>>
                        <a href="?evaluacion=1ev<?php echo (isset($curso_escolar)) ? '&curso_escolar='.$curso_escolar : ''; ?>">1ª Evaluación</a>
                    </li>
				    <li<?php echo ($evaluacion_seleccionada == '2ev') ? ' class="active"' : ''; ?>>
                        <a href="?evaluacion=2ev<?php echo (isset($curso_escolar)) ? '&curso_escolar='.$curso_escolar : ''; ?>">2ª Evaluación</a>
                    </li>
				    <li<?php echo ($evaluacion_seleccionada == 'ord') ? ' class="active"' : ''; ?>>
                        <a href="?evaluacion=ord<?php echo (isset($curso_escolar)) ? '&curso_escolar='.$curso_escolar : ''; ?>">Evaluación Ordinaria</a></li>
				    <li<?php echo ($evaluacion_seleccionada == 'ext') ? ' class="active"' : ''; ?>>
                        <a href="?evaluacion=ext<?php echo (isset($curso_escolar)) ? '&curso_escolar='.$curso_escolar : ''; ?>">Evaluación Extraordinaria</a>
                    </li>
				</ul>
			</div>
			
			<div class="col-sm-2 hidden-print">
				<?php if (! $evaluacionSinNotas): ?>
				<a href="pendientes.php?evaluacion=<?php echo $evaluacion_seleccionada; ?>&amp;recalcular=1" class="btn btn-sm btn-warning pull-right"><span class="fas fa-sync-alt fa-fw"></span> Recalcular</a>
				<?php endif; ?>
			</div>
		</div>
		
		<br>
		
		<div class="row">
			
			<div class="col-sm-12">
				
				<?php if ($evaluacionSinNotas): ?>
				<div class="text-center">
					<br><br><br>
					<p class="lead">No se han importado las notas de esta evaluación</p>
					<br><br><br><br>
				</div>
				<?php else: ?>
				
				<?php 
				$flag = 0;
				$num_unidades = 1;
				$num_columnas = 0;
				$nomcurso = "";
				?>
				<?php foreach ($resultados_evaluaciones as $evaluacion): ?>
				
				<?php if ($nomcurso != $evaluacion['nomcurso']): ?>
				<?php if ($flag == 1): ?>
				</tbody>
					<tfoot>
						<tr>
							<th>Totales</th>
							<?php for ($i = 0; $i < $num_columnas; $i++): ?>
							<th class="text-center"><?php echo ${matriculados.$i}; ?></th>
							<th class="text-center text-success"><?php echo ${aprobados.$i}; ?><br><small>(<?php echo ceil(number_format((${aprobados.$i} * 100) / ${matriculados.$i}, 1)); ?>%)</small></th>
							<th class="text-center text-danger"><?php echo ${suspensos.$i}; ?><br><small>(<?php echo ceil(number_format((${suspensos.$i} * 100) / ${matriculados.$i}, 1)); ?>%)</small></th>
							<?php unset(${matriculados.$i}); ?>
							<?php unset(${aprobados.$i}); ?>
							<?php unset(${suspensos.$i}); ?>
							<?php endfor; ?>
						</tr>
					</tfoot>
				</table>
				
				<hr>
				
				<?php endif; ?>
				<?php 
				$flag = 1;
				$num_unidades = 1;
				$num_columnas = 0;
				$total_alumnos = 0;
				?>
				
				<h4 class="text-info"><?php echo $evaluacion['nomcurso']; ?></h4>
				
				<table class="table table-bordered table-hover" style="<?php echo (count($evaluacion['asignaturas']) < 9) ? 'width: auto; ' : ''; ?> font-size: 11px;">
					<thead>
						<tr>
							<th width="65" rowspan="2">Unidad</th>
							<?php for ($i = 0; $i < count($evaluacion['asignaturas']); $i++): ?>
							<th class="text-center" colspan="3"><abbr data-bs="tooltip" data-html="true" title="<?php echo $evaluacion['asignaturas'][$i]['nombre']; ?><br>[Cód.: <?php echo $evaluacion['asignaturas'][$i]['codigo']; ?>]"><?php echo $evaluacion['asignaturas'][$i]['abrev']; ?></abbr></th>
							<?php endfor; ?>
						</tr>
						<tr>
							<?php for ($i = 0; $i < count($evaluacion['asignaturas']); $i++): ?>
							<?php if (count($evaluacion['asignaturas']) < 9) $tamano_celda = '50px'; else $tamano_celda = ((100 / count($evaluacion['asignaturas'])) / 3); ?>
							<th class="text-center" width="<?php echo $tamano_celda; ?>%"><abbr data-bs="tooltip" title="Matriculados">M.</abbr></th>
							<th class="text-center" width="<?php echo $tamano_celda; ?>%"><abbr data-bs="tooltip" title="Aprobados">A.</abbr></th>
							<th class="text-center" width="<?php echo $tamano_celda; ?>%"><abbr data-bs="tooltip" title="Suspensos">S.</abbr></th>
							<?php unset($tamano_celda); ?>
							<?php endfor; ?>
						</tr>
					</thead>
					<tbody>
				<?php endif; ?>
						<tr>
							<th><?php echo $evaluacion['nomunidad']; ?></th>
							<?php for ($i = 0; $i < count($evaluacion['asignaturas']); $i++): ?>
							<?php if ($evaluacion['asignaturas'][$i]['matriculados'] < 0): ?>
							<td class="text-center active"><?php $evaluacion['asignaturas'][$i]['matriculados'] = 0; ?></td>
							<td class="text-center active"><?php $evaluacion['asignaturas'][$i]['aprobados'] = 0; ?></td>
							<td class="text-center active"><?php $evaluacion['asignaturas'][$i]['suspensos'] = 0; ?></td>
							<?php else: ?>
							<td class="text-center"><?php echo $evaluacion['asignaturas'][$i]['matriculados']; ?></td>
							<td class="text-center text-success"><?php echo $evaluacion['asignaturas'][$i]['aprobados']; ?><br><small>(<?php echo number_format(($evaluacion['asignaturas'][$i]['aprobados'] * 100) / $evaluacion['asignaturas'][$i]['matriculados'], 0); ?>%)</small></td>
							<td class="text-center text-danger"><?php echo $evaluacion['asignaturas'][$i]['suspensos']; ?><br><small>(<?php echo number_format(($evaluacion['asignaturas'][$i]['suspensos'] * 100) / $evaluacion['asignaturas'][$i]['matriculados'], 0); ?>%)</small></td>
							<?php endif; ?>
							<?php endfor; ?>
						</tr>
				
				<?php 
				$total_alumnos += $evaluacion['total_alumnos'];
				$num_columnas = count($evaluacion['asignaturas']);
				for ($i = 0; $i < count($evaluacion['asignaturas']); $i++) {
					${matriculados.$i} += $evaluacion['asignaturas'][$i]['matriculados'];
					${aprobados.$i} += $evaluacion['asignaturas'][$i]['aprobados'];
					${suspensos.$i} += $evaluacion['asignaturas'][$i]['suspensos'];
				}
				$num_unidades++;
				$nomcurso = $evaluacion['nomcurso']; 
				?>
				<?php endforeach; ?>
				
				</tbody>
					<tfoot>
						<tr>
							<th>Totales</th>
							<?php for ($i = 0; $i < $num_columnas; $i++): ?>
							<th class="text-center"><?php echo ${matriculados.$i}; ?></th>
							<th class="text-center text-success"><?php echo ${aprobados.$i}; ?><br><small>(<?php echo number_format((${aprobados.$i} * 100) / ${matriculados.$i}, 0); ?>%)</small></th>
							<th class="text-center text-danger"><?php echo ${suspensos.$i}; ?><br><small>(<?php echo number_format((${suspensos.$i} * 100) / ${matriculados.$i}, 0); ?>%)</small></th>
							<?php unset(${matriculados.$i}); ?>
							<?php unset(${aprobados.$i}); ?>
							<?php unset(${suspensos.$i}); ?>
							<?php endfor; ?>
						</tr>
					</tfoot>
				</table>
				<?php endif; ?>
			</div>
			
		</div>
		
	</div>

	<?php include("../../../pie.php"); ?>

</body>
</html>