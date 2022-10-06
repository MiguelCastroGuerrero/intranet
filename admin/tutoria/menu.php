<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed'); ?>

<div class="container hidden-print">
	
	<?php if (acl_permiso($carg, array('1'))): ?>
	<a href="preferencias.php" class="btn btn-sm btn-default pull-right"><span class="fas fa-cog fa-lg"></span></a>
	<?php endif; ?>
	
	<!-- Button trigger modal -->

	<a href="#"class="btn btn-default btn-sm pull-right hidden-print" data-toggle="modal" data-target="#modalAyuda" style="margin-left:25px; margin-right: 5px;">
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
					<p>Las páginas del tutor presentan información específica sobre los alumnos de su tutoría.<br>
					El menú superior abre las distintas opciones que se le presentan al tutor. 
					La página principal (<b>Resumen</b>) contiene datos sobre los aspectos más relevantes de la vida escolar de los alumnos: Faltas de Asistencia, Problemas de Conducta, Actividades Extraescolares, Informes de Tutoría y de Tareas por ausencia, intervenciones de tutoría, Devolución de libros a la Biblioteca, etc. También presenta una lista de los mensajes de Padres/Alumnos y acceso a su página de estos si el Centro dispone de ese servicio.</p>						
					<p>Las <b>Intervenciones de Tutoría</b> permiten mantener un diaro en el que el Tutor registra sus intervenciones sobre alumnos de su grupo o sus padres. La página presenta el formulario de intervenciones y una lista con todas las intervenciones realizadas ordenadas por fecha. Al hacer clic sobre un alumno de esta lista, se visualiza la intervención en el formulario (pudiendo editarla, borrarla, etc.) y aparece el historial de las intervenciones sobre el alumno bajo el formulario. La Intervención va asociada a una <em>causa (Evolución académica, Faltas de asistencia, etc.)</em> y a un <em>tipo (Entrevista telefónica o personal, etc.)</em> y </p>
					<p></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Entendido</button>
				</div>
			</div>
		</div>
	</div>
		
	<?php if (strstr($_SESSION['cargo'],'1') == TRUE || strstr($_SESSION['cargo'],'8') == TRUE): ?>
		
<form method="post" action="">
<div class="pull-right"><?php $result = mysqli_query($db_con, "SELECT DISTINCT FTUTORES.unidad, tutor, curso FROM FTUTORES, alma where alma.unidad=FTUTORES.unidad ORDER BY FTUTORES.unidad ASC"); ?>
<?php if(mysqli_num_rows($result)): ?> <select
	class="form-control input-sm" id="tutor" name="tutor"
	onchange="submit()" style="width:280px;">
	<?php while($row = mysqli_fetch_array($result)): $curso_tutor=$row[2];?>
	<option value="<?php echo $row['tutor'].' ==> '.$row['unidad']; ?>"
	<?php echo ($_SESSION['mod_tutoria']['tutor'].' ==> '.$_SESSION['mod_tutoria']['unidad'] == $row['tutor'].' ==> '.$row['unidad']) ? 'selected' : ''; ?>><?php echo $row['unidad'].' - '.nomprofesor($row['tutor']); ?></option>
	<?php endwhile; ?>
</select> <?php else: ?> <select class="form-control" id="tutor"
	name="tutor" disabled>
	<option value=""></option>
</select> <?php endif; ?> <?php mysqli_free_result($result); ?></div>
</form>
	<?php endif; ?>
<?php
$result2 = mysqli_query($db_con, "SELECT DISTINCT curso FROM alma where unidad= '".$_SESSION['mod_tutoria']['unidad']."'");
$query2= mysqli_fetch_array($result2);
$curso_tutor=$query2[0]; 
?>
<ul class="nav nav-tabs hidden-print">
	<li
	<?php echo (strstr($_SERVER['REQUEST_URI'],'index.php')==TRUE) ? ' class="active"' : ''; ?>><a
		href="index.php">Resumen</a></li>
	<li
	<?php echo (strstr($_SERVER['REQUEST_URI'],'intervencion.php')==TRUE) ? ' class="active"' : ''; ?>><a
		href="intervencion.php">Intervenciones</a></li>
		<li	class="dropdown<?php echo (strstr($_SERVER['REQUEST_URI'],'Tutor')==TRUE) ? ' active' : ''; ?>">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#"> Menú de Tutoría <span
		class="caret"></span> </a>
	<ul class="dropdown-menu" role="menu">
		<li><a href="../datos/datos.php?unidad=<?php echo $_SESSION['mod_tutoria']['unidad'] ?>">Datos de alumnos/as</a></li>
		<li><a href="../cursos/listados.php?unidad=<?php echo $_SESSION['mod_tutoria']['unidad']; ?>" target="_blank">Listado de alumnos/as</a></li>
		<?php if ($config['mod_matriculacion']==1 and (stristr($curso_tutor,"E.S.O")==TRUE or stristr($curso_tutor,"Bach")==TRUE)): 
		if(stristr($curso_tutor,"E.S.O")==TRUE){ $nivel_grupo = substr($curso_tutor,0,1)."ESO"; $pag_matr = "consultas.php";}
		if(stristr($curso_tutor,"Bach")==TRUE){ $nivel_grupo = substr($curso_tutor,0,1)."BACH"; $pag_matr = "consultas_bach.php";}
		$letra_un = explode("-",$_SESSION['mod_tutoria']['unidad']);
		$letra_unidad = $letra_un[1];
		?>	
		<li><a href="../matriculas/<?php echo $pag_matr;?>?curso=<?php echo $nivel_grupo;?>&grupo_actual=<?php echo $letra_unidad;?>&listado_total=1&consulta=1" target="_blank">Lista de materias en la matrícula</a></li>	
		<li><a href="matriculas.php">Datos importantes de la Matrícula</a></li>	
		<?php endif; ?>
		<?php if (isset($config['mod_sms']) && $config['mod_sms']): ?>
		<li><a href="../../sms/alumnado.php">Enviar SMS</a></li>	
		<?php endif; ?>
		<?php if (isset($config['mod_asistencia']) && $config['mod_asistencia']): ?>
		<li><a href="../../faltas/justificar/index.php">Justificar Faltas de Asistencia del Grupo</a></li>	
		<?php endif; ?>
		<li><a href="consulta_fotografias.php">Fotografías de alumnos/as</a></li>
		<li><a href="consulta_mesas.php">Asignación de mesas</a></li>
		<li><a href="../../xml/jefe/form_carnet.php">Credenciales de alumnos</a></li>
		<li><a href="consulta_absentismo.php">Alumnos absentistas</a></li>
		<li class="divider"></li>
		<li><a href="../../admin/actividades/indexextra.php">Actividades Complementarias / Extraescolares</a></li>
		<?php if($_SERVER['SERVER_NAME']=="iesmonterroso.org"): ?>
		<li class="divider"></li>
		<?php endif; ?>
		<?php if($config['centro_provincia']=="Málaga"): ?>
		<li class="divider"></li>
		<li><a href="https://www.educacionenmalaga.es/orientamalaga/plan-provincial-2/" target="_blank">Plan Provincial contra el Absentismo Escolar</a></li>
		<?php endif; ?>
	</ul>
	</li>
	<li
		class="dropdown<?php echo (strstr($_SERVER['REQUEST_URI'],'informe_')==TRUE) ? ' active' : ''; ?>">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#"> Informes <span
		class="caret"></span> </a>
	<ul class="dropdown-menu" role="menu">
		<li><a href="../informes/cinforme.php?unidad=<?php echo $_SESSION['mod_tutoria']['unidad']; ?>">Informe de un alumno/a</a></li>
		<li><a href="../infotutoria/infotut.php?unidad=<?php echo $_SESSION['mod_tutoria']['unidad']; ?>">Informe de tutoría</a></li>
		<li><a href="../tareas/infotut.php?unidad=<?php echo $_SESSION['mod_tutoria']['unidad']; ?>">Informe de tareas</a></li>
		<li class="divider"></li>
		<li><a href="../evaluaciones/index.php">Actas de Evaluación</a></li>
		<?php 
		$inf_t = mysqli_query($db_con,"select idcurso from unidades where nomunidad = '".$_SESSION['mod_tutoria']['unidad']."'"); 
		$id_t = mysqli_fetch_array($inf_t);
		$id_curso = $id_t[0];
		?>
		<li><a href="../tutoria/informe_evaluaciones.php">Informes de Evaluación</a></li>
		<li class="divider"></li>
		<?php 
		if ($id_curso=="101140"):
		?>
		<li><a href="../matriculas/consulta_transito.php">Informes de Tránsito</a></li>
		<li class="divider"></li>
		<?php endif; ?>
		<li><a href="informe_memoria.php">Memoria de tutoría</a></li>
	</ul>
	</li>

	<?php if(strstr($curso_tutor,"E.S.O.")==TRUE and date('m') > '05' and date('m') < '10'): ?>
	<li><a
		href="../libros-texto/programa-gratuidad/index.php">Programa de Gratuidad</a></li>
	<?php endif; ?>
</ul>

</div>
