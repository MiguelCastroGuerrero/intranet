<?php
require('../../bootstrap.php');

if (! isset($_GET['diasem']) && ! isset($_GET['hora'])) {
	exit('No direct script access allowed');
}

if (file_exists('config.php')) {
	include('config.php');
}

// VARIABLES URI
if (isset($_GET['diasem'])) $diasem = mysqli_real_escape_string($db_con, $_GET['diasem']);
if (isset($_GET['hora'])) $hora = mysqli_real_escape_string($db_con, $_GET['hora']);


$fechahoy = date('Y-m-d H:i:s');
$dsemana = strftime('%u', strtotime($fechahoy));
$diasdif = $diasem - $dsemana;
$fechaselec = strtotime ('+'.$diasdif.' day', strtotime($fechahoy)) ;
$fechaselec = date ('Y-m-d' , $fechaselec);

include("../../menu.php");
include("menu.php");
?>

<div class="container">
	
	<div class="page-header">
		<h2 style="display: inline;">Guardias de aula <small>Registrar guardia</small></h2>
	
		<!-- Button trigger modal -->
		<a href="#"class="btn btn-default btn-sm pull-right hidden-print" data-toggle="modal" data-target="#modalAyuda">
			<span class="fas fa-question fa-lg"></span>
		</a>

		<!-- Modal -->
		<div class="modal fade" id="modalAyuda" tabindex="-1" role="dialog" aria-labelledby="modal_ayuda_titulo" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
						<h4 class="modal-title" id="modal_ayuda_titulo">Instrucciones de uso</h4>
					</div>
					<div class="modal-body">
						<p>Este módulo permite registrar las sustituciones de profesores ausentes que hemos
						hecho en su aula (guardias de aula). Aparece de entrada el número de sustituciones
						de todos los miembros del Equipo de Guardia. Si hacemos clic sobre el nombre de un
						Compañero de guardia aparecen en la parte inferior de la página las sustituciones que
						ha realizado ese profesor. Hay que tener en cuenta que en la selección de profesores
						a sustituir sólo aparecen los profesores que tienen hora lectiva en ese momento
						según el horario importado en la Intranet.</p>	
						<p>Al registrar una sustitución cualquier compañero de la Guardia, aparece señalada
						en la parte superior de la página de tal modo que todos los compañeros puedan ver
						quién ha sustituido a quien en un aula durante esa hora.</p>
						<p>El archivo de configuración que se encuentra en el directorio intranet/admin/guardias/ permite elegir entre presentar opciones de guardias de media hora o sólo hora completa. La opción por defecto presenta la posibiidad de elegir entre media y hora completa. Edítalo para ocultar el desplegable y forzar las guardias de hora completa.</p>
						<p>Las sustituciones sólo pueden registrarse hasta dos días después de realizarse.
						Si nos olvidamos de hacerlo, tendremos que pedir al Equipo Directivo que nos la
						registren.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Entendido</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
	<div class="row">
		
		<div class="col-sm-6">
			
			<div class="well">
			
				<form method="post" action="procesa_guardias.php">
					
					<fieldset>
						<legend>Información de la guardia</legend>
						
						<input type="hidden" name="profesor" value="<?php echo nomprofesor($pr); ?>">
						<input type="hidden" name="diasdif" value="<?php echo $diasdif; ?>">
						
						<div class="row">
						
							<div class="col-sm-7">
								
								<div class="form-group">
									<label for="fecha_guardia">Fecha</label>
									<input type="hidden" name="fecha_guardia" value="<?php echo strftime("%d-%m-%Y", strtotime($fechaselec)); ?>">
									<p class="form-control text-info"><?php echo strftime("%A, %e de %B de %Y", strtotime($fechaselec)); ?></p>
								</div>
							
							</div>
							
							<div class="col-sm-5">
							
								<div class="form-group">
									<label for="hora">Hora</label>
									<input type="hidden" name="hora_guardia" value="<?php echo $hora; ?>">
									<p class="form-control text-info"><?php echo $hora; ?>ª hora</p>
								</div>
							
							</div>
						
						</div>
						
						<div class="form-group">
							<label for="fecha">Profesor/a ausente</label>
							<select class="form-control" id="ausente" name="ausente">
								<option value=""></option>
								<?php $result = mysqli_query($db_con, "SELECT DISTINCT prof FROM horw WHERE dia = '$diasem' AND hora = '$hora' AND (c_asig <> '25' OR a_grupo LIKE 'GUCON%') AND a_grupo NOT LIKE '' ORDER BY prof ASC");?>
								<?php while ($row = mysqli_fetch_array($result)): ?>
								<option value="<?php echo nomprofesor($row['prof']); ?>" <?php echo ($row['prof'] == $ausente) ? 'selected' : ''; ?>><?php echo nomprofesor($row['prof']); ?></option>
								<?php endwhile; ?>
							</select>
						</div>
						
						<?php $array_turnos = array(1 => 'Hora completa', 2 => '1ª media hora', 3 => '2ª media hora'); ?>
						
						<div class="form-group">
							<label class="turno_guardia">Turno:</label> 
							<select	class="form-control" id="turno_guardia" name="turno_guardia" required>
								<?php if (isset($config['guardias']['media_hora']) && $config['guardias']['media_hora'] == 1): ?>
								<?php for ($i = 1; $i < count($array_turnos)+1; $i++): ?>
								<option value="<?php echo $i; ?>"><?php echo $array_turnos[$i]; ?></option>
								<?php endfor; ?>
								<?php else: ?>
								<option value="<?php echo 1; ?>"><?php echo $array_turnos[1]; ?></option>
								<?php endif; ?>
							</select>
						</div>
						
						<button class="btn btn-primary" type="submit" name="submit">Registrar guardia</button>
						<a href="//<?php echo $config['dominio']; ?>/intranet/index.php" class="btn btn-default">Cancelar</a>
						
					</fieldset>
				
				</form>
				
			</div><!-- /.well -->
		
		</div><!-- /.col-sm-6 -->
		
		
		<div class="col-sm-6">
			
			<h4>Sustituciones realizadas durante la <?php echo $hora; ?>ª hora del <?php echo strftime("%A", strtotime($fechaselec)); ?></h4>
			
			<?php $result = mysqli_query($db_con, "SELECT prof FROM horw WHERE dia = '$diasem' AND hora = '$hora' AND c_asig = '25' AND a_asig <> 'GUCON' ORDER BY prof ASC"); ?>
			<?php if (mysqli_num_rows($result)): ?>
			
			<div class="table-responsive">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Profesor/a de guardia</th>
							<th>Total</th>
							<th>Unidades</th>
						</tr>
					</thead>
					<tbody>
						<?php $i = 1; ?>
						<?php while ($row = mysqli_fetch_array($result)): ?>
						<?php $result2 = mysqli_query($db_con, "SELECT turno FROM guardias WHERE profesor = '".$row['prof']."' AND dia = '$diasem' AND hora = '$hora'"); ?>
						<?php
						$cont = 0;
						while ($row2 = mysqli_fetch_array($result2)) {
							if ($row2['turno'] == 2) $cont = $cont + 0.5;
							elseif ($row2['turno'] == 3 ) $cont = $cont + 0.5;
							else $cont = $cont + 1;
						}
						?>
						<?php $result3 = mysqli_query($db_con, "SELECT distinct profe_aula, dia, hora FROM guardias WHERE profesor = '".$row['prof']."' and dia = '$diasem' and hora = '$hora'");?>
						<?php
						$unidad_gu="";
						while($row3 = mysqli_fetch_array($result3)){						
							$result4 = mysqli_query($db_con, "SELECT distinct a_grupo FROM horw_faltas WHERE prof = '$row3[0]' and dia = '$diasem' and hora = '$hora'");
							//echo "SELECT distinct a_grupo FROM horw_faltas WHERE prof = '$row3[0]' and dia = '$row3[1]' and hora = '$row3[2]'<br>";
							while ($row4 = mysqli_fetch_array($result4)) {
								$unidad_gu.="$row4[0], ";
								//break;
							}
							$unidad_gu = substr($unidad_gu,0,-2);
						}
						
						?>
						<tr>
							<td><a href="consulta_profesores.php?diasem=<?php echo $diasem; ?>&hora=<?php echo $hora; ?>&profesor=<?php echo nomprofesor($row['prof']); ?>"><?php echo nomprofesor($row['prof']); ?></a></td>
							<td><?php echo $cont; ?></td>
							<td><?php echo $unidad_gu; ?></td>
						</tr>
						<?php $i++; ?>
						<?php endwhile; ?>
						<?php unset($i); ?>
					</tbody>
				</table>
			</div>
						
			<?php else: ?>
			
			<br><br>
			<p class="text-lead text-center text-muted">No se han encontrado profesores de guardia.</p>
			<br><br>
			
			<?php endif; ?>
			
		</div><!-- /.col-sm-6 -->
		
	</div><!-- /.row -->
	
</div><!-- /.container -->
	
	<?php include("../../pie.php"); ?>
	
</body>
</html>
