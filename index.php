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

				<?php if ($_SERVER['SERVER_NAME'] != "iesmonterroso.org" && date('Y') < 2020 && date('m') == 12 && date('d') > 8): ?>
				<div class="alert alert-info">
					<h4>Importante:</h4>
					<p>A partir del <strong>1 de enero de 2020</strong> la clave de acceso a la intranet pasa a ser la misma que la clave de Séneca para el <strong>personal docente</strong>. La mayoría de los usuarios ya comparten la misma contraseña en Séneca y en la intranet, por lo que no notarán ningún cambio. Pero aquellos usuarios que tenéis claves distintas en ambas plataformas debéis proceder a utilizar únicamente la clave de Séneca.</p>

					<p>Si por alguna razón olvidáis o bloqueáis la clave de la intranet tenéis que utilizar el mismo sistema que seguís cuando se bloquea vuestra clave en Séneca: buscad a algún miembro del equipo directivo para que pueda reiniciar vuestra clave en Séneca o seguid las instrucciones de recuperación de contraseña de Séneca vía móvil o correo electrónico.</p>
				</div>
				<?php endif; ?>

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
		var esTarea = $(this).find('#estareafamilia-' + idp).attr('aria-pressed');

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
