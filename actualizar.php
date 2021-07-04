<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed');

/*
  @descripcion: Tabla para control de faltas de asistencia
  @fecha: 13 de julio de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Tabla de control de faltas'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con,"CREATE TABLE IF NOT EXISTS `control_faltas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profesor` int(11) NOT NULL,
  `unidad` varchar(128) COLLATE utf8_spanish_ci NOT NULL,
  `dia` tinyint(1) NOT NULL,
  `hora` tinyint(1) NOT NULL,
  `fecha` date NOT NULL,
  `asignatura` varchar(12) COLLATE utf8_spanish_ci NOT NULL,
  `numero` tinyint(2) NOT NULL,
  `hoy` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Tabla de control de faltas', NOW())");
}

/*
  @descripcion: Actualizacion tabla de noticias
  @fecha: 31 de julio de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Actualizacion tabla noticias'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `noticias` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, CHANGE `slug` `titulo` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `content` `contenido` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `contact` `autor` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `timestamp` `fechapub` DATETIME NOT NULL, CHANGE `clase` `categoria` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `fechafin` `fechafin` DATE NULL DEFAULT NULL, CHANGE `pagina` `pagina` CHAR(2) NOT NULL;");
  mysqli_query($db_con, "ALTER TABLE `noticias` CHANGE `fechafin` `fechafin` DATE NULL DEFAULT NULL AFTER `fechapub`;");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Actualizacion tabla noticias', NOW())");
}

/*
  @descripcion: Creación tabla tareas
  @fecha: 9 de septiembre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Tabla tareas'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "DROP TABLE IF EXISTS `tareas`");
  mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `tareas` (
    `id` int(10) unsigned AUTO_INCREMENT PRIMARY KEY,
    `idea` varchar(12) NOT NULL,
    `titulo` tinytext NOT NULL,
    `tarea` text NOT NULL,
    `estado` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `fechareg` datetime NOT NULL,
    `prioridad` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Tabla tareas', NOW())");
}

mysqli_free_result($actua);

/*
  @descripcion: Corrección estructura tabla evaluaciones_actas
  @fecha: 5 de octubre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Estructura tabla evaluaciones_actas'");
if (! mysqli_num_rows($actua)) {

  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM evaluaciones_actas WHERE Field = 'asistentes'");
  if (! mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `evaluaciones_actas` ADD `asistentes` VARCHAR(255) NULL AFTER `texto_acta`");
    mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Estructura tabla evaluaciones_actas', NOW())");
  }

}

mysqli_free_result($actua);

/*
  @descripcion: Ampliación columna asistentes en tabla evaluaciones_actas
  @fecha: 18 de octubre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Columna asistentes tabla evaluaciones_actas'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `evaluaciones_actas` CHANGE `asistentes` `asistentes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Columna asistentes tabla evaluaciones_actas', NOW())");
}

mysqli_free_result($actua);

/*
  @descripcion: Ampliación de caracteres en la columna "página" para incluir sistema de documentación permanente
  @fecha: 21 de octubre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Estructura tabla Noticias'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE  `noticias` CHANGE  `pagina`  `pagina` CHAR( 3 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Estructura tabla Noticias', NOW())");
}

mysqli_free_result($actua);

/*
  @descripcion: Cambio del tipo de datos (varchar a time) de los campos hora_inicio y hora_fin
  @fecha: 21 de noviembre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Estructura tabla Tramos'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE  `tramos` CHANGE  `hora_inicio`  `hora_inicio` TIME NOT NULL ,
CHANGE  `hora_fin`  `hora_fin` TIME NOT NULL");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Estructura tabla Tramos', NOW())");
}

mysqli_free_result($actua);


/*
  @descripcion: Eliminado archivos de exportación. A partir de ahora se genera y se fuerza la descarga. De esta manera evitamos que queden los archivos publicados en la red
  @fecha: 21 de noviembre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Exportar usuarios TIC'");
if (! mysqli_num_rows($actua)) {
  if (file_exists(INTRANET_DIRECTORY.'/xml/jefe/TIC/download.php')) unlink(INTRANET_DIRECTORY.'/xml/jefe/TIC/download.php');
  if (file_exists(INTRANET_DIRECTORY.'/xml/jefe/TIC/alumnos.txt')) unlink(INTRANET_DIRECTORY.'/xml/jefe/TIC/alumnos.txt');
  if (file_exists(INTRANET_DIRECTORY.'/xml/jefe/TIC/profesores.txt')) unlink(INTRANET_DIRECTORY.'/xml/jefe/TIC/profesores.txt');
  if (file_exists(INTRANET_DIRECTORY.'/xml/jefe/TIC/alumnos_moodle.txt')) unlink(INTRANET_DIRECTORY.'/xml/jefe/TIC/alumnos_moodle.txt');
  if (file_exists(INTRANET_DIRECTORY.'/xml/jefe/TIC/profesores_moodle.txt')) unlink(INTRANET_DIRECTORY.'/xml/jefe/TIC/profesores_moodle.txt');
  if (file_exists(INTRANET_DIRECTORY.'/xml/jefe/TIC/profesores_gsuite.csv')) unlink(INTRANET_DIRECTORY.'/xml/jefe/TIC/profesores_gsuite.csv');

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Exportar usuarios TIC', NOW())");
}

/*
  @descripcion: Eliminado archivo de configuración de Trendoo
  @fecha: 2 de diciembre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Eliminado archivo config.php de Trendoo'");
if (! mysqli_num_rows($actua)) {
  if (file_exists(INTRANET_DIRECTORY.'/lib/trendoo/config.php')) unlink(INTRANET_DIRECTORY.'/lib/trendoo/config.php');

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Eliminado archivo config.php de Trendoo', NOW())");
}

/*
  @descripcion: Nuevo campo en tabla reg_principal
  @fecha: 7 de diciembre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Nuevo campo en tabla reg_principal'");
if (! mysqli_num_rows($actua)) {

  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM reg_principal WHERE Field = 'tutorlegal'");
  if (! mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `reg_principal` ADD `tutorlegal` VARCHAR(255) NULL");
    mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Nuevo campo en tabla reg_principal', NOW())");
  }
}

/*
  @descripcion: Nuevo campo en tabla Absentismo
  @fecha: 10 de diciembre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Nuevo campo en tabla Absentismo'");
if (! mysqli_num_rows($actua)) {

  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM absentismo WHERE Field = 'fecha_registro'");
  if (! mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `absentismo` ADD `fecha_registro` DATE NOT NULL AFTER `serv_sociales`");
    mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Nuevo campo en tabla Absentismo', NOW())");
  }
}


/*
  @descripcion: Registro de agente de usuario en tabla reg_principal e reg_intranet
  @fecha: 16 de diciembre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Registro agente de usuario'");
if (! mysqli_num_rows($actua)) {

  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM reg_principal WHERE Field = 'useragent'");
  if (! mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `reg_principal` ADD `useragent` VARCHAR(255) NULL");
  }

  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM reg_intranet WHERE Field = 'useragent'");
  if (! mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `reg_intranet` ADD `useragent` VARCHAR(255) NULL");
  }

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Registro agente de usuario', NOW())");
}

/*
  @descripcion: Eliminado archivo salir.php para evitar filtro de contenidos de la Junta.
  @fecha: 21 de diciembre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Eliminado archivo salir.php'");
if (! mysqli_num_rows($actua)) {

  if (file_exists(INTRANET_DIRECTORY.'/salir.php')) unlink(INTRANET_DIRECTORY.'/salir.php');

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Eliminado archivo salir.php', NOW())");
}

/*
  @descripcion: Añadido campo para segundo factor de autenticación en tabla c_profes
  @fecha: 27 de diciembre de 2017
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Módulo TOTP'");
if (! mysqli_num_rows($actua)) {

  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM c_profes WHERE Field = 'totp_secret'");
  if (! mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `c_profes` ADD `totp_secret` CHAR(16) NULL");
  }

  // Las siguientes lineas solucionan un error en actualización anterior
  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM reg_principal WHERE Field = 'totp_secret'");
  if (mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `reg_principal` DROP `totp_secret`;");
  }

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Módulo TOTP', NOW())");
}

/*
  @descripcion: Cambio nombre de actividad Servicio de guardia
  @fecha: 8 de enero de 2018
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Nombre actividad Servicio de guardia'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "UPDATE `horw` SET `asig` = 'Servicio de guardia (No Lectiva)' WHERE `c_asig` = '25' AND `asig` = 'Servicio de guardia'");
  mysqli_query($db_con, "UPDATE `horw_faltas` SET `asig` = 'Servicio de guardia (No Lectiva)' WHERE `c_asig` = '25' AND `asig` = 'Servicio de guardia'");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Nombre actividad Servicio de guardia', NOW())");
}

/*
  @descripcion: Nuevo módulo de incidencias TIC
  @fecha: 26 de enero de 2018
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Nuevo módulo incidencias TIC'");
if (! mysqli_num_rows($actua)) {

  // Creamos la tabla de inventario de material TIC
  mysqli_query($db_con, "DROP TABLE `inventario_tic`");
  mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `inventario_tic` (
  `numregistro` varchar(30) NOT NULL,
  `numserie` varchar(30) DEFAULT NULL,
  `tipo` varchar(80) NOT NULL,
  `articulo` int(6) unsigned NOT NULL,
  `proveedor` int(6) unsigned NOT NULL,
  `expediente` varchar(30) DEFAULT NULL,
  `procedencia` varchar(80) DEFAULT NULL,
  `localizacion` varchar(80) DEFAULT NULL,
  `adscripcion` varchar(80) DEFAULT NULL,
  `fechaalta` date DEFAULT NULL,
  `fechabaja` date DEFAULT NULL,
  `motivobaja` text,
  `estado` varchar(30) NOT NULL,
  `descripcion` text,
  `dotacionapae` text,
  `observaciones` text,
  `marcadobaja` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`numregistro`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;");

  // Creamos la nueva tabla de incidencias TIC
  mysqli_query($db_con, "DROP TABLE `incidencias_tic`");
  mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `incidencias_tic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `solicitante` varchar(12) NOT NULL,
  `dependencia` varchar(30) DEFAULT NULL,
  `problema` smallint(3) unsigned NOT NULL,
  `descripcion` text,
  `estado` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `fecha_estado` date NULL,
  `numincidencia` char(10) DEFAULT NULL,
  `resolucion` text NULL,
  PRIMARY KEY (`id`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;");

  // Migración de datos de la tabla partestic
  $result = mysqli_query($db_con, "SELECT `fecha`, `profesor`, `descripcion`, `estado`, `nincidencia` FROM `partestic` ORDER BY `parte` ASC") or die (mysqli_error($db_con));
  while($row = mysqli_fetch_array($result)) {
    $result_profesor = mysqli_query($db_con, "SELECT `idea` FROM `departamentos` WHERE `nombre` = '".$row['profesor']."'");
    $row_profesor = mysqli_fetch_array($result_profesor);

    if ($row['estado'] != 'solucionado') $migracion_estado = 1;
    else $migracion_estado = 3;

    if ($_SERVER['SERVER_NAME'] == 'iesantoniomachado.es' || $_SERVER['SERVER_NAME'] == 'iesbahiamarbella.es') {
      if (stristr($row['descripcion'], ':') == true) {
        $exp_aula_descripcion = explode(':', $row['descripcion']);
        $dependencia = trim($exp_aula_descripcion[0]);
        $descripcion = trim($exp_aula_descripcion[1]);
      }
      else {
        $dependencia = "";
        $descripcion = $row['descripcion'];
      }

      if (stristr($descripcion, '|') == true) {
        $exp_resolucion_descripcion = explode('|', $descripcion);
        $resolucion = trim($exp_resolucion_descripcion[1]);
        $descripcion = trim($exp_resolucion_descripcion[0]);
        $migracion_estado = 2;
      }
      else {
        $resolucion = "";
      }
    }
    else {
      $dependencia = "";
      $descripcion = $row['descripcion'];
      $resolucion = "";
    }

    mysqli_query($db_con, "INSERT INTO `incidencias_tic` (`fecha`, `solicitante`, `dependencia`, `problema`, `descripcion`, `estado`, `numincidencia`, `resolucion`) VALUES ('".$row['fecha']."', '".$row_profesor['idea']."', '".$dependencia."', 901, '".$descripcion."', $migracion_estado, '".$row['nincidencia']."', '".$resolucion."')");
  }

  // Eliminamos tabla antigua
  mysqli_query($db_con, "DROP TABLE `partestic`");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Nuevo módulo incidencias TIC', NOW())");
}

/*
  @descripcion: Modificación de la tabla tutoría para registrar intervenciones sobre el grupo
  @fecha: 12 de febrero de 2018
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modificación tabla tutoria'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `tutoria` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `claveal` `claveal` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `apellidos` `apellidos` VARCHAR(42) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `nombre` `nombre` VARCHAR(24) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `jefatura` `jefatura` TINYINT(1) NOT NULL DEFAULT '0';");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modificación tabla tutoria', NOW())");
}


/*
  @descripcion: Modificación de la tabla textos_gratis
  @fecha: 12 de febrero de 2018
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modificación tabla textos_gratis'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `textos_gratis` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `isbn` `isbn` CHAR(13) NULL DEFAULT NULL, CHANGE `ean` `ean` CHAR(13) NULL DEFAULT NULL, CHANGE `ano` `ano` YEAR(4) NULL DEFAULT NULL;");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modificación tabla textos_gratis', NOW())");
}


/*
  @descripcion: Modulo de libros de texto (Solución de errores en importación)
  @fecha: 5 de marzo de 2018
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modulo de libros de texto (Solución de errores)'");
if (! mysqli_num_rows($actua)) {

  // Creamos la nueva tabla para el registro de libros de texto
  mysqli_query($db_con, "DROP TABLE IF EXISTS `libros_texto`");
  mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `libros_texto` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `materia` varchar(64) NOT NULL DEFAULT '',
  `isbn` char(13) DEFAULT NULL,
  `ean` char(13) DEFAULT NULL,
  `editorial` varchar(60) NOT NULL DEFAULT '',
  `titulo` varchar(100) NOT NULL DEFAULT '',
  `importe` decimal(5,2) NULL,
  `nivel` varchar(48) NOT NULL DEFAULT '',
  `programaGratuidad` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;");

  // Migramos los datos de la tabla textos_gratis del Programa de Gratuidad
  $result_update = mysqli_query($db_con, "SELECT `materia`, `isbn`, `ean`, `editorial`, `titulo`, `importe`, `nivel` FROM `textos_gratis` ORDER BY `nivel` ASC, `materia` ASC");
  while ($row_update = mysqli_fetch_array($result_update)) {
    if (! empty($row_update['isbn']) || ! empty($row_update['ean'])) {
      mysqli_query($db_con, "INSERT INTO `libros_texto` (`materia`, `isbn`, `ean`, `editorial`, `titulo`, `importe`, `nivel`, `programaGratuidad`) VALUES ('".$row_update['materia']."', '".$row_update['isbn']."', '".$row_update['ean']."', '".mysqli_real_escape_string($db_con, $row_update['editorial'])."', '".mysqli_real_escape_string($db_con, $row_update['titulo'])."', '".$row_update['importe']."', '".$row_update['nivel']."', 1)") or die (mysqli_error($db_con));
    }
  }
  mysqli_free_result($result_update);

  // Migramos los datos de la Textos de los libros de los departamentos
  $result_update = mysqli_query($db_con, "SELECT `Asignatura`, `isbn`, `Editorial`, `Titulo`, `Autor`, `Editorial`, `Nivel` FROM `Textos` ORDER BY `Nivel` ASC, `Asignatura` ASC");
  while ($row_update = mysqli_fetch_array($result_update)) {
    // Normalizamos el ISBN
    $row_update['isbn'] = str_ireplace('-', '', $row_update['isbn']);
    $row_update['isbn'] = str_ireplace('_', '', $row_update['isbn']);
    $row_update['isbn'] = str_ireplace(' ', '', $row_update['isbn']);
    $row_update['isbn'] = str_ireplace('.', '', $row_update['isbn']);
    $row_update['isbn'] = trim($row_update['isbn']);

    if (! empty($row_update['isbn'])) {
      mysqli_query($db_con, "INSERT INTO `libros_texto` (`materia`, `isbn`, `ean`, `editorial`, `titulo`, `importe`, `nivel`, `programaGratuidad`) VALUES ('".$row_update['Asignatura']."', '".$row_update['isbn']."', '', '".mysqli_real_escape_string($db_con, $row_update['Editorial'])."', '".mysqli_real_escape_string($db_con, $row_update['Titulo']).". ".mysqli_real_escape_string($db_con, $row_update['Autor'])."', 0.00, '".$row_update['Nivel']."', 0)");
    }
  }
  mysqli_free_result($result_update);

  // Creamos la nueva tabla para el registro del estado de los libros de texto del Programa de Gratuidad
  mysqli_query($db_con, "DROP TABLE IF EXISTS `libros_texto_alumnos`");
  mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `libros_texto_alumnos` (
  `claveal` varchar(12) NOT NULL,
  `materia` varchar(10) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT '',
  `devuelto` tinyint(1) NOT NULL DEFAULT '0',
  `fecha` datetime DEFAULT '0000-00-00 00:00:00',
  `curso` varchar(7) NOT NULL DEFAULT '',
  PRIMARY KEY (`claveal`,`materia`),
  KEY `claveal` (`claveal`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

  // Migramos los datos de la tabla textos_alumnos del Programa de Gratuidad
  $result_update = mysqli_query($db_con, "SELECT `claveal`, `materia`, `estado`, `devuelto`, `fecha`, `curso` FROM `textos_alumnos` ORDER BY `curso` ASC, `claveal` ASC, `materia` ASC");
  while ($row_update = mysqli_fetch_array($result_update)) {
    mysqli_query($db_con, "INSERT INTO `libros_texto_alumnos` (`claveal`, `materia`, `estado`, `devuelto`, `fecha`, `curso`) VALUES ('".$row_update['claveal']."', '".$row_update['materia']."', '".$row_update['estado']."', ".$row_update['devuelto'].", '".$row_update['fecha']."', '".$row_update['curso']."')");
  }
  mysqli_free_result($result_update);

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modulo de libros de texto (Solución de errores)', NOW())");
}


/*
  @descripcion: Cambio NC a Claveal en tabla grupos
  @fecha: 25 de marzo de 2018
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Cambio NC a Claveal en tabla grupos'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con,"ALTER TABLE `grupos` CHANGE `alumnos` `alumnos` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''");

  $actua = mysqli_query($db_con, "SELECT alumnos, curso, id FROM grupos");

  while ($cambio1 = mysqli_fetch_array($actua)) {
    $nc="";
    $nuevo_nc = explode(",",$cambio1[0]);
    foreach ($nuevo_nc as $value) {
      $clave = mysqli_query($db_con,"select claveal from FALUMNOS where nc='$value' and unidad='$cambio1[1]'");
      $clave2 = mysqli_fetch_array($clave);
        $nc.="$clave2[0],";
    }
    $nc = substr($nc, 0, -1);
    mysqli_query($db_con,"update grupos set alumnos='$nc' where id='$cambio1[2]'");
  }

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Cambio NC a Claveal en tabla grupos', NOW())");
}

/*
  @descripcion: Eliminado archivos de exportación de usuarios TIC
  @fecha: 3 de mayo de 2018
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Exportar usuarios TIC profesores'");
if (! mysqli_num_rows($actua)) {
  if (file_exists(INTRANET_DIRECTORY.'/xml/jefe/TIC/profesores.txt')) unlink(INTRANET_DIRECTORY.'/xml/jefe/TIC/profesores.txt');
  if (file_exists(INTRANET_DIRECTORY.'/xml/jefe/TIC/profesores_moodle.txt')) unlink(INTRANET_DIRECTORY.'/xml/jefe/TIC/profesores_moodle.txt');

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Exportar usuarios TIC profesores', NOW())");
}


/*
  @descripcion: Eliminado campo Nº Seguridad Social del alumno en tablas alma
  @fecha: 23 de junio de 2018
*/

$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Eliminar campo segsocial'");
if (! mysqli_num_rows($actua)) {
  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM `alma` WHERE Field = 'SEGSOCIAL'");
  if (mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `alma` DROP `SEGSOCIAL`");
  }
  mysqli_free_result($result_update);

  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM `alma_seg` WHERE Field = 'SEGSOCIAL'");
  if (mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `alma_seg` DROP `SEGSOCIAL`");
  }
  mysqli_free_result($result_update);

  $actualizacion_anio = substr($config['curso_actual'], 0, 4) - 1;
  while ($config['db_host_c'.$actualizacion_anio] != "") {

    mysqli_close($db_con);
    $db_con = mysqli_connect($config['db_host_c'.$actualizacion_anio], $config['db_user_c'.$actualizacion_anio], $config['db_pass_c'.$actualizacion_anio], $config['db_name_c'.$actualizacion_anio]) or die("<h1>Error " . mysqli_connect_error() . "</h1>");
    mysqli_query($db_con,"SET NAMES 'utf8'");

    $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM `alma` WHERE Field = 'SEGSOCIAL'");
    if (mysqli_num_rows($result_update)) {
      mysqli_query($db_con, "ALTER TABLE `alma` DROP `SEGSOCIAL`");
    }
    mysqli_free_result($result_update);

    $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM `alma_seg` WHERE Field = 'SEGSOCIAL'");
    if (mysqli_num_rows($result_update)) {
      mysqli_query($db_con, "ALTER TABLE `alma_seg` DROP `SEGSOCIAL`");
    }
    mysqli_free_result($result_update);

    $actualizacion_anio--;
  }
  unset($actualizacion_anio);
  unset($result_update);

  mysqli_close($db_con);
  $db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']) or die("<h1>Error " . mysqli_connect_error() . "</h1>");
  mysqli_query($db_con,"SET NAMES 'utf8'");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Eliminar campo segsocial', NOW())");
}

/*
  @descripcion: Añadido fecha de toma de posesión y cese de los profesores
  @fecha: 22 de septiembre de 2018
*/

$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Campos fechatoma y fechacese en tabla departamentos'");
if (! mysqli_num_rows($actua)) {
  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM `departamentos` WHERE Field = 'fechatoma'");
  if (! mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `departamentos` ADD `fechatoma` DATE NOT NULL DEFAULT '0000-00-00', ADD `fechacese` DATE NOT NULL DEFAULT '0000-00-00' ;");
  }

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Campos fechatoma y fechacese en tabla departamentos', NOW())");
}

/*
  @descripcion: Añadido Id de jornada en tramos horarios
  @fecha: 23 de septiembre de 2018
*/

$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Id jornada en tramos horarios'");
if (! mysqli_num_rows($actua)) {
  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM `tramos` WHERE Field = 'idjornada'");
  if (! mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `tramos` ADD `idjornada` int(12) unsigned NOT NULL ;");
  }

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Id jornada en tramos horarios', NOW())");
}

/*
  @descripcion: Añadido columna idactividad en horw
  @fecha: 25 de septiembre de 2018
*/

$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Columna idactividad en tabla horw'");
if (! mysqli_num_rows($actua)) {
  $result_update = mysqli_query($db_con, "SHOW COLUMNS FROM `horw` WHERE Field = 'idactividad'");
  if (! mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "ALTER TABLE `horw` ADD `idactividad` INT(11) UNSIGNED NULL AFTER `clase`;");
    mysqli_query($db_con, "ALTER TABLE `horw_faltas` ADD `idactividad` INT(11) UNSIGNED NULL AFTER `clase`;");

    // Actualizamos los códigos
    $result2_update = mysqli_query($db_con, "SELECT DISTINCT `id`, `c_asig` FROM `horw`");
    while ($row2_update = mysqli_fetch_array($result2_update)) {
      if ($row2_update['c_asig'] < 1000) {
        mysqli_query($db_con, "UPDATE `horw` SET `idactividad` = ".$row2_update['c_asig']." WHERE `id` = ".$row2_update['id']."");
        mysqli_query($db_con, "UPDATE `horw_faltas` SET `idactividad` = ".$row2_update['c_asig']." WHERE `id` = ".$row2_update['id']."");
      }
      else {
        mysqli_query($db_con, "UPDATE `horw` SET `idactividad` = 1 WHERE `id` = ".$row2_update['id']."");
        mysqli_query($db_con, "UPDATE `horw_faltas` SET `idactividad` = 1 WHERE `id` = ".$row2_update['id']."");
      }
    }
  }

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Columna idactividad en tabla horw', NOW())");
}

/*
  @descripcion: Tipos de recursos para reservar por defecto
  @fecha: 16 de diciembre de 2018
*/

$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Tipos de recursos para reservar'");
if (! mysqli_num_rows($actua)) {
  $result_update = mysqli_query($db_con, "SELECT `tipo` FROM `reservas_tipos` WHERE `id` = 1");
  if (! mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "INSERT INTO `reservas_tipos` (`id`, `tipo`, `observaciones`) VALUES (1, 'TIC', '')");
  }
  else {
    $row_update = mysqli_fetch_array($result_update);
    $tipo_reserva = $row_update['tipo'];
    if ($tipo_reserva != "TIC") {
      mysqli_query($db_con, "UPDATE `reservas_tipos` SET `tipo` = 'TIC' WHERE `id` = 1");
      mysqli_query($db_con, "DELETE FROM `reservas_elementos` WHERE `id_tipo` = 1");
    }
  }

  $result_update = mysqli_query($db_con, "SELECT `tipo` FROM `reservas_tipos` WHERE `id` = 2");
  if (! mysqli_num_rows($result_update)) {
    mysqli_query($db_con, "INSERT INTO `reservas_tipos` (`id`, `tipo`, `observaciones`) VALUES (2, 'Medios Audiovisuales', '')");
  }
  else {
    $row_update = mysqli_fetch_array($result_update);
    $tipo_reserva = $row_update['tipo'];
    if ($tipo_reserva != "Medios Audiovisuales") {
      mysqli_query($db_con, "UPDATE `reservas_tipos` SET `tipo` = 'Medios Audiovisuales' WHERE `id` = 2");
      mysqli_query($db_con, "DELETE FROM `reservas_elementos` WHERE `id_tipo` = 2");
    }
  }

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Tipos de recursos para reservar', NOW())");
}

/*
  @descripcion: Crea tabla compromiso_convivencia
  @fecha: 6 de enero de 2019
*/

$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Crea tabla compromiso_convivencia'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "DROP TABLE IF EXISTS `compromiso_convivencia`;");
  mysqli_query($db_con, "CREATE TABLE `compromiso_convivencia` (`nie` VARCHAR(12) NOT NULL, `fecha` DATETIME NOT NULL, PRIMARY KEY (`nie`)) ENGINE = MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Crea tabla compromiso_convivencia', NOW())");
}

/*
  @descripcion: Crea tabla puestos_alumnos_tic
  @fecha: 20 de enero de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Crea tabla puestos_alumnos_tic'");
if (! mysqli_num_rows($actua)) {
  // TABLA DE PUESTOS TIC
  mysqli_query($db_con, "DROP TABLE IF EXISTS `puestos_alumnos_tic`");
  mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `puestos_alumnos_tic` (
    `profesor` varchar(50) NOT NULL,
    `grupo` varchar(64) NOT NULL,
    `asignatura` varchar(30) NOT NULL,
    `aula` varchar(32) NOT NULL,
    `puestos` text COLLATE utf8_general_ci,
    `monopuesto` tinyint(1) UNSIGNED NOT NULL,
    PRIMARY KEY (`profesor`,`grupo`,`asignatura`,`aula`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Crea tabla puestos_alumnos_tic', NOW())");
}

/*
  @descripcion: Modicación estructura tabla puestos_alumnos
  @fecha: 20 de enero de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modicación estructura tabla puestos_alumnos'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `puestos_alumnos` ADD `estructura` VARCHAR(10) NOT NULL AFTER `puestos`;");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modicación estructura tabla puestos_alumnos', NOW())");
}

/*
  @descripcion: Modicación estructura tabla c_profes
  @fecha: 20 de enero de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modicación estructura tabla c_profes'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `c_profes` ADD `rgpd_mostrar_nombre` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `totp_secret`");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modicación estructura tabla c_profes', NOW())");
}

/*
  @descripcion: Modificación estructura tabla Fechorias
  @fecha: 23 de febrero de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modificación estructura tabla Fechorias'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `Fechoria` ADD `adjunto` VARCHAR(500) NULL DEFAULT NULL");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modificación estructura tabla Fechorias', NOW())");
}

/*
  @descripcion: Modificación estructura tabla mensajes
  @fecha: 23 de febrero de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modificación estructura tabla mensajes'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `mensajes` CHANGE `archivo` `archivo` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modificación estructura tabla mensajes', NOW())");
}


/*
  @descripcion: Modificación estructura tabla alma
  @fecha: 10 de marzo de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modificación estructura tabla alma'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `alma` ADD `NSEGSOCIAL` INT(12) NULL AFTER `FECHAMATRICULA`");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modificación estructura tabla alma', NOW())");
}

/*
  @descripcion: Modificación estructura tabla evalua_pendientes
  @fecha: 27 de marzo de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modificación estructura tabla evalua_pendientes'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE  `evalua_pendientes` CHANGE  `materia`  `materia` VARCHAR( 12 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modificación estructura tabla evalua_pendientes', NOW())");
}


/*
  @descripcion: Modificación estructura tabla matriculas.
  @fecha: 8 de abril de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modificación estructura tabla matriculas'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE  `matriculas` ADD  `nsegsocial` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");
  mysqli_query($db_con, "ALTER TABLE  `matriculas_bach` ADD  `nsegsocial` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");
  mysqli_query($db_con, "ALTER TABLE  `matriculas_backup` ADD  `nsegsocial` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");
  mysqli_query($db_con, "ALTER TABLE  `matriculas_bach_backup` ADD  `nsegsocial` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modificación estructura tabla matriculas', NOW())");
}

/*
  @descripcion: Modificación estructura tabla matriculas.
  @fecha: 27 de mayo de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Nuevos datos del alumno en tablas de matriculas'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE  `matriculas_bach` ADD  `parcial` tinyint(1) NOT NULL ");
  mysqli_query($db_con, "ALTER TABLE  `matriculas_bach_backup` ADD  `parcial` tinyint(1) NOT NULL ");

  mysqli_query($db_con, "ALTER TABLE  `matriculas` ADD  `correo_alumno` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");
  mysqli_query($db_con, "ALTER TABLE  `matriculas_bach` ADD  `correo_alumno` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");
  mysqli_query($db_con, "ALTER TABLE  `matriculas_backup` ADD  `correo_alumno` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");
  mysqli_query($db_con, "ALTER TABLE  `matriculas_bach_backup` ADD  `correo_alumno` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");

  mysqli_query($db_con, "ALTER TABLE  `matriculas` ADD `analgesicos` TINYINT( 1 ) NOT NULL");
  mysqli_query($db_con, "ALTER TABLE  `matriculas_bach` ADD `analgesicos` TINYINT( 1 ) NOT NULL");
  mysqli_query($db_con, "ALTER TABLE  `matriculas_backup` ADD `analgesicos` TINYINT( 1 ) NOT NULL");
  mysqli_query($db_con, "ALTER TABLE  `matriculas_bach_backup` ADD `analgesicos` TINYINT( 1 ) NOT NULL");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Nuevos datos del alumno en tablas de matriculas', NOW())");
}

/*
  @descripcion: Corrección registro libros de texto.
  @fecha: 24 de junio de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Corrección registro libros de texto'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `libros_texto` CHANGE `nivel` `nivel` VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';");
  mysqli_query($db_con, "UPDATE `libros_texto` SET `nivel` = '1º de Bachillerato (Humanidades y Ciencias Sociales (Lomce))' WHERE `nivel` = '1º de Bachillerato (Humanidades y Ciencias Socia'");
  mysqli_query($db_con, "UPDATE `libros_texto` SET `nivel` = '2º de Bachillerato (Humanidades y Ciencias Sociales (Lomce))' WHERE `nivel` = '2º de Bachillerato (Humanidades y Ciencias Socia'");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Corrección registro libros de texto', NOW())");
}

/*
  @descripcion: Aumento tamaño campo de reservas para observaciones
  @fecha: 13 de septiembre de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Aumento tamaño campo de reservas para observaciones'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `reservas` CHANGE `event1` `event1` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `event2` `event2` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `event3` `event3` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `event4` `event4` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `event5` `event5` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `event6` `event6` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `event7` `event7` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Aumento tamaño campo de reservas para observaciones', NOW())");
}


/*
  @descripcion: Tabla de problemas de convivencia de Séneca
  @fecha: 15 de septiembre de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Tabla de problemas de convivencia de Séneca'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "CREATE TABLE `listafechorias_seneca` (
    `ID` int(4) NOT NULL AUTO_INCREMENT,
    `fechoria` varchar(255) DEFAULT NULL,
    `medidas` varchar(64) DEFAULT NULL,
    `medidas2` longtext,
    `tipo` varchar(10) DEFAULT NULL,
    PRIMARY KEY (`ID`)
  ) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;");


  mysqli_query($db_con, "INSERT INTO `listafechorias_seneca` (`ID`, `fechoria`, `medidas`, `medidas2`, `tipo`) VALUES
  (1, 'Perturbación del normal desarrollo de las actividades de clase (Contraria)', 'Amonestación escrita', 'Imponer correcciones como: estancia en el Aula de Convivencia varios días; estancia de un familiar en el aula, con el alumno, durante varios días; o expulsión del centro entre 1 y 29 días en función de la gravedad.', 'grave'),
  (2, 'Falta de colaboración sistemática en la realización de las actividades (Contraria)', 'Amonestación escrita', 'Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); expulsarlo de clase (medida extraordinaria que debe ir acompañada con escrito del profesor a los padres). El tutor tratará el caso con la familia y propondrá a Jefatura medidas a adoptar.', 'grave'),
  (3, 'Impedir o dificultar el estudio a sus compañeros (Contraria)', 'Amonestación escrita', 'Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); expulsarlo de clase (medida extraordinaria). El tutor tratará el caso con Jefatura  para adoptar medidas.', 'grave'),
  (4, 'Faltas injustificadas de puntualidad (Contraria)', 'Amonestación escrita', 'Seguir protocolo: a) Llamada telefónica a la familia b) Escrito a la familia c) Escrito certificado con acuse de recibo a la familia d) Traslado del caso a Asuntos Sociales.', 'grave'),
  (5, 'Faltas injustificadas de asistencia a clase (Contraria)', 'Amonestación escrita', 'Seguir protocolo: a) Llamada telefónica a la familia b) Escrito a la familia c) Escrito certificado con acuse de recibo a la familia d) Traslado del caso a Asuntos Sociales.', 'grave'),
  (6, 'Actuaciones incorrectas hacia algún miembro de la comunidad educativa (Contraria)', 'Amonestación escrita', 'Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); expulsarlo de clase  (medida extraordinaria que debe ir acompañada con escrito del profesor a los padres). La petición de excusas se considerará un atenuante a valorar. El tutor tratará el caso con la familia y propondrá a Jefatura medidas a adoptar.', 'grave'),
  (7, 'Daños en instalaciones o docum. del Centro o en pertenencias de un miembro (Contraria)', 'Amonestación escrita', 'El tutor tratará el caso con la familia y el alumno y familia realizará trabajos complementarios para la comunidad y  restaurará los daños o pagará los gastos de reparación o restitución.', 'grave'),
  (8, 'Vejaciones o humillaciones contra un miembro de la comunidad educativa (Grave)', 'Amonestación escrita', 'Petición publica de disculpas y comunicación con la familia. Si el hecho es grave, iniciar los trámites legales oportunos (Asuntos Sociales, Policía Nacional, etc.) Imponer expulsión del centro entre 1 y 29 dependiendo de la gravedad.', 'muy grave'),
  (9, 'Actuaciones perjudiciales para la salud y la integridad, o incitación a ellas (Grave)', 'Amonestación escrita', 'Si el hecho es grave, iniciar los trámites legales oportunos (Asuntos Sociales, Policía Nacional, etc.).  Entrega de trabajo relacionado con el hecho y la salud. Imponer sanción de estancia en el Aula de Convivencia o  expulsión del centro entre 1 y 29 dependiendo de la gravedad.', 'muy grave'),
  (10, 'Amenazas o coacciones a un miembro de la comunidad educativa', 'Amonestación escrita', 'Petición publica de disculpas y comunicación con la familia. Si el hecho es grave, iniciar los trámites legales oportunos (Asuntos Sociales, Policía Nacional, etc.) Imponer correcciones como: estancia en el Aula de Convivencia varios días; o expulsión del centro entre 1 y 29  dependiendo de la gravedad', 'muy grave'),
  (11, 'Suplantación de la personalidad, y falsificación o sustracción de documentos (Grave)', 'Amonestación escrita', 'Si el hecho es grave, iniciar los trámites legales oportunos (Asuntos Sociales, Policía Nacional, etc.) Imponer expulsión del centro entre 1 y 29 dependiendo de la gravedad.', 'muy grave'),
  (12, 'Deterioro grave de instalac. o docum. del Centro, o pertenencias de un miembro (Grave)', 'Amonestación escrita', 'Jefatura de Estudios tratará el caso con la familia y el alumno y familia realizará trabajos complementarios para la comunidad y  restaurará los daños o pagará los gastos de reparación o restitución.', 'muy grave'),
  (13, 'Reiteración en un mismo curso de conductas contrarias a normas de convivencia (Grave)', 'Amonestación escrita', 'Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); realizar trabajos para la comunidad; o estancia en el Aula de Convivencia entre 1 y 3 días.', 'muy grave'),
  (14, 'Impedir el normal desarrollo de las actividades del Centro (Grave)', 'Amonestación escrita', 'Jefatura tratará el caso con la familia. Imponer correcciones como: estancia en el Aula de Convivencia varios días; estancia de un familiar en el aula, con el alumno, durante varios días; o expulsión del centro entre 1 y 29 días en función de la gravedad', 'muy grave'),
  (15, 'Incumplimiento de las correcciones impuestas (Grave)', 'Amonestación escrita', 'Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); realizar trabajos para la comunidad; o estancia en el Aula de Convivencia entre 1 y 3 días.', 'muy grave');");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Tabla de problemas de convivencia de Séneca', NOW())");
}

/*
  @descripcion: Ampliación reservas a horario de tarde
  @fecha: 26 de noviembre de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Ampliación reservas a horario de tarde'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "ALTER TABLE `reservas` ADD `event8` VARCHAR(255) NULL AFTER `event7`, ADD `event9` VARCHAR(255) NULL AFTER `event8`, ADD `event10` VARCHAR(255) NULL AFTER `event9`, ADD `event11` VARCHAR(255) NULL AFTER `event10`, ADD `event12` VARCHAR(255) NULL AFTER `event11`, ADD `event13` VARCHAR(255) NULL AFTER `event12`, ADD `event14` VARCHAR(255) NULL AFTER `event13`;");

  mysqli_query($db_con, "ALTER TABLE `reservas` CHANGE `servicio` `servicio` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Ampliación reservas a horario de tarde', NOW())");
}

/*
  @descripcion: Ampliación campo IP para direcciones IPv6
  @fecha: 01 de diciembre de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Ampliación campo IP para direcciones IPv6'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "ALTER TABLE `reg_intranet` CHANGE `ip` `ip` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Ampliación campo IP para direcciones IPv6', NOW())");
}

/*
  @descripcion: Cambios en tabla c_profes
  @fecha: 01 de diciembre de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Cambios en tabla c_profes'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "ALTER TABLE `c_profes` ADD `correo_verificado` TINYINT(1) NOT NULL DEFAULT '0' AFTER `correo`;");
  mysqli_query($db_con, "ALTER TABLE `c_profes` ADD `telefono_verificado` TINYINT(1) NOT NULL DEFAULT '0' AFTER `telefono`;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Cambios en tabla c_profes', NOW())");
}

/*
  @descripcion: Cambios en alma. Teléfono de tutores legales
  @fecha: 07 de diciembre de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Cambios en alma. Teléfono de tutores legales'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "ALTER TABLE `alma` ADD `TELEFONOTUTOR` CHAR(9) NULL DEFAULT NULL AFTER `NOMBRETUTOR`;");
  mysqli_query($db_con, "ALTER TABLE `alma` ADD `TELEFONOTUTOR2` CHAR(9) NULL DEFAULT NULL AFTER `NOMBRETUTOR2`;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Cambios en alma. Teléfono de tutores legales', NOW())");
}

/*
  @descripcion: Eliminar archivos obsoletos
  @fecha: 22 de diciembre de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Eliminar archivos obsoletos'");
if (! mysqli_num_rows($actua)) {

  @unlink(INTRANET_DIRECTORY . '/loginSeneca.php');
  @unlink(INTRANET_DIRECTORY . '/login_totp.php');
  @unlink(INTRANET_DIRECTORY . '/totp.php');
  @unlink(INTRANET_DIRECTORY . '/clave.php');
  @unlink(INTRANET_DIRECTORY . '/salir.php');
  @unlink(INTRANET_DIRECTORY . '/xml/jefe/index_temas.php');
  @unlink(INTRANET_DIRECTORY . '/xml/jefe/informes/sesiones.php');

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Eliminar archivos obsoletos', NOW())");
}

/*
  @descripcion: Generamos el secreto para la instancia de la Intranet
  @fecha: 22 de diciembre de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Secreto de la Intranet'");
if (! mysqli_num_rows($actua)) {

  function randomPassword($long = 13)
  {
    $alfabeto = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
      $pass = array();
      $long_alfabeto = strlen($alfabeto) - 1;
      for ($i = 0; $i < $long; $i++) {
          $p = rand(0, $long_alfabeto);
          $pass[] = $alfabeto[$p];
      }
      return implode($pass);
  }

  if (! isset($config['intranet_secret']) || empty($config['intranet_secret'])) {
    $intranet_secret = randomPassword(25);

    if ($file = fopen(CONFIG_FILE, 'a')) {
      fwrite($file, "\r\n");
      fwrite($file, "\$config['intranet_secret']\t\t\t= '$intranet_secret';\r\n");
    }
  }

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Secreto de la Intranet', NOW())");
}

/*
  @descripcion: Añadimos campo vistas a la tabla noticias
  @fecha: 11 de enero de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Campo vista en tabla noticias'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "ALTER TABLE `noticias` ADD `vistas` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `categoria`;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Campo vista en tabla noticias', NOW())");
}

/*
  @descripcion: Ampliación campo pass en tabla c_profes
  @fecha: 12 de enero de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Ampliación campo pass en tabla c_profes'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "ALTER TABLE `c_profes` CHANGE `pass` `pass` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Ampliación campo pass en tabla c_profes', NOW())");
}

/*
  @descripcion: Clave primaria en FTUTORES
  @fecha: 11 de febrero de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Clave primaria en FTUTORES'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "ALTER TABLE FTUTORES DROP PRIMARY KEY;");
  mysqli_query($db_con, "ALTER TABLE `FTUTORES` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Clave primaria en FTUTORES', NOW())");
}

/*
  @descripcion: Tabla para las adaptaciones curriculares
  @fecha: 04 de abril de 2020
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Tabla de adaptaciones'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "CREATE TABLE `adaptaciones` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `alumno` varchar(12) NOT NULL,
    `unidad` varchar(64) NOT NULL,
    `materia` varchar(128) NOT NULL,
    `departamento` varchar(96) NOT NULL,
    `profesor` varchar(64) NOT NULL,
    `texto` text NOT NULL,
    `fecha` date NOT NULL,
    `curso` varchar(96) NOT NULL,
     PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Tabla de adaptaciones', NOW())");
}


/*
  @descripcion: Tablas para los trámites de audiencia y matrículas
  @fecha: 04 de abril de 2020
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Tabla de audiencias'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `audiencia` (
  `claveal` varchar(12) NOT NULL,
  `texto` text NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`claveal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Tabla de audiencias', NOW())");
}

/*
  @descripcion: Tablas para los informes de evaluación extraordinaria
  @fecha: 05 de junio de 2020
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Tablas de informes extraordinaria'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `informe_extraordinaria` (
  `id_informe` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profesor` varchar(12) COLLATE latin1_spanish_ci NOT NULL,
  `asignatura` varchar(64) COLLATE latin1_spanish_ci NOT NULL,
  `unidad` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `curso` varchar(96) COLLATE latin1_spanish_ci NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `modalidad` varchar(12) COLLATE latin1_spanish_ci DEFAULT NULL,
  `fechareg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `plantilla` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_informe`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=36");

  mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `informe_extraordinaria_alumnos` (
  `id_informe` int(10) unsigned NOT NULL,
  `id_contenido` int(10) unsigned NOT NULL,
  `claveal` varchar(12) COLLATE latin1_spanish_ci NOT NULL,
  `debe_recuperar` int(1) NOT NULL DEFAULT '0',
  `actividades` text COLLATE latin1_spanish_ci,
  PRIMARY KEY (`id_informe`,`id_contenido`,`claveal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci");

  mysqli_query($db_con, "CREATE TABLE IF NOT EXISTS `informe_extraordinaria_contenidos` (
  `id_informe` int(10) unsigned NOT NULL,
  `id_contenido` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unidad` varchar(30) COLLATE latin1_spanish_ci NOT NULL,
  `titulo` varchar(90) COLLATE latin1_spanish_ci NOT NULL,
  `contenidos` text COLLATE latin1_spanish_ci NOT NULL,
  `actividades` text COLLATE latin1_spanish_ci NOT NULL,
  `fechareg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_contenido`,`id_informe`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=39");
  

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Tablas de informes extraordinaria', NOW())");
}

/*
  @descripcion: Modificación campo nombre de la tabla departamentos y c_profes
  @fecha: 19 de septiembre de 2020
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modificación campo nombre de la tabla departamentos y c_profes'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "ALTER TABLE `departamentos` CHANGE `NOMBRE` `NOMBRE` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
  mysqli_query($db_con, "ALTER TABLE `c_profes` CHANGE `PROFESOR` `PROFESOR` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
  
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modificación campo nombre de la tabla departamentos y c_profes', NOW())");
}

/*
  @descripcion: Creación de las tablas del módulo de informes de pendientes
  @fecha: 18 de octubre de 2020
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Tablas para informes de pendientes'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "create table IF NOT EXISTS informe_pendientes select * from informe_extraordinaria");
  mysqli_query($db_con, "create table IF NOT EXISTS informe_pendientes_alumnos select * from informe_extraordinaria_alumnos");
  mysqli_query($db_con, "create table IF NOT EXISTS informe_pendientes_contenidos select * from informe_extraordinaria_contenidos");
    
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Tablas para informes de pendientes', NOW())");
}


/*
  @descripcion: Modificación campo dni en tabla mensajes
  @fecha: 04 de noviembre de 2020
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Modificación campo dni en tabla mensajes'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `mensajes` CHANGE `dni` `dni` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Modificación campo dni en tabla mensajes', NOW())");
}

/*
  @descripcion: Nuevos campos correo tutores legales en tabla alma
  @fecha: 12 de noviembre de 2020
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Nuevos campos correo tutores legales en tabla alma'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `alma` ADD `CORREOTUTOR` VARCHAR(255) NULL AFTER `NOMBRETUTOR`;");
  mysqli_query($db_con, "ALTER TABLE `alma` ADD `CORREOTUTOR2` VARCHAR(255) NULL AFTER `SEGUNDOAPELLIDOTUTOR2`;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Nuevos campos correo tutores legales en tabla alma', NOW())");
}


/*
  @descripcion: Eliminar profesores con usuario IdEA incorrecto
  @fecha: 08 de diciembre de 2020
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Eliminar profesores con usuario IdEA incorrecto'");
if (! mysqli_num_rows($actua)) {

  $actualiza_res = mysqli_query($db_con, "SELECT `profesor`, `idea` FROM `c_profes` WHERE `idea` LIKE '6%' OR `idea` LIKE '7%' OR `idea` = '';");

  if (mysqli_num_rows($actualiza_res)) {
    while ($actualiza_row = mysqli_fetch_array($actualiza_res)) {

    $asunto = "[IMPORTANTE] Se ha eliminado un empleado de la base de datos";
    $texto = "Se ha eliminado a <strong>".$actualiza_row['profesor']."</strong> de la base de datos debido a que no se importaron los datos correctamente en la última actualización. Por favor, actualice el Profesorado del Centro y/o Personal no docente.";

    $result_msg = mysqli_query($db_con, "INSERT INTO mens_texto (asunto, texto, origen) VALUES ('".$asunto."','".$texto."','admin')");
    $id_msg = mysqli_insert_id($db_con);

    $dir0 = mysqli_query($db_con, "SELECT DISTINCT `idea` FROM `departamentos` WHERE `cargo` LIKE '%1%'");
    while ($dir1 = mysqli_fetch_array($dir0)) {
      $rep0 = mysqli_query($db_con, "SELECT * FROM `mens_profes` WHERE `id_texto` = '$id_msg' AND `profesor` = '$dir1[0]'");
      $num0 = mysqli_fetch_row($rep0);
      if (strlen($num0[0]) < 1) {
        mysqli_query($db_con, "INSERT INTO `mens_profes` (`id_texto`, `profesor`) VALUES ('".$id_msg."','".$dir1[0]."')");
      }
    }
    mysqli_query($db_con, "UPDATE `mens_texto` SET `destino` = 'Equipo Directivo' WHERE `id` = '$id_msg'");

    mysqli_query($db_con, "DELETE FROM `c_profes` WHERE `idea` = '".$actualiza_row['idea']."' LIMIT 1;");
    mysqli_query($db_con, "DELETE FROM `departamentos` WHERE `idea` = '".$actualiza_row['idea']."' LIMIT 1;");
    mysqli_query($db_con, "DELETE FROM `calendario_categorias` WHERE `idea` = '".$actualiza_row['idea']."' LIMIT 1;");
    }

  }
  
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Eliminar profesores con usuario IdEA incorrecto', NOW())");
}


/*
  @descripcion: Tabla para registrar correos
  @fecha: 23 de enero de 2021
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Tabla para registrar correos'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con,"CREATE TABLE IF NOT EXISTS `correos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `destino` varchar(72) NOT NULL,
  `correo` varchar(72) NOT NULL,
  `fecha` datetime NOT NULL,
  `texto` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Tabla para registrar correos', NOW())");
}

/*
  @descripcion: Actualización campo horas en tabla ausencias
  @fecha: 12 de febrero de 2021
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Actualización campo horas en tabla ausencias'");
if (! mysqli_num_rows($actua)) {
  
  mysqli_query($db_con, "ALTER TABLE `ausencias` CHANGE `horas` `horas` VARCHAR(100) NOT NULL DEFAULT '0';");
  
  $result = mysqli_query($db_con, "SELECT id, horas FROM ausencias");
  while ($row = mysqli_fetch_array($result)) {
    $hora_string = (string)$row['horas'];
    $hora_guardar = '';
    for ($i=0;$i<strlen($hora_string);$i++) {
      $hora_guardar .= $hora_string[$i].',';
    }
    $hora_guardar = trim($hora_guardar, ',');
    mysqli_query($db_con, "UPDATE `ausencias` SET `horas` = '$hora_guardar' WHERE `id` = ".$row['id']);
  }
  
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Actualización campo horas en tabla ausencias', NOW())");
}


/*
  @descripcion: Eliminado campo html en tabla reservas
  @fecha: 30 de abril de 2021
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Eliminado campo html en tabla reservas'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `reservas` DROP `html`");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Eliminado campo html en tabla reservas', NOW())");
}

/*
  @descripcion: Actualizacion tabla matriculas
  @fecha: 18 de mayo de 2021
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Actualizacion tabla matriculas 2021'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `matriculas` ADD `optativa8` TINYINT(1) NULL AFTER `analgesicos`, ADD `optativa9` TINYINT(1) NULL AFTER `optativa8`;");
  mysqli_query($db_con, "ALTER TABLE `matriculas` ADD `optativa28` TINYINT(1) NULL AFTER `optativa9`, ADD `optativa29` TINYINT(1) NULL AFTER `optativa28`;");
  mysqli_query($db_con, "ALTER TABLE `matriculas` ADD `cuenta` TINYINT(1) NULL AFTER `optativa29`;");
  mysqli_query($db_con, "ALTER TABLE `matriculas_bach` ADD `salida` TINYINT(1) NULL AFTER `optativa29`;");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Actualizacion tabla matriculas 2021', NOW())");
}