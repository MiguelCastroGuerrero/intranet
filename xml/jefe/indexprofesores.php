<?
session_start();
include("../../config.php");
include_once('../../config/version.php');

// COMPROBAMOS LA SESION
if ($_SESSION['autentificado'] != 1) {
	$_SESSION = array();
	session_destroy();
	
	if(isset($_SERVER['HTTPS'])) {
	    if ($_SERVER["HTTPS"] == "on") {
	        header('Location:'.'https://'.$dominio.'/intranet/salir.php');
	        exit();
	    } 
	}
	else {
		header('Location:'.'http://'.$dominio.'/intranet/salir.php');
		exit();
	}
}

if($_SESSION['cambiar_clave']) {
	if(isset($_SERVER['HTTPS'])) {
	    if ($_SERVER["HTTPS"] == "on") {
	        header('Location:'.'https://'.$dominio.'/intranet/clave.php');
	        exit();
	    } 
	}
	else {
		header('Location:'.'http://'.$dominio.'/intranet/clave.php');
		exit();
	}
}


registraPagina($_SERVER['REQUEST_URI'],$db_host,$db_user,$db_pass,$db);


$profe = $_SESSION['profi'];
if(!(stristr($_SESSION['cargo'],'1') == TRUE))
{
header('Location:'.'http://'.$dominio.'/intranet/salir.php');
exit;	
}

include("../../menu.php");
?>

<div class="container">
	
	<!-- TITULO DE LA PAGINA -->
	<div class="page-header">
		<h2>Administración <small>Importación de profesores</small></h2>
	</div>
	
	<?php $result = mysqli_query($db_con, "SELECT * FROM profesores LIMIT 1"); ?>
	<?php if(mysqli_num_rows($result)): ?>
	<div class="alert alert-warning">
		Ya existe información en la base de datos. Este proceso actualizará la información de los profesores. Es recomendable realizar una <a href="copia_db/index.php" class="alert-link">copia de seguridad</a> antes de proceder a la importación de los datos.
	</div>
	<?php endif; ?>
	
	<!-- SCAFFOLDING -->
	<div class="row">
	
		<!-- COLUMNA IZQUIERDA -->
		<div class="col-sm-6">
			
			<div class="well">
				
				<form enctype="multipart/form-data" method="post" action="profesores.php">
					<fieldset>
						<legend>Importación de profesores</legend>
						
						<div class="form-group">
						  <label for="archivo"><span class="text-info">RelMatProUni.txt</span></label>
						  <input type="file" id="archivo" name="archivo" accept="text/plain">
						</div>
						
						<br>
						
					  <button type="submit" class="btn btn-primary" name="enviar">Importar</button>
					  <a class="btn btn-default" href="../index.php">Volver</a>
				  </fieldset>
				</form>
				
			</div><!-- /.well -->
			
		</div><!-- /.col-sm-6 -->
		
		
		<div class="col-sm-6">
			
			<h3>Información sobre la importación</h3>
			
			<p>Este apartado se encarga de importar la relación de <strong>profesores, aulas y asignaturas</strong> que imparten. Esto permitirá comprobar incoherencias en el horario de Horw y Séneca.</p>
			
			<p>Para obtener el archivo de exportación de alumnos debe dirigirse al apartado <strong>Personal</strong>, <strong>Personal del centro</strong>, <strong>Unidades y materias</strong> <em>(icono arriba a la derecha)</em>. Muestre todos los profesores y haga clic en el botón <strong>Exportar datos</strong>. El formato de exportación debe ser <strong>Texto plano</strong>.</p>

		</div>
		
	
	</div><!-- /.row -->
	
</div><!-- /.container -->
  
<?php include("../../pie.php"); ?>
	
</body>
</html>
