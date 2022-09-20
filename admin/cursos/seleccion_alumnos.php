<?php
require('../../bootstrap.php');

// Redireccionamos al profesorado de PT o refuerzo a otra página
$es_pt_o_ref = 0;
$result_pt_o_ref = mysqli_query($db_con, "SELECT id FROM horw WHERE (c_asig = '21' OR c_asig = '136') AND prof = '$pr' LIMIT 1");
if (mysqli_num_rows($result_pt_o_ref)) {
  $es_pt_o_ref = 1;
}

if (isset($_POST['guardar_cambios'])) {

    $asignatura_ant = "";
    $unidad_ant = "";
    $array_nc = array();

    $flag_primera_vez = 1;

    foreach($_POST as $key => $val){

        $exp_key = explode(';', $key);
        $asignatura = $exp_key[1];
        $unidad = $exp_key[2];
        $unidad = str_replace('_', ' ', $unidad);
        $nc = $exp_key[3];

        if ($asignatura != $asignatura_ant || $unidad != $unidad_ant) {
            // Saltamos este bloque la primera vez que se ejecuta foreach o si key corresponde al botón de envío de formulario
            if ($flag_primera_vez > 1 || $key != 'guardar_cambios') {
                $result_alumnos_grupo = mysqli_query($db_con, "SELECT alma.apellidos, alma.nombre, alma.claveal FROM alma WHERE alma.unidad = '".$unidad_ant."' ORDER BY alma.apellidos ASC, alma.nombre ASC");
                $total_alumnos_grupo = mysqli_num_rows($result_alumnos_grupo);

                $result_alumnos = mysqli_query($db_con, "SELECT alma.apellidos, alma.nombre, alma.claveal FROM alma WHERE alma.unidad = '".$unidad_ant."' AND alma.combasi LIKE '%".$asignatura_ant."%' ORDER BY alma.apellidos ASC, alma.nombre ASC");
                $total_alumnos = mysqli_num_rows($result_alumnos);
                $alumnos_seleccionados = count($array_nc);

                if (($total_alumnos != $alumnos_seleccionados) || ($total_alumnos != $total_alumnos_grupo)) {

                    $nc_separado_por_comas = implode(",", $array_nc);

                    // Comprobamos si el profesor ya seleccionó alumnos. En ese caso actualizamos los datos, si no, insertamos en la tabla
                    $result_seleccion = mysqli_query($db_con, "SELECT id FROM grupos WHERE profesor = '$pr' AND asignatura = '$asignatura_ant' AND curso = '$unidad_ant'");
                    if (mysqli_num_rows($result_seleccion)) {
                        $row_seleccion = mysqli_fetch_array($result_seleccion);
                        $id_seleccion = $row_seleccion['id'];
                        mysqli_query($db_con, "UPDATE grupos SET alumnos = '$nc_separado_por_comas' WHERE id = '$id_seleccion'");
                    }
                    else {
                        if (!empty($asignatura_ant) && !empty($nc_separado_por_comas)) {
                        mysqli_query($db_con, "INSERT INTO grupos (profesor, asignatura, curso, alumnos) VALUES ('$pr', '$asignatura_ant', '$unidad_ant', '$nc_separado_por_comas')") or die (mysqli_error($db_con));
                        }
                    }
                }
                else {
                    $result_seleccion = mysqli_query($db_con, "SELECT id FROM grupos WHERE profesor = '$pr' AND asignatura = '$asignatura_ant' AND curso = '$unidad_ant'");
                    if (mysqli_num_rows($result_seleccion)) {
                        $row_seleccion = mysqli_fetch_array($result_seleccion);
                        $id_seleccion = $row_seleccion['id'];

                        mysqli_query($db_con, "DELETE FROM grupos WHERE id = '$id_seleccion'");
                    }
                }

            }

            $array_nc = array();
            array_push($array_nc, $nc);
        }
        else {
            array_push($array_nc, $nc);
        }

        $asignatura_ant = $asignatura;
        $unidad_ant = $unidad;

        $flag_primera_vez = 2;
	}
}

if (isset($_POST['restablecer_seleccion'])) {
    $result_grupos = mysqli_query($db_con, "SELECT id FROM grupos WHERE profesor = '$pr'");
    if (mysqli_num_rows($result_grupos)) {
        mysqli_query($db_con, "DELETE FROM grupos WHERE profesor = '$pr'");
    }
}

include("../../menu.php");
include("../informes/menu_alumno.php");

?>
    <?php if (isset($es_pt_o_ref) && $es_pt_o_ref): ?>
    <div class="container">
      <ul class="nav nav-tabs">
        <li class="active"><a href="seleccion_alumnos.php">Por materias</a></li>
        <li><a href="seleccion_alumnos_pt_refuerzo.php">Refuerzo Pedagógico o Pedagogía Terapéutica</a></li>
      </ul>
    </div>
    <?php endif; ?>

    <div class="container">

        <!-- TITULO DE LA PAGINA -->
        <div class="page-header">
            <h2>Mi alumnado <small>Selección de alumnos por materias</small></h2>
        </div>

        <!-- SCAFFOLDING -->
        <div class="row">

            <div class="col-sm-12">
                <div class="alert alert-info">
                    <p>Desmarca aquellos alumnos a los que no impartes materia. Esto evitará que aparezca las notificaciones de Informes de tareas o tutoría, o aparezca en tus listados de grupos y faltas de asistencia.</p>
                </div>

                <?php $result = mysqli_query($db_con, "SELECT DISTINCT p.nivel, p.materia, p.grupo, m.codigo FROM profesores AS p, asignaturas AS m WHERE p.profesor = '".$pr."' AND p.materia = m.nombre AND p.nivel = m.curso AND m.abrev NOT LIKE '%\_%'"); ?>
                <?php if (mysqli_num_rows($result)): ?>
                <form action="" method="post">
                    <div class="panel-group" id="materias" role="tablist" aria-multiselectable="true">

                        <?php $i = 0; ?>
                        <?php while ($row = mysqli_fetch_array($result)): ?>

                        <?php 
                        $asig_bach = mysqli_query($db_con,"select distinct codigo from materias where nombre like (select distinct nombre from materias where codigo = '".$row['codigo']."' limit 1) and grupo like '".$row['grupo']."' and codigo not like '".$row['codigo']."' and abrev not like '%\_%'");
                            if (mysqli_num_rows($asig_bach)>0) {
                                $esBachillerato = 1;
                                $as_bach=mysqli_fetch_array($asig_bach);
                                $cod_asig_bach = $as_bach[0];
                                $aux=" (combasi like '%".$row['codigo'].":%' or combasi like '%$cod_asig_bach:%')";
                            }
                            else{
                                $aux=" combasi like '%".$row['codigo'].":%'";   
                            }

                        $result_alumnos = mysqli_query($db_con, "SELECT alma.apellidos, alma.nombre, alma.claveal FROM alma WHERE alma.unidad = '".$row['grupo']."' AND ".$aux." ORDER BY alma.apellidos ASC, alma.nombre ASC");
                        ?>
                        <?php $total_alumnos_unidad = mysqli_num_rows($result_alumnos); ?>

                        <?php
                        $nc_alumnos_seleccionados = array();
                        $result_alumnos_seleccionados = mysqli_query($db_con, "SELECT alumnos FROM grupos WHERE profesor = '".$pr."' AND asignatura = '".$row['codigo']."' AND curso = '".$row['grupo']."'");
                        if (mysqli_num_rows($result_alumnos_seleccionados)) {
                            $row_alumnos_seleccionados = mysqli_fetch_array($result_alumnos_seleccionados);
                            $alumnos_seleccionados = $row_alumnos_seleccionados['alumnos'];
                            $alumnos_seleccionados = rtrim($alumnos_seleccionados, ',');
                            $nc_alumnos_seleccionados =  explode(',', $alumnos_seleccionados);
                            $total_alumnos_seleccionados = count($nc_alumnos_seleccionados);
                        }
                        else {
                            $total_alumnos_seleccionados = $total_alumnos_unidad;
                        }
                        ?>

                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="heading<?php echo $i; ?>">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#materias" href="#collapse<?php echo $i; ?>" aria-expanded="true" aria-controls="collapse<?php echo $i; ?>" style="display: block;">
                                    <?php echo $row['grupo'].' <small>('.$row['nivel'].')</small> - '.$row['materia'].' <span class="text-muted pull-right">'.$total_alumnos_seleccionados.'/'.$total_alumnos_unidad.' <span class="hidden-xs">alumnos</span></span>'; ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse<?php echo $i; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $i; ?>">
                                <div class="panel-body">

                                    <?php while ($row_alumno = mysqli_fetch_array($result_alumnos)): ?>
									<?php 
									$bilingue = "";
									$matr = mysqli_fetch_array(mysqli_query($db_con,"select bilinguismo from matriculas_bach where claveal= '".$row_alumno['claveal']."'")); 
									$bil = $matr[0];
									if($bil == "Si"){ $bilingue = " (Bil.)";}
									?>
                                    <?php
                                    $nombre_checkbox = 'checkbox;'.$row['codigo'].';'.$row['grupo'].';'.$row_alumno['claveal'];
                                    if (mysqli_num_rows($result_alumnos_seleccionados) > 0 && in_array($row_alumno['claveal'], $nc_alumnos_seleccionados)) {
                                        $checkbox_checked = "checked";
                                    }
                                    elseif (! mysqli_num_rows($result_alumnos_seleccionados)) {
                                        $checkbox_checked = "checked";
                                    } else {
                                        $checkbox_checked = "";
                                    }
                                    ?>
                                    <div class="checkbox">
                                        <label for="<?php echo $nombre_checkbox; ?>">
                                            <input type="checkbox" name="<?php echo $nombre_checkbox; ?>" id="<?php echo $nombre_checkbox; ?>" value="1" <?php echo $checkbox_checked; ?>> <?php echo $row_alumno['apellidos'].', '.$row_alumno['nombre'].$bilingue; ?>
                                        </label>
                                    </div>
                                    <?php endwhile; ?>

                                </div>
                            </div>
                        </div>
                        <?php $i++; ?>
                        <?php endwhile; ?>

                    </div>

                    <button type="submit" class="btn btn-primary" name="guardar_cambios">Guardar cambios</button>
                    <button type="submit" class="btn btn-default" name="restablecer_seleccion">Restablecer selección</button>
                </form>

                <?php else: ?>

                <div class="well text-center">
                    <p class="lead">No tienes materias asignadas.</p>
                    <p>O bien no has registrado el horario en Séneca o aún no se ha actualizado la información en la Intranet.</p>
                    <p>Contacta con algún miembro del equipo directivo para actualizar la información.</p>
                </div>

                <?php endif; ?>
            </div>

        </div><!-- /.row -->

    </div><!-- /.container -->

	<?php include("../../pie.php"); ?>

</body>
</html>
