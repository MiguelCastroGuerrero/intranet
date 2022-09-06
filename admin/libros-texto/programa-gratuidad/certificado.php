<?php
require('../../../bootstrap.php');
require("../../../pdf/mc_table.php");

acl_acceso($_SESSION['cargo'], array(1));

class GranPDF extends PDF_MC_Table {
	function SetFontSpacing($size) {
        $size = ($size / 100);
        if($this->FontSpacingPt==$size)
            return;
        $this->FontSpacingPt = $size;
        $this->FontSpacing = $size/$this->k;
        if ($this->page>0)
            $this->_out(sprintf('BT %.3f Tc ET', $size));
    }

    function Header() {
        global $config;

        $this->SetTextColor(48, 46, 43);
        $this->SetFontSpacing(-10);
        $this->SetY(14);
        $this->SetFont('Noto Sans HK Bold','',16);
        $this->Cell(80,5,'Junta de Andalucía',0,1);
        $this->SetY(15);
        $this->Cell(75);
        $this->SetFontSpacing(0);
        $this->SetFont('Noto Sans HK','',10);
        $this->Cell(80,5,'Consejería de Desarrollo Educativo y Formación Profesional',0,1);
        $this->Cell(75);
        $this->SetTextColor(53, 110, 59);
        $this->SetFont('Noto Sans HK','',7);
        $this->Cell(80,5,mb_strtoupper($config['centro_denominacion']),0,1);
        $this->SetTextColor(255, 255, 255);
    }
    function Footer() {
        global $config;

        $this->SetTextColor(53, 110, 59);
        $this->Image( '../../../img/pie.jpg', 0, 250, 25, '', 'jpg' );
        $this->SetY(275);
        $this->SetFont('Noto Sans HK','',7);
        $this->Cell(75);
        $this->Cell(80,4,$config['centro_direccion'].'. '.$config['centro_codpostal'].', '.$config['centro_localidad'].' ('.$config['centro_provincia'] .')',0,1);
        $this->Cell(75);
        $this->Cell(80,4,'Telf: '.$config['centro_telefono'].' '.(($config['centro_fax']) ? '   Fax: '.$config['centro_fax'] : ''),0,1);
        $this->Cell(75);
        $this->Cell(80,4,'Correo-e: '.$config['centro_email'],0,1);
        $this->SetTextColor(255, 255, 255);
    }
}


$MiPDF = new GranPDF('P', 'mm', 'A4');
$MiPDF->AddFont('Noto Sans HK Bold','','NotoSansHK-Bold.php');
$MiPDF->AddFont('Noto Sans HK Bold','B','NotoSansHK-Bold.php');
$MiPDF->AddFont('Noto Sans HK','','NotoSansHK-Regular.php');
$MiPDF->AddFont('Noto Sans HK','B','NotoSansHK-Bold.php');

$MiPDF->AddFont('NewsGotT','','NewsGotT.php');
$MiPDF->AddFont('NewsGotT','B','NewsGotTb.php');
$MiPDF->AddFont('ErasDemiBT','','ErasDemiBT.php');
$MiPDF->AddFont('ErasDemiBT','B','ErasDemiBT.php');
$MiPDF->AddFont('ErasMDBT','','ErasMDBT.php');
$MiPDF->AddFont('ErasMDBT','I','ErasMDBT.php');

$MiPDF->SetMargins(25, 20, 20);
$MiPDF->SetDisplayMode('fullpage');

// OBTENEMOS LOS DATOS DE LOS ALUMNOS
if (isset($_GET['claveal']) && intval($_GET['claveal'])) {
    $claveal = $_GET['claveal'];
    $result_alumno = mysqli_query($db_con, "SELECT `claveal`, `apellidos`, `nombre`, `unidad`, `curso`, `padre`, `sexo`, `domicilio`, `codpostal`, `provinciaresidencia` FROM `alma` WHERE `claveal` = '$claveal' LIMIT 1");
    unset($claveal);

    if (isset($_GET['reposicion']) && $_GET['reposicion'] == 1) {
        $esReposicion = 1;
    }
}
elseif ((isset($_GET['curso']) && ! empty($_GET['curso'])) && (isset($_GET['grupo']) && ! empty($_GET['grupo']))) {
    $grupo = $_GET['grupo'];
    $result_alumno = mysqli_query($db_con, "SELECT `claveal`, `apellidos`, `nombre`, `unidad`, `curso`, `padre`, `sexo`, `domicilio`, `codpostal`, `provinciaresidencia` FROM `alma` WHERE `unidad` = '$grupo' ORDER BY `apellidos` ASC, `nombre` ASC");
    unset($grupo);

    $esReposicion = 0;
}
elseif (isset($_GET['curso'])) {
    $curso = $_GET['curso'];
    $result_alumno = mysqli_query($db_con, "SELECT `claveal`, `apellidos`, `nombre`, `unidad`, `curso`, `padre`, `sexo`, `domicilio`, `codpostal`, `provinciaresidencia` FROM `alma` WHERE `curso` = '$curso' ORDER BY `unidad` ASC, `apellidos` ASC, `nombre` ASC");
    unset($curso);

    $esReposicion = 0;
}
else {
    $result_alumno = mysqli_query($db_con, "SELECT `claveal`, `apellidos`, `nombre`, `unidad`, `curso`, `padre`, `sexo`, `domicilio`, `codpostal`, `provinciaresidencia` FROM `alma` WHERE `curso` LIKE '%E.S.O.' ORDER BY `curso` ASC, `unidad` ASC, `apellidos` ASC, `nombre` ASC");

    $esReposicion = 0;
}

while ($alumno = mysqli_fetch_array($result_alumno)) {

    // Variables necesarias para el funcionamiento

    $esComunicadoReposicion = 0;
    $fecha_entrega = '';
    $fecha_entrega_aux = '';

    // OBTENEMOS LOS LIBROS DE TEXTO DEL CURSO
    $libros = array();
    $result = mysqli_query($db_con, "SELECT `isbn`, `ean`, `titulo`, `editorial`, `importe`, `materia`, `programaGratuidad` FROM `libros_texto` WHERE `nivel` = '".$alumno['curso']."'");
    while ($row = mysqli_fetch_array($result)) {
        if ($row['programaGratuidad']) {
            $libro = array(
                'isbn'      => $row['isbn'],
                'ean'       => $row['ean'],
                'titulo'    => $row['titulo'],
                'editorial' => $row['editorial'],
                'importe'   => $row['importe'],
                'materia'   => $row['materia']
            );

            array_push($libros, $libro);
        }
    }
    mysqli_free_result($result);
    unset($libro);

    // OBTENEMOS LOS DATOS DEL ESTADO DE LOS LIBROS
    $estado_libros = array();
    $result_estado_libros = mysqli_query($db_con, "SELECT `claveal`, `materia`, `estado`, `devuelto`, `fecha` FROM `libros_texto_alumnos` WHERE `claveal` = '".$alumno['claveal']."'");

    if (mysqli_num_rows($result_estado_libros)) {

        $MiPDF->Addpage();

        while ($row = mysqli_fetch_array($result_estado_libros)) {

            $result_materia = mysqli_query($db_con, "SELECT `nombre` FROM `asignaturas` WHERE `codigo` = '".$row['materia']."' LIMIT 1");
            $row_materia = mysqli_fetch_array($result_materia);

            // Recorremos el array de libros de texto para relacionar datos
            foreach ($libros as $libro) {
                if ($libro['materia'] == $row_materia['nombre']) {

                    $estado_libro = array(
                        'isbn'      => $libro['isbn'],
                        'ean'       => $libro['ean'],
                        'titulo'    => $libro['titulo'],
                        'editorial' => $libro['editorial'],
                        'importe'   => $libro['importe'],
                        'materia'   => $row_materia['nombre'],
                        'estado'    => $row['estado']
                    );

                    // Si el libro está en mal estado o perdido activamos el flag para mostrar el comunicado de reposición;
                    // en otro caso, marcamos el libro como devuelto.
                    if ((($row['estado'] == 'M' || $row['estado'] == 'N') && $row['devuelto'] == 0) && ! $esReposicion) {
                        $esComunicadoReposicion = 1;
                    }
                    else {

                        // Comprobamos que el valor devuelto es 0 antes de actualizar el estado de devolución y la fecha de entrega.
                        // Esto evitará que se actualice el estado de devolución y la fecha real de entrega si un usuario vuelve
                        // a generar el informe.
                        if (! $row['devuelto']) {
                            $fecha_hora_entrega = date('Y-m-d H:i:s');
                            mysqli_query($db_con, "UPDATE `libros_texto_alumnos` SET `devuelto` = 1, `fecha` = '$fecha_hora_entrega' WHERE `claveal` = '".$row['claveal']."' AND `materia` = '".$row['materia']."' AND `curso` = '".$config['curso_actual']."' LIMIT 1");

                            // Comprobamos las fechas de entrega de los libros
                            $fecha_entrega_libro = strftime('%e de %B de %Y', strtotime($fecha_hora_entrega));
                            if ($fecha_entrega_libro != $fecha_entrega_aux) {
                                $fecha_entrega_aux = $fecha_entrega_libro;
                                $fecha_entrega .= $fecha_entrega_libro.'; ';
                            }
                        }

                    }
                }
            }

            array_push($estado_libros, $estado_libro);
        }
        mysqli_free_result($result);
        unset($estado_libro);

        // INFORMACION DE LA CARTA
        $MiPDF->SetY(45);
        $MiPDF->SetFont('Noto Sans HK', '', 10);
        $MiPDF->Cell(75, 5, '', 0, 0, 'L', 0);
        $MiPDF->Cell(75, 5, $alumno['padre'], 0, 1, 'L', 0);
        $MiPDF->Cell(75, 12, '', 0, 0, 'L', 0);
        $MiPDF->Cell(75, 5, $alumno['domicilio'], 0, 1, 'L', 0);
        $MiPDF->Cell(75, 0, '', 0, 0, 'L', 0);
        $MiPDF->Cell(75, 5, $alumno['codpostal'].' '.mb_strtoupper($alumno['provinciaresidencia'], 'UTF-8'), 0, 1, 'L', 0);
        $MiPDF->Cell(0, 12, '', 0, 1, 'L', 0);

        // SI EL ALUMNO TIENE LIBROS EN MAL ESTADO O PERDIDOS MOSTRAMOS EL COMUNICADO DE REPOSICIÓN.
        // EN EL CASO DE QUE ESTÉN EN BUEN O SUFICIENTE ESTADO O HAYA REPUESTO LOS LIBROS MOSTRAMOS
        // EL CERTIFICADO DE ENTREGA DE LIBROS

        if ($esComunicadoReposicion && ! $esReposicion) {

            // CUERPO DE LA CARTA
            $MiPDF->SetFont('Noto Sans HK', 'B', 10);
            $MiPDF->Multicell(0, 5, mb_strtoupper('Comunicación del deber de reposición de libros de texto', 'UTF-8'), 0, 'C', 0);
            $MiPDF->Ln(5);

            $MiPDF->SetFont('Noto Sans HK', '', 10);

            $fecha_hoy = strftime('%e de %B de %Y', strtotime(date('Y-m-d')));
            $cuerpo = 'Estimada familia:

La Dirección del centro, que preside la Comisión del Consejo Escolar para la gestión y supervisión del Programa de Gratuidad de Libros de Texto, le comunica la siguiente incidencia en referencia al uso y conservación de los libros de texto de los que dispone su '.(($alumno['sexo'] == 'H') ? 'hijo' : 'hija').' '.$alumno['nombre'].' '.$alumno['apellidos'].' en el curso '.$alumno['curso'].' para el seguimiento de las actividades lectivas:';

            $MiPDF->Multicell( 0, 5, $cuerpo, 0, 'L', 0 );
            $MiPDF->Ln(5);

            // TABLA CON RELACIÓN DE ESTADO DE LIBROS DE TEXTO
            $MiPDF->SetWidths(array(80, 25, 60));
            $MiPDF->SetFont('Noto Sans HK', 'B', 10);
            $MiPDF->SetTextColor(255, 255, 255);
            $MiPDF->SetFillColor(61, 61, 61);
            $MiPDF->Row(array('Libros afectados', 'Importe', 'Incidencia detectada'), 0, 5);

            $MiPDF->SetTextColor(0, 0, 0);
            $MiPDF->SetFont('Noto Sans HK', '', 10);

            $libros_prestados = 0;
            $total_reposicion = 0.00;
            foreach ($estado_libros as $estado_libro) {
                switch ($estado_libro['estado']) {
                    case 'R' : $texto_estado = 'Tiene manchas diversas / Páginas pintadas / Texto subrayado'; break;
                    case 'M' : $texto_estado = 'Páginas rotas / Tiene los lomos o esquinas deteriorados'; break;
                    case 'N' : $texto_estado = 'Extraviado'; break;
                }

                if ($estado_libro['estado'] == 'M' || $estado_libro['estado'] == 'N') {
                    $total_reposicion += $estado_libro['importe'];
                    $MiPDF->Row(array('[EAN: '.$estado_libro['ean'].'] '.$estado_libro['titulo'], $estado_libro['importe'].' EUR', $texto_estado), 1, 5);
                }
            }

            if ($total_reposicion > 0) {
                $MiPDF->SetFont('Noto Sans HK', 'B', 10);

                $MiPDF->Cell(80, 8, 'TOTAL ', 0, 0, 'R', 0);
                $MiPDF->Cell(25, 8, $total_reposicion.' EUR', 0, 0, 'L', 0);
                $MiPDF->Cell(60, 8, '', 0, 1, 'L', 0);

                $MiPDF->Ln(3);

                $MiPDF->SetFont('Noto Sans HK', '', 10);

                $texto_reposicion = 'De acuerdo con el artículo 4 de la Orden de 27 de abril de 2005, los alumnos y alumnas que participan en el Programa de Gratuidad en Libros de Texto tienen obligación de hacer un uso adecuado y cuidadoso de los libros de textos, y de reponer aquellos extraviados o deteriorados de forma culpable o malintencionanda. Por ello, le informamos de su deber de proceder a la resposición del material citado, o en cu caso, al abono del importe del mismo, en el plazo de diez días a partir de la recepción de esta comunicación.';
                $MiPDF->Multicell( 0, 5, $texto_reposicion, 0, 'L', 0);

                $MiPDF->SetFont('Noto Sans HK', '', 10);
            }

            $MiPDF->Ln(5);

            $MiPDF->Cell(0, 5, 'En '.$config['centro_localidad'].', a '.$fecha_hoy.'.', 0, 1, 'L', 0);

            $MiPDF->Ln(10);

            //FIRMAS
            $MiPDF->Cell(90, 5, 'Reciban un cordial saludo.', 0, 0, 'L', 0);
            $MiPDF->Cell(55, 5, '(Sello del centro)', 0, 1, 'C', 0);
            $MiPDF->Cell(55, 20, '', 0, 0, 'C', 0);
            $MiPDF->Cell(55, 20, '', 0, 1, 'C', 0);
            $MiPDF->SetFont('Noto Sans HK', '', 10);
            $MiPDF->Cell(90, 5, 'Fdo. Presidente / Presidenta del Consejo Escolar', 0, 0, 'L', 0);
            $MiPDF->Cell(55, 5, '', 0, 1, 'C', 0);

        }
        else {
            $prestadoSeptiembre = 0;

            // CUERPO DE LA CARTA
            $MiPDF->SetFont('Noto Sans HK', 'B', 10);
            $MiPDF->Multicell(0, 5, mb_strtoupper('Certificación de entrega de libros', 'UTF-8'), 0, 'C', 0);
            $MiPDF->Ln(5);

            $MiPDF->SetFont('Noto Sans HK', '', 10);

            $fecha_hoy = strftime('%e de %B de %Y', strtotime(date('Y-m-d')));
            $cuerpo = 'D./Dª. '.$config['directivo_secretaria'].', como Secretario/a del centro '.$config['centro_denominacion'].', y con el visto bueno del Director/a,

CERTIFICO: que el '.(($alumno['sexo'] == 'H') ? 'alumno' : 'alumna').' '.$alumno['nombre'].' '.$alumno['apellidos'].', '.(($alumno['sexo'] == 'H') ? 'matriculado' : 'matriculada').' en este centro en el curso '.$alumno['unidad'].' ('.$alumno['curso'].'), ha hecho entrega de los libros que se le asignaron con cargo al Programa de Gratuidad de Libros de Texto, con fecha '.$fecha_entrega.', en el estado de conservación que se indica:';

            $MiPDF->Multicell( 0, 5, $cuerpo, 0, 'L', 0 );
            $MiPDF->Ln(5);

            // TABLA CON RELACIÓN DE ESTADO DE LIBROS DE TEXTO
            $MiPDF->SetWidths(array(120, 40));
            $MiPDF->SetFont('Noto Sans HK', 'B', 10);
            $MiPDF->SetTextColor(255, 255, 255);
            $MiPDF->SetFillColor(61, 61, 61);
            $MiPDF->Row(array('Libros de texto', 'Estado'), 0, 5);

            $MiPDF->SetTextColor(0, 0, 0);
            $MiPDF->SetFont('Noto Sans HK', '', 10);


            foreach ($estado_libros as $estado_libro) {
                switch ($estado_libro['estado']) {
                    case 'B' : $texto_estado = 'Buen estado'; break;
                    case 'R' : $texto_estado = 'Suficiente'; break;
                    case 'S' : $texto_estado = 'Prestado para Septiembre'; break;
                }

                if ($estado_libro['estado'] == 'B' || $estado_libro['estado'] == 'R' || $estado_libro['estado'] == 'S') {
                    if ($estado_libro['estado'] == 'S') $prestadoSeptiembre = 1;
                    $MiPDF->Row(array('[EAN: '.$estado_libro['ean'].'] '.$estado_libro['titulo'], $texto_estado), 1, 5);
                }
            }

            if ($prestadoSeptiembre) {
                $MiPDF->Ln(5);
                $MiPDF->SetFont('Noto Sans HK', 'B', 10);
                $MiPDF->Multicell(0, 5, 'Importante: Los libros prestados para Septiembre deben ser devueltos el día de la Convocatoria Extraordinaria en buen estado.', 0, 'L', 0);
            }

            $MiPDF->SetFont('Noto Sans HK', '', 10);

            $MiPDF->Ln(5);

            $MiPDF->Cell(0, 5, 'En '.$config['centro_localidad'].', a '.$fecha_hoy.'.', 0, 1, 'L', 0);

            $MiPDF->Ln(10);

            //FIRMAS
            $MiPDF->Cell(90, 5, 'El/La Secretario/a', 0, 0, 'C', 0);
            $MiPDF->Cell(55, 5, 'El/La Director/a', 0, 1, 'C', 0);
            $MiPDF->Cell(55, 20, '', 0, 0, 'C', 0);
            $MiPDF->Cell(55, 20, '', 0, 1, 'C', 0);
            $MiPDF->SetFont('Noto Sans HK', '', 10);
            $MiPDF->Cell(90, 5, 'Fdo. '.$config['directivo_secretaria'], 0, 0, 'C', 0);
            $MiPDF->Cell(55, 5, 'Fdo. '.$config['directivo_direccion'], 0, 1, 'C', 0);
        }


    }

}

$MiPDF->Output();
