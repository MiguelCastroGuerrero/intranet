<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1));

if (file_exists('config.php')) {
	include('config.php');
}

include("../../menu.php");

if (isset($_POST['profesor'])) $profesor = $_POST['profesor']; else $profesor="";

include 'menu.php';
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
						<p>Selecciona el Profesor al que quieres apuntar una sustitución no registrada. Te 
						aparecerá el horario del Profesor, para que puedas determinar con precisión la hora 
						de la guardia (1ª hora, 2ª hora, etc) del día en cuestión.</p>
						<p>Seleccionas a continuación el Profesor sustituido. Al hacer clic en el campo de 
						la fecha, aparecerá una nueva ventana con el calendario en el que debes pinchar sobre 
						la fecha elegida. Escribe la hora de la guardia (1, 2, 3, etc) y envía los datos.</p>
						<p>Si quieres consultar el historial de guardias de un Profesor, pincha en 
						<em>Consultar guardias y profesores</em>. Selecciona el Profesor y aparecerá un 
						histórico con todas las sustituciones realizadas. Si pinchas en una de las guardias 
						de su horario, podrás ver las sustituciones de todos los profesores de esa guardia 
						en esa hora a lo largo del curso.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Entendido</button>
					</div>
				</div>
			</div>
		</div>
		
	</div>
	
	
	<?php if (! $config['mod_horarios']): ?>
	<div class="alert alert-warning">
		<h5>Módulo desactivado</h5>
		El módulo de Horarios debe ser activado en la Configuración general de la Intranet para poder acceder a estas páginas, y ahora mismo está desactivado.
	</div>
	<?php else: ?>
	
	<div class="row">
		
		<div class="col-sm-6">
		
			<div class="well">
				
				<form action="" method="post">
					
					<fieldset>
						<legend>Profesor/a de guardia</legend>
				
						<div class="form-group">
							<label for="profesor">Seleccione profesor/a</label> 
							<select class="form-control" id="profesor" name="profesor" onchange="submit()" required>
								<option value=""></option>
								<?php $result = mysqli_query($db_con, "SELECT DISTINCT prof FROM horw WHERE c_asig = '25' ORDER BY prof ASC"); ?>
								<?php while ($row = mysqli_fetch_array($result)): ?>
								<option value="<?php echo nomprofesor($row['prof']); ?>" <?php echo (nomprofesor($row['prof']) == nomprofesor($profesor)) ? 'selected' : ''; ?>><?php echo nomprofesor($row['prof']); ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					
					</fieldset>
					
				</form>
				
				<?php if (isset($profesor) && !empty($profesor)): ?>
				<?php include("inc_horario.php"); ?>
				<?php endif; ?>
				
			</div><!-- /.well -->
			
		</div><!-- /.col-sm-6 -->
		
		<div class="col-sm-6">
		
			<div class="well">
				
				<form action="procesa_guardias.php" method="post">
				
					<fieldset>
						<legend>Datos de la guardia</legend>
					
						<input type="hidden" id="profesorh" name="profesor" value="<?php echo $profesor; ?>">
						
						<div class="form-group">
							<label for="ausente">Profesor/a ausente</label>
							<select class="form-control" id="ausente" name="ausente" required>
								<option value=""></option>
							    <?php $result = mysqli_query($db_con, "SELECT DISTINCT prof FROM horw WHERE prof <>'".$profesor."' ORDER BY prof ASC"); ?>
								<?php while ($row = mysqli_fetch_array($result)): ?>
								<option value="<?php echo nomprofesor($row['prof']); ?>"><?php echo nomprofesor($row['prof']); ?></option>
								<?php endwhile; ?>
							</select>
					    </div>
					    
					    <div class="row">
					    
					    	<div class="col-md-6">
					    		
					    		<div class="form-group" id="datetimepicker1">     
					    			<label for="fecha_guardia">Fecha</label>
					    				<div class="input-group">
					    					<input type="text" class="form-control" id="fecha_guardia" name="fecha_guardia" value="<?php echo date('d-m-Y'); ?>" data-date-format="DD-MM-YYYY" required>
					    				<span class="input-group-addon"><span class="far fa-calendar"></span></span>
					    			</div>   
					    		</div>
					    		
					    	</div><!-- /.col-md-6 -->
					    	
					    	<div class="col-md-6">
					    		
					    		<div class="form-group">
					    			<label for="hora_guardia">Hora:</label> 
					    			<select class="form-control" id="hora_guardia" name="hora_guardia" required>
					    				<?php $result_tramos = mysqli_query($db_con, "SELECT `hora`, `hora_inicio`, `hora_fin` FROM `tramos` ORDER BY `horini` ASC"); ?>
					    				<?php $total_tramos = mysqli_num_rows($result_tramos); ?>
					    				<?php $i = 0; ?>
					    				<?php while ($tramo = mysqli_fetch_array($result_tramos)): ?>
					    				<?php $i++; ?>
					    				<?php if (strpos('R', $tramo['hora']) === false && strpos('Rn', $tramo['hora']) === false): ?>
					    				<option value="<?php echo $tramo['hora']; ?>" <?php echo ($i == $hora) ? 'selected' : ''; ?>><?php echo $tramo['hora']; ?>ª hora - De <?php echo substr($tramo['hora_inicio'], 0, 5); ?> a <?php echo substr($tramo['hora_fin'], 0, 5); ?></option><
					    				<?php endif; ?>
					    				<?php endwhile; ?>
					    			</select>
					    		</div>
					    		
					    	</div><!-- /.col-md-6 -->
					    
					    </div><!-- /.row -->
						
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
						
						<br>
			
						<button type="submit" name="submit" class="btn btn-primary">Enviar datos</button>
					
					</fieldset>
				</form>
		
			</div><!-- /.well -->
			
		</div><!-- /.col-sm-6 -->
		
	</div><!-- /.row -->
	
	<?php endif; ?>
	
</div><!-- /.container -->

	<?php include("../../pie.php"); ?>
	
<script>  
$(function ()  
{ 
	$('#datetimepicker1').datetimepicker({
		language: 'es',
		pickTime: false
	})
});  
</script>
	
</body>
</html>
