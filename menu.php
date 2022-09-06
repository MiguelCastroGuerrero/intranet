<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed');

if (isset($_POST['submit_tarea']) && isset($_POST['id_tarea'])) {
	$menu_idtarea = limpiarInput($_POST['id_tarea'], 'numeric');
	$menu_result_tarea = mysqli_query($db_con, "UPDATE tareas SET estado = 1 WHERE id = $menu_idtarea LIMIT 1");
	unset($menu_idtarea);
}

// FEED RSS
function obtenerNovedadesConsejeria() {
	$titulo_feed = '';
	$rss_novedades = array();
	$numero_novedades = 5;

	$feed = new SimplePie();

	$feed->set_timeout(5);
	$feed->set_feed_url("https://www.juntadeandalucia.es/educacion/portals/delegate/rss/ced/portalconsejeria/-/-/-/true/OR/true/ishare_noticefrom/DESC/");
	$feed->set_output_encoding('UTF-8');
	$feed->enable_cache(false);
	$feed->init();
	$feed->handle_content_type();

	$titulo_feed = ($feed->get_title()) ? $feed->get_title() : 'Novedades - Consejería Educación';

	for ($x = 0; $x < $feed->get_item_quantity($numero_novedades); $x++) {
		array_push($rss_novedades, $feed->get_item($x));
	}

	return array(
		'titulo' 	=> $titulo_feed,
		'contenido' => $rss_novedades
	);
}
$novedadesConsejeria = obtenerNovedadesConsejeria();

?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Intranet del <?php echo $config['centro_denominacion']; ?>">
	<meta name="author" content="IESMonterroso (https://github.com/IESMonterroso/intranet/)">
	<meta name="robots" content="noindex, nofollow">

	<title>Intranet &middot; <?php echo $config['centro_denominacion']; ?></title>

	<!-- BOOTSTRAP CSS CORE -->
	<link href="//<?php echo $config['dominio']; ?>/intranet/css/<?php echo (isset($_SESSION['tema'])) ? $_SESSION['tema'] : 'bootstrap.min.css'; ?>" rel="stylesheet">

	<!-- CUSTOM CSS THEME -->
	<link href="//<?php echo $config['dominio']; ?>/intranet/css/animate.css" rel="stylesheet">
	<link href="//<?php echo $config['dominio']; ?>/intranet/css/otros.css" rel="stylesheet">

	<!-- PLUGINS CSS -->
	<link href="//<?php echo $config['dominio']; ?>/intranet/vendor/fontawesome-free-5.13.0-web/css/all.css" rel="stylesheet">
	<link href="//<?php echo $config['dominio']; ?>/intranet/js/datetimepicker/bootstrap-datetimepicker.css" rel="stylesheet">
	<?php if(isset($PLUGIN_DATATABLES) && $PLUGIN_DATATABLES): ?>
	<link href="//<?php echo $config['dominio']; ?>/intranet/js/datatables/dataTables.bootstrap.min.css" rel="stylesheet">
	<?php endif; ?>
	<?php if(isset($PLUGIN_COLORPICKER) && $PLUGIN_COLORPICKER): ?>
	<link href="//<?php echo $config['dominio']; ?>/intranet/js/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
	<?php endif; ?>
	<?php if(isset($_GET['tour']) && $_GET['tour']): ?>
	<link href="//<?php echo $config['dominio']; ?>/intranet/js/bootstrap-tour/bootstrap-tour.min.css" rel="stylesheet">
	<?php endif; ?>
</head>

<body>

	<nav id="topmenu" class="navbar <?php echo (isset($_SESSION['fondo'])) ? $_SESSION['fondo'] : 'navbar-default'; ?> navbar-fixed-top hidden-print" role="navigation">
		<div class="container-fluid">

			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar"><span class="sr-only">Cambiar navegación</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<a class="navbar-brand" href="//<?php echo $config['dominio']; ?>/intranet/"><?php echo $config['centro_denominacion']; ?></a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="navbar">
				<ul class="nav navbar-nav">
					<!-- MENÚ DE USUARIO -->
					<li class="dropdown" id="bs-tour-usermenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<span class="far fa-user-circle fa-fw fa-lg"></span> <small><?php echo $pr; ?></small> <b class="caret"></b> <span id="sesionMenuTiempo" class="label label-default hidden" style="margin-left: 10px;"></span></a>

						<ul class="dropdown-menu" style="min-width: 340px !important;">
							<li class="hidden-xs">
								<div style="padding: 3px 20px;">
									<?php if(isset($_SESSION['user_admin']) && $_SESSION['user_admin']): ?>
									<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
										<div class="form-group" style="margin-bottom: 0;">
											<select class="form-control input-sm" id="view_as_user" name="view_as_user" onchange="submit()">
												<?php $result_perfiles = mysqli_query($db_con, "SELECT nombre, idea FROM departamentos ORDER BY nombre ASC"); ?>
												<?php while($row_perfiles = mysqli_fetch_assoc($result_perfiles)): ?>
												<option value="<?php echo $row_perfiles['nombre']; ?>"<?php echo ($row_perfiles['nombre'] == $_SESSION['profi']) ? ' selected' : ''; ?>><?php echo $row_perfiles['nombre']; ?></option>
												<?php endwhile; ?>
												<?php mysqli_free_result($result_perfiles); ?>
											</select>
										</div>
									</form>
									<?php else: ?>
									<span style="font-weight: bold;"><?php echo $pr; ?></span><br>
									<?php endif; ?>
									<small class="text-muted">
										Último acceso:
										<?php
										$time = mysqli_query($db_con, "select fecha from reg_intranet where profesor = '".$idea."' order by fecha desc limit 2");
										$num = 0;
										while($last = mysqli_fetch_array($time)) {
											$num+=1;

											if($num == 2) {
												echo strftime('%A, %e %B, %H:%M', strtotime($last['fecha']));
											}
										}
										?>
									</small>
									<div class="clearfix"></div>
								</div>
							</li>
							<li class="divider hidden-xs"></li>
							<li><a href="//<?php echo $config['dominio']; ?>/intranet/usuario.php"><i class="fas fa-user fa-fw"></i> Información de la cuenta</a></li>
							<li><a href="//<?php echo $config['dominio']; ?>/intranet/logout.php"><i class="fas fa-sign-out-alt fa-fw"></i> Cerrar sesión</a></li>
						</ul>
					</li>
				</ul>

				<div class="navbar-right">
					<ul class="nav navbar-nav">

						<li class="hidden-xs"><a href="//<?php echo $config['dominio']; ?>/intranet/index.php" data-bs="tooltip" title="Inicio" data-placement="bottom" data-container="body"><i class="fas fa-home fa-fw fa-lg"></i></a></li>
						<li class="visible-xs"><a href="//<?php echo $config['dominio']; ?>/intranet/index.php" data-bs="tooltip" title="Inicio" data-placement="bottom" data-container="body"><i class="fas fa-home fa-fw fa-lg"></i> Inicio</a></li>
						<?php if (isset($config['mod_documentos']) && $config['mod_documentos']): ?>
						<li class="hidden-xs"><a href="//<?php echo $config['dominio']; ?>/intranet/documentos/" data-bs="tooltip" title="Documentos" data-placement="bottom" data-container="body"><i class="far fa-folder-open fa-fw fa-lg"></i></a></li>
						<li class="visible-xs"><a href="//<?php echo $config['dominio']; ?>/intranet/documentos/" data-bs="tooltip" title="Documentos" data-placement="bottom" data-container="body"><i class="far fa-folder-open fa-fw fa-lg"></i> Documentos</a></li>
						<?php endif; ?>

						<!-- TAREAS -->
						<?php $result_tareas = mysqli_query($db_con, "SELECT id, idea, titulo, tarea, estado, fechareg, prioridad FROM tareas WHERE idea = '".$idea."' AND estado = 0 ORDER BY prioridad ASC, fechareg DESC"); ?>
						<li class="visible-xs <?php echo (strstr($_SERVER['REQUEST_URI'],'intranet/tareas/')) ? 'active' : ''; ?>"><a href="//<?php echo $config['dominio']; ?>/intranet/tareas/index.php"><i class="fas fa-tasks fa-fw fa-lg"></i> Tareas</a></li>
						<li id="bs-tour-tareas" class="dropdown hidden-xs">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-bs="tooltip" title="Tareas pendientes" data-placement="bottom" data-container="body">
								<i class="fas fa-tasks fa-fw fa-lg <?php if(mysqli_num_rows($result_tareas)): ?>text-warning<?php endif; ?>"></i> <b class="caret"></b>
							</a>

							<ul class="dropdown-menu dropdown-messages">
								<li class="dropdown-header"><h5>Tareas pendientes</h5></li>
								<li class="divider"></li>
								<?php if(mysqli_num_rows($result_tareas)): ?>
								<?php while ($row_tareas = mysqli_fetch_array($result_tareas)): ?>
								<li>
									<a href="//<?php echo $config['dominio']; ?>/intranet/tareas/tarea.php?id=<?php echo $row_tareas['id']; ?>">
										<div class="row">
											<div class="col-sm-2">
												<form action="" method="post">
													<input type="hidden" name="id_tarea" value="<?php echo $row_tareas['id']; ?>">
													<button type="submit" name="submit_tarea" class="btn btn-sm btn-default"><span class="fas fa-check fa-fw"></span></button>
												</form>
											</div>
											<div class="col-sm-10">
												<div class="text-warning">
													<span class="pull-right text-muted"><em><?php echo strftime('%e %b',strtotime($row_tareas['fechareg'])); ?></em></span>
													<strong><?php echo substr(stripslashes($row_tareas['titulo']),0 , 96); ?></strong>
												</div>
												<div class="text-warning">
													<?php echo substr(stripslashes(strip_tags($row_tareas['tarea'])),0 , 96); ?>
												</div>
											</div>
										</div>
									</a>
								</li>
								<li class="divider"></li>
								<?php endwhile; ?>
								<?php mysqli_free_result($result_tareas); ?>
								<?php else: ?>
								<li><p class="text-center text-muted pad10">No tienes tareas pedientes.</p></li>
								<li class="divider"></li>
								<?php endif; ?>
								<li style="padding: 0 20px; font-size: 0.9em;">
									<div class="row">
										<div class="col-sm-6" style="border-right: 1px solid #dedede; padding: 11px 0 !important;">
											<a class="text-block text-center" href="//<?php echo $config['dominio']; ?>/intranet/tareas/" style="display: block;"><strong>Ver tareas</strong></a>
										</div>
										<div class="col-sm-6" style="padding: 11px 0 !important;">
											<a class="text-center" href="//<?php echo $config['dominio']; ?>/intranet/tareas/tarea.php" style="display: block;"><strong>Crear tarea</strong></a>
										</div>
									</div>
								</li>
							</ul>
						</li>

						<!-- MENSAJES -->
						<?php $result_mens = mysqli_query($db_con, "SELECT ahora, asunto, id, id_profe, recibidoprofe, texto, origen FROM mens_profes, mens_texto WHERE mens_texto.id = mens_profes.id_texto AND profesor='".$_SESSION['ide']."' AND recibidoprofe = 0 ORDER BY ahora DESC LIMIT 0, 5"); ?>
						<li class="visible-xs <?php echo (strstr($_SERVER['REQUEST_URI'],'intranet/admin/mensajes/')) ? 'active' : ''; ?>"><a href="//<?php echo $config['dominio']; ?>/intranet/admin/mensajes/index.php"><i class="far fa-envelope fa-fw fa-lg"></i> Mensajes</a></li>
						<li id="bs-tour-mensajes" class="dropdown hidden-xs">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-bs="tooltip" title="Mensajes recibidos" data-placement="bottom" data-container="body">
								<i id="icono_notificacion_mensajes" class="far fa-envelope fa-fw fa-lg <?php echo (mysqli_num_rows($result_mens)) ? 'text-warning' : ''; ?>"></i> <b class="caret"></b>
							</a>

							<ul class="dropdown-menu dropdown-messages">
								<li class="dropdown-header"><h5>Últimos mensajes</h5></li>
								<li class="divider"></li>
								<?php mysqli_free_result($result_mens); ?>
								<?php $result_mens = mysqli_query($db_con, "SELECT ahora, asunto, id, id_profe, recibidoprofe, texto, origen FROM mens_profes, mens_texto WHERE mens_texto.id = mens_profes.id_texto AND profesor='".$_SESSION['ide']."' ORDER BY ahora DESC LIMIT 0, 5"); ?>
								<?php if (mysqli_num_rows($result_mens)): ?>
								<?php while ($row_mens = mysqli_fetch_array($result_mens)): ?>
								<li id="menu_mensaje_<?php echo $row_mens['id_profe']; ?>">
									<a href="//<?php echo $config['dominio']; ?>/intranet/admin/mensajes/mensaje.php?id=<?php echo $row_mens['id']; ?>&idprof=<?php echo $row_mens['id_profe']; ?>">
										<div <?php echo ($row_mens['recibidoprofe']==0) ? 'class="text-warning"' : ''; ?>>
										<?php $result_dest = mysqli_query($db_con, "SELECT nombre FROM departamentos WHERE idea='".$row_mens['origen']."' LIMIT 1"); ?>
										<?php $row_dest = mysqli_fetch_array($result_dest); ?>
											<span class="pull-right text-muted"><em><?php echo strftime('%e %b',strtotime($row_mens['ahora'])); ?></em></span>
											<strong><?php echo nomprofesor($row_dest['nombre']); ?></strong>
										</div>
										<div <?php echo ($row_mens['recibidoprofe']==0) ? 'class="text-warning"' : ''; ?>>
											<?php echo substr(stripslashes($row_mens['asunto']),0 , 96); ?>
										</div>
									</a>
								</li>
								<li class="divider"></li>
								<?php endwhile; ?>
								<?php mysqli_free_result($result_mens); ?>
								<?php else: ?>
								<li><p class="text-center text-muted">No tienes mensajes pendientes.</p></li>
								<li class="divider"></li>
								<?php endif; ?>
								<li style="padding: 0 20px; font-size: 0.9em;">
									<div class="row">
										<div class="col-sm-6" style="border-right: 1px solid #dedede; padding: 11px 0 !important;">
											<a class="text-block text-center" href="//<?php echo $config['dominio']; ?>/intranet/admin/mensajes/" style="display: block;"><strong>Ver mensajes</strong></a>
										</div>
										<div class="col-sm-6" style="padding: 11px 0 !important;">
											<a class="text-center" href="//<?php echo $config['dominio']; ?>/intranet/admin/mensajes/redactar.php" style="display: block;"><strong>Redactar</strong></a>
										</div>
									</div>
								</li>
							</ul>
						</li>

						<!-- CONSEJERIA DE EDUCACION -->
						<li class="visible-xs"><a href="https://www.juntadeandalucia.es/educacion/portals/web/ced"><i class="fas fa-rss fa-rotate-270 fa-fw fa-lg"></i> Consejería de Desarrollo Educativo y Formación Profesional</a></li>
						<li id="bs-tour-consejeria" class="dropdown hidden-xs">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-bs="tooltip" title="Consejería de Desarrollo Educativo y Formación Profesional" data-placement="bottom" data-container="body">
								<i class="icon-junta fa-fw fa-lg"></i> <b class="caret"></b>
							</a>

							<ul class="dropdown-menu dropdown-feed">
								<li class="dropdown-header"><h5><?php echo $novedadesConsejeria['titulo']; ?></h5></li>
								<li class="divider"></li>
								<?php if (count($novedadesConsejeria['contenido'])): ?>
								<?php foreach ($novedadesConsejeria['contenido'] as $item): ?>
								<li>
									<a href="<?php echo $item->get_permalink(); ?>" target="_blank">
										<span class="pull-right text-muted"><em><?php echo strftime('%e %b',strtotime($item->get_date('j M Y, g:i a'))); ?></em></span>
										<?php echo substr($item->get_title(), 13); ?>
									</a>
								</li>
								<li class="divider"></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><p class="text-center text-muted">Este módulo no está disponible en estos momentos. Disculpe las molestias.</p></li>
								<li class="divider"></li>
								<?php endif; ?>
								<li style="padding: 0 20px; font-size: 0.9em;">
									<div class="row">
										<div class="col-sm-6" style="border-right: 1px solid #dedede; padding: 11px 0 !important;">
											<a class="text-center" href="http://www.juntadeandalucia.es/educacion/portals/web/ced" target="_blank" style="display: block;"><strong>Ver novedades</strong></a>
										</div>
										<div class="col-sm-6" style="padding: 11px 0 !important;">
											<a class="text-center" href="https://www.juntadeandalucia.es/educacion/senecav2/seneca/jsp/portal/" target="_blank" style="display: block;"><strong>Portal Séneca</strong></a>
										</div>
									</div>
								</li>
							</ul>
						</li>

						<li class="hidden-xs"><a href="//<?php echo $config['dominio']; ?>/intranet/logout.php" data-bs="tooltip" title="Cerrar sesión" data-placement="bottom" data-container="body"><i class="fas fa-sign-out-alt fa-fw fa-lg"></i></a></li>
						<li class="visible-xs"><a href="//<?php echo $config['dominio']; ?>/intranet/logout.php" data-bs="tooltip" title="Cerrar sesión" data-placement="bottom" data-container="body"><i class="fas fa-sign-out-alt fa-fw fa-lg"></i> Cerrar sesión</a></li>

					</ul>

				</div><!-- /.navbar-right -->

			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

	<?php unset($foto_usuario); ?>
