<?php
require("../bootstrap.php");

$limite_caracteres_sms = 160;

acl_acceso($_SESSION['cargo'], array('1', '2', '6', '7', '8', 'a'));

if (acl_permiso($_SESSION['cargo'], array('1', '8'))) {

  // Obtenemos las unidades del centro
  $unidades = array();
  $result = mysqli_query($db_con, "SELECT DISTINCT `nomunidad` FROM `unidades` ORDER BY `nomunidad` ASC");
  while ($row = mysqli_fetch_array($result)) {
    array_push($unidades, $row['nomunidad']);
  }

  // Obtenemos la unidad seleccionada del formulario
  if (isset($_POST['unidad']) && in_array($_POST['unidad'], $unidades)) {
    $unidadSeleccion = $_POST['unidad'];
  }
}

// Obtenemos los alumnos del centro con número de teléfono móvil registrado
$alumnado = array();
if (acl_permiso($_SESSION['cargo'], array('2'))) {
  $result = mysqli_query($db_con, "SELECT `claveal`, `apellidos`, `nombre`, `unidad`, `telefono`, `telefonourgencia` FROM `alma` WHERE `unidad` = '".$_SESSION['mod_tutoria']['unidad']."' ORDER BY `unidad` ASC, `apellidos` ASC, `nombre` ASC");
}
elseif (acl_permiso($_SESSION['cargo'], array('1', '8'))) {
  if (isset($unidadSeleccion) && ! empty($unidadSeleccion)) {
    $result = mysqli_query($db_con, "SELECT `claveal`, `apellidos`, `nombre`, `unidad`, `telefono`, `telefonourgencia` FROM `alma` WHERE `unidad` = '".$unidadSeleccion."' ORDER BY `unidad` ASC, `apellidos` ASC, `nombre` ASC") or die (mysqli_error($db_con));
  }
  else {
    $result = mysqli_query($db_con, "SELECT `claveal`, `apellidos`, `nombre`, `unidad`, `telefono`, `telefonourgencia` FROM `alma` ORDER BY `unidad` ASC, `apellidos` ASC, `nombre` ASC");
  }
}

while ($row = mysqli_fetch_array($result)) {

  $movil = "";

  if ((strlen($row['telefono']) == 9 && (substr($row['telefono'], 0, 1) == 6 || substr($row['telefono'], 0, 1) == 7)) || (strlen($row['telefonourgencia']) == 9 && (substr($row['telefonourgencia'], 0, 1) == 6 || substr($row['telefonourgencia'], 0, 1) == 7))) {

    if (substr($row['telefono'], 0, 1) == 6 || substr($row['telefono'], 0, 1) == 7) {
      $movil = $row['telefono'];
    }
    elseif (substr($row['telefonourgencia'], 0, 1) == 6 || substr($row['telefonourgencia'], 0, 1) == 7) {
      $movil = $row['telefonourgencia'];
    }

    $alumno = array(
      'nie'               => $row['claveal'],
      'apellidos'         => $row['apellidos'],
      'nombre'            => $row['nombre'],
      'unidad'            => $row['unidad'],
      'movil'             => $movil
    );

    array_push($alumnado, $alumno);
  }
}
unset($alumno);

// Causas
if (acl_permiso($_SESSION['cargo'], array('8'))) {
  $causas = array(
    "Dificultades de aprendizaje",
    "Dificultades de integración",
    "Evolución académica",
    "Faltas de Asistencia",
    "Problemas de convivencia",
    "Problemas familiares, personales",
    "Orientación académica y profesional",
    "Técnicas de estudio",
    "Otras"
  );
}
else {
  $causas = array(
    "Estado general del Alumno",
    "Evolución académica",
    "Faltas de Asistencia",
    "Problemas de convivencia",
    "Otras"
  );
}

// Procesamos el formulario
if (isset($_POST['enviar'])) {
  $msg_error = 0;

  if (isset($_POST['alumnos']) && is_array($_POST['alumnos'])) {
    $alumnosSeleccionados = $_POST['alumnos'];
  }
  else {
    $msg_error = "Debe seleccionar al menos a un destinatario.";
  }

  if (isset($_POST['mensaje']) && strlen($_POST['mensaje']) <= $limite_caracteres_sms) {
    $mensaje = $_POST['mensaje'];
  }
  elseif (isset($_POST['mensaje']) && strlen($_POST['mensaje']) > $limite_caracteres_sms) {
    $mensaje = substr($_POST['mensaje'], 0, $limite_caracteres_sms);
  }
  else {
    $msg_error = "Ha ocurrido un error al procesar el mensaje.";
  }

  if (isset($_POST['causa']) && ! empty($_POST['causa'])) {
    $causaSeleccionada = $_POST['causa'];
  }
  else {
    $msg_error = "Debe seleccionar el motivo del mensaje.";
  }

  if (! $msg_error) {
    $fecha = date('Y-m-d H:i:s');    
    $cont = 0;
    $recipients = array();
    foreach ($alumnosSeleccionados as $alumnoSeleccionado) {
      $cont++;
      $key = array_search($alumnoSeleccionado, array_column($alumnado, 'nie'));
      $movil = $alumnado[$key]['movil'];
      array_push($recipients, '+34'.$movil);
    }

    $auth = smsLogin($config['mod_sms_user'], $config['mod_sms_pass']);
    
    $smsSent = sendSMS($auth, array(
        "message" => $mensaje,
        "message_type" => MESSAGE_HIGH_QUALITY,
        "returnCredits" => false,
        "recipient" => $recipients,
        "sender" => $config['mod_sms_id']
    ));

    if ($smsSent->result == "OK") {
      $msg_success = "SMS enviado ".(($cont > 1) ? "a los destinatarios" : "al destinatario").".";

      $accion = "Envío de SMS";
      $fecha = date('Y-m-d H:i:s');

      // Registramos el SMS enviado
      foreach ($alumnosSeleccionados as $alumnoSeleccionado) {
        $key = array_search($alumnoSeleccionado, array_column($alumnado, 'nie'));
        $movil = $alumnado[$key]['movil'];
        $apellidos = $alumnado[$key]['apellidos'];
        $nombre = $alumnado[$key]['nombre'];
        $claveal = $alumnado[$key]['nie'];
        $unidad = $alumnado[$key]['unidad'];

        // Registramos el SMS en nuestra base de datos
        $result_sms = mysqli_query($db_con, "INSERT INTO `sms` (`fecha`, `telefono`, `mensaje`, `profesor`) VALUES ('$fecha','$movil','$mensaje','$pr')");
        if (! $result_sms){
          $msg_error = "No se ha podido registrar el SMS en la base de datos. Error: ".mysqli_error($db_con);
        }

        // Registramos intervención de tutoría
        if (acl_permiso($_SESSION['cargo'], array('2'))) {
          $tutor = $pr;
          $jefatura = 0;
          $orienta = 0;
          $mensaje_emisor = "el tutor o la tutora";
        }
        elseif (acl_permiso($_SESSION['cargo'], array('1'))) {
          $result_tutor = mysqli_query("SELECT `tutor` FROM `FTUTORES` WHERE `unidad` = '".$unidad."' LIMIT 1");
          $row_tutor = mysqli_fetch_array($result_tutor);
          $tutor = $row_tutor['tutor'];
          $jefatura = 1;
          $orienta = 0;
          $mensaje_emisor = "jefatura de estudios";
        }
        elseif (acl_permiso($_SESSION['cargo'], array('8'))) {
          $result_tutor = mysqli_query("SELECT `tutor` FROM `FTUTORES` WHERE `unidad` = '".$unidad."' LIMIT 1");
          $row_tutor = mysqli_fetch_array($result_tutor);
          $tutor = $row_tutor['tutor'];
          $jefatura = 0;
          $orienta = 1;
          $mensaje_emisor = "depto. de orientación educativa";
        }

        $result_tutoria = mysqli_query($db_con, 'INSERT INTO `tutoria` (`apellidos`, `nombre`, `tutor`, `unidad`, `observaciones`, `causa`, `accion`, `fecha`, `claveal`, `jefatura`, `orienta`) VALUES ("'.$apellidos.'","'.$nombre.'","'.$tutor.'","'.$unidad.'","'.$mensaje.'","'.$causaSeleccionada.'","'.$accion.'","'.$fecha.'","'.$claveal.'", "'.$jefatura.'", "'.$orienta.'")');
        if (! $result_tutoria) {
          $msg_error = "No se ha podido registrar la intervención de tutoría. Error: ".mysqli_error($db_con);
        }
        else {
          $msg_success .= " Se ha registrado una intervención de tutoría ".(($cont > 1) ? "sobre los alumnos seleccionados" : "sobre el alumno seleccionado").".";
        }
      }

      unset($alumnosSeleccionados);
      unset($mensaje);
      unset($apellidos);
      unset($nombre);
      unset($claveal);
      unset($unidad);
      unset($tutor);
    } else {
      $msg_error = "No se ha podido enviar el SMS.";
    }
  }
}

include("../menu.php");
if (acl_permiso($_SESSION['cargo'], array('2'))) {
  include("../admin/mensajes/menu.php");
}
else {
  include("menu.php");
}
?>

  <div class="container">

    <div class="page-header">
      <h2>SMS <small>Familia y alumnado</small></h2>
    </div>

    <?php if (isset($msg_error) && $msg_error): ?>
    <div class="alert alert-danger alert-block">
      <?php echo $msg_error; ?>
    </div>
    <?php endif; ?>

    <?php if (isset($msg_success) && $msg_success): ?>
    <div class="alert alert-success alert-block">
      <?php echo $msg_success; ?>
    </div>
    <?php endif; ?>

    <div class="well">
      <form action="alumnado.php" method="post">
        <fieldset>
          <legend>Familia y alumnado</legend>

          <div class="row">

            <div class="col-sm-5">
              <?php if (! acl_permiso($_SESSION['cargo'], array('2'))): ?>
              <div class="form-group">
                <label for="unidad">Unidad</label>
                <select class="form-control" id="unidad" name="unidad" onchange="submit()">
                  <option value="">Todas las unidades</option>
                  <?php foreach ($unidades as $unidad): ?>
                  <option value="<?php echo $unidad; ?>"<?php echo ($unidad == $unidadSeleccion) ? ' selected' : ''; ?>><?php echo $unidad; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php endif; ?>

              <div class="form-group">
                <label for="alumnos">Alumnos</label>
                <p>
                  <button type="button" class="btn btn-default btn-sm" id="seleccionarTodo"><i class="far fa-check-square"></i> Seleccionar todo</button>
                  <button type="button" class="btn btn-default btn-sm" id="deseleccionarTodo"><i class="far fa-square"></i> Deseleccionar todo</button>
                </p>
                <select class="form-control" id="alumnos" name="alumnos[]" size="<?php echo (acl_permiso($_SESSION['cargo'], array('2'))) ? '12': '8'; ?>" multiple required>
                  <?php foreach ($alumnado as $alumno): ?>
                  <option value="<?php echo $alumno['nie']; ?>"<?php echo (in_array($alumno['nie'], $alumnosSeleccionados)) ? ' selected' : ''; ?>><?php echo $alumno['unidad']; ?> - <?php echo $alumno['apellidos'].', '.$alumno['nombre']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

            </div><!-- /.col-sm-5 -->

            <div class="col-sm-7">

              <div class="form-group">
                <label for="mensaje">Mensaje</label>
                <textarea class="form-control" name="mensaje" id="mensaje" class="mensaje" rows="8" maxlength="160" placeholder="Introduzca el texto" required><?php echo (isset($mensaje) && ! empty($mensaje)) ? $mensaje : ''; ?></textarea>
                <p>
                  <small>
                    <i class="fas fa-users fa-fw"></i> Destinatarios: <strong id="numeroDestinatarios">0</strong></strong> &middot;
                    <i class="fas fa-font fa-fw"></i> Caracteres disponibles: <strong id="numeroCaracteres"><?php echo $limite_caracteres_sms; ?></strong> / <strong><?php echo $limite_caracteres_sms; ?></strong>
                  </small>
                </p>
              </div>

              <div class="form-group">
                <label for="causa">Motivo del mensaje</label>
                <select class="form-control" id="causa" name="causa" required>
                  <option value=""></option>
                  <?php foreach ($causas as $causa): ?>
                  <option value="<?php echo $causa; ?>" <?php echo ($causa == $causaSeleccionada) ? ' selected' : ''; ?>><?php echo $causa; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

            </div><!-- /.col-sm-7 -->

          </div><!-- /.row -->

          <div class="row">
            <div class="col-sm-12">
              <button type="submit" class="btn btn-primary" name="enviar">Enviar SMS</button>
            </div>
          </div>

        </fieldset>
      </form>
    </div><!-- /.well -->

  </div>

<?php include("../pie.php"); ?>

  <script>
  function alumnosSeleccionados() {
    $("#alumnos")
    .change(function() {
      var cont = 0;
      $("#alumnos option:selected").each(function() {
        cont++;
      });
      $("#numeroDestinatarios").text(cont);
    })
    .trigger("change");
  }
  alumnosSeleccionados();

  $('#seleccionarTodo').click( function() {
    $('#alumnos option').prop('selected', true);
    alumnosSeleccionados();
  });

  $('#deseleccionarTodo').click( function() {
    $('#alumnos option').prop('selected', false);
    alumnosSeleccionados();
  });

  $('#mensaje').keyup(function () {
    var max = <?php echo $limite_caracteres_sms; ?>;
    var len = $(this).val().length;
    if (len <= max) {
      var char = max - len;
      $('#numeroCaracteres').text(char);
    }
  });
  </script>

</body>
</html>
