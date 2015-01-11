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
?>
<?php
include("../../menu.php");
include("../menu.php");
?>
<div class="container">
<br />
<div class="page-header">
  <h2>Faltas de Asistencia <small> importaci�n de la Jornada Escolar del centro</small></h2>
<div class="row">

<br />
<FORM ENCTYPE="multipart/form-data" ACTION="jornada.php" METHOD="post">
<div class="form-group">

  <div class="well well-large" style="width:600px; margin:auto;" align="left">
<p class="help-block"><span style="color:#9d261d">(*) </span>Descarga el archivo <strong>DetTipJornda.txt</strong> desde S�neca --> Centro --> Calendario y Jornada --> Jornada escolar. Pulsa sobre la <strong>Jornada Escolar</strong> (en azul) y luego sobre <strong>Detalle</strong> para acceder a la p�gina de la Jornada Escolar. Selecciona <strong>Texto Plano</strong> como tipo de archivo para la exportaci�n.</p>
  <br />
  <div class="controls">
  <label class="control-label" for="file">Selecciona el archivo con los datos de la Jornada Escolar
  </label>
  <input type="file" name="archivo" class="input input-file col-sm-4" id="file">
  <hr>
  <div align="center">
    <INPUT type="submit" name="enviar" value="Aceptar" class="btn btn-primary">
  </div>
  </div>
  </div>
</FORM>
<br />
</div>
</div>
    <? 
include("../../pie.php");
?>
</body>
</html>
