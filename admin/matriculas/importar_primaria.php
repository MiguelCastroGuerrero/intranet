<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1));

function abreviatura_tipo($tipo) {
  switch ($tipo) {
    case 'Instituto de Educación Secundaria': return 'IES'; break;
    case 'Colegio de Educación Infantil y Primaria': return 'CEIP'; break;
    case 'Colegio de Educación Primaria': return 'CEPr'; break;
    default: return $tipo;
  }
}

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


// Obtenemos las credenciales de los centros adscritos registrados y número de alumnos importados
$centros_adscritos = array();
$result = mysqli_query($db_con, "SELECT `codcentro`, `colegio`, `pass_cifrada` FROM `transito_control`;");
if ($row_centros_adscritos = mysqli_fetch_array($result)) {

  $result_alumnos = mysqli_query($db_con, "SELECT `claveal` FROM `alma_primaria` WHERE `colegio` = '".$row_centros_adscritos['colegio']."';");

  $centro_adscrito = array(
    'codigo' => $row_centros_adscritos['codcentro'],
    'nombre' => $row_centros_adscritos['colegio'],
    'clave' => $row_centros_adscritos['pass_cifrada'],
    'alumnos' => mysqli_num_rows($result_alumnos)
  );
  array_push($centros_adscritos, $centro_adscrito);
}

include("../../menu.php");
include("menu.php");
?>
<div class="container">

  <div class="page-header">
    <h2>Matriculación de Alumnos <small>Importación alumnos de Primaria</small></h2>
  </div>

  <div class="row">

    <div class="col-sm-6">
      <form enctype="multipart/form-data" action="../../xml/jefe/alma_primaria.php" method="post">
        <div class="well well-large">
          <fieldset>
            <legend>Importación alumnos de Primaria</legend>
            <div class="form-group">
              <label for="centro_educativo" class="text-info">Seleccione centro educativo adscrito</label>
              <select id="centro_educativo" name="centro_educativo" class="form-control">
              <?php foreach($centros as $centro): ?>
                <?php if ($centro['titularidad'] == "Público" && $centro['municipio'] == $config['centro_localidad'] && stripos($centro['tipo'], "Primaria") !== false): ?>
                <option value="<?php echo $centro['codigo']; ?>"><?php echo $centro['codigo']; ?> - <?php echo abreviatura_tipo($centro['tipo']); ?> <?php echo $centro['nombre']; ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
              </select>
            </div>

            <br>

            <div class="form-group">
              <label for="archivo1" class="text-info">RegAlum.txt</label>
              <input type="file" id="archivo1" name="archivo1" accept="text/plain">
            </div>

            <br>

            <button type="submit" name="enviar" class="btn btn-primary">Importar datos</button>
          </fieldset>
        </div>
      </form>
      
      <h4>Centros adscritos registrados</h4>
      <?php if (count($centros_adscritos)): ?>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Centro educativo</th>
            <th>Usuario</th>
            <th>Contraseña</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($centros_adscritos as $centro_adscrito): ?>
          <tr>
            <td><?php echo $centro_adscrito['nombre']; ?></td>
            <td><?php echo $centro_adscrito['codigo']; ?></td>
            <td><?php echo descifrarTexto($centro_adscrito['clave']); ?></td>
            <td><?php echo $centro_adscrito['alumnos']; ?> alumnos</td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
      <p class="text-muted">No hay centros adscritos registrados en la base de datos.</p>
      <?php endif; ?>
    </div><!-- /.col-sm-6 -->

    <div class="col-sm-6">
      <h3>Información sobre la importación</h3>

      <p>El módulo de Matriculación permite importar los datos de los alumnos de colegios adscritos al Centro, 
        facilitando enormemente la tarea al tomar los datos de los alumnos/as. Para contar con los datos de los 
        Colegios, los Directores de los mismos deben proporcionar el archivo <strong>RegAlum.txt</strong> de Séneca.</p>

      <p>Para obtener el archivo de exportación de alumnos debe dirigirse al apartado <strong>Alumnado</strong>,
       <strong>Alumnado del centro</strong>. Muestre todos los alumnos del centro y haga clic en el botón 
       <strong>Exportar datos</strong>. El formato de exportación debe ser <strong>Texto plano</strong>.</p>

      <p><strong>Hay que tener en cuenta que el módulo de importación supone que el formato de las grupos de los Colegios 
        es semejante al de los Institutos</strong>, por lo que se espera que el nombre sea del tipo <strong>6P-A</strong>, 
        <strong>6º A</strong>, <strong>6º Primaria A</strong> etc. Si el Colegio no sigue ese criterio, es necesario editar 
        los archivos de Séneca y buscar / reemplazar el nombre de las Unidades para ajustarlo a los criterios de la Intranet 
        antes de proceder a la importación.</p>

      <p>La importación de los alumnos de Primaria también permite que los Colegios adscritos puedan registrar los Informes 
        de Tránsito de los alumnos de 6º a través del módulo correspondiente de la Página del Centro 
        (<a href="https://<?php echo $config['dominio']; ?>/transito/" target="_blank">https://<?php echo $config['dominio']; ?>/transito/</a>). 
        Estos datos prueden luego ser consultados en Informes de Tránsito en el Módulo de Matriculación de la Intranet.</p>
    </div><!-- /.col-sm-6 -->

  </div><!-- /.row -->

</div><!-- /.container -->

<?php include("../../pie.php"); ?>
