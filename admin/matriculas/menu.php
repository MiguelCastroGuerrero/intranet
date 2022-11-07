<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed');

if (isset($_GET['curso'])) {$curso = $_GET['curso'];}elseif (isset($_POST['curso'])) {$curso = $_POST['curso'];}else{$curso="";}
if (isset($_GET['dni'])) {$dni = $_GET['dni'];}elseif (isset($_POST['dni'])) {$dni = $_POST['dni'];}else{$dni="";}
if (isset($_GET['enviar'])) {$enviar = $_GET['enviar'];}elseif (isset($_POST['enviar'])) {$enviar = $_POST['enviar'];}else{$enviar="";}
if (isset($_GET['id'])) {$id = $_GET['id'];}elseif (isset($_POST['id'])) {$id = $_POST['id'];}else{$id="";}
if (isset($_GET['listados'])) {$listados = $_GET['listados'];}elseif (isset($_POST['listados'])) {$listados = $_POST['listados'];}else{$listados="";}
if (isset($_GET['listado_total'])) {$listado_total = $_GET['listado_total'];}elseif (isset($_POST['listado_total'])) {$listado_total = $_POST['listado_total'];}else{$listado_total="";}
if (isset($_GET['imprimir'])) {$imprimir = $_GET['imprimir'];}elseif (isset($_POST['imprimir'])) {$imprimir = $_POST['imprimir'];}else{$imprimir="";}
if (isset($_GET['cambios'])) {$cambios = $_GET['cambios'];}elseif (isset($_POST['cambios'])) {$cambios = $_POST['cambios'];}else{$cambios="";}
if (isset($_GET['sin_matricula'])) {$sin_matricula = $_GET['sin_matricula'];}elseif (isset($_POST['sin_matricula'])) {$sin_matricula = $_POST['sin_matricula'];}else{$sin_matricula="";}
?>

	<div class="container hidden-print">

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
						<p>Este módulo permite matricular a los alumnos a través de un formulario accesible desde internet. La matriculación puede realizarse a través de la propia intranet o bien desde la página pública del Centro. Este segundo método es el preferido para que los alumnos se registren masivamente, y la intranet se reserva para que la Dirección pueda matricular casos especiales (alumnos que no pertenecen al Centro, alumnos que se matriculan tarde, etc.). El módulo que permite el registro de datos desde la página pública está incluído en el código de nuestra página en este mismo repositorio <a href="https://github.com/IESMonterroso/pagina_centros">aquí</a>.</p>
						<br>
						<h4>Matricular alumnos</h4>
						<p>El módulo de la página pública se activa entre las fechas de inicio y fin que deben seleccionarse en los <em> <a href="preferencias.php">Ajustes y opciones del módulo</a></em>. El acceso al módulo aparece en el menú de los perfiles Dirección, Orientación y Administración durante los meses que van de Junio a Diciembre, aunque la Dirección siempre puede acceder desde <em>Administración de la Intranet</em>. Tanto la intranet como la página pública contienen un formulario de registro idéntico. Si el alumno pertenece al Centro aparecen en primer lugar los <em>datos personales y familiares</em> que ya se habían registrado en Séneca. Si el alumno pertenece a un CEIP o IES adscritos hay que pedir al Director del mismo que nos envíe una copia de la exportación de alumnos desde Séneca (Alumnado --&gt; Alumnado --&gt; Alumnado del Centro --&gt; Aceptar (arriba a la derecha) --&gt; Exportar (arriba a la izquierda) --&gt; Exportar datos al formato: Texto plano). Una vez tengamos los archivos de los centros adscritos debemos importarlos desde la página correspondiente en el menú <b>Herramientas</b>. Una vez comprobados los datos personales el alumno debe seleccionar las asignaturas optativas, actividades de refuerzo, religión, etc. Debe elegir las optativas y refuerzos tanto del siguiente curso como del mismo en el que ya se encuentra. De este modo, al importar las notas de la Evaluación Extraordinaria en Septiembre la matrícula se asigna automáticamente en función de si este ha promocionado de curso o no. <br>Cuando se alcanza la fecha de fin de matriculación ya podemos imprimir de forma masiva los formularios y autorizaciones de la matrícula para entregar a los alumnos.</p>
						<br>
						<h4>Método que seguimos para la matriculación.</h4>
						<p>Nosotros seguimos el siguiente método concreto durante el proceso. Durante el mes de Mayo se informa a los tutores de un nivel del proceso de registro de datos poniendo un ejemplo visual de un alumno. Los alumnos son informados por el tutor del proceso y se les recuerda el NIE. Durante las dos primeras semanas de Junio los alumnos se matriculan. Les dividimos en dos grupos: los de 1º y 2º de ESO realizan la matriculación en el aula con un carro de ordenadores portátiles o en las salas de Informática; los alumnos mayores lo hacen desde su casa. Los Directores de los Centros adscritos informan a los padres del proceso, y les ofrecen el NIE para se matriculen desde la página pública o pasen por nuestro Centro para que les registremos.<br>
						También puede solicitarse la matriculación a través de la Intranet. En este caso, debemos buscar el NIE del alumno si este pertenece a nuestro Centro; de lo contrario, debe introducirse el curso y el DNI de padres o alumno y proceder a rellenar todos los datos, tanto personales como académicos.
						<p>Cuando el registro masivo ha terminado, se imprimen los formularios. Los Centros adscritos reciben el conjunto de matrículas de sus alumnos; los tutores los distribuyen para que los nenes los lleven a casa; los padres firman el impreso y lo devuelven al Centro; el colegio o IES nos devuelve el conjunto de los impresos para que los administrativos puedan proceder a la matriculación en Séneca. <br>Nuestro alumnos reciben el formulario el mismo día en que se entregan las notas de la Evaluación Ordinaria y los libros de texto gratuitos, y entregan la matrícula firmada por sus padres en las fechas elegidas en la ventanilla de la administración para cada nivel.</p>
						<br>
						<h4>Consultas</h4>
						<p>Las consultas nos presentan los alumnos matriculados por Nivel. Los datos más importantes (optativas, refuerzos, bilinguismo, PMAR, etc) también aparecen. <em>Búsqueda avanzada</em> permite filtrar los datos bajo cualquier criterio, así como ordenar la tabla de múltiples maneras. <br>La estructura de la tabla es la siguiente:</p>
						<li>Cuando los administrativos o miembros de la dirección del Centro reciben el impreso ya firmado, pulsan sobre la casilla de verificación que aparece delante de cada alumno. Esto significa que el alumno ha completado su matriculación. </li>
						<li>El nombre del alumno contiene un enlace a la matrícula, o bien al <em>Informe de Tránsito</em> si se utiliza en el Centro.</li>
						<li>GR1 es el nombre del grupo del que procede el alumno; GR2 es la letra del grupo que le asignamos al alumno en el curso del que se ha matriculado. Introducimos la letra y enviamos los datos del formulario al final de la tabla.</li>
						<li>Si el Centro es bilingüe, puede marcar a los alumnos correspondientes.</li>
						<li>Si el alumno es de diversificación (PMAR), puede hacer lo mismo.</li>
						<li>Las optativas parecen normalmente en la forma de iniciales del nombre de la asignatura (Taller de Teatro aparece como TT). Los actividades de refuerzo o ampliación aparecen con el número de la selección. Al final de la tabla aparecen los números asociados a los nombres.</li>
						<li>La columna de Observaciones presenta iconos descriptivos de alguna anomalía que hay que considerar: bien el alumno presenta datos personales modificados que requieren cambios en Séneca; bien el alumno ha sido marcado por la Dirección como que NO promociona y sin embargo presenta notas en la evaluación extraordinaria que indican que SÍ promociona.</li>
						<li>La siguiente columna permite indicar si el alumno promociona o no promociona. Esta opción se marca automáticamente <b>cuando importamos las calificaciones y actualizamos los alumnos tras la Evaluación Extraordinaria de Septiembre</b>. Como Séneca introduce entonces los datos "Promociona" y "Repite" en el archivo de los alumnos, lass opciones SI y NO de la columna aparecen marcadas automáticamente en la tabla de consultas para los distintos niveles. Una vez comprobado que la columna de Promoción se ajusta al notón correcto ya podemos pulsar sobre el botón <b>Enviar datos</b> del formulario. Al procesarse la información eviada, los alumnos se recolocarán de acuerdo a si promocionan o no promocionana (en este caso el alumno repetidor volverá al curso que repite y desaparecerá del curso en el que se había matriculado).<br>
						Este proceso automatizado que sucede en septiembre puede activarse manualmente siendo nosotros los que seleccionemos a los alumnos repetidores. Simplemente marcamos el alumno en la opción NO y enviamos los datos: el alumno será devuelto al nivel anterior en el que se encontraba. La intervención manual tiene preferencia sobre la opción automatizada: si marco SI a un alumno PIL o marco NO a un alumno antes de septiembre la opción no puede ser anulada por Séneca.</li>
						<li>La columna Camb. indica si un alumno ha realizado cambios en la matrícula posteriores a la entrega (alumnos que cambian optativas a última hora, etc.). Pero es un campo personalizable que puede cumplir cualquier otra función que el Centro vea más importante.</li>
						<li>Cuando la matrícula que ha registrado un alumno sufre un cambio relevante, por ejemplo porque finalmente va a repetir curso y hay que moverlo de nivel, se crea una copia de seguridad en la tabla matriculas_backup y aparece el icono posible restauración de datos. Si hemos cometido un error y queremos recuperar los datos originales de la matrícula del alumno, sólo tenemos que pulsar sobre el icono y todo volverá a ser como la primera vez.</li>
						<li>La columna Conv. nos indica el número de problemas de conducta que se han registrado para un alumno a lo largo del último curso escolar.</li>
						<li>Otros muestra iconos descriptivos de datos personales de interés sobre un alumno: si el alumno tiene alguna enfermedad que los profesores deben conocer; si tenemos autorizaciónpara publicar sus fotos; si los padres están divorciados y a quién corresponde la guarda legal del nene, etc.</li>
						<p>Al final de la tabla hay un conjunto de botones para distintas funciones. <em>Imprimir</em> presenta en papel la matríccula y autorizaciones para entregar en la Administración del Centro. En septiembre también imprimimos las <em>Carátulas</em>, porque estas contienen datos que facilitan el trabajo a los administrativos. <em>Ver cambios en datos</em> nos muestra las diferencias en los datos personales que habrá que modificar en Séneca. <em>Alumnos sin matricular</em> nos dice qué alumnos por Nivel no se han registrado o bien lo han hecho pero no han entregado el formulario en el Centro. <em>Listado PDF total</em> sólo funciona cuando se han asignado grupos a los alumnos, y contiene datos sobre Religión, Optativas, etc. <em>Listado simple</em> nos muestra una lista de alumnos por grupo sencilla al modo de Séneca.</p>
						<br>
						<h4>Ajustes y Opciones del módulo</h4>
						<p>Para utilizar el módulo de matriculación es necesario precisar tanto las fechas de inicio y fin del proceso de matrciculación como el nombre de itinerarios, optativas y actividades en los distintos niveles.<br>
						<br>
						La segunda parte se ocupa de los nombres de asignaturas en los distintos niveles. Cada Centro debe escribir las optativas y actividades que se imparten en su IES. Se ha mantenido el conjunto de asignaturas del IES Monterroso como base para trabajar y como ejemplo para simplificar la tarea.
						</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Entendido</button>
					</div>
				</div>
			</div>
		</div>

		<?php if (acl_permiso($carg, array('1'))): ?>
		<a href="preferencias.php" class="btn btn-sm btn-default pull-right" style="margin-right:2px;"><span class="fas fa-cog fa-lg"></span></a>
		<?php endif; ?>

		<ul class="nav nav-tabs">
			<li<?php echo (strstr($_SERVER['REQUEST_URI'],'previsiones.php')==TRUE) ? ' class="active"' : ''; ?>><a href="previsiones.php">Previsiones de matrícula</a></li>
			<li class="dropdown<?php echo (strstr($_SERVER['REQUEST_URI'],'consultas')==TRUE) ? ' active' : ''; ?>">
			  <a class="dropdown-toggle" data-toggle="dropdown" href="#">
			    Consultas <span class="caret"></span>
			  </a>
			  <ul class="dropdown-menu" role="menu">
			  	<li><a href="consultas.php">Matriculas de ESO</a></li>
			    <li><a href="consultas_bach.php">Matriculas de Bachillerato</a></li>
			  </ul>
			</li>
			<li class="dropdown<?php echo (strstr($_SERVER['REQUEST_URI'],'index')==TRUE) ? ' active' : ''; ?>">
			  <a class="dropdown-toggle" data-toggle="dropdown" href="#">
			    Matriculación <span class="caret"></span>
			  </a>
			  <ul class="dropdown-menu" role="menu">
			  	<li><a href="index.php">Matricular en ESO</a></li>
			    <li><a href="index_bach.php">Matricular en Bachillerato</a></li>
			  </ul>
			</li>
			<li class="dropdown<?php echo (strstr($_SERVER['REQUEST_URI'],'importar')==TRUE) ? ' active' : ''; ?>">
			  <a class="dropdown-toggle" data-toggle="dropdown" href="#">
			    Herramientas <span class="caret"></span>
			  </a>
			  <ul class="dropdown-menu" role="menu">
			  	<li><a href="importar_primaria.php">Importar Alumnado de Primaria</a></li>
			  	<li><a href="index_secundaria.php">Importar Alumnado de ESO</a></li>
			  	<li><a href="activar.php">Activar matriculación</a></li>
			  </ul>
			</li>
			<?php if ($config['matriculas']['transito']==1) { ?>
				<li><a href="consulta_transito.php">Informes de Tránsito</a></li>
			<?php }	?>
		</ul>

	</div>
