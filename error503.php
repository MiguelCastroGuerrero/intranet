<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed');
header('HTTP/1.1 503 Service Temporarily Unavailable', true, 404);
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
	<link href="//<?php echo $config['dominio']; ?>/intranet/vendor/fontawesome-free-5.13.0-web/css/all.css" rel="stylesheet">
	<link href="//<?php echo $config['dominio']; ?>/intranet/css/animate.css" rel="stylesheet">
	<link href="//<?php echo $config['dominio']; ?>/intranet/css/otros.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <style type="text/css">
  /* Space out content a bit */
  body {
    padding-top: 70px;
    padding-bottom: 20px;
  }

  /* Everything but the jumbotron gets side spacing for mobile first views */
  .header,
  .footer {
    padding-right: 15px;
    padding-left: 15px;
  }

  /* Custom page header */
  .header {
    border-bottom: 1px solid #e5e5e5;
  }

	h1 {
		font-size: 52px !important;
	}
  /* Make the masthead heading the same height as the navigation */
  .header h3 {
    padding-bottom: 19px;
    margin-top: 0;
    margin-bottom: 0;
    line-height: 40px;
  }

  /* Custom page footer */
  .footer {
    padding-top: 19px;
    color: #777;
    border-top: 1px solid #e5e5e5;
  }

  /* Customize container */
  @media (min-width: 768px) {
    .container {
      max-width: 730px;
    }
  }
  .container-narrow > hr {
    margin: 30px 0;
  }

  /* Main marketing message and sign up button */
  .jumbotron {
    text-align: center;
    border-bottom: 1px solid #e5e5e5;
  }
  .jumbotron .btn {
    padding: 14px 24px;
    font-size: 21px;
  }

  /* Supporting marketing content */
  .marketing {
    margin: 40px 0;
  }
  .marketing p + h4 {
    margin-top: 28px;
  }

  /* Responsive: Portrait tablets and up */
  @media screen and (min-width: 768px) {
    /* Remove the padding we set earlier */
    .header,
    .footer {
      padding-right: 0;
      padding-left: 0;
    }
    /* Space out the masthead */
    .header {
      margin-bottom: 30px;
    }
    /* Remove the bottom border on the jumbotron for visual effect */
    .jumbotron {
      border-bottom: 0;
    }
  }
  </style>
</head>

<body>

    <div class="container">

      <div class="jumbotron">
        <h1>Intranet</h1>

				<br>

				<h2>Página no disponible temporalmente</h2>
        <p>Esta página no está disponible temporalmente. Intente de nuevo más tarde.</p>

				<br>
				<br>

        <a class="btn btn-lg btn-success" href="//<?php echo $config['dominio']; ?>" role="button">Ir a la página del Centro</a>
      </div>

    </div> <!-- /container -->

</body>
</html>
