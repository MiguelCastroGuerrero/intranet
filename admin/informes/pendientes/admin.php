<?php
require('../../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1, 2, 4));

include("../../../menu.php");
include("menu.php");
?>	
	<div class="container">
		
		<div class="page-header">
			<h2>Informe individual para alumnos con materias pendientes <small> <br>Administración de informes</small></h2>
		</div>

		<?php if(isset($msg_error) && $msg_error): ?>
		<div class="alert alert-danger alert-fadeout">
			<?php echo $msg_error; ?>
		</div>
		<?php endif; ?>

		<div class="row">
			<?php 
			$cursos = array('E.S.O.','Bachillerato','Otros');
			foreach ($cursos as $valor_curso) {
				if($valor_curso == "E.S.O."){ $extra_curso = "and alma.curso like '%E.S.O.%'";}
				elseif ($valor_curso == "Bachillerato"){ $extra_curso = "and alma.curso like '%Bachillerato%'";}
				elseif ($valor_curso == "Otros"){ $extra_curso = "and (alma.curso not like '%Bachiller%' and alma.curso not like '%E.S.O.%')";}
			?>
			<div class="col-sm-4">

				<table class="table table-striped table-bordered datatable" style="width:100%;">
					   <caption><?php echo $valor_curso; ?></caption>
					<thead>
						<tr>
							<th>Alumno</th>
							<th>Curso</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php 
						// OBTENEMOS INFORMES
						
						$al_pendiente = mysqli_query($db_con,"select distinct pendientes.claveal, alma.apellidos, alma.nombre, alma.unidad from pendientes, alma where pendientes.claveal = alma.claveal $extra_curso order by alma.unidad, apellidos, nombre asc");	
						while ($alumno_informe = mysqli_fetch_array($al_pendiente)) {
							$cod = mysqli_query($db_con,"select distinct codigo from pendientes where claveal='$alumno_informe[0]'");							
							while($cod_pend = mysqli_fetch_array($cod)){
								
								$a_pend = mysqli_query($db_con,"select distinct nombre, curso from asignaturas where codigo='$cod_pend[0]' and abrev not like '%\_%' limit 1");
								
								if(mysqli_num_rows($a_pend)<1){
								$a_pend = mysqli_query($db_con,"select distinct nombre, curso from asignaturas where codigo='$cod_pend[0]' limit 1");
								}
								$asignat = mysqli_fetch_array($a_pend);
								
								$asignatura_pend = $asignat['nombre'];
								
								if(stristr($asignat['curso'], "Bachill")==TRUE){
									$curso_pend = "1".substr($asignat['curso'],1,100);
								}
								else{
									$curso_pend = $asignat['curso'];
								}
															
								$n_inf = mysqli_query($db_con,"select id_informe from informe_pendientes where asignatura like '$asignatura_pend' and curso like '$curso_pend' $extra_dep1");
								//echo "select id_informe from informe_pendientes where asignatura like '$asignatura_pend' and curso like '$curso_pend' $extra_dep1<br>";
								
								if(mysqli_num_rows($n_inf)>0){							
						?>						
						<tr>
							<td><?php echo $alumno_informe['apellidos'].", ".$alumno_informe['nombre']; ?></td>
							<td><?php echo $alumno_informe['unidad']; ?></td>
							<td class="pull-right">
								<a href="//<?php echo $config['dominio']; ?>/intranet/admin/informes/pendientes/pdf.php?claveal=<?php echo $alumno_informe['claveal']; ?>" target="_blank" data-bs="tooltip" title="Imprimir PDF con informe de materias pendientes."><span class="fas fa-print fa-fw fa-lg"></span></a>&nbsp;						
							</td>
						</tr>
						<?php break;} } }?>
					</tbody>
				</table>

			</div><!-- /.col-sm-4 -->

		<?php } ?>

		</div><!-- /.row -->
		


	</div>

	<?php include("../../../pie.php"); ?>

</body>
</html>
