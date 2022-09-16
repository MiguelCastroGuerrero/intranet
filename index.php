<?php
require('bootstrap.php');

// Acciones
if (isset($_GET['action'])) {
	$action = limpiarInput($_GET['action'], 'alpha');

	switch ($action) {
		// Reenvío de email de verificación
		case 'reenviarEmail':
			correoValidacion();
			$pendientes_reenviarEmail = 1;
			break;
		
		default:
			break;
	}
}

if ($_GET['resetea_mensaje']==1) {
	$idea_mensaje = limpiarInput($_GET['idea_mensaje'], 'alphanumeric');

	mysqli_query($db_con,"update mens_profes set recibidoprofe='1' where profesor='".$idea_mensaje."'");
}

include("menu.php");
?>

	<div class="container-fluid" style="padding-top: 15px;">

		<div class="row">

			<!-- COLUMNA IZQUIERDA -->
			<div class="col-md-3">

				<div id="bs-tour-menulateral">
				<?php include("menu_lateral.php"); ?>
				</div>

				<div id="bs-tour-ausencias">
				<?php include("admin/ausencias/widget_ausencias.php"); ?>
				</div>

				<div id="bs-tour-tareas-2">
				<?php include("tareas/widget_tareas.php"); ?>
				</div>

			</div><!-- /.col-md-3 -->


			<!-- COLUMNA CENTRAL -->
			<div class="col-md-5">

				<?php
				if (acl_permiso($carg, array('2'))) {
					if (file_exists('admin/tutoria/config.php')) {
						include('admin/tutoria/config.php');
					}
					include("admin/tutoria/inc_pendientes.php");
				}
				?>
				<div id="bs-tour-pendientes">
				<?php include ("pendientes.php"); ?>
				</div>

				<?php if (acl_permiso($carg, array('1'))): ?>
				<?php include ("estadisticas/inc_estadisticas_admin.php"); ?>
				<?php elseif (acl_permiso($carg, array('2'))): ?>
				<?php include ("estadisticas/inc_estadisticas_tutores.php"); ?>
				<?php endif; ?>

		        <div class="bs-module">
		        <?php include("admin/noticias/widget_noticias.php"); ?>
		        </div>

		        <br>

			</div><!-- /.col-md-5 -->



			<!-- COLUMNA DERECHA -->
			<div class="col-md-4">

				<div id="bs-tour-buscar">
				<?php include("buscar.php"); ?>
				</div>

				<br><br>

				<div id="bs-tour-calendario">
				<?php
				define('MOD_CALENDARIO', 1);
				include("calendario/widget_calendario.php");
				?>
				</div>

				<br><br>

				<?php if($config['mod_horarios'] && ($dpto !== "Admin" && $dpto !== "Administracion" && $dpto !== "Conserjeria" && $dpto !== "Servicio Técnico y/o Mantenimiento")): ?>

				<div id="bs-tour-horario">
					<h4><span class="far fa-clock fa-fw"></span> Horario</h4>
					<?php include("horario.php"); ?>
				</div>

				<?php elseif ($dpto == "Admin"): ?>

				<h4><span class="far fa-clock fa-fw"></span> Horario</h4>
				<div class="text-center">
					<a class="btn btn-sm btn-default" href="xml/jefe/horarios/index.php" style="margin-top:18px;">Crear/Modificar horario</a>
				</div>

				<?php endif; ?>

			</div><!-- /.col-md-4 -->

		</div><!-- /.row -->

	</div><!-- /.container-fluid -->

	<?php include("pie.php"); ?>

	<?php if (acl_permiso($carg, array('1'))): ?>
	<script src="//<?php echo $config['dominio'];?>/intranet/estadisticas/estadisticas_admin.js"></script>
	<?php elseif (acl_permiso($carg, array('2'))): ?>
	<script src="//<?php echo $config['dominio'];?>/intranet/estadisticas/estadisticas_tutores.js"></script>
	<?php endif; ?>

	<script>
	function notificar_mensajes(nmens) {
		if(nmens > 0) {
			$('#icono_notificacion_mensajes').addClass('text-warning');
		}
		else {
			$('#icono_notificacion_mensajes').removeClass('text-warning');
		}
	}

	<?php if (isset($mensajes_pendientes) && $mensajes_pendientes): ?>
	var mensajes_familias = $("#lista_mensajes_familias li").size();
	var mensajes_profesores = $("#lista_mensajes li").size();
	var mensajes_pendientes = <?php echo $mensajes_pendientes; ?>;
	notificar_mensajes(mensajes_pendientes);
	<?php endif; ?>

	$('.modalmens').on('hidden.bs.modal', function (event) {
		var idp = $(this).data('verifica');
		var esTarea = $(this).find('#estarea-' + idp).attr('aria-pressed');

		if (esTarea == 'true') {
			$.post( "./admin/mensajes/post_verifica.php", { "idp" : idp, "esTarea" : true }, null, "json" )
			.done(function( data, textStatus, jqXHR ) {
				if ( data.status ) {
					if (mensajes_profesores < 2) {
					$('#alert_mensajes').slideUp();
					}
					else {
					$('#mensaje_link_' + idp).slideUp();
					}
					$('#menu_mensaje_' + idp + ' div').removeClass('text-warning');
					mensajes_profesores--;
					mensajes_pendientes--;
					notificar_mensajes(mensajes_pendientes);

					location.reload();
				}
			});
		}
		else {
			$.post( "./admin/mensajes/post_verifica.php", { "idp" : idp }, null, "json" )
			.done(function( data, textStatus, jqXHR ) {
				if ( data.status ) {
					if (mensajes_profesores < 2) {
					$('#alert_mensajes').slideUp();
					}
					else {
					$('#mensaje_link_' + idp).slideUp();
					}
					$('#menu_mensaje_' + idp + ' div').removeClass('text-warning');
					mensajes_profesores--;
					mensajes_pendientes--;
					notificar_mensajes(mensajes_pendientes);
				}
			});
		}

	});

	$('.modalmensfamilia').on('hidden.bs.modal', function (event) {
		var idf = $(this).data("verifica-familia");
		var esTarea = $(this).find('#estareafamilia-' + idf).attr('aria-pressed');

		if (esTarea == 'true') {
			$.post( "./admin/mensajes/post_verifica.php", { "idf" : idf, "esTarea" : true }, null, "json" )
			  .done(function( data, textStatus, jqXHR ) {
			      if ( data.status ) {
			          if (mensajes_familias < 2 ) {
			          	$('#alert_mensajes_familias').slideUp();
			          }
			          else {
			          	$('#mensaje_link_familia_' + idf).slideUp();
			          }
			          mensajes_familias--;
			          mensajes_pendientes--;
			          notificar_mensajes(mensajes_pendientes);

			          location.reload();
			      }
			});
		}
		else {
			$.post( "./admin/mensajes/post_verifica.php", { "idf" : idf }, null, "json" )
			  .done(function( data, textStatus, jqXHR ) {
			      if ( data.status ) {
			          if (mensajes_familias < 2 ) {
			          	$('#alert_mensajes_familias').slideUp();
			          }
			          else {
			          	$('#mensaje_link_familia_' + idf).slideUp();
			          }
			          mensajes_familias--;
			          mensajes_pendientes--;
			          notificar_mensajes(mensajes_pendientes);
			      }
			});
		}
	});
	</script>

	<?php if(isset($_GET['tour']) && $_GET['tour']): ?>
	<script src="//<?php echo $config['dominio'];?>/intranet/js/bootstrap-tour/bootstrap-tour.min.js"></script>
	<?php include("./js/bootstrap-tour/intranet-tour.php"); ?>
	<?php endif; ?>

</body>
</html>
