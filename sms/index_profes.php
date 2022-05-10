<?php
require('../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1, 8));

$profe = $_SESSION['profi'];

include("../menu.php");
include("menu.php");

if (isset($_GET['submit0'])) {$submit0 = $_GET['submit0'];}elseif (isset($_POST['submit0'])) {$submit0 = $_POST['submit0'];}else{$submit0="";}
if (isset($_GET['text'])) {$text = $_GET['text'];}elseif (isset($_POST['text'])) {$text = $_POST['text'];}else{$text="";}
if (isset($_GET['nombre'])) {$nombre = $_GET['nombre'];}elseif (isset($_POST['nombre'])) {$nombre = $_POST['nombre'];}else{$nombre="";}
if (isset($_GET['extid'])) {$extid = $_GET['extid'];}elseif (isset($_POST['extid'])) {$extid = $_POST['extid'];}else{$extid="";}
if (isset($_GET['mobile'])) {$mobile = $_GET['mobile'];}elseif (isset($_POST['mobile'])) {$mobile = $_POST['mobile'];}else{$mobile="";}
//variables();
?>
<script>
function contar(form,name) {
  n = document.forms[form][name].value.length;
  t = 160;
  if (n > t) {
    document.forms[form][name].value = document.forms[form][name].value.substring(0, t);
  }
  else {
    document.forms[form]['result'].value = t-n;
  }
}
</script> 
<div class="container">

<div class="page-header">
  <h2>SMS <small> Envío de mensajes</small></h2>
</div>
<br>
<div class="row">

<?php
 if ($config['mod_sms']) {

// Procesado de los datos del Formulario
if($submit0 == "Enviar SMS")
{
if(empty($text)){
		echo '<div align="center"><div class="alert alert-danger alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4>ATENCIÓN:</h4>
No has escrito ningún texto para el Mensaje.<br />Vuelve atrás, redacta el texto e inténtalo de nuevo.
          </div></div>';
		  exit();
}
  	if(strlen($text) > "2")
	{ 
	foreach($nombre as $idea_profe)
	{
	$tel0 = mysqli_query($db_con, "select telefono, profesor from c_profes where idea = '$idea_profe'");
	$tel1 = mysqli_fetch_array($tel0);
	
	if (strlen($tel1[0])==9) {
		$mobile.=$tel1[0].",";
		$nombre_pr.=$tel1[1].", ";
	}
	}

	$mobile=substr($mobile,0,strlen($mobile)-1);
	$nombre_pr=substr($nombre_pr,0,strlen($nombre_pr)-2);

	$sms_n = mysqli_query($db_con, "select max(id) from sms");
	$n_sms =mysqli_fetch_array($sms_n);
	$extid = $n_sms[0]+1;

	$exp_moviles = explode(',', $mobile);
	$recipients = array();
	
	foreach ($exp_moviles as $num_movil) {
		
		$num_movil = trim($num_movil);
		
		if(strlen($num_movil) == 9) {
			array_push($recipients, '+34'.$num_movil);
		}
		else {
			echo "
			<div class=\"alert alert-error\">
				<strong>Error:</strong> No se pudo enviar el SMS al teléfono (+34) ".$num_movil.". Corrija la información de contacto del alumno/a en Séneca e importe los datos nuevamente.
			</div>
			<br>";
		}
	}

	// ENVIO DE SMS
	$auth = smsLogin($config['mod_sms_user'], $config['mod_sms_pass']);

	$smsSent = sendSMS($auth, array(
	    "message" => $text,
	    "message_type" => MESSAGE_HIGH_QUALITY,
	    "returnCredits" => false,
	    "recipient" => $recipients,
	    "sender" => $config['mod_sms_id']
	));

	if ($smsSent->result == "OK") {
	    mysqli_query($db_con, "insert into sms (fecha,telefono,mensaje,profesor) values (now(),'$mobile','$text','$profe')");
	
			echo '<div align="center"><div class="alert alert-success alert-block fade in">
	            <button type="button" class="close" data-dismiss="alert">&times;</button>
								El mensaje SMS se ha enviado correctamente a los siguientes profesores: '.$nombre_pr.'.
	          </div></div>';
	}
	
}	
}
else
{
	 if(stristr($_SESSION['cargo'],'1') == TRUE or stristr($_SESSION['cargo'],'8') == TRUE){
	 	
		?>
		
<div class="col-md-4 col-md-offset-2">

        <?php
	}
	else{
	echo '<div class="col-md-4 col-md-offset-2">';	
	}
?>
<div class="well well-large" align="left">
<form method="post" action="index_profes.php" name="nameform" class="form-vertical">

<?php
if(empty($text)){$text = "";}
echo "<div class='form-group'>
<label>Texto del mensaje</label>
<TEXTAREA name='text' class='form-control' rows='4'  onkeydown=\"contar('nameform','text')\" onkeyup=\"contar('nameform','text')\">$text</TEXTAREA></div>
		<div class='form-group'>
		<label>Caracteres restantes:</label> <INPUT name=result value=160 class='form-control' readonly='true'></div>";
?>
      	

        <br /><input type="submit" name="submit0" value="Enviar SMS" class="btn btn-primary"/>

  <?	  
  if(stristr($_SESSION['cargo'],'1') == TRUE or stristr($_SESSION['cargo'],'8') == TRUE)
	    {	
		?>
</div>
</div>
<div class="col-sm-4">
<div class="well">
<div class='form-group'>
<label>Selección de Profesores<?php echo "<span class='text-info'>: $unidad</span>"; ?></label>
        <?php
  		echo '<SELECT  name=nombre[] multiple=multiple class="form-control" style="height:370px">';
  
  		$profe_sms = mysqli_query($db_con, "SELECT distinct idea, nombre FROM departamentos order by nombre asc");
  
        while($profesor_sms = mysqli_fetch_array($profe_sms)) 
	   {
	echo "<OPTION value='$profesor_sms[0]'>$profesor_sms[1]</OPTION>";
		}
	echo  '</select></div>';
		} 	
		
		
		
?>
  </form>
<?php
}
 } 
 else {
	 echo '<div align="center"><div class="alert alert-warning alert-block fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4>ATENCIÓN:</h4>
El módulo de envío de SMS debe ser activado en la Configuración general de la Intranet para poder acceder a estas páginas, y ahora mismo está desactivado.
          </div></div>';
 }
?> 

</div>
</div>
</div>

</div>
</div>
</div>
<?php include("../pie.php");?>

</body>
</html>