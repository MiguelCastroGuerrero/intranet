<?php
require('../../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1, 2, 4));

if (isset($_SESSION['mod_tutoria']['unidad'])) {
	$extra_tutor= "and alma.unidad= '".$_SESSION['mod_tutoria']['unidad']."'";
}

// Si es jefe de departamento
	$extra_dep1 = " OR ";
	$dep1 = mysqli_query($db_con, "SELECT distinct materia, nivel from profesores where profesor in (select distinct nombre from departamentos where departamento like '".$_SESSION['dpt']."')");
	while ($row_dep1 = mysqli_fetch_array($dep1)) {
		$extra_dep1.=" (asignatura like '$row_dep1[0]' and curso = '$row_dep1[1]') OR";
	}
	$extra_dep1 = substr($extra_dep1,0,-3);

$PLUGIN_DATATABLES = 1;
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
				elseif ($valor_curso == "Otros"){ $extra_curso = "and alma.curso not like '%Bachillerato%' and alma.curso not like '%E.S.O.%'";}
			?>
			<div class="col-sm-6">

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
						
						$al_pendiente = mysqli_query($db_con,"select distinct pendientes.claveal, alma.apellidos, alma.nombre, alma.unidad from pendientes, alma where pendientes.claveal = alma.claveal $extra_tutor $extra_curso order by alma.unidad, apellidos, nombre");	
						while ($alumno_informe = mysqli_fetch_array($al_pendiente)) {
							$cod = mysqli_query($db_con,"select distinct codigo from pendientes where claveal='$alumno_informe[0]'");
							
							while($cod_pend = mysqli_fetch_array($cod)){
								
								$asignat = mysqli_fetch_array(mysqli_query($db_con,"select distinct nombre, curso from asignaturas where codigo='$cod_pend[0]' and abrev not like '%\_%' limit 1"));
								$asignatura_pend = $asignat['nombre'];
								$curso_pend = $asignat['curso'];
							
								$n_inf = mysqli_query($db_con,"select id_informe from informe_pendientes where asignatura like '$asignatura_pend' and curso like '$curso_pend' $extra_dep1");
								
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

	<script>
	$(document).ready(function() {
	  var table = $('.datatable').DataTable({
		"scrollY":        "400px",
	  	  "paging":   false,
	      "ordering": true,
	      "info":     false,

	  		"lengthMenu": [[15, 35, 50, -1], [15, 35, 50, "Todos"]],

	  		"order": [[ 1, "desc" ]],

	  		"language": {
	  		            "lengthMenu": "_MENU_",
	  		            "zeroRecords": "No se ha encontrado ningún resultado con ese criterio.",
	  		            "info": "Página _PAGE_ de _PAGES_",
	  		            "infoEmpty": "No hay resultados disponibles.",
	  		            "infoFiltered": "(filtrado de _MAX_ resultados)",
	  		            "search": "Buscar: ",
	  		            "paginate": {
	  		                  "first": "Primera",
	  		                  "next": "Última",
	  		                  "next": "",
	  		                  "previous": ""
	  		                }
	  		        }
	  	});
	});
	</script>
</body>
</html>