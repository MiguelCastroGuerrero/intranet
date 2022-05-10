<?php
require("../bootstrap.php");

$limite_caracteres_sms = 160;

acl_acceso($_SESSION['cargo'], array('1', '2', '4', '8'));

if (acl_permiso($_SESSION['cargo'], array('1', '8'))) {

  // Obtenemos las departamentos del centro
  $departamentos = array();
  $result = mysqli_query($db_con, "SELECT DISTINCT `departamento` FROM `departamentos` WHERE `departamento` <> 'Admin' AND `departamento` <> 'Administracion' AND `departamento` <> 'Conserjeria' AND `departamento` <> '' ORDER BY `departamento` ASC");
  while ($row = mysqli_fetch_array($result)) {
    array_push($departamentos, $row['departamento']);
  }

  // Obtenemos la unidad seleccionada del formulario
  if (isset($_POST['departamentos']) && in_array($_POST['departamentos'], $departamentos)) {
    $departamentoSeleccion = $_POST['departamentos'];
  }
}

// Obtenemos los profesores del centro con número de teléfono móvil registrado
$empleados = array();
if (acl_permiso($_SESSION['cargo'], array('4'))) {
  $result = mysqli_query($db_con, "SELECT `departamentos`.`departamento`, `c_profes`.`idea`, `c_profes`.`profesor`, `c_profes`.`telefono` FROM `c_profes` JOIN `departamentos` ON `c_profes`.`idea` = `departamentos`.`idea` WHERE `departamentos`.`departamento` = '".$_SESSION['dpt']."' ORDER BY `departamentos`.`departamento` ASC, `c_profes`.`profesor` ASC");
}
elseif (acl_permiso($_SESSION['cargo'], array('1', '8'))) {
  if (isset($departamentoSeleccion) && ! empty($departamentoSeleccion)) {
    $result = mysqli_query($db_con, "SELECT `departamentos`.`departamento`, `c_profes`.`idea`, `c_profes`.`profesor`, `c_profes`.`telefono` FROM `c_profes` JOIN `departamentos` ON `c_profes`.`idea` = `departamentos`.`idea` WHERE `departamentos`.`departamento` = '".$departamentoSeleccion."' ORDER BY `departamentos`.`departamento` ASC, `c_profes`.`profesor` ASC");
  }
  else {
    $result = mysqli_query($db_con, "SELECT `departamentos`.`departamento`, `c_profes`.`idea`, `c_profes`.`profesor`, `c_profes`.`telefono` FROM `c_profes` JOIN `departamentos` ON `c_profes`.`idea` = `departamentos`.`idea` ORDER BY `departamentos`.`departamento` ASC, `c_profes`.`profesor` ASC");
  }
}

while ($row = mysqli_fetch_array($result)) {

  $movil = "";

  if ((strlen($row['telefono']) == 9 && (substr($row['telefono'], 0, 1) == 6 || substr($row['telefono'], 0, 1) == 7))) {

    $movil = $row['telefono'];

    if (strstr($row['profesor'], ',') == true) {
      $exp_profesor = explode(',', $row['profesor']);
      $apellidos = trim($exp_profesor[0]);
      $nombre = trim($exp_profesor[1]);
    }
    else {
      $apellidos = '';
      $nombre = $row['profesor'];
    }

    $empleado = array(
      'idea'              => $row['idea'],
      'apellidos'         => $apellidos,
      'nombre'            => $nombre,
      'departamento'      => $row['departamento'],
      'movil'             => $movil
    );

    array_push($empleados, $empleado);
  }
}
unset($empleado);

// Procesamos el formulario
if (isset($_POST['enviar'])) {
  $msg_error = 0;

  if (isset($_POST['empleados']) && is_array($_POST['empleados'])) {
    $empleadosSeleccionados = $_POST['empleados'];
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

  if (! $msg_error) {
    $fecha = date('Y-m-d H:i:s'); 
    $cont = 0;
    $recipients = array();
    foreach ($empleadosSeleccionados as $empleadoSeleccionado) {
      $cont++;
      $key = array_search($empleadoSeleccionado, array_column($empleados, 'idea'));
      $movil = $empleados[$key]['movil'];
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
        unset($empleadosSeleccionados);
        unset($mensaje);
    }
    else {
      $msg_error = "No se ha podido enviar el SMS.";
    }

  }
}

include("../menu.php");
if (acl_permiso($_SESSION['cargo'], array('4'))) {
  include("../admin/mensajes/menu.php");
}
else {
  include("menu.php");
}
?>

  <div class="container">

    <div class="page-header">
      <h2>SMS <small>Profesorado</small></h2>
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
      <form action="profesorado.php" method="post">
        <fieldset>
          <legend>Profesorado</legend>

          <div class="row">

            <div class="col-sm-5">
              <?php if (! acl_permiso($_SESSION['cargo'], array('4'))): ?>
              <div class="form-group">
                <label for="departamentos">Departamentos</label>
                <select class="form-control" id="departamentos" name="departamentos" onchange="submit()">
                  <option value="">Todos los departamentos</option>
                  <?php foreach ($departamentos as $departamento): ?>
                  <option value="<?php echo $departamento; ?>"<?php echo ($departamento == $departamentoSeleccion) ? ' selected' : ''; ?>><?php echo $departamento; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php endif; ?>

              <div class="form-group">
                <label for="empleados">Profesorado</label>
                <p>
                  <button type="button" class="btn btn-default btn-sm" id="seleccionarTodo"><i class="far fa-check-square"></i> Seleccionar todo</button>
                  <button type="button" class="btn btn-default btn-sm" id="deseleccionarTodo"><i class="far fa-square"></i> Deseleccionar todo</button>
                </p>
                <select class="form-control" id="empleados" name="empleados[]" size="<?php echo (acl_permiso($_SESSION['cargo'], array('4'))) ? '12': '8'; ?>" multiple required>
                  <?php foreach ($empleados as $empleado): ?>
                  <option value="<?php echo $empleado['idea']; ?>"<?php echo (in_array($empleado['idea'], $empleadosSeleccionados)) ? ' selected' : ''; ?>><?php echo $empleado['departamento']; ?> - <?php echo (! empty($empleado['apellidos'])) ? $empleado['apellidos'].', '.$empleado['nombre'] : $empleado['nombre']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

            </div><!-- /.col-sm-5 -->

            <div class="col-sm-7">

              <div class="form-group">
                <label for="mensaje">Mensaje</label>
                <textarea class="form-control" name="mensaje" id="mensaje" class="mensaje" rows="12" maxlength="160" placeholder="Introduzca el texto" required><?php echo (isset($mensaje) && ! empty($mensaje)) ? $mensaje : ''; ?></textarea>
                <p>
                  <small>
                    <i class="fas fa-users fa-fw"></i> Destinatarios: <strong id="numeroDestinatarios">0</strong></strong> &middot;
                    <i class="fas fa-font fa-fw"></i> Caracteres disponibles: <strong id="numeroCaracteres"><?php echo $limite_caracteres_sms; ?></strong> / <strong><?php echo $limite_caracteres_sms; ?></strong>
                  </small>
                </p>
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
  function empleadosSeleccionados() {
    $("#empleados")
    .change(function() {
      var cont = 0;
      $("#empleados option:selected").each(function() {
        cont++;
      });
      $("#numeroDestinatarios").text(cont);
    })
    .trigger("change");
  }
  empleadosSeleccionados();

  $('#seleccionarTodo').click( function() {
    $('#empleados option').prop('selected', true);
    empleadosSeleccionados();
  });

  $('#deseleccionarTodo').click( function() {
    $('#empleados option').prop('selected', false);
    empleadosSeleccionados();
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
