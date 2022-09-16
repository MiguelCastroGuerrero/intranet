<?php
require('../../bootstrap.php');

if($_POST['token']) $token = $_POST['token'];
if(!isset($token)) $token = time();

if (acl_permiso($_SESSION['cargo'], array('2'))) {
	$equipoEducativo = array();

	$grupoTutoria = "";
	if (isset($_SESSION['mod_tutoria']['unidad'])) {
		$grupoTutoria = $_SESSION['mod_tutoria']['unidad'];
	}

	$result = mysqli_query($db_con, "SELECT DISTINCT `profesores`.`profesor`, `c_profes`.`idea` FROM `profesores` JOIN `c_profes` ON `profesores`.`profesor` = `c_profes`.`PROFESOR` WHERE `grupo` = '".$grupoTutoria."'");
	while ($row = mysqli_fetch_array($result)) {
		array_push($equipoEducativo, $row['idea']);
	}
}

if (isset($_POST['enviar'])) {
	$profe_envia = $_SESSION['profi'];
	$cor_pr = mysqli_query($db_con, "select correo from c_profes where profesor = '$profe_envia'");
	$cor_pr0 = mysqli_fetch_array($cor_pr);
	$mail_from = $cor_pr0[0];

	$titulo = stripslashes(mysqli_real_escape_string($db_con, $_POST['tema']));
	$contenido = $_POST['texto'];

	require_once(INTRANET_DIRECTORY."/lib/phpmailer/PHPMailerAutoload.php");
	$mail = new PHPMailer();
	if (isset($config['email_smtp']['isSMTP']) && $config['email_smtp']['isSMTP']) {
		$mail->isSMTP();
		$mail->Host = $config['email_smtp']['hostname'];
		$mail->SMTPAuth = $config['email_smtp']['smtp_auth'];
		$mail->Port = $config['email_smtp']['port'];
		$mail->SMTPSecure = $config['email_smtp']['smtp_secure'];

		$mail->Username = $config['email_smtp']['username'];
		$mail->Password = $config['email_smtp']['password'];

		$mail->setFrom($config['email_smtp']['username'], utf8_decode($config['centro_denominacion']));
	}
	else {
		$mail->Host = "localhost";
		$mail->setFrom('no-reply@'.$config['dominio'], utf8_decode($config['centro_denominacion']));
	}

	$mail->AddReplyTo($mail_from, $profe_envia);
	$mail->IsHTML(true);

	$exp_nomprof = explode(', ', $profe_envia);
	$nombre_prof = trim($exp_nomprof[1].' '.$exp_nomprof[0]);

	$message = file_get_contents(INTRANET_DIRECTORY.'/lib/mail_template/index.htm');
	$message = str_replace('{{dominio}}', $config['dominio'], $message);
	$message = str_replace('{{centro_denominacion}}', $config['centro_denominacion'], $message);
	$message = str_replace('{{centro_codigo}}', $config['centro_codigo'], $message);
	$message = str_replace('{{centro_direccion}}', $config['centro_direccion'], $message);
	$message = str_replace('{{centro_codpostal}}', $config['centro_codpostal'], $message);
	$message = str_replace('{{centro_localidad}}', $config['centro_localidad'], $message);
	$message = str_replace('{{centro_provincia}}', $config['centro_provincia'], $message);
	$message = str_replace('{{centro_telefono}}', $config['centro_telefono'], $message);
	$message = str_replace('{{centro_fax}}', $config['centro_fax'], $message);
	$message = str_replace('{{centro_email}}', $config['centro_email'], $message);
	$message = str_replace('{{titulo}}', 'Nuevo mensaje: '.$titulo, $message);
	$message = str_replace('{{contenido}}', $contenido, $message);
	$message = str_replace('{{autor}}', $nombre_prof, $message);

	$mail->msgHTML(utf8_decode($message));
	$mail->Subject = utf8_decode('Nuevo mensaje: '.$titulo);
	$mail->AltBody = utf8_decode($titulo.' '.$contenido);

	foreach($_POST as $var => $valor) {
		$dni=$var;
		$cambia[$dni]=$valor;
	}

	foreach($cambia as $eldni => $valor){
		$mail0=mysqli_query($db_con, "select correo, PROFESOR from c_profes where dni='$eldni'");
		if (mysqli_num_rows($mail0)>0) {
			$numcor++;
		}
		$mail1=mysqli_fetch_row($mail0);
		$direccion = $mail1[0];
		$profes = $mail1[1];
		if(filter_var($direccion, FILTER_VALIDATE_EMAIL)) {
			$mail->AddAddress($direccion, $profes);
		}

	}

	for ($i=0;$i<5;$i++) {
		$varname{$i} = $_FILES['fil'.$i]['name'];
		$vartemp{$i} = $_FILES['fil'.$i]['tmp_name'];
		if($varname{$i} != "") {
			$mail->AddAttachment($vartemp{$i}, $varname{$i});
		}
	}


	if(!$mail->Send()) {
		$msg_class = "alert-danger";
		$msg = "Error: " . $mail->ErrorInfo;
	} else {
		if (!($numcor>0)) {
			$msg_class = "alert-danger";
			$msg = '<strong>Atención:</strong> No has seleccionado <em>destinatario</em> para enviar el correo.';
		}
		else {
			$msg_class = "alert-success";
			$msg = "El mensaje ha sido enviado.";
			

			foreach($cambia as $eldni => $valor){
				$mail0=mysqli_query($db_con, "select correo, PROFESOR from c_profes where dni='$eldni'");
				if (mysqli_num_rows($mail0)>0) {
					$numcor++;
				}
				$mail1=mysqli_fetch_row($mail0);
				$direccion = $mail1[0];
				if(filter_var($direccion, FILTER_VALIDATE_EMAIL)) {
					$sql_mail = mysqli_query($db_con, "insert into `correos` (id, destino, correo, fecha, texto) VALUES ('','".$direccion."','".$mail_from."', NOW(),'".$contenido."')");
				}
			}

		}
	}
}


$page_header = "Enviar correo electrónico";
include("../../menu.php");
include("menu.php");
?>

	<div class="container">

		<div class="page-header">
			<h2>Mensajes <small><?php echo $page_header; ?></small></h2>
		</div>

		<?php if($msg): ?>
		<div class="alert <?php echo $msg_class; ?> alert-block"><?php echo $msg; ?>
		</div>
		<?php endif; ?>


		<form enctype="multipart/form-data" method="post" name="cargos">

		<div class="row">

			<div class="col-sm-7">

				<div class="well">

					<fieldset>
						<legend>Redactar correo</legend>

						<input type="hidden" name="token" value="<?php echo $token; ?>">

						<div class="form-group">
							<label for="tema">Asunto</label>
							<input type="text" class="form-control" id="tema" name="tema" placeholder="Asunto del correo">
						</div>

						<div class="form-group">
							<label for="texto" class="sr-only">Contenido</label>
							<textarea class="form-control" id="texto" name="texto" rows="10"></textarea>
						</div>


						<div id="adjuntos"></div>

						<br>

						<button type="submit" class="btn btn-primary" name="enviar">Enviar correo</button>
						<button type="button" class="btn btn-default" onclick="crear(this)">Adjuntar archivo</button>
						<a class="btn btn-default" href="index.php">Cancelar</a>

					</fieldset>

				</div>

			</div>

			<div class="col-sm-5">

				<div id="botones_grupos">
					<a href="javascript:seleccionar_todo()"	class="btn btn-sm btn-info">Todos los Profesores</a>&nbsp;
					<a href="javascript:seleccionar_tutor()" class="btn btn-sm btn-info">Tutores</a>&nbsp;
					<a href="javascript:seleccionar_jd()" class="btn btn-sm btn-info">Jefes de Depto.</a>&nbsp;<hr>
					<?php if (acl_permiso($_SESSION['cargo'], array('2'))): ?>
					<a href="javascript:seleccionar_equipoEducativo()" class="btn btn-sm btn-info">Equipo educativo</a>&nbsp;
					<?php endif; ?>
					<a href="javascript:seleccionar_ca()" class="btn btn-sm btn-info">Coord. de Area</a>&nbsp;
					<?php if ($config['mod_bilingue']==1) { ?>
					<a href="javascript:seleccionar_bil()" class="btn btn-sm btn-info">Bilingüismo</a>
					<?php } ?>
					<a href="javascript:seleccionar_ed()" class="btn btn-sm btn-info">Dirección</a>
				</div>

				<br>

				<div class="panel-group" id="departamentos">
					<?php $result = mysqli_query($db_con, "SELECT DISTINCT `departamento` FROM `departamentos` WHERE `departamento` <> 'Admin' AND `departamento` <> 'Administracion' AND `departamento` <> 'Conserjeria' AND `departamento` <> 'Servicio Técnico y/o Mantenimiento' AND `departamento` <> '' ORDER BY `departamento` ASC"); ?>
					<?php $i = 0; ?>
					<?php while ($departamento = mysqli_fetch_array($result)): ?>
				  <div class="panel panel-default">
				    <div class="panel-heading">
				    	<h4 class="panel-title">
				    		<a data-toggle="collapse" data-parent="#departamentos" href="#departamento<?php echo $i; ?>">
				        	<?php echo $departamento['departamento']; ?>
				      	</a>
				    	</h4>
				    </div>
				    <div id="departamento<?php echo $i; ?>" class="panel-collapse collapse <?php if($i==0) echo 'in'; ?>">
				      <div class="panel-body">

				      <?php $profesores = mysqli_query($db_con, "SELECT distinct `c_profes`.`profesor`, `c_profes`.`idea`, `c_profes`.`dni`, `correo`, `cargo` FROM `c_profes`, `departamentos` WHERE `c_profes`.`correo_verificado` = 1 AND `departamentos`.`idea` = `c_profes`.`idea` AND `departamento`= '$departamento[0]' AND `profesor` <> 'Administrador' AND `c_profes`.`correo` IS NOT NULL ORDER BY `c_profes`.`profesor`"); ?>
				      <?php if(mysqli_num_rows($profesores)>0): ?>

			        <?php while($profesor = mysqli_fetch_array($profesores)): ?>
	        			<?php $pro = $profesor[0]; ?>
	        			<?php $idea = $profesor[1]; ?>
	        			<?php $dni = $profesor[2]; ?>
	         			<?php $correo = $profesor[3]; ?>
	         			<?php $perf = $profesor[4]; ?>
	         			<?php $n_i = $n_i + 1; ?>

      					<div class="checkbox">
      						<label>
         						<input type="checkbox" id="dato0" data-equipoEducativo="<?php echo (in_array($idea, $equipoEducativo)) ? $grupoTutoria : ''; ?>" name="<?php echo $dni;?>" value="cambio"> <?php echo nomprofesor($pro); ?>
         					</label>
      					</div>
      					<input type="hidden" name="<?php echo $dni.":".$perf;?>" value="<?php echo $perf;?>">
							<?php endwhile; ?>

							<?php else: ?>
     						<p class="muted">No hay profesores en este departamento con correo electrónico</p>
							<?php endif; ?>

							</div>
						</div>
					</div>

					<?php $i++; ?>
					<?php endwhile; ?>
				</div>
			</div>

		</div>

	</div>

	</form>

</div>


	<?php include('../../pie.php'); ?>

<script>

function seleccionar_todo(){
	for (i=0;i<document.cargos.elements.length;i++)
		if(document.cargos.elements[i].type == "checkbox")
			document.cargos.elements[i].checked=1
}
function deseleccionar_todo(){
	for (i=0;i<document.cargos.elements.length;i++)
		if(document.cargos.elements[i].type == "checkbox")
			document.cargos.elements[i].checked=0
}
<?php if (acl_permiso($_SESSION['cargo'], array('2'))): ?>
function seleccionar_equipoEducativo(){
	deseleccionar_todo()
for (i=0;i<document.cargos.elements.length;i++){
		if(document.cargos.elements[i].type == "checkbox"){
		valorCasilla = document.cargos.elements[i].getAttribute('data-equipoEducativo');
		if(valorCasilla == "<?php echo $grupoTutoria; ?>"){
			document.cargos.elements[i].checked=1;
}
}
}
}
<?php endif; ?>
function seleccionar_tutor(){
	deseleccionar_todo()
for (i=0;i<document.cargos.elements.length;i++){
		if(document.cargos.elements[i].type == "hidden"){
		valorCasilla = document.cargos.elements[i].value;
		valorReal = valorCasilla.indexOf("2");
		if(valorReal >= "0"){
			document.cargos.elements[i-1].checked=1;
}
}
}
}
function seleccionar_jd(){
	deseleccionar_todo()
	for (i=0;i<document.cargos.elements.length;i++){
		if(document.cargos.elements[i].type == "hidden"){
		valorCasilla = document.cargos.elements[i].value;
		valorReal = valorCasilla.indexOf("4");
		if(valorReal >= "0"){
			document.cargos.elements[i-1].checked=1;
}
}
}
}
function seleccionar_ed(){
	deseleccionar_todo()
	for (i=0;i<document.cargos.elements.length;i++){
		if(document.cargos.elements[i].type == "hidden"){
		valorCasilla = document.cargos.elements[i].value;
		valorReal = valorCasilla.indexOf("1");
		if(valorReal >= "0"){
			document.cargos.elements[i-1].checked=1;
}
}
}
}
function seleccionar_ca(){
	deseleccionar_todo()
	for (i=0;i<document.cargos.elements.length;i++){
		if(document.cargos.elements[i].type == "hidden"){
		valorCasilla = document.cargos.elements[i].value;
		valorReal = valorCasilla.indexOf("9");
		if(valorReal >= "0"){
			document.cargos.elements[i-1].checked=1;
}
}
}
}
function seleccionar_bil(){
	deseleccionar_todo()
	for (i=0;i<document.cargos.elements.length;i++){
		if(document.cargos.elements[i].type == "hidden"){
		valorCasilla = document.cargos.elements[i].value;
		valorReal = valorCasilla.indexOf("a");
		if(valorReal >= "0"){
			document.cargos.elements[i-1].checked=1;
}
}
}
}

num=0;
function crear(obj) {
  if(num<5) {
	  fi = document.getElementById('adjuntos'); // 1
	  contenedor = document.createElement('div'); // 2
	  contenedor.id = 'div'+num; // 3
	  fi.appendChild(contenedor); // 4

	  ele = document.createElement('input'); // 5
	  ele.type = 'file'; // 6
	  ele.name = 'fil'+num; // 6
	  contenedor.appendChild(ele); // 7

	  ele = document.createElement('input'); // 5
	  ele.type = 'button'; // 6
	  ele.className = 'btn btn-danger btn-sm';
	  ele.value = 'Eliminar'; // 8
	  ele.name = 'div'+num; // 8
	  ele.onclick = function () {borrar(this.name)} // 9
	  contenedor.appendChild(ele); // 7
	  num++;
  }
}
function borrar(obj) {
  fi = document.getElementById('adjuntos'); // 1
  fi.removeChild(document.getElementById(obj)); // 10
  num--;
}
</script>

	<script>
	$(document).ready(function() {

		// EDITOR DE TEXTO
		tinymce.init({
			selector: 'textarea#texto',
			language: 'es',
			height: 300,
			plugins: 'print preview fullpage paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars',
			imagetools_cors_hosts: ['picsum.photos'],
			menubar: 'file edit view insert format tools table help',
			toolbar: 'undo redo | bold italic underline strikethrough | fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap | fullscreen  preview save print | insertfile image media template link anchor | ltr rtl',
			toolbar_sticky: true,
			autosave_ask_before_unload: true,
			autosave_interval: "30s",
			autosave_prefix: "{path}{query}-{id}-",
			autosave_restore_when_empty: false,
			autosave_retention: "2m",
			image_advtab: true,
			
			/* enable title field in the Image dialog*/
			image_title: true,
			/* enable automatic uploads of images represented by blob or data URIs*/
			automatic_uploads: true,
			/*
			URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
			images_upload_url: 'postAcceptor.php',
			here we add custom filepicker only to Image dialog
			*/
			file_picker_types: 'image',
			/* and here's our custom image picker*/
			file_picker_callback: function (cb, value, meta) {
			var input = document.createElement('input');
			input.setAttribute('type', 'file');
			input.setAttribute('accept', 'image/*');

			/*
			  Note: In modern browsers input[type="file"] is functional without
			  even adding it to the DOM, but that might not be the case in some older
			  or quirky browsers like IE, so you might want to add it to the DOM
			  just in case, and visually hide it. And do not forget do remove it
			  once you do not need it anymore.
			*/

			input.onchange = function () {
			  var file = this.files[0];

			  var reader = new FileReader();
			  reader.onload = function () {
			    /*
			      Note: Now we need to register the blob in TinyMCEs image blob
			      registry. In the next release this part hopefully won't be
			      necessary, as we are looking to handle it internally.
			    */
			    var id = 'blobid' + (new Date()).getTime();
			    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
			    var base64 = reader.result.split(',')[1];
			    var blobInfo = blobCache.create(id, file, base64);
			    blobCache.add(blobInfo);

			    /* call the callback and populate the Title field with the file name */
			    cb(blobInfo.blobUri(), { title: file.name });
			  };
			  reader.readAsDataURL(file);
			};

			input.click();
			}
		});

		if (localStorage['summernote-<?php echo $token; ?>']) {
			$('#texto').code(localStorage['summernote-<?php echo $token; ?>']);
		}

	});
	</script>

</body>
</html>
