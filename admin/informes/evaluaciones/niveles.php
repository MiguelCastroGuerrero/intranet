<?php
require('../../../bootstrap.php');

include("../../../menu.php");
include("menu.php");

if (isset($_GET['curso_escolar'])) $curso_escolar = $_GET['curso_escolar'];

//echo 'EVALUACION ACTUAL: '.trimestreActual().'<br>';

if (isset($_GET['evaluacion'])) 
    $evaluacion_seleccionada = $_GET['evaluacion'];
    //else $evaluacion_seleccionada = '1ev';
else { 
    switch (trimestreActual()) { //seleccionamos por defecto la evaluación anterior que ya está finalizada
        case 1: $evaluacion_seleccionada = 'evi'; break;
        case 2: $evaluacion_seleccionada = '1ev'; break;
        case 3: $evaluacion_seleccionada = '2ev'; break;
        case 4: $evaluacion_seleccionada = 'ord'; break;
        case 5: $evaluacion_seleccionada = 'ext'; break;
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
	mysqli_query($db_con, "DROP TABLE `informe_evaluaciones_unidades_".$evaluacion_seleccionada."`;");
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
	$result_informe = mysqli_query($db_con, "SELECT * FROM `informe_evaluaciones_unidades_".$evaluacion_seleccionada."` JOIN cursos ON `informe_evaluaciones_unidades_".$evaluacion_seleccionada."`.idcurso = cursos.idcurso JOIN unidades ON `informe_evaluaciones_unidades_".$evaluacion_seleccionada."`.idunidad = unidades.idunidad ORDER BY cursos.idcurso ASC, unidades.nomunidad ASC");
	if (mysqli_num_rows($result_informe)) {
		while ($row_resultados_evaluaciones = mysqli_fetch_array($result_informe, MYSQLI_ASSOC)) $resultados_evaluaciones[] = $row_resultados_evaluaciones;
	}
	else {
		// Creamos las tablas necesarias para el funcionamiento del módulo
		mysqli_query($db_con, "CREATE TABLE `informe_evaluaciones_unidades_".$evaluacion_seleccionada."` (
		  `idcurso` int(12) NOT NULL,
		  `idunidad` int(12) NOT NULL,
		  `total_alumnos` tinyint(4) NOT NULL,
		  `repiten_alumnos` tinyint(4) NOT NULL,
		  `cero_suspensos` tinyint(4) NOT NULL,
		  `uno_suspensos` tinyint(4) NOT NULL,
		  `dos_suspensos` tinyint(4) NOT NULL,
		  `tres_suspensos` tinyint(4) NOT NULL,
		  `cuatro_suspensos` tinyint(4) NOT NULL,
		  `cinco_suspensos` tinyint(4) NOT NULL,
		  `seis_suspensos` tinyint(4) NOT NULL,
		  `siete_suspensos` tinyint(4) NOT NULL,
		  `ocho_suspensos` tinyint(4) NOT NULL,
		  `nueve_o_mas_suspensos` tinyint(4) NOT NULL,
		  `promocionan` decimal(5,2) NOT NULL,
		  `titulan` tinyint(4) NOT NULL,
		  PRIMARY KEY(`idcurso`,`idunidad`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
		
		// Obtenemos las unidades del centro
		if ($evaluacion_seleccionada == 'evi') {
			$result_unidades = mysqli_query($db_con, "SELECT cursos.idcurso, cursos.nomcurso, unidades.idunidad, unidades.nomunidad FROM unidades JOIN cursos ON unidades.idcurso = cursos.idcurso WHERE cursos.nomcurso LIKE '%E.S.O.%' OR cursos.nomcurso LIKE '%Bachillerato%' ORDER BY cursos.idcurso ASC, unidades.nomunidad ASC");
		}
		else {
			$result_unidades = mysqli_query($db_con, "SELECT cursos.idcurso, cursos.nomcurso, unidades.idunidad, unidades.nomunidad FROM unidades JOIN cursos ON unidades.idcurso = cursos.idcurso ORDER BY cursos.idcurso ASC, unidades.nomunidad ASC");
		}
		
		//
		$row_unidades = array();
		while ($row = mysqli_fetch_array($result_unidades, MYSQLI_ASSOC)) $row_unidades[] = $row;
		
		foreach ($row_unidades as $unidades) {
			
			$idcurso = $unidades['idcurso'];
			$curso = $unidades['nomcurso'];
			
			$idunidad = $unidades['idunidad'];
			$unidad = $unidades['nomunidad'];
			$unidades['repiten_alumnos'] = 0;
			$unidades['cero_suspensos'] = 0;
			$unidades['uno_suspensos'] = 0;
			$unidades['dos_suspensos'] = 0;
			$unidades['tres_suspensos'] = 0;
			$unidades['cuatro_suspensos'] = 0;
			$unidades['cinco_suspensos'] = 0;
			$unidades['seis_suspensos'] = 0;
			$unidades['siete_suspensos'] = 0;
			$unidades['ocho_suspensos'] = 0;
			$unidades['nueve_o_mas_suspensos'] = 0;
			$unidades['promocionan'] = 0;
			$unidades['titulan'] = 0;
			
			// Obtenemos el número de alumnos
			$result_alumnos_unidad = mysqli_query($db_con, "SELECT alma.claveal1, notas.".$evaluaciones[$evaluacion_seleccionada].", alma.matriculas, alma.estadomatricula FROM alma JOIN notas ON alma.claveal1 = notas.claveal WHERE alma.unidad = '".$unidades['nomunidad']."' AND alma.curso = '".$unidades['nomcurso']."'");
			$unidades['total_alumnos'] = mysqli_num_rows($result_alumnos_unidad);
			
			// Obtenemos las notas de los alumnos
			$row_alumnos_unidad = array();
			while ($row = mysqli_fetch_array($result_alumnos_unidad, MYSQLI_ASSOC)) $row_alumnos_unidad[] = $row;
			
			foreach ($row_alumnos_unidad as $alumno) {
				
				$suspensos = 0;
				
				$alumno[$evaluaciones[$evaluacion_seleccionada]] = rtrim($alumno[$evaluaciones[$evaluacion_seleccionada]], ';');
				
				$exp_asignaturas = explode(';', $alumno[$evaluaciones[$evaluacion_seleccionada]]);
				
				foreach ($exp_asignaturas as $asignatura) {
					
					$exp_notas_asignatura = explode(':', trim($asignatura));
					$idasignatura = trim($exp_notas_asignatura[0]);
					$idcalificacion = trim($exp_notas_asignatura[1]);
					
					if ($idcalificacion != '') { 
						$result_calificacion = mysqli_query($db_con, "SELECT abreviatura FROM calificaciones WHERE codigo = '".$idcalificacion."'");
						$row_calificacion = mysqli_fetch_array($result_calificacion);
						if (! intval($row_calificacion['abreviatura']) || $row_calificacion['abreviatura'] < 5) 
                            				$suspensos++;
					}
				}
				
				switch ($suspensos) {
					case 0 : $unidades['cero_suspensos']++; break;
					case 1 : $unidades['uno_suspensos']++; break;
					case 2 : $unidades['dos_suspensos']++; break;
					case 3 : $unidades['tres_suspensos']++; break;
					case 4 : $unidades['cuatro_suspensos']++; break;
					case 5 : $unidades['cinco_suspensos']++; break;
					case 6 : $unidades['seis_suspensos']++; break;
					case 7 : $unidades['siete_suspensos']++; break;
					case 8 : $unidades['ocho_suspensos']++; break;
					default: $unidades['nueve_o_mas_suspensos']++;
				}
				
				// Comprobamos si repite curso o no
				if ($alumno['matriculas'] > 1) $unidades['repiten_alumnos']++;

				if (($evaluacion_seleccionada=="ord" OR $evaluacion_seleccionada=="ext") AND (stristr($curso, 'E.S.O.') == TRUE and stristr($curso, '4º de E.S.O.')==FALSE) and stristr($alumno['estadomatricula'], "Promociona")==TRUE) {
					$unidades['titulan']++;
				}
				elseif(($evaluacion_seleccionada=="ord" OR $evaluacion_seleccionada=="ext") AND (stristr($curso, '4º de E.S.O.') == TRUE OR (stristr($curso, '2') == TRUE AND stristr($curso, 'E.S.O.') == FALSE)) and stristr($alumno['estadomatricula'], "Obtiene Título")==TRUE) {
					$unidades['titulan']++; $unidades['promocionan']++;
				}
				else{
				
				// Comprobamos si promociona
				if ($suspensos < 3 and ((stristr($curso, 'E.S.O.') == TRUE and stristr($curso, '4º de E.S.O.') == FALSE) OR (stristr($curso, '1') == TRUE AND stristr($curso, 'E.S.O.') == FALSE))) $unidades['promocionan']++;
				
				// Comprobamos si titula
				if (stristr($curso, '4º de E.S.O.') == TRUE OR (stristr($curso, '2') == TRUE AND stristr($curso, 'E.S.O.') == FALSE)) {
					if ($suspensos == 0) {$unidades['titulan']++; $unidades['promocionan']++;}
				}
				else {
					if ($suspensos < 3) $unidades['titulan']++;
					}
				}
			}
			
			// Cambiamos el número de alumnos que promocionan por el porcentaje
			$unidades['promocionan'] = ($unidades['titulan'] * 100) / $unidades['total_alumnos'];
			
			// Añadimos a la base de datos
			mysqli_query($db_con, "INSERT INTO `informe_evaluaciones_unidades_".$evaluacion_seleccionada."` (`idcurso`, `idunidad`, `total_alumnos`, `repiten_alumnos`, `cero_suspensos`, `uno_suspensos`, `dos_suspensos`, `tres_suspensos`, `cuatro_suspensos`, `cinco_suspensos`, `seis_suspensos`, `siete_suspensos`, `ocho_suspensos`, `nueve_o_mas_suspensos`, `promocionan`, `titulan`) VALUES ('".$idcurso."', '".$idunidad."', '".$unidades['total_alumnos']."', '".$unidades['repiten_alumnos']."', '".$unidades['cero_suspensos']."', '".$unidades['uno_suspensos']."', '".$unidades['dos_suspensos']."', '".$unidades['tres_suspensos']."', '".$unidades['cuatro_suspensos']."', '".$unidades['cinco_suspensos']."', '".$unidades['seis_suspensos']."', '".$unidades['siete_suspensos']."', '".$unidades['ocho_suspensos']."', '".$unidades['nueve_o_mas_suspensos']."', '".$unidades['promocionan']."', '".$unidades['titulan']."');");
			
			// Añadimos la información al array
			array_push($resultados_evaluaciones, $unidades);
		}
		
		mysqli_free_result($result_cursos);
	}
	
	$evaluacionSinNotas = 0;
}

?>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.bundle.min.js"></script>
	<script type="text/javascript">
	window.chartColors = {
		red: 'rgb(244, 67, 54)',
		pink: 'rgb(255, 64, 129)',
		purple: 'rgb(156, 39, 176)',
		deeppurple: 'rgb(124, 77, 255)',
		indigo: 'rgb(63, 81, 181)',
		blue: 'rgb(68, 138, 255)',
		lightblue: 'rgb(3, 169, 244)',
		cyan: 'rgb(0, 188, 212)',
		teal: 'rgb(0, 150, 136)',
		green: 'rgb(76, 175, 80)',
		lightgreen: 'rgb(139, 195, 74)',
		lime: 'rgb(205, 220, 57)',
		yellow: 'rgb(255, 235, 59)',
		amber: 'rgb(255, 193, 7)',
		orange: 'rgb(255, 152, 0)',
		deeporange: 'rgb(255, 87, 34)',
		brown: 'rgb(121, 85, 72)',
		grey: 'rgb(158, 158, 158)',
		bluegrey: 'rgb(96, 125, 139)'
	};
	</script>

	<div class="container">
		
		<div class="page-header">
			<h2>Informes de evaluaciones <small>Estadísticas por niveles</small></h2>
		</div>
		
		<div class="row">
			<div class="col-sm-10">
				<?php if (file_exists(INTRANET_DIRECTORY . '/config_datos.php')): ?>
				<form class="col-sm-2 pull-left" method="get" action="">
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
				<a href="niveles.php?evaluacion=<?php echo $evaluacion_seleccionada; ?>&amp;recalcular=1" class="btn btn-sm btn-warning pull-right"><span class="fas fa-sync-alt fa-fw"></span> Recalcular</a>
				<?php endif; ?>
			</div>
		</div>
		
		<br>
				
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
		$nomcurso = "";
		$aux_nomcurso = "";

		$chart_n = 0;
		?>

        <?php foreach ($resultados_evaluaciones as $evaluacion): ?>

		<?php if ($nomcurso != $evaluacion['nomcurso']): ?>
		<?php if ($flag == 1): ?>

		</div>
		<br><br>
		<div class="row">

			<div class="col-sm-10">

				<h4 class="text-danger">Resultados globales de <?php echo $aux_nomcurso;?></h4>
				
				<?php $chart_n++; ?>
                <canvas id="chart_<?php echo $chart_n; ?>" width="940" height="200"></canvas>
		        <script>
					var ctx = document.getElementById("chart_<?php echo $chart_n; ?>");
					
					var myChart = new Chart(ctx, {
						type: 'bar',
						data: {
						  labels: [
						  	"0 supensas", 
						  	"1 supensa", 
						  	"2 supensas", 
						  	"3 supensas", 
						  	"4 supensas", 
						  	"5 supensas", 
						  	"6 supensas", 
						  	"7 supensas", 
						  	"8 supensas", 
						  	"9+ supensas",
						  	"Prom. / Tit."
						  ],
						  datasets: [{
						    data: [
						    	<?php echo $cero_suspensos; ?>,
						    	<?php echo $uno_suspensos; ?>, 
						    	<?php echo $dos_suspensos; ?>, 
						    	<?php echo $tres_suspensos; ?>, 
						    	<?php echo $cuatro_suspensos; ?>, 
						    	<?php echo $cinco_suspensos; ?>, 
						    	<?php echo $seis_suspensos; ?>,
						    	<?php echo $siete_suspensos; ?>,
						    	<?php echo $ocho_suspensos; ?>,
						    	<?php echo $nueve_o_mas_suspensos; ?>,
								<?php echo $titulan; ?>
					    	],
						    backgroundColor: [
						    	window.chartColors.lightgreen,
						    	window.chartColors.green,
						    	window.chartColors.teal,
						    	window.chartColors.cyan,
						    	window.chartColors.blue,
						    	window.chartColors.indigo,
						    	window.chartColors.deeppurple,
						    	window.chartColors.purple,
						    	window.chartColors.pink,
						    	window.chartColors.red,
						    	window.chartColors.orange
					    	]
						  }]
						},
						options: {
						  events: false,
						  legend: {
						  	display: false
						  },
						  tooltips: {
						  	enabled: false
						  },
					      scales: {
					        xAxes: [{
					          ticks: {
					          	stepSize: 10
					          }
					        }]
					      },
					      animation: {
							duration: 0,
							onComplete: function () {
							    // render the value of the chart above the bar
							    var ctx = this.chart.ctx;
							    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, 'normal', Chart.defaults.global.defaultFontFamily);
							    ctx.fillStyle = this.chart.config.options.defaultFontColor;
							    ctx.textAlign = 'center';
							    ctx.textBaseline = 'bottom';
							    this.data.datasets.forEach(function (dataset) {
							        for (var i = 0; i < dataset.data.length; i++) {
							            var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
							            if(dataset.data[i]>0) ctx.fillText(dataset.data[i], model.x, model.y);
							        }
							    });
							}
						  }
					    }
					});
		        </script>

		        </div>

		    </div>
		    <br><br>
				<?php endif; // $flag == 1 ?>

		        <?php 
		        $flag = 1;
		        $num_unidades = 1;
		        $total_alumnos = 0;
		        $alumnos_repiten = 0;
		        $cero_suspensos = 0;
		        $uno_suspensos = 0;
		        $dos_suspensos = 0;
		        $tres_suspensos = 0;
		        $cuatro_suspensos = 0;
		        $cinco_suspensos = 0;
		        $seis_suspensos = 0;
		        $siete_suspensos = 0;
		        $ocho_suspensos = 0;
		        $nueve_o_mas_suspensos = 0;
		        $promocionan = 0;
		        $titulan = 0;
		        ?>

				<div class="row">

				<legend class="text-info"><?php echo $evaluacion['nomcurso']; ?></legend>				
				
				<?php endif; // $nomcurso != $evaluacion['nomcurso'] ?>
				<?php $aux_nomcurso = $evaluacion['nomcurso']; ?>

				<div class="col-sm-4">

	                        	<?php $chart_n++; ?>

	                        	<canvas id="chart_<?php echo $chart_n; ?>" width="200" height="200"></canvas>
	                        	<script>
								var ctx = document.getElementById("chart_<?php echo $chart_n; ?>");
								
								var myChart = new Chart(ctx, {
									type: 'bar',
									data: {
									  labels: [
									  	"Repetidores",
									  	"0 supensas", 
									  	"1 supensa", 
									  	"2 supensas", 
									  	"3 supensas", 
									  	"4 supensas", 
									  	"5 supensas", 
									  	"6 supensas", 
									  	"7 supensas", 
									  	"8 supensas", 
									  	"9+ supensas"
									  ],
									  datasets: [{
									    data: [
									    	<?php echo $evaluacion['repiten_alumnos']; ?>,
									    	<?php echo $evaluacion['cero_suspensos']; ?>,
									    	<?php echo $evaluacion['uno_suspensos']; ?>, 
									    	<?php echo $evaluacion['dos_suspensos']; ?>, 
									    	<?php echo $evaluacion['tres_suspensos']; ?>, 
									    	<?php echo $evaluacion['cuatro_suspensos']; ?>, 
									    	<?php echo $evaluacion['cinco_suspensos']; ?>, 
									    	<?php echo $evaluacion['seis_suspensos']; ?>,
									    	<?php echo $evaluacion['siete_suspensos']; ?>,
									    	<?php echo $evaluacion['ocho_suspensos']; ?>,
									    	<?php echo $evaluacion['nueve_o_mas_suspensos']; ?>
								    	],
									    backgroundColor: [
									    	window.chartColors.yellow,
									    	window.chartColors.lightgreen,
									    	window.chartColors.green,
									    	window.chartColors.teal,
									    	window.chartColors.cyan,
									    	window.chartColors.blue,
									    	window.chartColors.indigo,
									    	window.chartColors.deeppurple,
									    	window.chartColors.purple,
									    	window.chartColors.pink,
									    	window.chartColors.red
								    	]
									  }]
									},
									options: {
										responsive: true,
										events: false,
									    legend: {
									  		display: false
									    },
									    tooltips: {
									  		enabled: false
									    },
										title: {
											display: true, 
											text: '<?php echo $evaluacion['nomunidad']." (".$evaluacion['total_alumnos'].")"; ?>'
										},
										animation: {
											duration: 0,
											onComplete: function () {
											    // render the value of the chart above the bar
											    var ctx = this.chart.ctx;
											    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, 'normal', Chart.defaults.global.defaultFontFamily);
											    ctx.fillStyle = this.chart.config.options.defaultFontColor;
											    ctx.textAlign = 'center';
											    ctx.textBaseline = 'bottom';
											    this.data.datasets.forEach(function (dataset) {
											        for (var i = 0; i < dataset.data.length; i++) {
											            var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
											            if(dataset.data[i]>0) ctx.fillText(dataset.data[i], model.x, model.y);
											        }
											    });
											}
										}
									}
								});
						        </script>
				
	                    <?php 
	                    $total_alumnos += $evaluacion['total_alumnos'];
	                    $alumnos_repiten += $evaluacion['repiten_alumnos'];
	                    $cero_suspensos += $evaluacion['cero_suspensos'];
	                    $uno_suspensos += $evaluacion['uno_suspensos'];
	                    $dos_suspensos += $evaluacion['dos_suspensos'];
	                    $tres_suspensos += $evaluacion['tres_suspensos'];
	                    $cuatro_suspensos += $evaluacion['cuatro_suspensos'];
	                    $cinco_suspensos += $evaluacion['cinco_suspensos'];
	                    $seis_suspensos += $evaluacion['seis_suspensos'];
	                    $siete_suspensos += $evaluacion['siete_suspensos'];
	                    $ocho_suspensos += $evaluacion['ocho_suspensos'];
	                    $nueve_o_mas_suspensos += $evaluacion['nueve_o_mas_suspensos'];
	                    $promocionan += $evaluacion['promocionan'];
	                    $titulan += $evaluacion['titulan'];

	                    $num_unidades++; 
	                    $nomcurso = $evaluacion['nomcurso']; 
	                    ?>          
	            
	            </div>          
				
				<?php endforeach; // $resultados_evaluaciones as $evaluacion ?>

				<br><br>
				<div class="row">

					<div class="col-sm-10">

						<h4 class="text-danger">Resultados globales de <?php echo $aux_nomcurso;?></h4>
						
						<?php $chart_n++; ?>
		                <canvas id="chart_<?php echo $chart_n; ?>" width="940" height="200"></canvas>
				        <script>
							var ctx = document.getElementById("chart_<?php echo $chart_n; ?>");
							
							var myChart = new Chart(ctx, {
								type: 'bar',
								data: {
								  labels: [
								  	"0 supensas", 
								  	"1 supensa", 
								  	"2 supensas", 
								  	"3 supensas", 
								  	"4 supensas", 
								  	"5 supensas", 
								  	"6 supensas", 
								  	"7 supensas", 
								  	"8 supensas", 
								  	"9+ supensas",
								  	"Prom. / Tit."
								  ],
								  datasets: [{
								    data: [
								    	<?php echo $cero_suspensos; ?>,
								    	<?php echo $uno_suspensos; ?>, 
								    	<?php echo $dos_suspensos; ?>, 
								    	<?php echo $tres_suspensos; ?>, 
								    	<?php echo $cuatro_suspensos; ?>, 
								    	<?php echo $cinco_suspensos; ?>, 
								    	<?php echo $seis_suspensos; ?>,
								    	<?php echo $siete_suspensos; ?>,
								    	<?php echo $ocho_suspensos; ?>,
								    	<?php echo $nueve_o_mas_suspensos; ?>,
										<?php echo $titulan; ?>
							    	],
								    backgroundColor: [
								    	window.chartColors.lightgreen,
								    	window.chartColors.green,
								    	window.chartColors.teal,
								    	window.chartColors.cyan,
								    	window.chartColors.blue,
								    	window.chartColors.indigo,
								    	window.chartColors.deeppurple,
								    	window.chartColors.purple,
								    	window.chartColors.pink,
								    	window.chartColors.red,
								    	window.chartColors.orange
							    	]
								  }]
								},
								options: {
								  events: false,
								  legend: {
								  	display: false
								  },
								  tooltips: {
								  	enabled: false
								  },
							      scales: {
							        xAxes: [{
							          ticks: {
							          	stepSize: 10
							          }
							        }]
							      },
							      animation: {
									duration: 0,
									onComplete: function () {
									    // render the value of the chart above the bar
									    var ctx = this.chart.ctx;
									    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, 'normal', Chart.defaults.global.defaultFontFamily);
									    ctx.fillStyle = this.chart.config.options.defaultFontColor;
									    ctx.textAlign = 'center';
									    ctx.textBaseline = 'bottom';
									    this.data.datasets.forEach(function (dataset) {
									        for (var i = 0; i < dataset.data.length; i++) {
									            var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
									            if(dataset.data[i]>0) ctx.fillText(dataset.data[i], model.x, model.y);
									        }
									    });
									}
								  }
							    }
							});
				        </script>

				        </div>

				    </div>
				
			<?php endif; // $evaluacionSinNotas ?>
			</div>
		</div>

<?php include("../../../pie.php"); ?>
