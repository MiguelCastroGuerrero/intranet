<?php
require('../../bootstrap.php');

function abreviatura_tipo($tipo) {
  switch ($tipo) {
    case 'Instituto de Educación Secundaria': return 'IES'; break;
    case 'Colegio de Educación Infantil y Primaria': return 'CEIP'; break;
    case 'Colegio de Educación Primaria': return 'CEPr'; break;
    default: return $tipo;
  }
}

acl_acceso($_SESSION['cargo'], array(1));

// https://www.juntadeandalucia.es/datosabiertos/portal/api/3/action/package_show?id=directorio-de-centros-docentes-de-andalucia

// Asignamos el nombre al archivo de descarga
$file = INTRANET_DIRECTORY.'/admin/matriculas/centroseducativosandalucia.csv';

// Eliminamos el archivo si existiese y creamos uno nuevo
if (file_exists($file)) unlink($file);

$fp = fopen($file, "w");

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://www.juntadeandalucia.es/datosabiertos/portal/dataset/e039df22-4b82-4d0d-9884-0ab5952e24e4/resource/28f27960-079a-4ec7-ab7a-b7e972572575/download/centroseducativosandalucia.csv");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_FILE, $fp);

curl_exec($ch);
curl_close($ch);
fclose($fp);

$centros = array();

$fila = 0;
$i = 0;


if (($gestor = fopen($file, "r")) !== FALSE) {
    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
        
        $fila++;

        if ($fila > 1) {
          $numero = count($datos);
          if (! empty($datos[0])) {
            for ($c=0; $c < $numero; $c++) {
              switch ($c) {
                case 0: $centros[$i]['codigo'] = $datos[$c]; break;
                case 1: $centros[$i]['tipo'] = $datos[$c]; break;
                case 2: $centros[$i]['nombre'] = $datos[$c]; break;
                case 3: $centros[$i]['titularidad'] = $datos[$c]; break;
                case 4: $centros[$i]['domicilio'] = $datos[$c]; break;
                case 5: $centros[$i]['localidad'] = $datos[$c]; break;
                case 6: $centros[$i]['municipio'] = $datos[$c]; break;
                case 7: $centros[$i]['provincia'] = $datos[$c]; break;
                case 8: $centros[$i]['codpostal'] = $datos[$c]; break;
                case 8: $centros[$i]['telefono'] = $datos[$c]; break;
              }
            }
          }

          $i++;
      }
    }
    fclose($gestor);
}

unlink($file);

// PROCESAMOS EL FORMULARIO
if (! isset($_POST['centro_educativo']) || ! isset($_FILES['archivo1'])) {
  $es_error = 1;
  $msg_error = "No ha seleccionado centro educativo o archivo RegAlum.txt";
}
else {

  $es_error = 0;

  // Eliminamos tabla alma_primaria
  mysqli_query($db_con, "DROP TABLE `alma_primaria`;");

  // Creación de la tabla alma_primaria
  $create_table = "CREATE TABLE  `alma_primaria` (
  `Alumno/a` varchar( 255 ) default NULL ,
  `ESTADOMATRICULA` varchar( 255 ) default NULL ,
  `CLAVEAL` varchar( 12 ) ,
  `DNI` varchar( 10 ) default NULL ,
  `DOMICILIO` varchar( 255 ) default NULL ,
  `CODPOSTAL` varchar( 255 ) default NULL ,
  `LOCALIDAD` varchar( 255 ) default NULL ,
  `FECHA` varchar( 255 ) default NULL ,
  `PROVINCIARESIDENCIA` varchar( 255 ) default NULL ,
  `TELEFONO` varchar( 255 ) default NULL ,
  `TELEFONOURGENCIA` varchar( 255 ) default NULL ,
  `TELEFONOALUMNO` varchar( 255 ) default NULL ,
  `CORREOALUMNO` varchar( 64 ) default NULL ,
  `CORREO` varchar( 64 ) default NULL ,
  `CURSO` varchar( 255 ) default NULL ,
  `NUMEROEXPEDIENTE` varchar( 255 ) default NULL ,
  `UNIDAD` varchar( 255 ) default NULL ,
  `apellido1` varchar( 255 ) default NULL ,
  `apellido2` varchar( 255 ) default NULL ,
  `NOMBRE` varchar( 30 ) default NULL ,
  `DNITUTOR` varchar( 255 ) default NULL ,
  `PRIMERAPELLIDOTUTOR` varchar( 255 ) default NULL ,
  `SEGUNDOAPELLIDOTUTOR` varchar( 255 ) default NULL ,
  `NOMBRETUTOR` varchar( 255 ) default NULL ,
  `CORREOTUTOR` varchar( 255 ) default NULL ,
  `TELEFONOTUTOR` char( 9 ) default NULL ,
  `SEXOPRIMERTUTOR` varchar( 255 ) default NULL ,
  `DNITUTOR2` varchar( 255 ) default NULL ,
  `PRIMERAPELLIDOTUTOR2` varchar( 255 ) default NULL ,
  `SEGUNDOAPELLIDOTUTOR2` varchar( 255 ) default NULL ,
  `CORREOTUTOR2` varchar( 255 ) default NULL ,
  `NOMBRETUTOR2` varchar( 255 ) default NULL ,
  `SEXOTUTOR2` varchar( 255 ) default NULL ,
  `TELEFONOTUTOR2` char( 9 ) default NULL ,
  `LOCALIDADNACIMIENTO` varchar( 255 ) default NULL ,
  `ANOMATRICULA` varchar( 4 ) default NULL ,
  `MATRICULAS` varchar( 255 ) default NULL ,
  `OBSERVACIONES` varchar( 255 ) default NULL,
  `PROVINCIANACIMIENTO` varchar( 255 ) default NULL ,
  `PAISNACIMIENTO` varchar( 255 ) default NULL ,
  `EDAD` varchar( 2 ) default NULL ,
  `NACIONALIDAD` varchar( 32 ) default NULL,
  `SEXO` varchar( 1 ) default NULL ,
  `FECHAMATRICULA` varchar( 255 ) default NULL,
  `NSEGSOCIAL` varchar( 15 ) default NULL, 
  `ENFERMEDAD` varchar( 255 ) default NULL,
  `TRATAMIENTO` varchar( 255 ) default NULL,
  `ALERGIAMEDICAMENTOS` varchar( 255 ) default NULL,
  `INTOLERANCIAS` varchar( 255 ) default NULL,
  `CUSTODIA` varchar( 255 ) default NULL,
  `FAMILIANUMEROSA` varchar( 255 ) default NULL,
  `COLEGIO` varchar( 96 ) default NULL,
  PRIMARY KEY (`CLAVEAL`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci ";

  $result = mysqli_query($db_con, $create_table);

  if (! $result) {
    $es_error = 1;
    $msg_error = "Ha ocurrido un error al crear la tabla alma_primaria.";
  }
  else {
    // Creamos índice de la tabla
    mysqli_query($db_con, "ALTER TABLE `alma_primaria` ADD INDEX (`CLAVEAL`);");

    // Borramos datos del curso anterior
    mysqli_query($db_con, "TRUNCATE TABLE `transito_datos`;");


    // Obtenemos el tipo y nombre del centro adscrito
    $codigo_centro_adscrito = limpiarInput($_POST['centro_educativo'],'numeric');

    foreach($centros as $centro) {
      if ($centro['codigo'] == $codigo_centro_adscrito) {
        $nombre_centro_adscrito = $centro['nombre'];
        $nombre_completo_centro_adscrito = abreviatura_tipo($centro['tipo']).' '.$centro['nombre'];
        break;
      }
    }
    
    // GENERACIÓN DE CREDENCIALES

    // Comprobamos si el centro está registrado en la tabla
    $result = mysqli_query($db_con, "SELECT `id` FROM `transito_control` WHERE `codcentro` = '$codigo_centro_adscrito' LIMIT 1;");

    if (! mysqli_num_rows($result)) {
      // Generamos credenciales para el centro educativo
      $contrasena_aleatoria = generateRandomPassword(20);
      $contrasena_cifrada = cifrarTexto($contrasena_aleatoria);
      $contrasena_aleatoria_bcrypt = intranet_password_hash($contrasena_aleatoria);

      $result = mysqli_query($db_con, "INSERT INTO `transito_control` (`codcentro`, `colegio`, `pass`, `pass_cifrada`) VALUES ('$codigo_centro_adscrito', '$nombre_completo_centro_adscrito', '$contrasena_aleatoria_bcrypt', '$contrasena_cifrada');");
      if (! $result) {
        $es_error = 1;
        $msg_error = "No se ha podido generar las credenciales para el centro educativo adscrito.";
      }
      else {
        $credendeciales_generadas = 1;
      }
    }
    else {
      $credendeciales_generadas = 0;
    }

    // FIN GENERACIÓN DE CREDENCIALES
    if (! $es_error) {
      
      if (! $fp = fopen($_FILES['archivo1']['tmp_name'], "r")) {
        $msg_error = "Ha ocurrido un error al leer el archivo RegAlum.txt.";
      }
      else {

        $row = 0;
        $cuenta_registros = 0;
        while (!feof($fp)) {
          $row++;

          $linea="";
          $lineasalto="";
          $dato="";
          $linea=fgets($fp);

          // Saltamos el encabezado del archivo TXT
          if ($row > 8) {
            $lineasalto = "INSERT INTO alma_primaria VALUES (";
            $tr=explode("|",$linea);
            
            foreach ($tr as $valor){ 
              $dato.= "\"". trim(utf8_encode($valor)) . "\", ";
                }
            $dato=substr($dato,0,strlen($dato)-2); 
            $lineasalto.=$dato; 
            $lineasalto.=", \"".$nombre_completo_centro_adscrito."\"";
            $lineasalto.=");";
            //echo $lineasalto."<br>";
            mysqli_query($db_con, $lineasalto);
            $cuenta_registros++;
          }
        }
        fclose($fp);

        // EL PROCESO DE AQUÍ PARA ABAJO DEBERÍA DESAPARECER

        // Procesamos datos
        mysqli_query($db_con, "ALTER TABLE  alma_primaria
        ADD  `APELLIDOS` VARCHAR( 40 ) NULL AFTER  `UNIDAD` ,
        ADD  `NIVEL` VARCHAR( 5) NULL AFTER  `NOMBRE` ,
        ADD  `GRUPO` VARCHAR( 1 ) NULL AFTER  `NIVEL`,
        ADD  `PADRE` VARCHAR( 78 ) NULL AFTER  `GRUPO`");

        // Separamos Nivel y Grupo si sigue el modelo clásico del guión (1E-F, 2B-C, etc)
        $result = mysqli_query($db_con, "SELECT UNIDAD, CLAVEAL FROM alma_primaria");  
        while ($row = mysqli_fetch_array($result)) {
          if (substr($row[0],-1)=="A") {
            $unidad_cole = "6P-A";
          }
          elseif (substr($row[0],-1)=="B") {
            $unidad_cole = "6P-B";
          }
          elseif (substr($row[0],-1)=="C") {
            $unidad_cole = "6P-C";
          }
          elseif (substr($row[0],-1)=="D") {
            $unidad_cole = "6P-D";
          }
          else {
            $unidad_cole = "6P-A";
          }   
          $trozounidad0 = explode("-",$unidad_cole);
          $actualiza= "UPDATE alma_primaria SET UNIDAD = '$unidad_cole', NIVEL = '$trozounidad0[0]', GRUPO = '$trozounidad0[1]' where CLAVEAL = '$row[1]'";
          mysqli_query($db_con, $actualiza);
        }

        // Apellidos unidos formando un solo campo.
        $result = mysqli_query($db_con, "SELECT apellido1, apellido2, CLAVEAL, NOMBRE FROM alma_primaria");
        while ($row = mysqli_fetch_array($result)) {
          $apellidos = trim($row[0]). " " . trim($row[1]);
          $apellidos1 = trim($apellidos);
          $nombre = $row[3];
          $nombre1 = trim($nombre);
          $actualiza1= "UPDATE alma_primaria SET APELLIDOS = \"". $apellidos1 . "\", NOMBRE = \"". $nombre1 . "\" where CLAVEAL = \"". $row[2] . "\"";
          mysqli_query($db_con, $actualiza1);
        }

        // Apellidos y nombre del padre.
        $result = mysqli_query($db_con, "SELECT PRIMERAPELLIDOTUTOR, SEGUNDOAPELLIDOTUTOR, NOMBRETUTOR, CLAVEAL FROM alma_primaria");
        while  ($row = mysqli_fetch_array($result)) {
          $apellidosP = trim($row[2]). " " . trim($row[0]). " " . trim($row[1]);
          $apellidos1P = trim($apellidosP);
          $actualiza1P= "UPDATE alma_primaria SET PADRE = \"". $apellidos1P . "\" where CLAVEAL = \"". $row[3] . "\"";
          mysqli_query($db_con, $actualiza1P);
        }

        // Eliminación de campos innecesarios por repetidos
        mysqli_query($db_con, "ALTER TABLE alma_primaria
        DROP `apellido1`,
        DROP `Alumno/a`,
        DROP `apellido2`,
        DROP `estadomatricula`");

        // FIN PROCESAMIENTO ALMA_PRIMARIA

        $alumnos_centro_adscrito = array();
        $result = mysqli_query($db_con, "SELECT `apellidos`, `nombre`, `claveal`, `nacionalidad`, `unidad` FROM `alma_primaria` WHERE `colegio` = '$nombre_completo_centro_adscrito';");
        while ($row_alumno = mysqli_fetch_array($result)) {
          $alumno = array(
            'apellidos' => $row_alumno['apellidos'],
            'nombre' => $row_alumno['nombre'],
            'claveal' => $row_alumno['claveal'],
            'unidad' => $row_alumno['unidad'],
            'nacionalidad' => $row_alumno['nacionalidad']
          );
          array_push($alumnos_centro_adscrito, $alumno);
        }

      }

    }
  }

}


include("../../menu.php");
?>

<div class="container">

  <div class="page-header">
    <h2>Administración <small>Importación alumnado de Primaria de centros adscritos</small></h2>
  </div>

  <?php if (isset($msg_error)): ?>
  <div class="alert alert-danger">
    <p><strong>Error:</strong> <?php echo $msg_error; ?></p>
  </div>
  <?php endif; ?>

   <?php if (isset($msg_success)): ?>
  <div class="alert alert-danger">
    <p><?php echo $msg_success; ?></p>
  </div>
  <?php endif; ?>

  
  <div class="row">

    <div class="col-sm-12">

    <?php if (! $es_error): ?>
      <h4>Centro educativo: <?php echo $nombre_completo_centro_adscrito; ?></h4>

      <?php if ($credendeciales_generadas): ?>
      <br>
      <p>Se han generado las siguientes credenciales de acceso para el centro adscrito:</p>
      <br>

      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Usuario</th>
            <th>Contraseña</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo $codigo_centro_adscrito; ?></td>
            <td><?php echo descifrarTexto($contrasena_cifrada); ?></td>
          </tr>
        </tbody>
      </table>
      <?php endif; ?>
      
      <br>
      <p>Se han importado <?php echo $cuenta_registros; ?> alumnos/as a la base de datos.</p>
      <br>

      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Alumno/a</th>
            <th>NIE</th>
            <th>Unidad</th>
            <th>Nacionalidad</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($alumnos_centro_adscrito as $alumno): ?>
          <tr>
            <td><?php echo $alumno['apellidos'].', '.$alumno['nombre']; ?></td>
            <td><?php echo $alumno['claveal']; ?></td>
            <td><?php echo $alumno['unidad']; ?></td>
            <td><?php echo $alumno['nacionalidad']; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>

      <input type="button" value="Volver atrás" name="boton" onClick="history.back(2)" class="btn btn-primary btn-sm" />

    </div><!-- /.col-sm-12 -->

  </div><!-- /.row -->


</div>

<?php include("../../pie.php"); ?>
