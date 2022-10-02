<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed');


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
  @descripcion: Añadimos campo vistas a la tabla noticias
  @fecha: 11 de enero de 2019
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Campo vista en tabla noticias'");
if (! mysqli_num_rows($actua)) {

  mysqli_query($db_con, "ALTER TABLE `noticias` ADD `vistas` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `categoria`;");

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Campo vista en tabla noticias', NOW())");
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


/*
  @descripcion: Actualizacion campos tabla alma
  @fecha: 13 de septiembre de 2021
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Actualizacion campos tabla alma'");
if (! mysqli_num_rows($actua)) {

  if (mysqli_num_rows(mysqli_query($db_con, "SHOW COLUMNS FROM `alma` LIKE 'Teléfono personal alumno/a'")) == 1 ) {
    mysqli_query($db_con, "ALTER TABLE `alma` CHANGE `Teléfono personal alumno/a` `TELEFONOALUMNO` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
  }

  if (mysqli_num_rows(mysqli_query($db_con, "SHOW COLUMNS FROM `alma` LIKE 'Correo electrónico personal alumno/a'")) == 1 ) {
    mysqli_query($db_con, "ALTER TABLE `alma` CHANGE `Correo electrónico personal alumno/a` `CORREOALUMNO` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
  }

  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Actualizacion campos tabla alma', NOW())");
}

/*
  @descripcion: Ampliación campo nomunidad en tabla unidades
  @fecha: 2 de octubre de 2022
*/
$actua = mysqli_query($db_con, "SELECT `modulo` FROM `actualizacion` WHERE `modulo` = 'Ampliación campo nomunidad en tabla unidades'");
if (! mysqli_num_rows($actua)) {
  mysqli_query($db_con, "ALTER TABLE `unidades` CHANGE `nomunidad` `nomunidad` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
  mysqli_query($db_con, "INSERT INTO `actualizacion` (`modulo`, `fecha`) VALUES ('Ampliación campo nomunidad en tabla unidades', NOW())");
}
