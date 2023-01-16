<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed'); 

if (isset($_GET['q'])) {$expresion = $_GET['q'];}elseif (isset($_POST['q'])) {$expresion = $_POST['q'];}else{$expresion="";}
?>
	<div class="container hidden-print">
		
		<!-- Button trigger modal -->
		<a href="#"class="btn btn-default btn-sm pull-right hidden-print" data-toggle="modal" data-target="#modalAyuda" style="margin-right: 5px;">
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
						<p>Este módulo permite a los miembros de cualquier equipo educativo crear documentos para las adaptaciones curriculares no significativas. Se ofrece una plantilla con indicaciones para su redacción, así como los distintos elementos que debe incluir una adaptación curricular del tipo mencionado.</p>
						<p>Seleccionamos el grupo, materia y alumno. Redactamos el documento y lo registramos en la base de datos. Puede ser imprimido generando un archivo en formato PDF. Podemos visualizar las adaptaciones que hemos elaborado en la tabla de la izquierda. Podemos editarlo, eliminarlo e imprimirlo.</p>
						<p>El profesor tiene permiso para ver sus adaptaciones; el jefe del departamento puede acceder a todas las adaptaciones de las materias de su departamento; el equipo directivo puede ver todas las adaptaciones de los alumnos, materias y departamentos.</p>
						<p>La ruta al entrar en Séneca para cumplimentar la propuesta curricular de una ACNS para un alumno/a que previamente ha debido crear el tutor/a es la siguiente: <em>Alumnado –> Gestión de la Orientación -> Medidas Específicas (alumnado NEAE) -> Adaptación Curricular No Significativa ->  Selecciono el curso académico correspondiente –> En el menú que se despliega selecciono “Apartados” -> Selecciono la propuesta curricular de mi asignatura, pulso detalle, cumplimento todos los apartados –> Pulso validar</em>.</p> 
						<p>Documentaci&oacute;n adjunta: Se puede adjuntar alg&uacute;n documento si se desea.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Entendido</button>
					</div>
				</div>
			</div>
		</div>
		
  	 	<ul class="nav nav-tabs">
 			<li<?php echo (strstr($_SERVER['REQUEST_URI'],'index.php')==TRUE) ? ' class="active"' : ''; ?>><a href="index.php">Registrar o consultar ACNS</a></li>	
          	<li<?php echo (strstr($_SERVER['REQUEST_URI'],'administracion.php')==TRUE) ? ' class="active"' : ''; ?>><a href="administracion.php">Administrar ACNS</a></li>
		</ul>
	</div>