<?php
require('../bootstrap.php');


if(!(stristr($_SESSION['cargo'],'1') == TRUE))
{
header('Location:'.'http://'.$dominio.'/intranet/salir.php');
exit;	
}


include("../menu.php");
?>

<div class="container">
	
	<!-- TITULO DE LA PAGINA -->
	<div class="page-header">
		<h2>Administraci�n <small>Funciones, configuraci�n, importaci�n de datos,...</small></h2>
	</div>
	
	
	<div class="row">
		
		<!-- COLUMNA IZQUIERDA -->
		<div class="col-sm-4">
		
			<div class="well">
			<?php include("menu.php");?>
			</div>
			
		</div><!-- /.col-sm-4 -->
		
		
		<!-- COLUMNA DERECHA -->
		<div class="col-sm-8">
			
			<h3>Descripci�n de los m�dulos e instrucciones.</h3>
			
			<div class="text-justify">
			<p>	
			Esta es la pagina de Administraci�n de la Intranet y de las Bases de Datos de la misma. A continuaci�n siguen algunas explicaciones sobre la mayor�a de los m�dulos de gesti�n.</p>
			<hr>
			<p>La <strong>primera opci�n (<span class="text-info">Cambiar la Configuraci�n</span>)</strong> permite editar y modificar los datos de la configuraci�n que se crearon cuando se instal� la Intranet por primera vez.</p> 
			
			<hr>
			<p>	
			El <strong>segundo grupo de opciones (<span class="text-info">A Principio de curso...</span>)</strong> crea las tablas principales: Alumnos, Profesores, Asignaturas, Calificaciones y Horarios. Hay que tener a mano varios archivos que descargamos de Seneca y los programas generadores de horarios. </p>
			<ul>
			
			<li>Los <span class="text-info">Alumnos, Asignaturas y Sistemas de Calificaciones</span> se crean una sola vez a comienzo de curso, aunque luego podemos actualizarlos cuando queramos. En este proceso se crean las tablas de Alumnos y se les asigna un n�mero de aula. Tambi�n se generan dos archivos preparados para el Alta masiva de Alumnos y Profesores en Gesuser y en Moodle (los coloca en intranet/xml/jefe/TIC/), as� como la tabla de Allumnos con asignaturas pendientes. Necesitamos dos archivos de S�neca: 
			<ul>
			  <li>el de los alumnos. Lo descargamos desde S�neca. Alumnado --&gt; Alumnado --&gt; Alumnado del Centro --&gt; Aceptar (arriba a la derecha) --&gt; Exportar (arriba a la izquierda) --&gt; Exportar datos al formato: Texto plano. El archivo que se descarga se llama RelPerCen.txt</li>
			  <li>el de las evaluaciones. Se descarga de Seneca desde &quot;Intercambio de Informaci�n --&gt; Exportaci�n desde Seneca --&gt; Exportaci�n de Calificaciones&quot;. Arriba a la derecha hay un icono para crear un nuevo documento con los datos de las evaluaciones; seleccionar todos los grupos del Centro para una evaluaci�n (la primera vale, por ejemplo) y a�adirlos a la lista. Cuando hay�is terminado, pulsad en el icono de confirmaci�n y al cabo de un minuto volved a la p�gina de exportaci�n de calificaciones y ver�is que se ha generado un archivo comprimido que pod�is descargar. </li>
			</ul>
			</li>
			<li>Los <span class="text-info">Datos generales del Centro</span>. Este m�dulo se encarga de importar la relaci�n de <strong>cursos</strong> y <strong>unidades</strong> del Centro registrados en S�neca, as� como la relaci�n de <strong>materias</strong> que se imparten y <strong>actividades</strong> del personal docente. Se importar� tambi�n la relaci�n de <strong>dependencias</strong>, que se utilizar� para realizar reservas de aulas o consultar el horario de las mismas.</li>
			<li><span class="text-info">Los Departamentos</span>. Se descarga desde S�neca --&gt; Personal --&gt; Personal del centro  --&gt; Exportar (arriba a la izquierda) --&gt; Exportar datos al formato: Texto plano.</li>
			<li><span class="text-info">Los Horarios</span>. Este es el �nico archivo que no se descarga de S�neca, sino de los Programas generadores de Horarios. Requiere el archivo con extensi�n XML que se genera con el programa generador de horarios para subir los datos del Horario a S�neca. Este m�dulo tambi�n se encarga de preparar el archivo para exportar a S�neca que crean los programas de Horarios (Horw, etc.), evitando tener que registrar manualmente los horarios de cada profesor. La adaptaci�n que realiza este m�dulo es conveniente, ya que la compatibilidad con S�neca de los generadores de horarios tiene limitaciones (C�digo �nico de las asignaturas de Bachillerato, Diversificaci�n, etc.). Es necesario tener a mano el archivo en formato XML que se exporta desde Horw o cualquier otro generador de Horarios. La Intranet puede funcionar sin horarios, pero sus funciones quedan muy limitadas. Por esta raz�n conviene hacer lo posible por importar un horario dentro de la aplicaci�n. En caso de no poder hacerlo, todav�a nos queda la posibilidad de crear el horario dentro de la aplicaci�n con el m�dulo Profesores --&gt; Modificar / Crear Horarios.</li>			  
			<li><span class="text-info">Los Profesores</span>. Se descarga desde S�neca --&gt; Personal --&gt; Personal del centro --&gt; Unidades y Materias  --&gt; Exportar (arriba a la izquierda) --&gt; Exportar datos al formato: Texto plano.
			</li>
<li>Los <span class="text-info">D�as Festivos</span> y la <span class="text-info">Jornada Escolar</span> deben importarse incluso si no contamos con horario, porque varios m�dulos de la misma (Calendario, etc.) dependen de los datos.
</li>
<li><span class="text-info">Modificar ROF</span> permite ajustar la lista y tipo de Problemas de Convivencia al Reglamento de Organizaci�n y Funcionamiento que est� en vigor en nuestro Centro. </li>
<li><span class="text-info">El Sistema de Reservas</span> se configura bien en la p�gina de Administraci�n de la Intranet (<em>Configuraci�n del Sistema de Reservas</em>) o bien en la propia p�gina de las Reservas (<em>Men� --> Gesti�n del Sistema de Reservas --> Crear Tipos de Recursos</em>). Defines en primer lugar los <em>Tipos de Recurso</em> que vas abrir al sistema (Ordenadores TIC y Medios Audiovisuales aparecen por defecto). Una vez definidos los Tipos de Recursos, crea los <em>Elementos</em> (Carrito n� 1, Videoproyector n� 1, etc.) dentro de cada Tipo. Puedes a�adir una descripci�n o informaci�n m�s precisa en el campo <em>Observaciones</em>, y aparecer� bajo el nombre del recurso en la p�gina de entrada del m�dulo de reservas.
</li>
<li>La �ltima opci�n, <span class="text-info">Limpiar Horarios</span>, se debe ejecutar cuando los cambios en los horarios se han dado por terminados y estos se encuentran en perfecto estado en S�neca. Supone que hemos actualizado en primer lugar los profesores.</li>			
			</ul>
			</p>
			<hr>
			<p>
			    El <strong>tercer grupo (<span class="text-info">Actualizaci�n</span>)</strong> permite actualizar los datos de Alumnos, Profesores y Departamentos del Centro. Esta pensado para la actualizaci�n de los alumnos que se van matriculando a lo largo del Curso, as� como para la puesta al d�a de la lista de Profesores y Departamentos. Los archivos requeridos son los mismos que hemos mencionado m�s arriba.</p>
			    <hr>
			    <p>
			El <strong>cuarto grupo de opciones</strong> afecta a los <strong><span class="text-info">Profesores</span></strong>.<br> 
			 Una vez se han creado los Departamentos y Profesores, es necesario seleccionar los <span class="text-info">Perfiles de los Profesores</span> para que la aplicaci�n se ajuste a las funciones propias de cada profesor ( Tutor, Direcci�n, Jefe de Departamento, etc. ). <br>
			 Tambi�n desde aqu� se puede <span class="text-info">Restablecer las contrase�as</span> de los profesores que se hayan olvidado de la misma. Al restablecerla, el profesor deber� entrar de nuevo con el DNI como contrase�a, lo que le llevar� a la p�gina desde la que tendr� que cambiarla con los criterios de S�neca. <br>
			 <span class="text-info">Copiar datos de un profesor a otro</span> cambia los datos de un profesor que ha sido sustituido por los datos del profesor que lo sustituye, de tal manera que el nuevo profesor pueda entrar en la Intranet con los datos heredados del profesor sustitu�do.<br>
			 Podemos tambi�n <span class="text-info">Gestionar los Departamentos</span>, editar sus nombres, unirlos, y asignar profesores a los mismos. Es conveniente hacerlo a principio de curso.<br>
			 <span class="text-info">Subir fotos</span> nos permite realizar una descarga masiva de fotos de los profesores a la Intranet. Se comprimen todas las fotos en un archivo con la extensi�n <em>ZIP</em>. El nombre de la foto de un profesor se construye a partir del usuario IdEA ( <em>mgargon732.jpg</em>, por ejemplo). Si nuestro Servidor no permite subir un archivo de tanto tama�o, podemos crear varios archvos comprimidos m�s peque�os y subirlos uno a uno.<br>
			 <span class="text-info">Informe de accesos</span> ofrece informaci�n sobre el uso de la Intranet por parte de los Profesores.
			 </p>
			 <hr>
			 		 
			<p>El <strong>quinto grupo <span class="text-info">(Alumnos)</span></strong> toca asuntos varios relacionados con los mismos. </p>
			<ul>
			 <li><span class="text-info">Las Listas de Grupos</span>. Supone que se han realizado todas las tareas anteriores (Horario, Profesores, Alumnos, etc.). Presenta la lista de todos los Grupos del Centro en formato PDF, preparada para ser imprimida y entregada a los Profesores a principios de Curso. </li>
			<li><span class="text-info">Carnet de los Alumnos</span> genera los carnet de los alumnos del Centro preparados para ser imprimidos. Este m�dulo supone que se han subido las fotos de los alumnos a la intranet utilizando el enlace <span class="text-info">Subir fotos de alumnos</span>, a continuaci�n.</li>
			<li><span class="text-info">Subir fotos de alumnos</span> permite hacer una subida en bloque de todas las fotos de los alumnos para sean utilizadas por los distintos m�dulos de la Intranet. Para ello, necesitaremos crear un archivo comprimido ( .zip ) con todos los archivos de fotos de los alumnos. Cada archivo de foto tiene como nombre el NIE de S�neca (el N�mero de Identificaci�n que S�neca asigna a cada alumno ) seguido de la extensi�n <em>.jpg</em> o <em>.jpeg</em>. El nombre t�pico de un archivo de foto quedar�a por ejemplo as�: <em>1526530.jpg</em>. </li>
			  <li><span class="text-info">Libros de Texto gratuitos</span> es un conjunto de p�ginas pensadas para registrar el estado de los libros de cada alumno dentro del Programa de Ayudas al Estudio de la Junta, e imprimir los certificados correspondientes (incluidas las facturas en caso de mal estado o p�rdida del material).</li>
			    <li><span class="text-info">Matriculaci�n de alumnos</span> es un m�dulo que permite matricular a los alumnos a trav�s de la intranet o, en su caso, a trav�s de internet (si el m�dulo se ha incorporado a la p�gina principal del Centro). Los tutores, a final de curso, ayudan a los alumnos a matricularse en una sesi�n de tutor�a. Posteriormente el Centro imprime los formularios de la matr�cula y se los entregan a los alumnos para ser firmados por sus padres y entregados de nuevo en el IES. El Equipo directivo cuenta entonces con la ventaja de poder administrar los datos f�cilmente para formar los grupos de acuerdo a una gran variedad de criterios. El m�dulo incluye una p�gina que realiza previsiones de matriculaci�n de alumnos en las distintas evaluaciones.</li> 
			   <li> <span class="text-info">Informe de accesos</span> ofrece informaci�n sobre el uso del <em><b>Acceso para Alumnos</b></em> de la <b>P�gina p�blica del Centro</b> por parte de los Alumnos y sus Padres. Supone que la <em>P�gina del Centro</em> o su m�dulo de <em>Acceso para Alumnos</em> est� operativa. La P�gina del Centro puede ser descargada desde <a href="https://mgarcia39@github.com/IESMonterroso/pagina_centros" target="_blank">GitHub</a>. Posteriormente debemos leer el archivo README con informaci�n sobre la misma y editar el archivo de configuraci�n (<em>conf_principal.php</em>) para ajustarlo a nuestro Centro.</li>
			    </ul>
			<hr>
			
			<p>	
			El <strong>sexto grupo (<span class="text-info">Notas de Evaluaci�n</span>)</strong> crea y actualiza la tabla de las Notas de Evaluaci�n que aparecen en los Informes de la Intranet, pero tambi�n presenta las Calificaciones del alumno en la pagina principal. Los archivos necesarios se descargan de S�neca desde &quot;Intercambio de Informaci�n --&gt; Exportaci�n desde Seneca --&gt; Exportaci�n de Calificaciones&quot;.</p>
			<hr>
			
			<p>El <strong>�ltimo grupo <span class="text-info">(Base de datos)</span></strong> permite realizar copias de seguridad de las bases de datos que contienen los datos esenciales de la Intranet. La copia de seguridad crea un archivo, comprimido o en formato texto (SQL), en un directorio de la aplicaci�n ( /intranet/xml/jefe/copia_db/ ). Esta copia puede ser descargada una vez creada. Tambi�n podemos restaurar la copia de seguridad seleccionando el archivo que hemos creado anteriormente. </p>
			
		</div><!-- /.col-sm-8 -->
		
	</div><!-- /.col-sm-8 -->
		
	</div><!-- /.row-->

</div><!-- /.container -->


<?php include("../pie.php");?>  
</body>
</html>
