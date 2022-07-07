<?php
require('../../bootstrap.php');

// OBTENEMOS TODOS LOS NIVELES DEL CENTRO EDUCATIVO
$niveles = array();
$result = mysqli_query($db_con, "SELECT `idcurso`, `nomcurso` FROM `cursos` ORDER BY `idcurso` ASC");
while ($row = mysqli_fetch_array($result)) {

    $nivel = array(
        'id'          => $row['idcurso'],
        'nombre'      => $row['nomcurso']
    );

    array_push($niveles, $nivel);
}
mysqli_free_result($result);
unset($nivel);

// OBTENEMOS TODOS LOS LIBROS DE TEXTOS
$libros = array();
$result = mysqli_query($db_con, "SELECT `id`, `isbn`, `ean`, `materia`, `editorial`, `titulo`, `nivel`, `importe`, `programaGratuidad` FROM `libros_texto` ORDER BY `nivel` ASC, `materia` ASC");
while ($row = mysqli_fetch_array($result)) {

    $libro = array(
        'id'        => $row['id'],
        'isbn'      => $row['isbn'],
        'ean'       => $row['ean'],
        'materia'   => $row['materia'],
        'editorial' => $row['editorial'],
        'titulo'    => $row['titulo'],
        'importe'   => str_ireplace('.', ',', $row['importe']),
        'nivel'     => $row['nivel'],
        'gratuidad' => $row['programaGratuidad']
    );

    array_push($libros, $libro);
}
mysqli_free_result($result);
unset($libro);

// Procesamos la variable de selección de curso
if (isset($_GET['curso'])) {
    $curso = urldecode($_GET['curso']);
}
else {
    $curso = $niveles[0]['nombre'];
}

// OBTENEMOS TODAS LAS MATERIAS QUE SE IMPARTEN EN EL CENTRO EDUCATIVO
$materias = array();
$result = mysqli_query($db_con, "SELECT DISTINCT `codigo`, `nombre` FROM `asignaturas` WHERE `curso` = '$curso' AND `abrev` NOT LIKE '%\_%' AND `nombre` NOT LIKE 'Tutoría%' ORDER BY `nombre` ASC");
while ($row = mysqli_fetch_array($result)) {

    $materia = array(
        'codigo'    => $row['codigo'],
        'nombre'    => $row['nombre']
    );

    array_push($materias, $materia);
}
mysqli_free_result($result);
unset($materia);

// OBTENEMOS EL LISTADO DE EDITORIALES
$editoriales = array(
  "Advanced Methods Co. Sociedad de Responsabilidad Limitada de Capital Variable",
  "Algaida Editores, S.A.",
  "Almadraba Editorial-Hermes Editora General",
  "Almuzara,S.L.",
  "Altamar",
  "AMCO (Advanced Methods Co)",
  "Arán Ediciones, S.L.",
  "Arcobaleno 2000, S.L.",
  "Artatac Educación",
  "Aula Digital-Text S.L.",
  "Aulaelectrica.es",
  "Burlington Books, S.A.",
  "Cambridge University Press",
  "Casals S.A.",
  "CESVIMAP",
  "Cultura Clásica, S.L.",
  "DIFUSION",
  "Digital @Tre, S.L.",
  "ECREATUS, S.L.",
  "Edelvives (Editorial Luis Vives, S.A.)",
  "Ediciones Akal, S.A.",
  "Ediciones Alfar, S.A.",
  "Ediciones Aljibe, S.A.",
  "Ediciones Bilingües, S.L.",
  "Ediciones del Genal",
  "Ediciones del Laberinto, S.L.",
  "Ediciones del Serbal, S.L.",
  "Ediciones EO, S.L.U.",
  "Ediciones La Ñ, S.L.",
  "Ediciones Maestro",
  "Ediciones Octaedro, S.L.",
  "Ediciones Paraninfo, S.A.",
  "Ediciones Sandoval, S.L.",
  "Ediciones SM, S.A.",
  "Ediciones Vicens Vives Primaria",
  "Ediciones Vicens Vives, S.A.",
  "Editilde S.L.",
  "Editora Social y Cultural, S.L.",
  "Editorial Alegoría",
  "Editorial Algar",
  "Editorial CSV",
  "Editorial Donostiarra, S.A.",
  "Editorial Ecir, S.A.",
  "Editorial Edelsa",
  "Editorial Editex, S.A.",
  "Editorial E.L.I.",
  "Editorial Elzevir, S.L.",
  "Editorial GEU",
  "Editorial La Calesa, S.A.",
  "Editorial Langenscheidt.",
  "Editorial Linaria del Sur Ediciones, S.L.",
  "Editorial Mad, S.L.",
  "Editorial Marfil, S.A.",
  "Editorial Merial, S.L.",
  "Editorial Murali Bristol SL",
  "Editorial Nadal-Arcada, S.L.",
  "Editorial Paidotribo, S.L.",
  "Editorial Planeta SAU",
  "Editorial Prodidacta Abril Edicions",
  "Editorial Sansy",
  "Editorial Síntesis, S.A.",
  "Editorial Stanley",
  "Editorial Teide, S.A.",
  "Editorial Toro Mítico S.L.",
  "Editorial Vicens Vives S.A.",
  "Ele Medios, S.L.",
  "Eleven Digital-Text, S.L.",
  "ERNST KLETT SPRACHEN GMBH",
  "ESObook",
  "Everest, S.A.",
  "Express Publishing",
  "Galinova Editorial, S.L.",
  "Grupo Anaya, S.A.",
  "Grupo Editorial Bruño, S.L.",
  "Guadiel -Edebé, S.L.",
  "Herder Editorial",
  "Hueber Verlag",
  "Impresiones Ordás",
  "José Abel Fernández Fernández",
  "Kip Ediciones",
  "Klett- Langenscheidt",
  "Klett-edebé",
  "La Galera, S.A.",
  "Linguaframe, S.L.",
  "Macmillan, S.A.",
  "Manualidades Escolares, S.A.",
  "Mcgraw-Hill Interamericana De España, Sau",
  "Milton Education (Kuaderno Solutions,S.L.)",
  "Nueva Carisch España, S.L",
  "Octaedro Andalucía (Ediciones Mágina, S.L.)",
  "Outlook",
  "Oxford University Press España, S.A.",
  "Pearson Educacion, S.A.",
  "Penta Editorial (Monte Alto Ediciones, S.L.)",
  "Pila Teleña, S.L.",
  "Piles, Editorial de Musica S.A.",
  "POLYGON EDUCATION,SL",
  "Proyecto Educa",
  "Proyecto Sur de Ediciones S.L.",
  "Real Musical Publicaciones y Ediciones, S.A.",
  "Saganet Multimedia",
  "San Pablo Comunicación, S.A.",
  "Santillana Grazalema, S.L.",
  "Santillana-Richmond",
  "SD Editores.",
  "Secretariado de Publicaciones de la Universidad de Sevilla",
  "Sgel Libros, S.A.",
  "Snappet",
  "Tabarca Llibres, S.L.",
  "Tekman Books, S.L.",
  "Texto Editores S.L.",
  "Videocinco, S.A.",
  "Wanceulen. Editorial Deportiva, S.L."
);

// IMPORTACIÓN DE LIBROS DE TEXTOS DEL PROGRAMA DE GRATUIDAD
if (acl_permiso($_SESSION['cargo'], array(1)) && (stristr($curso, 'E.S.O.') == true || stristr($curso, 'F.P.B.') == true)) {
    if (isset($_POST['submitImportacion'])) {
        if (isset($_FILES['archivo']) && ! empty($_FILES['archivo']["tmp_name"])) {

            $file = fopen($_FILES['archivo']["tmp_name"], "r") or die("Error: No ha sido posible abrir el archivo.");

            mysqli_query($db_con, "DELETE FROM `libros_texto` WHERE `nivel` = '$curso'");

            $numlinea = 0;
            while (!feof($file)) {
                $numlinea++;

                $linea = fgets($file);

                if ($numlinea > 8) {
                    $exp_linea = explode('|', $linea);

                    $materia    = mysqli_real_escape_string($db_con, utf8_encode(trim($exp_linea[0])));
                    $isbn       = trim($exp_linea[1]);
                    $ean        = trim($exp_linea[2]);
                    $editorial  = mysqli_real_escape_string($db_con, utf8_encode(trim($exp_linea[3])));
                    $titulo     = mysqli_real_escape_string($db_con, utf8_encode(trim($exp_linea[4])));
                    $importe    = trim($exp_linea[7]);
                    if (empty($importe)) {
                        $importe = 0.00;
                    }
                    else {
                        $importe = str_ireplace(',','.', $importe);
                    }

                    if (! empty($isbn) || ! empty($ean)) {
                        $result = mysqli_query($db_con, "INSERT INTO `libros_texto` (`materia`, `isbn`, `ean`, `editorial`, `titulo`, `importe`, `nivel`, `programaGratuidad`) VALUES ('$materia', '$isbn', '$ean', '$editorial', '$titulo', '$importe', '$curso', 1)") or die ("Error: ". mysqli_error($db_con));
                    }
                }
            }
            fclose($file);
            header('Location:'.'index.php?curso='.$curso);
            exit();
        }
        else {
            $msg_error = true;
            $msg_error_text = "No ha seleccionado el archivo.";
        }

    }
}

// OPCIONES PARA EQUIPO DIRECTIVO Y JEFES DE DEPARTAMENTO
if (acl_permiso($_SESSION['cargo'], array(1, 4))) {

    // AÑADIR O EDITAR LIBRO DE TEXTO
    if (isset($_POST['guardarLibro'])) {

        $idlibro    = trim($_POST['idlibro']);
        $materia    = trim(addslashes(strip_tags($_POST['materia'])));
        $isbn       = trim($_POST['isbn']);
        $ean        = trim($_POST['ean']);
        $editorial  = trim(addslashes(strip_tags($_POST['editorial'])));
        $titulo     = trim(addslashes(strip_tags($_POST['titulo'])));
        $importe    = trim(addslashes(strip_tags($_POST['importe'])));
        if (empty($importe)) {
            $importe = 0.00;
        }
        else {
            $importe = str_ireplace(',','.', $importe);
        }

        if (! empty($curso) && ! empty($materia) && (! empty($isbn) || ! empty($ean)) && ! empty($titulo) && ! empty($editorial)) {

            // Comprobamos si se trata de una edición o nuevo libro
            if (! empty($idlibro)) {
                $result = mysqli_query($db_con, "UPDATE `libros_texto` SET `materia` = '$materia', `isbn` = '$isbn', `ean` = '$ean', `editorial` = '$editorial', `titulo` = '$titulo', `importe` = '$importe' WHERE `id` = $idlibro LIMIT 1");

                header('Location:'.'index.php?curso='.$curso);
                exit();
            }
            else {
                // Comprobamos si el ISBN o EAN existen.
                $result_isbn = mysqli_query($db_con, "SELECT `isbn`, `ean`, `titulo`, `nivel` FROM `libros_texto` WHERE `nivel` = '$curso' AND `isbn` <> '' AND `isbn` IS NOT NULL AND `isbn` = '$isbn' LIMIT 1");
                $result_ean = mysqli_query($db_con, "SELECT `isbn`, `ean`, `titulo`, `nivel` FROM `libros_texto` WHERE `nivel` = '$curso' AND `ean` <> '' AND `ean` IS NOT NULL AND `ean` = '$isbn' LIMIT 1");

                if (! mysqli_num_rows($result_isbn) && ! mysqli_num_rows($result_ean)) {
                    $result = mysqli_query($db_con, "INSERT INTO `libros_texto` (`materia`, `isbn`, `ean`, `editorial`, `titulo`, `importe`, `nivel`, `programaGratuidad`) VALUES ('$materia', '$isbn', '$ean', '$editorial', '$titulo', '$importe', '$curso', 0)");

                    if (! $result) {
                        $msg_error = true;
                        $msg_error_text = 'Se ha producido un error al actualizar los datos del libro de texto. Error: '.mysqli_error($db_con);
                    }
                    else {
                        header('Location:'.'index.php?curso='.$curso);
                        exit();
                    }
                }
                else {
                  if (mysqli_num_rows($result_isbn)) {
                    $row = mysqli_fetch_array($result_isbn);
                  }
                  else {
                    $row = mysqli_fetch_array($result_ean);
                  }

                  $msg_error = true;
                  $msg_error_text = 'Este libro ya existe con el título <strong><em>'.$row['titulo'].'</em></strong> para el curso <strong>'.$row['nivel'].'</strong>.';
                }
            }


        }
        else {
            $msg_error = true;
            $msg_error_text = 'Debe rellenar los campos obligatorios marcados con el símbolo (*).';
        }
    }

    // ELIMINAR LIBRO DE TEXTO
    if ((isset($_GET['accion']) && $_GET['accion'] == 'eliminar') && (isset($_GET['id']) && intval($_GET['id']))) {

        $id = $_GET['id'];

        // Comprobamos si se trata de un libro del Programa de Gratuidad.
        $result = mysqli_query($db_con, "SELECT `programaGratuidad` FROM `libros_texto` WHERE `id` = '$id' LIMIT 1");
        $row = mysqli_fetch_array($result);

        if (! $row['programaGratuidad']) {
            $result = mysqli_query($db_con, "DELETE FROM `libros_texto` WHERE `id` = $id LIMIT 1");

            if (! $result) {
                $msg_error = true;
                $msg_error_text = 'Se ha producido un error al registrar el libro de texto. Error: '.mysqli_error($db_con);
            }
            else {
                header('Location:'.'index.php?curso='.$curso);
                exit();
            }


        }
        else {
            $msg_error = true;
            $msg_error_text = 'No se pueden eliminar los libros asignados al Programa de Gratuidad de Libros de texto manualmente. Importe el listado de libros para reemplazar los datos actuales.';
        }
    }

}

include('../../menu.php');
include('../departamento/menu.php');?>
    <div class="container">

        <div class="page-header">
            <h2>Departamento de <?php echo $_SESSION['dpt']; ?> <small>Libros de texto</small></h2>
        </div>

        <div class="row">

            <div class="col-sm-12">

                <form action="" method="GET">
                    <div class="row">

                        <div class="col-sm-6 col-sm-offset-3">

                            <div class="well">

                                 <div class="form-group">
                                    <label for="curso">Curso:</label>
                                    <select class="form-control" name="curso" id="curso" onchange="submit()">
                                        <?php foreach ($niveles as $nivel): ?>
                                        <option value="<?php echo $nivel['nombre']; ?>"<?php echo (isset($curso) && $curso == $nivel['nombre']) ? ' selected' : ''; ?>><?php echo $nivel['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>

                        </div>
                    </div>
                </form>

                <br>

                <h3><?php echo $curso; ?></h3>

                <?php if (isset($msg_error) && $msg_error): ?>
                <br>

                <div class="alert alert-danger">
                    <?php echo $msg_error_text; ?>
                </div>
                <?php endif; ?>

                <?php if (acl_permiso($_SESSION['cargo'], array(1, 4))): ?>
                <br>
                <div class="hidden-print">
                    <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalLibroTexto">Añadir libro de texto</a>
                    <?php if (acl_permiso($_SESSION['cargo'], array(1))  && (stristr($curso, 'E.S.O.') == true || stristr($curso, 'F.P.B.') == true)): ?>
                    <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalImportarLibros">Programa de Gratuidad en Libros</a>
                    <?php endif; ?>
                </div>
                <br>
                <?php endif; ?>

                <?php
                $numlibros = 0;
                foreach ($libros as $libro) {
                    if ($libro['nivel'] == $curso) {
                        $numlibros++;
                    }
                }
                ?>
                <?php if ($numlibros): ?>
                <table class="table table-bordered table-condensed table-vcentered table-striped" style="font-size: 0.85em;">
                    <thead>
                        <tr>
                            <th>Libro de texto</th>
                            <th class="hidden-xs">Editorial</th>
                            <th class="hidden-xs">Materia</th>
                            <?php if (acl_permiso($_SESSION['cargo'], array(1, 4))): ?>
                            <th></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($libros as $libro): ?>
                        <?php if ($libro['nivel'] == $curso): ?>
                        <tr>
                            <td>
                                <p><strong><?php echo $libro['titulo']; ?></strong></p>
                                <p class="text-muted">
                                    <?php if (! empty($libro['isbn'])): ?><strong>ISBN:</strong> <?php echo $libro['isbn']; ?><?php endif; ?>
                                    <?php if (! empty($libro['isbn']) && ! empty($libro['ean'])): ?> &middot; <?php endif; ?>
                                    <?php if (! empty($libro['ean'])): ?><strong>EAN:</strong> <?php echo $libro['ean']; ?><?php endif; ?>
                                </p>
                            </td>
                            <td class="hidden-xs"><?php echo $libro['editorial']; ?></td>
                            <td class="hidden-xs"><?php echo $libro['materia']; ?></td>
                            <?php if (acl_permiso($_SESSION['cargo'], array(1, 4))): ?>
                            <td nowrap>
                                <?php if (! $libro['gratuidad']): ?>
                                <a href="#" class="btn btn-default btn-sm" bs-data="tooltip" title="Editar" data-toggle="modal" data-target="#modalLibroTexto" data-idlibro="<?php echo $libro['id']; ?>" data-materia="<?php echo $libro['materia']; ?>" data-isbn="<?php echo $libro['isbn']; ?>" data-ean="<?php echo $libro['ean']; ?>" data-titulo="<?php echo $libro['titulo']; ?>" data-editorial="<?php echo $libro['editorial']; ?>" data-importe="<?php echo $libro['importe']; ?>"><span class="far fa-edit fa-lg fa-fw"></span></a>
                                <a href="index.php?curso=<?php echo $curso; ?>&accion=eliminar&id=<?php echo $libro['id']; ?>" class="btn btn-danger btn-sm" bs-data="tooltip" title="Eliminar" data-bb="confirm-delete"><span class="far fa-trash-alt fa-lg fa-fw"></span></a>
                                <?php else: ?>
                                <a href="#" class="btn btn-default btn-sm disabled" bs-data="tooltip" title="No hay opciones disponibles para este libro"><span class="fas fa-lock fa-lg fa-fw"></span></a>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <br><br><br><br><br>
                <p class="lead text-center text-muted">No se han registrado libros de texto para este curso<p>
                <br><br><br><br><br><br>
                <?php endif; ?>

            </div>

        </div>

    </div>

    <?php if (acl_permiso($_SESSION['cargo'], array(1, 4))): ?>
    <!-- MODAL AÑADIR LIBRO DE TEXTO -->
    <div class="modal fade" id="modalLibroTexto" tabindex="-1" role="dialog" aria-labelledby="libroTexto">
        <div class="modal-dialog" role="document">
            <form action="" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="libroTexto">Añadir libro de texto</h4>
                    </div>
                    <div class="modal-body">

                        <div class="form-group-edit"></div>

                        <div class="form-group">
                            <label for="isbn">Curso</label>
                            <p class="form-control-static"><?php echo $curso; ?></p>
                        </div>

                        <div class="form-group">
                            <label for="materia">Materia <span class="text-danger">(*)</span></label>
                            <select class="form-control" id="materia" name="materia" required>
                                <?php foreach ($materias as $materia): ?>
                                <option value="<?php echo $materia['nombre']; ?>"><?php echo $materia['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group" id="isbn-control">
                            <label for="isbn">ISBN (International Standard Book Number) <span class="text-danger" id="isbn-require">(*)</span></label>
                            <input class="form-control" type="text" name="isbn" id="isbn" placeholder="ISBN de 10 o 13 dígitos" value="" maxlength="13">
                        </div>

                        <div class="form-group" id="ean-control">
                            <label for="ean">EAN (European Article Number) <span class="text-danger" id="ean-require">(*)</span> <span class="text-muted">(Comienza por 978)</span></label>
                            <input class="form-control" type="text" name="ean" id="ean" placeholder="EAN de 13 dígitos" value="" maxlength="13">
                        </div>

                        <div class="form-group">
                            <label for="titulo">Título <span class="text-danger">(*)</span></label>
                            <input class="form-control" type="text" name="titulo" id="titulo" placeholder="Título del libro" value="" required>
                        </div>

                        <div class="form-group">
                            <label for="editorial">Editorial <span class="text-danger">(*)</span></label>
                            <select class="form-control" type="text" name="editorial" id="editorial" required>
                              <option value="">Seleccione editorial</option>
                              <?php foreach ($editoriales as $editorial_lista): ?>
                              <option value="<?php echo $editorial_lista; ?>"><?php echo $editorial_lista; ?></option>
                              <?php endforeach; ?>
                          </select>
                        </div>

                        <div class="form-group">
                            <label for="importe">Importe <small class="text-muted">(Opcional)</small></label>
                            <div class="input-group">
                                <input class="form-control" type="text" name="importe" id="importe" placeholder="Importe del libro" value="">
                                <span class="input-group-addon">€</span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" name="guardarLibro" class="btn btn-primary">Guardar libro</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (acl_permiso($_SESSION['cargo'], array(1)) && (stristr($curso, 'E.S.O.') == true || stristr($curso, 'F.P.B.') == true)): ?>
    <!-- MODAL IMPORTAR LIBROS PROGRAMA GRATUIDAD EN LIBROS DE TEXTO -->
    <div class="modal fade" id="modalImportarLibros" tabindex="-1" role="dialog" aria-labelledby="importarLibros">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="importarLibros">Importar libros Programa Gratuidad</h4>
                    </div>
                    <div class="modal-body">
                        <div class="well">
                            <?php $exp_curso_seleccion = explode('/', $config['curso_actual']); ?>
                            <?php $curso_seleccion = $exp_curso_seleccion[0].'-'.($exp_curso_seleccion[0] + 1); ?>
                            <p>Para obtener el archivo RegAsiLibMat.txt debe dirigirse al apartado <strong>Alumnado</strong>, <strong>Ayuda al Estudio</strong>, <strong>Gratuidad en Libros de Texto</strong>, <strong>Asignación de Libros a Materias</strong>. Seleccione el año académico <strong><?php echo $curso_seleccion; ?></strong>; curso <strong><?php echo $curso; ?></strong>. Pulse el botón de exportación de datos y seleccione el formato <strong>Texto plano</strong>.</p>
                            <?php unset($curso_seleccion); ?>
                            <?php unset($exp_curso_seleccion); ?>
                        </div>

                        <br>

                        <div class="form-group">
                            <label for="archivo">RegAsiLibMat.txt</label>
                            <input type="file" id="archivo" name="archivo" accept="text/plain">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" name="submitImportacion" class="btn btn-primary">Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php include('../../pie.php'); ?>

    <?php if (acl_permiso($_SESSION['cargo'], array(1, 4))): ?>
    <script>
    function isValidISBN(isbn) {

        if(isbn.length != 10 && isbn.length != 13){
            return false;
        }

        if (isbn.length == 10) {

            isbn = isbn.replace(/[^\dX]/gi, '');
            var chars = isbn.split('');
            if(chars[9].toUpperCase() == 'X'){
                chars[9] = 10;
            }
            var sum = 0;
            for (var i = 0; i < chars.length; i++) {
                sum += ((10-i) * parseInt(chars[i]));
            }
            return ((sum % 11) == 0);

        }
        else {
            var last = isbn.substr(isbn.length-1, 1);
            isbn = isbn.substr(0, 12);
            var chars = isbn.split('');
            var sum = 0;
            for (var i = 0; i < chars.length; i++) {
                if (((i+1) % 2) == 0) {
                    sum += (parseInt(chars[i]) * 3);
                }
                else {
                    sum += (parseInt(chars[i]) * 1);
                }
            }
            var result = 10 - (sum % 10);
            return (result == last);
        }

    }

    function isValidEAN(ean) {
        if(ean.length != 13){
            return false;
        }
        var result = 0;
        for (counter = ean.length-1; counter >=0; counter--){
            result = result + parseInt(ean.charAt(counter)) * (1+(2*(counter % 2)));
        }

        return ((10 - (result % 10)) % 10 == 0);
    }

    $(document).ready(function() {
    $('#isbn').keyup(function() {
            var isbn = $('#isbn').val();

            if (isValidISBN(isbn)) {
                $('#isbn-control').addClass('has-success');
                $('#ean-require').hide();
            }
            else if (ean.length == 10 || ean.length == 13) {
                $('#isbn-control').addClass('has-error');
                $('#ean-require').show();
            }
            else {
                $('#isbn-control').removeClass('has-success');
                $('#ean-require').show();
            }
        });

        $('#ean').keyup(function() {
            var ean = $('#ean').val();
            if (isValidEAN(ean)) {
                $('#ean-control').addClass('has-success');
                $('#isbn-require').hide();
            }
            else if (ean.length == 13) {
                $('#ean-control').addClass('has-error');
                $('#isbn-require').show();
            }
            else {
                $('#ean-control').removeClass('has-success');
                $('#isbn-require').show();
            }
        });

        // EDICIÓN DE LIBROS DE TEXTOS
        $('#modalLibroTexto').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var idlibro = button.data('idlibro');
            if (idlibro) {
                var materia = button.data('materia');
                var isbn = button.data('isbn');
                var ean = button.data('ean');
                var titulo = button.data('titulo');
                var editorial = button.data('editorial');
                var importe = button.data('importe');
                var modal = $(this);
                modal.find('.modal-title').text(titulo);

                modal.find('.modal-body #materia').val(materia);
                modal.find('.modal-body #isbn').val(isbn);
                modal.find('.modal-body #ean').val(ean);
                modal.find('.modal-body #titulo').val(titulo);
                modal.find('.modal-body #editorial').val(editorial);
                modal.find('.modal-body #importe').val(importe);

                modal.find('.form-group-edit').html('<input type="hidden" name="idlibro" value="' + idlibro + '">');
            }
        })
  });
    </script>
    <?php endif; ?>

</body>
</html>
