<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed'); ?>
<div class="container hidden-print">
	
	<ul class="nav nav-tabs">
		<?php if (acl_permiso($_SESSION['cargo'], array('1','4','9'))){ ?>
		<li role="presentation" class="dropdown">
	    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
	      Actas <span class="caret"></span>
	    </a>
		    <ul class="dropdown-menu">
		      <li<?php if (strstr($_SERVER['REQUEST_URI'],'departamento/actas/')==TRUE) echo ' class="active"'; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/departamento/actas/">Actas</a></li>
	          	<li<?php echo (strstr($_SERVER['REQUEST_URI'],'administracion.php')==TRUE) ? ' class="active"' : ''; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/departamento/actas/administracion.php">Administrar actas</a></li>
		    </ul>
	 	</li>
	 <?php } else { ?>
		<li<?php if (strstr($_SERVER['REQUEST_URI'],'admin/departamento/actas/')==TRUE) echo ' class="active"'; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/departamento/actas/">Actas</a></li>
	<?php } ?>
	<?php if (acl_permiso($_SESSION['cargo'], array(1, 2))){ ?>
		<li role="presentation" class="dropdown">
	    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
	      Libros de texto <span class="caret"></span>
	    </a>
		    <ul class="dropdown-menu">
				<li<?php echo ((strstr($_SERVER['REQUEST_URI'],'libros-texto/index.php') == true)) ? ' class="active"' : '' ; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/libros-texto/index.php">Libros de texto</a></li>
					
				<li<?php echo ((strstr($_SERVER['REQUEST_URI'],'libros-texto/programa-gratuidad/index.php') == true)) ? ' class="active"' : '' ; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/libros-texto/programa-gratuidad/index.php">Programa de Gratuidad</a></li>
			</ul>
	 	</li>
	 	<?php } else { ?>
	 	<li<?php echo ((strstr($_SERVER['REQUEST_URI'],'libros-texto/index.php') == true)) ? ' class="active"' : '' ; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/libros-texto/index.php">Libros de texto</a></li>
	 	<?php } ?>	
		<li<?php if (strstr($_SERVER['REQUEST_URI'],'memoria.php')==TRUE) echo ' class="active"'; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/departamento/memoria.php">Memoria</a></li>		
			
		<li<?php if (strstr($_SERVER['REQUEST_URI'],'actividades/indexextra.php')==TRUE) echo ' class="active"'; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/actividades/indexextra.php">Actividades extraescolares</a></li>
		<li<?php if (strstr($_SERVER['REQUEST_URI'],'adaptaciones/')==TRUE) echo ' class="active"'; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/departamento/adaptaciones/">Adaptaciones curriculares (ACNS)</a></li>
		<li<?php if (strstr($_SERVER['REQUEST_URI'],'refuerzo/')==TRUE) echo ' class="active"'; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/departamento/refuerzo/">Informes PRA</a></li>
		<li role="presentation" class="dropdown">
	    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
	      Material <span class="caret"></span>
	    </a>
		    <ul class="dropdown-menu">
				<?php if (acl_permiso($_SESSION['cargo'], array(1, 4))){ ?>
				<li<?php if (strstr($_SERVER['REQUEST_URI'],'/pedidos/')==TRUE) echo ' class="active"'; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/departamento/pedidos/">Pedidos de material</a></li>
				<?php } ?>
				<li<?php if (strstr($_SERVER['REQUEST_URI'],'seleccion_alumnos.php')==TRUE) echo ' class="active"'; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/cursos/seleccion_alumnos.php">Inventario <?php echo $_SESSSION['cargo']; ?></a></li>	
			</ul>
	 	</li>
	</ul>

</div>
