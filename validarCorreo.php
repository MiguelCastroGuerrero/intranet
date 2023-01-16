<?php
require('bootstrap.php');

$verificacion = base64url_encode($_GET['verificar']);

$cadena = descifrarTexto($verificacion);
$exp_cadena = explode('|', $cadena);

$id_usuario = limpiarInput(trim($exp_cadena[0]), 'numeric');
$correo_usuario = filter_var(trim($exp_cadena[1]), FILTER_VALIDATE_EMAIL);

$result = mysqli_query($db_con, "SELECT `correo`, `idea` FROM `c_profes` WHERE `id` = '".$id_usuario."' LIMIT 1");
if (mysqli_num_rows($result)) {
	$row = mysqli_fetch_array($result);

	if ($row['correo'] == $correo_usuario) {
		$result_validacion = mysqli_query($db_con, "UPDATE `c_profes` SET `correo_verificado` = 1 WHERE `idea` = '".$row['idea']."' LIMIT 1");
		if (! $result_validacion) {
			$msg_txt = "Ha ocurrido un error al verificar el correo electrónico. Error: " . mysqli_error($db_con);
		}
		else {
			$msg_txt = "El correo ha sido validado correctamente";
		}
	}
	else {
		$msg_txt = "No se ha podido verificar el correo electrónico";
	}
}
else {
	$msg_txt = "No se ha podido verificar el correo electrónico";
}
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

	<link href="//<?php echo $config['dominio']; ?>/intranet/css/bootstrap.min.css" rel="stylesheet">
	<link href="//<?php echo $config['dominio']; ?>/intranet/vendor/fontawesome-free-5.15.4-web/css/all.css" rel="stylesheet">
	<link href="//<?php echo $config['dominio']; ?>/intranet/css/animate.css" rel="stylesheet">
	<link href="//<?php echo $config['dominio']; ?>/intranet/css/otros.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <style type="text/css">
  /* Space out content a bit */
  body {
    padding-top: 70px;
    padding-bottom: 20px;
  }

  /* Customize container */
  @media (min-width: 768px) {
    .container {
      max-width: 730px;
    }
  }
  </style>
</head>

<body>

    <div class="container">

      <div class="well text-center">
        <h1>Intranet</h1>

		<br>

		<h3><?php echo $msg_txt; ?></h3>

		<br>
		<br>

        <a class="btn btn-md btn-success" href="//<?php echo $config['dominio']; ?>/intranet/" role="button">Ir a la Intranet</a>
      </div>

    </div> <!-- /container -->

</body>
</html>
