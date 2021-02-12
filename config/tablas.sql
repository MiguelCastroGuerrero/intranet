--
-- Estructura de tabla para la tabla `absentismo`
--

DROP TABLE IF EXISTS `absentismo`;
CREATE TABLE IF NOT EXISTS `absentismo` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL DEFAULT '',
  `mes` char(2) NOT NULL DEFAULT '',
  `numero` bigint(21) NOT NULL DEFAULT '0',
  `unidad` varchar(64) NOT NULL,
  `jefatura` mediumtext,
  `tutoria` mediumtext,
  `orientacion` mediumtext,
  `serv_sociales` mediumtext,
  `fecha_registro` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acceso`
--

DROP TABLE IF EXISTS `acceso`;
CREATE TABLE IF NOT EXISTS `acceso` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profesor` varchar(10) NOT NULL,
  `fecha` date NOT NULL,
  `clase` tinyint(1) NOT NULL,
  `observaciones` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acceso_dias`
--

DROP TABLE IF EXISTS `acceso_dias`;
CREATE TABLE IF NOT EXISTS `acceso_dias` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `numero` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividadalumno`
--

DROP TABLE IF EXISTS `actividadalumno`;
CREATE TABLE IF NOT EXISTS `actividadalumno` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL DEFAULT '',
  `cod_actividad` int(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades_seneca`
--

DROP TABLE IF EXISTS `actividades_seneca`;
CREATE TABLE IF NOT EXISTS `actividades_seneca` (
  `regactividad` char(1) NOT NULL,
  `idactividad` int(11) UNSIGNED NOT NULL,
  `nomactividad` varchar(100) NOT NULL,
  `requnidadactividad` char(1) NOT NULL,
  `reqmateriaactividad` char(1) NOT NULL,
  PRIMARY KEY (`idactividad`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actualizacion`
--

DROP TABLE IF EXISTS `actualizacion`;
CREATE TABLE IF NOT EXISTS `actualizacion` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `modulo` varchar(128) NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adaptaciones`
--

DROP TABLE IF EXISTS `adaptaciones`;
CREATE TABLE `adaptaciones` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------


--
-- Estructura de tabla para la tabla `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
CREATE TABLE IF NOT EXISTS `alumnos` (
  `nombre` varchar(71) DEFAULT NULL,
  `unidad` varchar(255) DEFAULT NULL,
  `claveal` varchar(12) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas`
--

DROP TABLE IF EXISTS `asignaturas`;
CREATE TABLE IF NOT EXISTS `asignaturas` (
  `CODIGO` varchar(10) NOT NULL DEFAULT '',
  `NOMBRE` varchar(255) DEFAULT NULL,
  `ABREV` varchar(10) DEFAULT NULL,
  `CURSO` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audiencia`
--

CREATE TABLE IF NOT EXISTS `audiencia` (
  `claveal` varchar(12) NOT NULL,
  `texto` text NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`claveal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ausencias`
--

DROP TABLE IF EXISTS `ausencias`;
CREATE TABLE IF NOT EXISTS `ausencias` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profesor` varchar(64) NOT NULL DEFAULT '',
  `inicio` date NOT NULL DEFAULT '0000-00-00',
  `fin` date NOT NULL DEFAULT '0000-00-00',
  `horas` varchar(100) NOT NULL DEFAULT '0',
  `tareas` mediumtext NOT NULL,
  `ahora` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archivo` varchar(186) NOT NULL,
  `Observaciones` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblioteca`
--

DROP TABLE IF EXISTS `biblioteca`;
CREATE TABLE IF NOT EXISTS `biblioteca` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Autor` varchar(128) NOT NULL,
  `Titulo` varchar(128) NOT NULL,
  `Editorial` varchar(128) NOT NULL,
  `ISBN` varchar(15) NOT NULL,
  `Tipo` varchar(64) NOT NULL,
  `anoEdicion` int(4) NOT NULL,
  `extension` varchar(8) NOT NULL,
  `serie` int(11) UNSIGNED NOT NULL,
  `lugaredicion` varchar(48) NOT NULL,
  `tipoEjemplar` varchar(128) NOT NULL,
  `ubicacion` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblioteca_lectores`
--

DROP TABLE IF EXISTS `biblioteca_lectores`;
CREATE TABLE IF NOT EXISTS `biblioteca_lectores` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Codigo` varchar(12) NOT NULL,
  `DNI` varchar(12) NOT NULL,
  `Apellidos` varchar(48) NOT NULL,
  `Nombre` varchar(32) NOT NULL,
  `Grupo` varchar(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calendario`
--

DROP TABLE IF EXISTS `calendario`;
CREATE TABLE IF NOT EXISTS `calendario` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `categoria` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` longtext NOT NULL,
  `fechaini` date DEFAULT NULL,
  `horaini` time DEFAULT NULL,
  `fechafin` date DEFAULT NULL,
  `horafin` time DEFAULT NULL,
  `lugar` varchar(180) NOT NULL,
  `departamento` mediumtext,
  `profesores` mediumtext,
  `unidades` mediumtext,
  `asignaturas` mediumtext,
  `fechareg` datetime NOT NULL,
  `profesorreg` varchar(60) NOT NULL,
  `confirmado` tinyint(1) NOT NULL,
  `observaciones` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calendario_categorias`
--

DROP TABLE IF EXISTS `calendario_categorias`;
CREATE TABLE IF NOT EXISTS `calendario_categorias` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) NOT NULL,
  `fecha` date NOT NULL,
  `profesor` varchar(80) NOT NULL,
  `color` char(7) NOT NULL,
  `espublico` int(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

DROP TABLE IF EXISTS `calificaciones`;
CREATE TABLE IF NOT EXISTS `calificaciones` (
  `codigo` varchar(5) NOT NULL DEFAULT '',
  `nombre` varchar(64) DEFAULT NULL,
  `abreviatura` varchar(4) DEFAULT NULL,
  `orden` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

DROP TABLE IF EXISTS `cargos`;
CREATE TABLE IF NOT EXISTS `cargos` (
  `dni` varchar(9) NOT NULL DEFAULT '',
  `cargo` varchar(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dni`, `cargo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compromiso_convivencia`
--

DROP TABLE IF EXISTS `compromiso_convivencia`;
CREATE TABLE `compromiso_convivencia` (
  `nie` VARCHAR(12) NOT NULL,
  `fecha` DATETIME NOT NULL,
  PRIMARY KEY (`nie`)
) ENGINE = MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control`
--

DROP TABLE IF EXISTS `control`;
CREATE TABLE IF NOT EXISTS `control` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL,
  `pass` varchar(254) NOT NULL,
  `correo` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control_faltas`
--

CREATE TABLE IF NOT EXISTS `control_faltas` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profesor` int(11) UNSIGNED NOT NULL,
  `unidad` varchar(128) COLLATE utf8_spanish_ci NOT NULL,
  `dia` tinyint(1) UNSIGNED NOT NULL,
  `hora` tinyint(1) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `asignatura` varchar(12) COLLATE utf8_spanish_ci NOT NULL,
  `numero` tinyint(2) UNSIGNED NOT NULL,
  `hoy` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

--
-- Estructura de tabla para la tabla `control_matriculas`
--

DROP TABLE IF EXISTS `control_matriculas`;
CREATE TABLE IF NOT EXISTS `control_matriculas` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL,
  `pass` varchar(254) NOT NULL,
  `correo` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `convivencia`
--

DROP TABLE IF EXISTS `convivencia`;
CREATE TABLE IF NOT EXISTS `convivencia` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL,
  `dia` int(1) NOT NULL DEFAULT '0',
  `hora` char(1) NOT NULL DEFAULT '0',
  `trabajo` int(1) NOT NULL DEFAULT '0',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `observaciones` mediumtext NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

DROP TABLE IF EXISTS `cursos`;
CREATE TABLE IF NOT EXISTS `cursos` (
  `idcurso` int(11) UNSIGNED NOT NULL,
  `nomcurso` varchar(80) NOT NULL,
  PRIMARY KEY (`idcurso`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `c_profes`
--

DROP TABLE IF EXISTS `c_profes`;
CREATE TABLE IF NOT EXISTS `c_profes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pass` varchar(60) DEFAULT NULL,
  `PROFESOR` varchar(48) DEFAULT NULL,
  `dni` varchar(9) NOT NULL DEFAULT '',
  `idea` varchar(12) NOT NULL DEFAULT '',
  `correo` varchar(64) DEFAULT NULL,
  `correo_verificado` TINYINT(1) NOT NULL DEFAULT '0',
  `estado` tinyint(1) NOT NULL DEFAULT '0',
  `telefono` char(9) DEFAULT NULL,
  `telefono_verificado` TINYINT(1) NOT NULL DEFAULT '0',
  `totp_secret` CHAR(16) NULL,
  `rgpd_mostrar_nombre` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos`
--

DROP TABLE IF EXISTS `datos`;
CREATE TABLE IF NOT EXISTS `datos` (
  `id` int(4) NOT NULL DEFAULT '0',
  `nota` mediumtext NOT NULL,
  `ponderacion` char(3) DEFAULT NULL,
  `claveal` varchar(12) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_primaria`
--

DROP TABLE IF EXISTS `datos_primaria`;
CREATE TABLE IF NOT EXISTS `datos_primaria` (
  `apellidos` varchar(40) DEFAULT NULL,
  `nombre` varchar(30) DEFAULT NULL,
  `padre` varchar(78) DEFAULT NULL,
  `dnitutor` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

DROP TABLE IF EXISTS `departamentos`;
CREATE TABLE IF NOT EXISTS `departamentos` (
  `NOMBRE` varchar(48) DEFAULT NULL,
  `DNI` varchar(10) DEFAULT NULL,
  `DEPARTAMENTO` varchar(80) DEFAULT NULL,
  `CARGO` varchar(10) DEFAULT NULL,
  `idea` varchar(12) NOT NULL DEFAULT '',
  `fechatoma` DATE NOT NULL DEFAULT '0000-00-00',
  `fechacese` DATE NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`idea`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos_seneca`
--

DROP TABLE IF EXISTS `departamentos_seneca`;
CREATE TABLE IF NOT EXISTS `departamentos_seneca` (
  `iddepartamento` int(2) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomdepartamento` varchar(80) NOT NULL,
  PRIMARY KEY (`iddepartamento`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dependencias`
--

DROP TABLE IF EXISTS `dependencias`;
CREATE TABLE IF NOT EXISTS `dependencias` (
  `iddependencia` int(11) UNSIGNED NOT NULL,
  `nomdependencia` varchar(30) NOT NULL,
  `descdependencia` varchar(80) DEFAULT NULL,
  `reservadependencia` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`iddependencia`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `depto_pedidos`
--

DROP TABLE IF EXISTS `depto_pedidos`;
CREATE TABLE IF NOT EXISTS `depto_pedidos` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `departamento` varchar(80) NOT NULL,
  `responsable` varchar(80) NOT NULL,
  `id_acta` int(10) UNSIGNED NOT NULL,
  `justificacion` text,
  `incidencias` text,
  `condiciones` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `fechaRegistro` datetime NOT NULL,
  `entregado` tinyint(1) NOT NULL DEFAULT '0',
  `vistoSecretaria` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `depto_pedidos_detalles`
--

DROP TABLE IF EXISTS `depto_pedidos_detalles`;
CREATE TABLE IF NOT EXISTS `depto_pedidos_detalles` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `articulo` varchar(80) NOT NULL,
  `cantidad` tinyint(3) UNSIGNED NOT NULL,
  `importe` decimal(10,2) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`,`id_pedido`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones`
--

DROP TABLE IF EXISTS `evaluaciones`;
CREATE TABLE IF NOT EXISTS `evaluaciones` (
  `unidad` varchar(64) NOT NULL,
  `asignatura` varchar(64) NOT NULL,
  `evaluacion` char(3) NOT NULL,
  `profesor` mediumtext NOT NULL,
  `calificaciones` blob NOT NULL,
  PRIMARY KEY (`unidad`,`asignatura`,`evaluacion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones_actas`
--

DROP TABLE IF EXISTS `evaluaciones_actas`;
CREATE TABLE IF NOT EXISTS `evaluaciones_actas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unidad` varchar(64) NOT NULL,
  `evaluacion` char(3) NOT NULL,
  `fecha` date NOT NULL,
  `texto_acta` mediumtext NOT NULL,
  `asistentes` text DEFAULT NULL,
  `impresion` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evalua_pendientes`
--

DROP TABLE IF EXISTS `evalua_pendientes`;
CREATE TABLE IF NOT EXISTS `evalua_pendientes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `evaluacion` tinyint(1) NOT NULL,
  `claveal` varchar(12) NOT NULL,
  `codigo` int(6) NOT NULL,
  `materia` varchar(12) NOT NULL,
  `nota` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evalua_tutoria`
--

DROP TABLE IF EXISTS `evalua_tutoria`;
CREATE TABLE IF NOT EXISTS `evalua_tutoria` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unidad` varchar(32) NOT NULL,
  `evaluacion` varchar(32) NOT NULL,
  `alumno` varchar(10) NOT NULL,
  `campo` varchar(10) NOT NULL,
  `valor` mediumtext NOT NULL,
  PRIMARY KEY (`id`,`evaluacion`,`alumno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FALTAS`
--

DROP TABLE IF EXISTS `FALTAS`;
CREATE TABLE IF NOT EXISTS `FALTAS` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `CLAVEAL` varchar(12) NOT NULL DEFAULT '',
  `unidad` varchar(64) NOT NULL,
  `NC` tinyint(2) NOT NULL,
  `FECHA` date DEFAULT NULL,
  `DIA` tinyint(1) NOT NULL DEFAULT '0',
  `HORA` tinyint(1) DEFAULT NULL,
  `PROFESOR` int(7) DEFAULT NULL,
  `CODASI` varchar(10) DEFAULT NULL,
  `FALTA` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faltas_control`
--

DROP TABLE IF EXISTS `faltas_control`;
CREATE TABLE IF NOT EXISTS `faltas_control` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profesor` tinyint(4) NOT NULL,
  `alumno` int(11) UNSIGNED NOT NULL,
  `asignatura` int(11) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `num_profes` tinyint(4) NOT NULL,
  `hora` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faltas_profes`
--

DROP TABLE IF EXISTS `faltas_profes`;
CREATE TABLE IF NOT EXISTS `faltas_profes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profesor` char(2) DEFAULT NULL,
  `numero` bigint(21) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Fechoria`
--

DROP TABLE IF EXISTS `Fechoria`;
CREATE TABLE IF NOT EXISTS `Fechoria` (
  `CLAVEAL` varchar(12) COLLATE utf8_general_ci  NOT NULL DEFAULT '',
  `FECHA` date NOT NULL DEFAULT '0000-00-00',
  `ASUNTO` text COLLATE utf8_general_ci  NOT NULL,
  `NOTAS` text COLLATE utf8_general_ci  NOT NULL,
  `INFORMA` varchar(48) COLLATE utf8_general_ci  NOT NULL DEFAULT '',
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `grave` varchar(10) COLLATE utf8_general_ci  NOT NULL DEFAULT '',
  `medida` varchar(148) COLLATE utf8_general_ci  NOT NULL DEFAULT '',
  `expulsion` tinyint(2) NOT NULL DEFAULT '0',
  `inicio` date DEFAULT '0000-00-00',
  `fin` date DEFAULT '0000-00-00',
  `tutoria` text COLLATE utf8_general_ci ,
  `expulsionaula` tinyint(1) DEFAULT NULL,
  `enviado` char(1) COLLATE utf8_general_ci  NOT NULL DEFAULT '1',
  `recibido` char(1) COLLATE utf8_general_ci  NOT NULL DEFAULT '0',
  `aula_conv` tinyint(1) DEFAULT '0',
  `inicio_aula` date DEFAULT NULL,
  `fin_aula` date DEFAULT NULL,
  `horas` varchar(10) COLLATE utf8_general_ci  DEFAULT '123R456',
  `confirmado` tinyint(1) DEFAULT NULL,
  `tareas` char(2) COLLATE utf8_general_ci  DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `festivos`
--

DROP TABLE IF EXISTS `festivos`;
CREATE TABLE IF NOT EXISTS `festivos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `nombre` varchar(64) NOT NULL DEFAULT '',
  `docentes` char(2) NOT NULL DEFAULT '',
  `ambito` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos`
--

DROP TABLE IF EXISTS `fotos`;
CREATE TABLE IF NOT EXISTS `fotos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(18) NOT NULL DEFAULT '',
  `datos` mediumblob NOT NULL,
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tamaño` int(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FTUTORES`
--

DROP TABLE IF EXISTS `FTUTORES`;
CREATE TABLE IF NOT EXISTS `FTUTORES` (
  `unidad` varchar(64) NOT NULL,
  `TUTOR` varchar(48) NOT NULL DEFAULT '',
  `observaciones1` mediumtext NOT NULL,
  `observaciones2` mediumtext NOT NULL,
  `id` INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

DROP TABLE IF EXISTS `grupos`;
CREATE TABLE IF NOT EXISTS `grupos` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `profesor` varchar(48) NOT NULL DEFAULT '',
  `asignatura` int(6) NOT NULL DEFAULT '0',
  `curso` varchar(32) NOT NULL DEFAULT '',
  `alumnos` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guardias`
--

DROP TABLE IF EXISTS `guardias`;
CREATE TABLE IF NOT EXISTS `guardias` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profesor` varchar(64) NOT NULL DEFAULT '',
  `profe_aula` varchar(64) NOT NULL DEFAULT '',
  `dia` tinyint(1) NOT NULL DEFAULT '0',
  `hora` tinyint(1) NOT NULL DEFAULT '0',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fecha_guardia` date NOT NULL DEFAULT '0000-00-00',
  `turno` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hermanos`
--

DROP TABLE IF EXISTS `hermanos`;
CREATE TABLE IF NOT EXISTS `hermanos` (
  `telefono` varchar(255) DEFAULT NULL,
  `telefonourgencia` varchar(255) DEFAULT NULL,
  `hermanos` bigint(21) NOT NULL DEFAULT '0',
  PRIMARY KEY `telefono` (`telefono`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horw`
--

DROP TABLE IF EXISTS `horw`;
CREATE TABLE IF NOT EXISTS `horw` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dia` char(1) NOT NULL DEFAULT '',
  `hora` char(2) NOT NULL DEFAULT '',
  `a_asig` varchar(8) NOT NULL DEFAULT '',
  `asig` varchar(128) NOT NULL DEFAULT '',
  `c_asig` varchar(30) NOT NULL DEFAULT '',
  `prof` varchar(50) NOT NULL DEFAULT '',
  `no_prof` tinyint(4) DEFAULT NULL,
  `c_prof` varchar(30) NOT NULL DEFAULT '',
  `a_aula` varchar(32) NOT NULL DEFAULT '',
  `n_aula` varchar(64) NOT NULL DEFAULT '',
  `a_grupo` varchar(64) NOT NULL DEFAULT '',
  `nivel` varchar(10) NOT NULL DEFAULT '',
  `n_grupo` varchar(10) NOT NULL DEFAULT '',
  `clase` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencias_tic`
--

CREATE TABLE IF NOT EXISTS `incidencias_tic` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infotut_alumno`
--

DROP TABLE IF EXISTS `infotut_alumno`;
CREATE TABLE IF NOT EXISTS `infotut_alumno` (
  `ID` smallint(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `CLAVEAL` varchar(12) NOT NULL DEFAULT '',
  `APELLIDOS` varchar(30) NOT NULL DEFAULT '',
  `NOMBRE` varchar(24) NOT NULL DEFAULT '',
  `unidad` varchar(64) NOT NULL,
  `F_ENTREV` date NOT NULL DEFAULT '0000-00-00',
  `TUTOR` varchar(40) NOT NULL DEFAULT '',
  `FECHA_REGISTRO` date NOT NULL DEFAULT '0000-00-00',
  `valido` tinyint(1) NOT NULL DEFAULT '1',
  `motivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infotut_profesor`
--

DROP TABLE IF EXISTS `infotut_profesor`;
CREATE TABLE IF NOT EXISTS `infotut_profesor` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `profesor` varchar(48) NOT NULL DEFAULT '',
  `asignatura` varchar(64) NOT NULL DEFAULT '',
  `informe` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informe_extraordinaria`
--

CREATE TABLE IF NOT EXISTS `informe_extraordinaria` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informe_extraordinaria_alumnos`
--

CREATE TABLE IF NOT EXISTS `informe_extraordinaria_alumnos` (
  `id_informe` int(10) unsigned NOT NULL,
  `id_contenido` int(10) unsigned NOT NULL,
  `claveal` varchar(12) COLLATE latin1_spanish_ci NOT NULL,
  `debe_recuperar` int(1) NOT NULL DEFAULT '0',
  `actividades` text COLLATE latin1_spanish_ci,
  PRIMARY KEY (`id_informe`,`id_contenido`,`claveal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informe_extraordinaria_contenidos`
--

CREATE TABLE IF NOT EXISTS `informe_extraordinaria_contenidos` (
  `id_informe` int(10) unsigned NOT NULL,
  `id_contenido` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unidad` varchar(30) COLLATE latin1_spanish_ci NOT NULL,
  `titulo` varchar(90) COLLATE latin1_spanish_ci NOT NULL,
  `contenidos` text COLLATE latin1_spanish_ci NOT NULL,
  `actividades` text COLLATE latin1_spanish_ci NOT NULL,
  `fechareg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_contenido`,`id_informe`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intervenciones_profesores`
--

DROP TABLE IF EXISTS `intervenciones_profesores`;
CREATE TABLE IF NOT EXISTS `intervenciones_profesores` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idea` varchar(10) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `fecha` date NOT NULL,
  `causa` varchar(64) NOT NULL,
  `observaciones` mediumtext NOT NULL,
  `accion` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

DROP TABLE IF EXISTS `inventario`;
CREATE TABLE IF NOT EXISTS `inventario` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `clase` varchar(48) NOT NULL DEFAULT '',
  `lugar` varchar(48) NOT NULL DEFAULT '',
  `descripcion` mediumtext NOT NULL,
  `marca` varchar(32) NOT NULL DEFAULT '',
  `modelo` varchar(48) NOT NULL DEFAULT '',
  `serie` varchar(24) NOT NULL DEFAULT '',
  `unidades` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `fecha` varchar(10) NOT NULL DEFAULT '',
  `ahora` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DEPARTAMENTO` varchar(80) NOT NULL,
  `profesor` varchar(48) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_clases`
--

DROP TABLE IF EXISTS `inventario_clases`;
CREATE TABLE IF NOT EXISTS `inventario_clases` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `familia` varchar(64) NOT NULL DEFAULT '',
  `clase` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_lugares`
--

DROP TABLE IF EXISTS `inventario_lugares`;
CREATE TABLE IF NOT EXISTS `inventario_lugares` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lugar` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_tic`
--

CREATE TABLE IF NOT EXISTS `inventario_tic` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros_texto`
--

CREATE TABLE IF NOT EXISTS `libros_texto` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `materia` varchar(64) NOT NULL DEFAULT '',
  `isbn` char(13) DEFAULT NULL,
  `ean` char(13) DEFAULT NULL,
  `editorial` varchar(60) NOT NULL DEFAULT '',
  `titulo` varchar(100) NOT NULL DEFAULT '',
  `importe` decimal(5,2) DEFAULT NULL,
  `nivel` varchar(80) NOT NULL DEFAULT '',
  `programaGratuidad` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros_texto_alumnos`
--

CREATE TABLE IF NOT EXISTS `libros_texto_alumnos` (
  `claveal` varchar(12) NOT NULL,
  `materia` varchar(10) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT '',
  `devuelto` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fecha` datetime DEFAULT '0000-00-00 00:00:00',
  `curso` varchar(7) NOT NULL DEFAULT '',
  PRIMARY KEY (`claveal`,`materia`),
  KEY `claveal` (`claveal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `listafechorias`
--

DROP TABLE IF EXISTS `listafechorias`;
CREATE TABLE IF NOT EXISTS `listafechorias` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `fechoria` varchar(255) DEFAULT NULL,
  `medidas` varchar(64) DEFAULT NULL,
  `medidas2` longtext,
  `tipo` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Datos de tabla para la tabla `listafechorias`
--

INSERT INTO `listafechorias` VALUES (2,'Faltas reiteradas de puntualidad en la entrada a clase','Amonestación oral','El alumno siempre entrará en el aula. Caso de ser reincidente, se contactará con la familia y se le comunicará al tutor','leve'),(4,'La falta de asistencia a clase sin abandonar el centro','Llamada telefónica. Comunicación escrita','Se contactará con la familia para comunicar el hecho (teléfono o SMS) Grabación de la falta en el módulo informático.  Caso de reincidencia, seguir el protocolo: a) comunicación escrita, b)acuse de recibo, c) traslado del caso a Asuntos Sociales','leve'),(8,'Vestir con ropa indecorosa, no apropiada o permitida en el Centro','Amonestación oral. Llamada telefónica.','Contactar con la familia para que aporte ropa adecuada o traslade al alumno/a a su domicilio para el oportuno cambio de indumentaria. Requisar la prenda si procede (gorro o gorra, etc.)','leve'),(85,'El uso o tenencia del teléfono móvil en el aula por primera vez','Amonestación oral','Que el tutor avise a la familia para que recuerde las normas del centro y tenga conocimiento que se hay reincidencia la medida será más grave.\r\nLa confiscación del teléfono que permanecerá en el centro bajo llave durante 24 horas y será devuelto con el conocimiento y consentimiento de la familia.','leve'),(13,'Uso o tenencia de objetos o dispositivos tecnológicos que no proceden en el aula/centro (cartas, láser, cigarros electrónicos, cámara, aparatos de sonido, etc)','Amonestación oral','Requisar el/los objeto(s) y/o aparato y entregar en Jefatura para que sea retirado por la familia.','leve'),(14,'Arrojar al suelo de forma intencionada/despreocupada papeles o basura en general','Amonestación oral','Hacer que se retiren los objetos.  Ningún profesor permitirá que el aula esté sucia. Si es así, obligar al alumnado a la limpieza oportuna.','leve'),(16,'Hablar en clase sin autorización del profesor y molestando el desarrollo de la clase','Amonestación oral','Cambiar al alumno de sitio, o aislarlo en el aula o, si es reincidente, sancionarlo con pérdida de recreo o permaneciendo en el aula algunos minutos al final de la jornada o viniendo el lunes por la tarde.','leve'),(18,'Lanzar objetos, sin peligrosidad o agresividad, a un compañero','Amonestación oral','Hacer que el compañero le devuelva el objeto, que el alumno solicite permiso al profesor para que éste le permita, levantándose, entregar el objeto a su compañero.','leve'),(20,'No traer el material exigido para el desarrollo de una clase','Amonestación oral','Si reincide, contactar telefónicamente con la familia para que le aporte el material. Caso de existir alguna causa social que impida que el alumno tenga el material, solicitar la colaboración del centro o de las instituciones sociales oportunas.','leve'),(22,'Negarse a realizar en el aula las actividades encomendadas por el profesor','Amonestación oral','Contactar con la familia.','leve'),(23,'Beber, comer o mascar chicle de forma reiterada y tras ser advertido en el transcurso de una clase','Amonestación oral','Obligar a que guarde la bebida o la arroje a la basura.','leve'),(25,'Permanecer en el pasillo entre clase y clase fuera del periodo permitido y tras haber sido advertido','Amonestación oral','Si el alumno reincide en este tipo de comportamientos contactar con la familia.','leve'),(26,'Falta de cuidado, respeto y protección de los recursos personales o del Centro','Amonestación oral','Pedir disculpas públicamente y resarcir del posible daño a la persona o institución afectada.','leve'),(31,'Faltas reiteradas de puntualidad o asistencia que no estén justificadas','Amonestación escrita','Seguir protocolo: a) Llamada telefónica a la familia b) Escrito a la familia c) Escrito certificado con acuse de recibo a la familia d) Traslado del caso a Asuntos Sociales.','grave'),(32,'Conductas graves que impidan o dificulten a otros compañeros el ejercicio del estudio','Amonestación escrita','Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); expulsarlo de clase (medida extraordinaria). El tutor tratará el caso con Jefatura  para adoptar medidas.','grave'),(34,'Actos graves de incorrección con los miembros del Centro','Amonestación escrita','Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); expulsarlo de clase  (medida extraordinaria que debe ir acompañada con escrito del profesor a los padres). La petición de excusas se considerará un atenuante a valorar. El tutor tratará el caso con la familia y propondrá a Jefatura medidas a adoptar.','grave'),(36,'Perturbación grave del desarrollo de las actividades y cualquier incumplimiento grave de las normas de conducta','Amonestación escrita','Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); expulsarlo de clase (medida extraordinaria que debe ir acompañada con escrito del profesor a los padres). El tutor tratará el caso con la familia y propondrá a Jefatura medidas a adoptar.','grave'),(38,'Causar daños intencionados en las instalaciones o el material del centro o en las pertenencias de algún miembro de la Comunidad Educativa','Amonestación escrita','El tutor tratará el caso con la familia y el alumno y familia realizará trabajos complementarios para la comunidad y  restaurará los daños o pagará los gastos de reparación.','grave'),(40,'Incitación o estímulo a la comisión de una falta contraria a las Normas de Convivencia','Amonestación escrita','El tutor tratará el caso con la familia y propondrá a Jefatura las medidas correctoras a adoptar.','grave'),(41,'Reiteración de cinco o más faltas leves','Amonestación escrita','Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); realizar trabajos para la comunidad; o estancia en el Aula de Convivencia entre 1 y 3 días.','grave'),(42,'Incumplimiento de la sanción impuesta del Centro por una falta leve','Amonestación escrita','Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); realizar trabajos para la comunidad; o estancia en el Aula de Convivencia entre 1 y 3 días.','grave'),(45,'Grabación y/o difusión de imágenes/vídeos de miembros del Centro sin su autorización','Llamada telefónica. Comunicación escrita','Entrega de la grabación y posibles copias en Jefatura de Estudios. Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); realizar trabajos para la comunidad; o expulsión del centro varios días.\r\nSi el hecho es grave, iniciar los trámites legales oportunos (Asuntos Sociales, Policía Nacional, etc.) Imponer expulsión del centro entre 1 y 29 dependiendo de la gravedad.','muy grave'),(47,'Abandonar el Centro sin autorización antes de concluir el horario escolar','Amonestación escrita','Comunicación con la familia.  Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); realizar trabajos para la comunidad; o estancia en el Aula de Convivencia entre 1 y 3 días.','grave'),(49,'Fumar en el Centro (tanto en el interior del edificio como en los patios)','Amonestación escrita','Comunicación con la familia.  Entrega de trabajo relacionado con tabaco y salud. Si es reincidente, imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); realizar trabajos para la comunidad; o estancia en el Aula de Convivencia entre 1 y 3 días.','grave'),(51,'Mentir o colaborar para encubrir faltas propias o ajenas','Amonestación escrita','Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); realizar trabajos para la comunidad; o estancia en el Aula de Convivencia entre 1 y 3 días.','grave'),(54,'Actos graves de indisciplina, insultos o falta de respeto con los profesores y/o personal del centro','Amonestación escrita','Imponer correcciones como: estancia en el Aula de Convivencia varios días; expulsión del centro entre 1 y 3 días o entre 4 y 29 si es reincidente.','grave'),(55,'Injurias y ofensas contra un miembro de la comunidad educativa','Llamada telefónica. Comunicación escrita','Petición publica de disculpas. Imponer correcciones como: estancia en el Aula de Convivencia varios días; expulsión del centro entre 1 y 3 días o entre 4 y 29 si es reincidente','muy grave'),(56,'Vejaciones o humillaciones contra un miembro de la comunidad educativa','Llamada telefónica. Comunicación escrita','Petición publica de disculpas y comunicación con la familia. Si el hecho es grave, iniciar los trámites legales oportunos (Asuntos Sociales, Policía Nacional, etc.) Imponer correcciones como: estancia en el Aula de Convivencia varios días; o expulsión del centro entre 1 y 29  dependiendo de la gravedad','muy grave'),(58,'Amenazas o coacciones contra cualquier miembro de la comunidad educativa','Llamada telefónica. Comunicación escrita','Petición publica de disculpas y comunicación con la familia. Si el hecho es grave, iniciar los trámites legales oportunos (Asuntos Sociales, Policía Nacional, etc.) Imponer correcciones como: estancia en el Aula de Convivencia varios días; o expulsión del centro entre 1 y 29 dependiendo de la gravedad.','muy grave'),(61,'Uso de la violencia, ofensas y actos que atenten contra la intimidad o dignidad de los miembros del Centro','Llamada telefónica. Comunicación escrita','Petición publica de disculpas y comunicación con la familia. Si el hecho es grave, iniciar los trámites legales oportunos (Asuntos Sociales, Policía Nacional, etc.) Imponer expulsión del centro entre 1 y 29 dependiendo de la gravedad.','muy grave'),(66,'Deterioros  graves causados en las instalaciones, materiales y documentos del centro, o en las pertenencias de sus miembros','Llamada telefónica. Comunicación escrita','Jefatura de Estudios tratará el caso con la familia y el alumno y familia realizará trabajos complementarios para la comunidad y  restaurará los daños o pagará los gastos de reparación o restitución.','muy grave'),(67,'Suplantación de personalidad en actos de la vida docente y la falsificación o sustracción de documentos académicos','Llamada telefónica. Comunicación escrita','Si el hecho es muy grave, iniciar los trámites legales oportunos (Asuntos Sociales, Policía Nacional, etc.) Imponer expulsión del centro entre 1 y 29 dependiendo de la gravedad.','muy grave'),(88,'Otras conductas consideradas como graves no descritas y desarrolladas en el reglamento de organización y funcionamiento del centro','Amonestación escrita','Comunicación con la familia.  Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); realizar trabajos para la comunidad; o estancia en el Aula de Convivencia entre 1 y 3 días.','grave'),(89,'Reiteración en el mismo curso de conductas contrarias y graves a las normas de convivencia','Llamada telefónica. Comunicación escrita','Jefatura tratará el caso con la familia. Imponer correcciones como: estancia en el Aula de Reflexión Intensiva varios días; estancia de un familiar en el aula, con el alumno, durante varios días; o expulsión del centro entre 1 y 29 días en función de la gravedad','muy grave'),(76,'Cometer actos delictivos penados por nuestro Sistema Jurídico','Llamada telefónica. Comunicación escrita','Jefatura tratará el caso con la familia y, si es grave, denunciar en la Policía. Imponer correcciones como: estancia en el Aula de Convivencia varios días; estancia de un familiar en el aula, con el alumno, durante varios días; o expulsión del centro entre 1 y 29 días en función de la gravedad','muy grave'),(79,'Actuaciones perjudiciales para la salud o la integridad o la incitación a ellas','Llamada telefónica. Comunicación escrita','Jefatura tratará el caso con la familia y, si es grave, denunciar en la Policía. Traslado del caso al Dep. de Orientación o Asuntos Sociales. Trabajo sobre hábitos saludables. Imponer correcciones como: estancia en el Aula de Convivencia varios días; estancia de un familiar en el aula, con el alumno, durante varios días; o expulsión del centro entre 1 y 29 días en función de la gravedad','muy grave'),(81,'Cualquier acto muy grave dirigido directamente a impedir el normal desarrollo de las actividades','Llamada telefónica. Comunicación escrita','Jefatura tratará el caso con la familia. Imponer correcciones como: estancia en el Aula de Reflexión Intensiva varios días; estancia de un familiar en el aula, con el alumno, durante varios días; o expulsión del centro entre 1 y 29 días en función de la gravedad','muy grave'),(53,'Retraso injustificado en la devolución de material a la biblioteca del centro','Amonestación escrita','Jefatura de Estudios junto con el tutor/a,  tratarán el caso con la familia y el alumno y familia realizará trabajos complementarios para la comunidad y  restaurará los daños o pagará los gastos de reparación o restitución.','grave'),(1,'Permanecer en el aula o dentro del centro sin autorización durante el recreo tras haber sido advertido de que no se está permitido','Amonestación escrita','Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); expulsarlo de clase (medida extraordinaria que debe ir acompañada con escrito del profesor a los padres). El tutor tratará el caso con la familia y propondrá a Jefatura medidas a adoptar.','grave'),(83,'El uso reiterado del teléfono móvil en el centro en periodo lectivo (por 2a vez o posterior)','Amonestación escrita','Que el tutor avise a la familia para que recuerde las normas del centro y tenga conocimiento que se hay reincidencia la medida será más grave.\r\nLa confiscación del teléfono que permanecerá en el centro bajo llave durante el tiempo estimado - mayor de 24 hroas - y será devuelto con el conocimiento y consentimiento de la familia.','grave'),(84,'Cualquier primera situación grave en la que se muestre arrepentimiento','Amonestación oral','El alumno habrá pedido disculpas y dará muestras de verdadero arrepentimiento y ante cualquier situación similar a posteriori ya no se considerará ese nuevo arrepentimiento y se informaría a la familia.','leve'),(86,'Negarse a entregar el teléfono móvil al profesor cuando se le solicita/requisa desautorizando al mismo en el aula','Llamada telefónica. Comunicación escrita','Petición publica de disculpas. Jefatura tratará el caso con la familia. Imponer correcciones como: estancia en el Aula de Reflexión Intensiva varios días; estancia de un familiar en el aula, con el alumno, durante varios días; o expulsión del centro entre 1 y 29 días en función de la gravedad. La confiscación del teléfono que permanecerá en el centro bajo llave durante el tiempo estimado - mayor de 24 hroas - y será devuelto con el conocimiento y consentimiento de la familia.','muy grave'),(87,'Cooperar o encubrir la comisión de una falta muy grave','Llamada telefónica. Comunicación escrita','Jefatura tratará el caso con la familia. Imponer correcciones como: estancia en el Aula de Reflexión Intensiva varios días; estancia de un familiar en el aula, con el alumno, durante varios días; o expulsión del centro entre 1 y 29 días en función de la gravedad','muy grave');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `listafechorias_seneca`
--

CREATE TABLE `listafechorias_seneca` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `fechoria` varchar(255) DEFAULT NULL,
  `medidas` varchar(64) DEFAULT NULL,
  `medidas2` longtext,
  `tipo` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

--
-- Volcado de datos para la tabla `listafechorias_seneca`
--

INSERT INTO `listafechorias_seneca` (`ID`, `fechoria`, `medidas`, `medidas2`, `tipo`) VALUES
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
(15, 'Incumplimiento de las correcciones impuestas (Grave)', 'Amonestación escrita', 'Imponer correcciones como: pérdida de recreo; quedarse algunos minutos al final del periodo lectivo; obligarlo a que venga por la tarde (lunes); realizar trabajos para la comunidad; o estancia en el Aula de Convivencia entre 1 y 3 días.', 'muy grave');


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

DROP TABLE IF EXISTS `materias`;
CREATE TABLE IF NOT EXISTS `materias` (
  `CODIGO` varchar(10) NOT NULL,
  `NOMBRE` varchar(64) DEFAULT NULL,
  `ABREV` varchar(10) DEFAULT NULL,
  `CURSO` varchar(128) DEFAULT NULL,
  `GRUPO` varchar(255) NOT NULL,
  `id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_seneca`
--

DROP TABLE IF EXISTS `materias_seneca`;
CREATE TABLE IF NOT EXISTS `materias_seneca` (
  `idmateria` int(11) UNSIGNED NOT NULL,
  `nommateria` varchar(80) NOT NULL,
  `abrevmateria` varchar(8) DEFAULT NULL,
  `idcurso` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`idmateria`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas`
--

DROP TABLE IF EXISTS `matriculas`;
CREATE TABLE IF NOT EXISTS `matriculas` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL DEFAULT '',
  `apellidos` varchar(36) NOT NULL DEFAULT '',
  `nombre` varchar(24) NOT NULL DEFAULT '',
  `nacido` varchar(24) NOT NULL DEFAULT '',
  `provincia` varchar(16) NOT NULL DEFAULT '',
  `nacimiento` date NOT NULL DEFAULT '0000-00-00',
  `domicilio` varchar(64) NOT NULL DEFAULT '',
  `localidad` varchar(24) NOT NULL DEFAULT '',
  `dni` varchar(13) NOT NULL DEFAULT '',
  `padre` varchar(48) NOT NULL DEFAULT '',
  `dnitutor` varchar(13) NOT NULL DEFAULT '',
  `madre` varchar(48) NOT NULL DEFAULT '',
  `dnitutor2` varchar(13) NOT NULL DEFAULT '',
  `telefono1` int(10) NOT NULL DEFAULT '0',
  `telefono2` int(10) NOT NULL DEFAULT '0',
  `colegio` varchar(64) NOT NULL DEFAULT '',
  `otrocolegio` varchar(64) DEFAULT NULL,
  `letra_grupo` char(1) DEFAULT NULL,
  `correo` varchar(36) DEFAULT NULL,
  `idioma` varchar(6) NOT NULL DEFAULT '',
  `religion` varchar(22) NOT NULL DEFAULT '',
  `optativa1` tinyint(1) NOT NULL DEFAULT '0',
  `optativa2` tinyint(1) NOT NULL DEFAULT '0',
  `optativa3` tinyint(1) NOT NULL DEFAULT '0',
  `optativa4` tinyint(1) NOT NULL DEFAULT '0',
  `act1` tinyint(1) DEFAULT NULL,
  `act2` tinyint(1) DEFAULT NULL,
  `act3` tinyint(1) DEFAULT NULL,
  `act4` tinyint(1) DEFAULT NULL,
  `optativa21` tinyint(1) DEFAULT NULL,
  `optativa22` tinyint(1) DEFAULT NULL,
  `optativa23` tinyint(1) DEFAULT NULL,
  `optativa24` tinyint(1) DEFAULT NULL,
  `act21` tinyint(1) DEFAULT NULL,
  `act22` tinyint(1) DEFAULT NULL,
  `act23` tinyint(1) DEFAULT NULL,
  `act24` tinyint(1) DEFAULT NULL,
  `observaciones` mediumtext,
  `exencion` tinyint(1) DEFAULT NULL,
  `bilinguismo` char(2) DEFAULT NULL,
  `curso` varchar(5) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `promociona` tinyint(1) DEFAULT NULL,
  `transporte` tinyint(1) DEFAULT NULL,
  `ruta_este` varchar(42) DEFAULT NULL,
  `ruta_oeste` varchar(42) DEFAULT NULL,
  `sexo` varchar(6) NOT NULL DEFAULT '',
  `hermanos` tinyint(2) DEFAULT NULL,
  `nacionalidad` varchar(32) NOT NULL DEFAULT '',
  `itinerario` tinyint(1) DEFAULT NULL,
  `optativas4` varchar(32) DEFAULT NULL,
  `optativa5` tinyint(1) DEFAULT NULL,
  `optativa6` tinyint(1) DEFAULT NULL,
  `optativa7` tinyint(1) DEFAULT NULL,
  `diversificacion` tinyint(1) DEFAULT NULL,
  `optativa25` tinyint(1) DEFAULT NULL,
  `optativa26` tinyint(1) DEFAULT NULL,
  `optativa27` tinyint(1) DEFAULT NULL,
  `confirmado` tinyint(1) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT NULL,
  `grupo_actual` char(2) DEFAULT NULL,
  `revisado` tinyint(1) DEFAULT NULL,
  `enfermedad` varchar(254) NOT NULL,
  `otraenfermedad` varchar(254) NOT NULL,
  `foto` tinyint(1) NOT NULL,
  `divorcio` varchar(64) DEFAULT NULL,
  `matematicas3` char(1) NOT NULL,
  `ciencias4` char(1) NOT NULL,
  `nsegsocial` varchar(15) DEFAULT NULL,
  `correo_alumno` varchar(128) DEFAULT NULL,
  `analgesicos` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas`
--

DROP TABLE IF EXISTS `matriculas_backup`;
CREATE TABLE IF NOT EXISTS `matriculas_backup` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL DEFAULT '',
  `apellidos` varchar(36) NOT NULL DEFAULT '',
  `nombre` varchar(24) NOT NULL DEFAULT '',
  `nacido` varchar(24) NOT NULL DEFAULT '',
  `provincia` varchar(16) NOT NULL DEFAULT '',
  `nacimiento` date NOT NULL DEFAULT '0000-00-00',
  `domicilio` varchar(64) NOT NULL DEFAULT '',
  `localidad` varchar(24) NOT NULL DEFAULT '',
  `dni` varchar(13) NOT NULL DEFAULT '',
  `padre` varchar(48) NOT NULL DEFAULT '',
  `dnitutor` varchar(13) NOT NULL DEFAULT '',
  `madre` varchar(48) NOT NULL DEFAULT '',
  `dnitutor2` varchar(13) NOT NULL DEFAULT '',
  `telefono1` int(10) NOT NULL DEFAULT '0',
  `telefono2` int(10) NOT NULL DEFAULT '0',
  `colegio` varchar(64) NOT NULL DEFAULT '',
  `otrocolegio` varchar(64) DEFAULT NULL,
  `letra_grupo` char(1) DEFAULT NULL,
  `correo` varchar(36) DEFAULT NULL,
  `idioma` varchar(6) NOT NULL DEFAULT '',
  `religion` varchar(22) NOT NULL DEFAULT '',
  `optativa1` tinyint(1) NOT NULL DEFAULT '0',
  `optativa2` tinyint(1) NOT NULL DEFAULT '0',
  `optativa3` tinyint(1) NOT NULL DEFAULT '0',
  `optativa4` tinyint(1) NOT NULL DEFAULT '0',
  `act1` tinyint(1) DEFAULT NULL,
  `act2` tinyint(1) DEFAULT NULL,
  `act3` tinyint(1) DEFAULT NULL,
  `act4` tinyint(1) DEFAULT NULL,
  `optativa21` tinyint(1) DEFAULT NULL,
  `optativa22` tinyint(1) DEFAULT NULL,
  `optativa23` tinyint(1) DEFAULT NULL,
  `optativa24` tinyint(1) DEFAULT NULL,
  `act21` tinyint(1) DEFAULT NULL,
  `act22` tinyint(1) DEFAULT NULL,
  `act23` tinyint(1) DEFAULT NULL,
  `act24` tinyint(1) DEFAULT NULL,
  `observaciones` mediumtext,
  `exencion` tinyint(1) DEFAULT NULL,
  `bilinguismo` char(2) DEFAULT NULL,
  `curso` varchar(5) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `promociona` tinyint(1) DEFAULT NULL,
  `transporte` tinyint(1) DEFAULT NULL,
  `ruta_este` varchar(42) DEFAULT NULL,
  `ruta_oeste` varchar(42) DEFAULT NULL,
  `sexo` varchar(6) NOT NULL DEFAULT '',
  `hermanos` tinyint(2) DEFAULT NULL,
  `nacionalidad` varchar(32) NOT NULL DEFAULT '',
  `itinerario` tinyint(1) DEFAULT NULL,
  `optativas4` varchar(32) DEFAULT NULL,
  `optativa5` tinyint(1) DEFAULT NULL,
  `optativa6` tinyint(1) DEFAULT NULL,
  `optativa7` tinyint(1) DEFAULT NULL,
  `diversificacion` tinyint(1) DEFAULT NULL,
  `optativa25` tinyint(1) DEFAULT NULL,
  `optativa26` tinyint(1) DEFAULT NULL,
  `optativa27` tinyint(1) DEFAULT NULL,
  `confirmado` tinyint(1) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT NULL,
  `grupo_actual` char(2) DEFAULT NULL,
  `revisado` tinyint(1) DEFAULT NULL,
  `enfermedad` varchar(254) NOT NULL,
  `otraenfermedad` varchar(254) NOT NULL,
  `foto` tinyint(1) NOT NULL,
  `divorcio` varchar(64) DEFAULT NULL,
  `matematicas3` char(1) NOT NULL,
  `ciencias4` char(1) NOT NULL,
  `nsegsocial` varchar(15) DEFAULT NULL,
  `parcial` tinyint(1) NOT NULL,
  `correo_alumno` varchar(128) DEFAULT NULL,
  `analgesicos` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas_bach`
--

DROP TABLE IF EXISTS `matriculas_bach`;
CREATE TABLE IF NOT EXISTS `matriculas_bach` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL DEFAULT '',
  `apellidos` varchar(36) NOT NULL DEFAULT '',
  `nombre` varchar(24) NOT NULL DEFAULT '',
  `nacido` varchar(24) NOT NULL DEFAULT '',
  `provincia` varchar(16) NOT NULL DEFAULT '',
  `nacimiento` date NOT NULL DEFAULT '0000-00-00',
  `domicilio` varchar(64) NOT NULL DEFAULT '',
  `localidad` varchar(24) NOT NULL DEFAULT '',
  `dni` varchar(13) NOT NULL DEFAULT '',
  `padre` varchar(48) NOT NULL DEFAULT '',
  `dnitutor` varchar(13) NOT NULL DEFAULT '',
  `madre` varchar(48) NOT NULL DEFAULT '',
  `dnitutor2` varchar(13) NOT NULL DEFAULT '',
  `telefono1` int(10) NOT NULL DEFAULT '0',
  `telefono2` int(10) NOT NULL DEFAULT '0',
  `colegio` varchar(64) NOT NULL DEFAULT '',
  `otrocolegio` varchar(64) DEFAULT NULL,
  `letra_grupo` char(1) DEFAULT NULL,
  `correo` varchar(36) DEFAULT NULL,
  `idioma1` varchar(7) NOT NULL DEFAULT '',
  `idioma2` varchar(7) NOT NULL DEFAULT '',
  `religion` varchar(22) NOT NULL DEFAULT '',
  `observaciones` mediumtext,
  `curso` varchar(5) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `promociona` tinyint(1) DEFAULT NULL,
  `transporte` tinyint(1) DEFAULT NULL,
  `ruta_este` varchar(42) DEFAULT NULL,
  `ruta_oeste` varchar(42) DEFAULT NULL,
  `sexo` varchar(6) NOT NULL DEFAULT '',
  `hermanos` tinyint(2) DEFAULT NULL,
  `nacionalidad` varchar(32) NOT NULL DEFAULT '',
  `confirmado` tinyint(1) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT NULL,
  `grupo_actual` char(2) DEFAULT NULL,
  `revisado` tinyint(1) DEFAULT NULL,
  `itinerario1` tinyint(1) DEFAULT NULL,
  `itinerario2` tinyint(1) DEFAULT NULL,
  `optativa1` varchar(64) DEFAULT NULL,
  `optativa2` varchar(64) DEFAULT NULL,
  `optativa2b1` tinyint(1) DEFAULT NULL,
  `optativa2b2` tinyint(1) DEFAULT NULL,
  `optativa2b3` tinyint(1) DEFAULT NULL,
  `optativa2b4` tinyint(1) DEFAULT NULL,
  `optativa2b5` tinyint(1) DEFAULT NULL,
  `optativa2b6` tinyint(1) DEFAULT NULL,
  `optativa2b7` tinyint(1) DEFAULT NULL,
  `optativa2b8` tinyint(1) DEFAULT NULL,
  `repite` tinyint(1) NOT NULL DEFAULT '0',
  `enfermedad` varchar(254) NOT NULL,
  `otraenfermedad` varchar(254) NOT NULL,
  `foto` tinyint(1) NOT NULL,
  `divorcio` varchar(64) DEFAULT NULL,
  `bilinguismo` char(2) DEFAULT NULL,
  `religion1b` varchar(64) NOT NULL,
  `opt_aut21` int(1) NOT NULL,
  `opt_aut22` int(1) NOT NULL,
  `opt_aut23` int(1) NOT NULL,
  `opt_aut24` int(1) NOT NULL,
  `opt_aut25` int(1) NOT NULL,
  `opt_aut26` int(1) NOT NULL,
  `opt_aut27` int(11) UNSIGNED NOT NULL,
  `nsegsocial` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas_bach`
--

DROP TABLE IF EXISTS `matriculas_bach_backup`;
CREATE TABLE IF NOT EXISTS `matriculas_bach_backup` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL DEFAULT '',
  `apellidos` varchar(36) NOT NULL DEFAULT '',
  `nombre` varchar(24) NOT NULL DEFAULT '',
  `nacido` varchar(24) NOT NULL DEFAULT '',
  `provincia` varchar(16) NOT NULL DEFAULT '',
  `nacimiento` date NOT NULL DEFAULT '0000-00-00',
  `domicilio` varchar(64) NOT NULL DEFAULT '',
  `localidad` varchar(24) NOT NULL DEFAULT '',
  `dni` varchar(13) NOT NULL DEFAULT '',
  `padre` varchar(48) NOT NULL DEFAULT '',
  `dnitutor` varchar(13) NOT NULL DEFAULT '',
  `madre` varchar(48) NOT NULL DEFAULT '',
  `dnitutor2` varchar(13) NOT NULL DEFAULT '',
  `telefono1` int(10) NOT NULL DEFAULT '0',
  `telefono2` int(10) NOT NULL DEFAULT '0',
  `colegio` varchar(64) NOT NULL DEFAULT '',
  `otrocolegio` varchar(64) DEFAULT NULL,
  `letra_grupo` char(1) DEFAULT NULL,
  `correo` varchar(36) DEFAULT NULL,
  `idioma1` varchar(7) NOT NULL DEFAULT '',
  `idioma2` varchar(7) NOT NULL DEFAULT '',
  `religion` varchar(22) NOT NULL DEFAULT '',
  `observaciones` mediumtext,
  `curso` varchar(5) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `promociona` tinyint(1) DEFAULT NULL,
  `transporte` tinyint(1) DEFAULT NULL,
  `ruta_este` varchar(42) DEFAULT NULL,
  `ruta_oeste` varchar(42) DEFAULT NULL,
  `sexo` varchar(6) NOT NULL DEFAULT '',
  `hermanos` tinyint(2) DEFAULT NULL,
  `nacionalidad` varchar(32) NOT NULL DEFAULT '',
  `confirmado` tinyint(1) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT NULL,
  `grupo_actual` char(2) DEFAULT NULL,
  `revisado` tinyint(1) DEFAULT NULL,
  `itinerario1` tinyint(1) DEFAULT NULL,
  `itinerario2` tinyint(1) DEFAULT NULL,
  `optativa1` varchar(64) DEFAULT NULL,
  `optativa2` varchar(64) DEFAULT NULL,
  `optativa2b1` tinyint(1) DEFAULT NULL,
  `optativa2b2` tinyint(1) DEFAULT NULL,
  `optativa2b3` tinyint(1) DEFAULT NULL,
  `optativa2b4` tinyint(1) DEFAULT NULL,
  `optativa2b5` tinyint(1) DEFAULT NULL,
  `optativa2b6` tinyint(1) DEFAULT NULL,
  `optativa2b7` tinyint(1) DEFAULT NULL,
  `optativa2b8` tinyint(1) DEFAULT NULL,
  `repite` tinyint(1) NOT NULL DEFAULT '0',
  `enfermedad` varchar(254) NOT NULL,
  `otraenfermedad` varchar(254) NOT NULL,
  `foto` tinyint(1) NOT NULL,
  `divorcio` varchar(64) DEFAULT NULL,
  `bilinguismo` char(2) DEFAULT NULL,
  `religion1b` varchar(64) NOT NULL,
  `opt_aut21` int(1) NOT NULL,
  `opt_aut22` int(1) NOT NULL,
  `opt_aut23` int(1) NOT NULL,
  `opt_aut24` int(1) NOT NULL,
  `opt_aut25` int(1) NOT NULL,
  `opt_aut26` int(1) NOT NULL,
  `opt_aut27` int(11) UNSIGNED NOT NULL,
  `nsegsocial` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mem_dep`
--

DROP TABLE IF EXISTS `mem_dep`;
CREATE TABLE IF NOT EXISTS `mem_dep` (
  `DEPARTAMENTO` varchar(80) NOT NULL DEFAULT '',
  `jefe` varchar(150) NOT NULL,
  `p1` longtext NOT NULL,
  `p2` longtext NOT NULL,
  `p3` longtext NOT NULL,
  `p4` longtext NOT NULL,
  `p5` longtext NOT NULL,
  `p6` longtext NOT NULL,
  `p7` longtext NOT NULL,
  `p8` longtext NOT NULL,
  `p9` longtext NOT NULL,
  `p10` longtext NOT NULL,
  `p11` longtext NOT NULL,
  `p12` longtext NOT NULL,
  `p13` longtext NOT NULL,
  `p14` longtext NOT NULL,
  `p15` longtext NOT NULL,
  `p16` longtext NOT NULL,
  `p17` longtext NOT NULL,
  `p18` longtext NOT NULL,
  `p19` longtext NOT NULL,
  `p20` longtext NOT NULL,
  PRIMARY KEY (`DEPARTAMENTO`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

DROP TABLE IF EXISTS `mensajes`;
CREATE TABLE IF NOT EXISTS `mensajes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ahora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dni` varchar(10) NOT NULL DEFAULT '',
  `claveal` varchar(12) NOT NULL DEFAULT '0',
  `asunto` mediumtext NOT NULL,
  `texto` mediumtext NOT NULL,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `recibidotutor` tinyint(1) NOT NULL DEFAULT '0',
  `recibidopadre` tinyint(1) NOT NULL DEFAULT '0',
  `correo` varchar(72) DEFAULT NULL,
  `unidad` varchar(64) NOT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mens_profes`
--

DROP TABLE IF EXISTS `mens_profes`;
CREATE TABLE IF NOT EXISTS `mens_profes` (
  `id_profe` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_texto` int(11) UNSIGNED NOT NULL,
  `profesor` varchar(64) NOT NULL,
  `recibidoprofe` tinyint(1) NOT NULL DEFAULT '0',
  `recibidojefe` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_profe`,`id_texto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mens_texto`
--

DROP TABLE IF EXISTS `mens_texto`;
CREATE TABLE IF NOT EXISTS `mens_texto` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ahora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `origen` varchar(64) NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `texto` longtext NOT NULL,
  `destino` mediumtext NOT NULL,
  `oculto` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `morosos`
--

DROP TABLE IF EXISTS `morosos`;
CREATE TABLE IF NOT EXISTS `morosos` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `curso` varchar(50) NOT NULL,
  `apellidos` varchar(60) NOT NULL,
  `nombre` varchar(25) NOT NULL,
  `ejemplar` varchar(100) NOT NULL,
  `devolucion` varchar(10) NOT NULL,
  `hoy` date NOT NULL,
  `amonestacion` varchar(2) NOT NULL DEFAULT 'NO',
  `sms` varchar(2) NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

DROP TABLE IF EXISTS `notas`;
CREATE TABLE IF NOT EXISTS `notas` (
  `claveal` varchar(12) NOT NULL DEFAULT '0',
  `notas0` varchar(200) DEFAULT NULL,
  `notas1` varchar(200) DEFAULT NULL,
  `notas2` varchar(200) DEFAULT NULL,
  `notas3` varchar(200) DEFAULT NULL,
  `notas4` varchar(200) DEFAULT NULL,
  `promociona` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`claveal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas_cuaderno`
--

DROP TABLE IF EXISTS `notas_cuaderno`;
CREATE TABLE IF NOT EXISTS `notas_cuaderno` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profesor` varchar(48) NOT NULL DEFAULT '',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `nombre` varchar(64) NOT NULL DEFAULT '',
  `texto` mediumtext NOT NULL,
  `texto_pond` mediumtext NOT NULL,
  `asignatura` int(6) NOT NULL DEFAULT '0',
  `curso` varchar(36) NOT NULL DEFAULT '',
  `oculto` tinyint(1) NOT NULL DEFAULT '0',
  `visible_nota` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `orden` tinyint(2) NOT NULL DEFAULT '0',
  `Tipo` varchar(32) DEFAULT NULL,
  `color` varchar(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

DROP TABLE IF EXISTS `noticias`;
CREATE TABLE IF NOT EXISTS `noticias` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` text NOT NULL,
  `contenido` longtext NOT NULL,
  `autor` varchar(255) NOT NULL,
  `fechapub` datetime NOT NULL,
  `fechafin` date DEFAULT NULL,
  `categoria` varchar(200) NOT NULL,
  `vistas` INT UNSIGNED NOT NULL DEFAULT '0',
  `pagina` char(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nuevas`
--

DROP TABLE IF EXISTS `nuevas`;
CREATE TABLE IF NOT EXISTS `nuevas` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `abrev` varchar(5) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `texto` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ocultas`
--

DROP TABLE IF EXISTS `ocultas`;
CREATE TABLE IF NOT EXISTS `ocultas` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `aula` varchar(48) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partestic`
--

DROP TABLE IF EXISTS `partestic`;
CREATE TABLE IF NOT EXISTS `partestic` (
  `parte` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unidad` varchar(64) NOT NULL,
  `carro` char(2) DEFAULT NULL,
  `nserie` varchar(15) NOT NULL DEFAULT '',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `hora` char(2) DEFAULT '',
  `alumno` varchar(35) DEFAULT NULL,
  `profesor` varchar(64) NOT NULL DEFAULT '',
  `descripcion` mediumtext NOT NULL,
  `estado` varchar(12) NOT NULL DEFAULT 'activo',
  `nincidencia` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`parte`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pendientes`
--

DROP TABLE IF EXISTS `pendientes`;
CREATE TABLE IF NOT EXISTS `pendientes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL DEFAULT '',
  `codigo` varchar(8) NOT NULL DEFAULT '',
  `grupo` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

DROP TABLE IF EXISTS `profesores`;
CREATE TABLE IF NOT EXISTS `profesores` (
  `nivel` varchar(255) DEFAULT NULL,
  `materia` varchar(255) DEFAULT NULL,
  `grupo` varchar(255) DEFAULT NULL,
  `profesor` varchar(255) NOT NULL,
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores_seneca`
--

DROP TABLE IF EXISTS `profesores_seneca`;
CREATE TABLE IF NOT EXISTS `profesores_seneca` (
  `idprofesor` int(9) UNSIGNED NOT NULL,
  `nomprofesor` varchar(64) NOT NULL,
  `deptoprofesor` varchar(80) NOT NULL,
  `correoprofesor` varchar(80) DEFAULT NULL,
  `telefonoprofesor` char(9) DEFAULT NULL,
  PRIMARY KEY (`idprofesor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puestos_alumnos`
--

DROP TABLE IF EXISTS `puestos_alumnos`;
CREATE TABLE IF NOT EXISTS `puestos_alumnos` (
  `unidad` varchar(10) COLLATE utf8_general_ci NOT NULL,
  `puestos` text COLLATE utf8_general_ci,
  `estructura` varchar(10) COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`unidad`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puestos_alumnos_tic`
--

DROP TABLE IF EXISTS `puestos_alumnos_tic`;
CREATE TABLE IF NOT EXISTS `puestos_alumnos_tic` (
  `profesor` varchar(50) NOT NULL,
  `grupo` varchar(64) NOT NULL,
  `asignatura` varchar(30) NOT NULL,
  `aula` varchar(32) NOT NULL,
  `puestos` text COLLATE utf8_general_ci,
  `monopuesto` tinyint(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`profesor`,`grupo`,`asignatura`,`aula`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reg_intranet`
--

DROP TABLE IF EXISTS `reg_intranet`;
CREATE TABLE IF NOT EXISTS `reg_intranet` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profesor` varchar(48) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(40) NOT NULL DEFAULT '',
  `useragent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reg_paginas`
--

DROP TABLE IF EXISTS `reg_paginas`;
CREATE TABLE IF NOT EXISTS `reg_paginas` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_reg` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `pagina` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reg_principal`
--

DROP TABLE IF EXISTS `reg_principal`;
CREATE TABLE IF NOT EXISTS `reg_principal` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pagina` mediumtext NOT NULL,
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `claveal` varchar(12) NOT NULL DEFAULT '',
  `tutorlegal` varchar(255) DEFAULT NULL,
  `useragent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

DROP TABLE IF EXISTS `reservas`;
CREATE TABLE IF NOT EXISTS `reservas` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `eventdate` date NOT NULL,
  `dia` tinyint(1) NOT NULL DEFAULT '0',
  `html` tinyint(1) NOT NULL DEFAULT '0',
  `event1` varchar(255) DEFAULT NULL,
  `event2` varchar(255) NOT NULL DEFAULT '',
  `event3` varchar(255) NOT NULL DEFAULT '',
  `event4` varchar(255) NOT NULL DEFAULT '',
  `event5` varchar(255) NOT NULL DEFAULT '',
  `event6` varchar(255) NOT NULL DEFAULT '',
  `event7` varchar(255) NOT NULL DEFAULT '',
  `event8` varchar(255) NOT NULL DEFAULT '',
  `event9` varchar(255) NOT NULL DEFAULT '',
  `event10` varchar(255) NOT NULL DEFAULT '',
  `event11` varchar(255) NOT NULL DEFAULT '',
  `event12` varchar(255) NOT NULL DEFAULT '',
  `event13` varchar(255) NOT NULL DEFAULT '',
  `event14` varchar(255) NOT NULL DEFAULT '',
  `servicio` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas_elementos`
--

DROP TABLE IF EXISTS `reservas_elementos`;
CREATE TABLE IF NOT EXISTS `reservas_elementos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `elemento` varchar(128) NOT NULL,
  `id_tipo` tinyint(2) NOT NULL,
  `oculto` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas_hor`
--

DROP TABLE IF EXISTS `reservas_hor`;
CREATE TABLE IF NOT EXISTS `reservas_hor` (
  `dia` tinyint(1) NOT NULL DEFAULT '0',
  `hora1` varchar(24) DEFAULT NULL,
  `hora2` varchar(24) DEFAULT NULL,
  `hora3` varchar(24) DEFAULT NULL,
  `hora4` varchar(24) DEFAULT NULL,
  `hora5` varchar(24) DEFAULT NULL,
  `hora6` varchar(24) DEFAULT NULL,
  `hora7` varchar(24) DEFAULT NULL,
  `servicio` varchar(32) NOT NULL,
  PRIMARY KEY (`dia`,`servicio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas_tipos`
--

DROP TABLE IF EXISTS `reservas_tipos`;
CREATE TABLE IF NOT EXISTS `reservas_tipos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo` varchar(254) NOT NULL,
  `observaciones` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

--
-- Volcado de datos para la tabla `reservas_tipos`
--

INSERT INTO `reservas_tipos` (`id`, `tipo`, `observaciones`) VALUES
(1, 'TIC', ''),
(2, 'Medios Audiovisuales', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `r_departamento`
--

DROP TABLE IF EXISTS `r_departamento`;
CREATE TABLE IF NOT EXISTS `r_departamento` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contenido` longtext NOT NULL,
  `jefedep` varchar(255) DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DEPARTAMENTO` varchar(80) NOT NULL,
  `fecha` date NOT NULL,
  `impreso` tinyint(1) NOT NULL,
  `numero` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `r_departamento_backup`
--

DROP TABLE IF EXISTS `r_departamento_backup`;
CREATE TABLE IF NOT EXISTS `r_departamento_backup` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contenido` longtext NOT NULL,
  `jefedep` varchar(255) DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `departamento` varchar(48) NOT NULL,
  `fecha` date NOT NULL,
  `numero` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms`
--

DROP TABLE IF EXISTS `sms`;
CREATE TABLE IF NOT EXISTS `sms` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `telefono` mediumtext NOT NULL,
  `mensaje` varchar(160) NOT NULL DEFAULT '',
  `profesor` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE IF NOT EXISTS `tareas` (
  `id` int(10) unsigned AUTO_INCREMENT PRIMARY KEY,
  `idea` varchar(12) NOT NULL,
  `titulo` tinytext NOT NULL,
  `tarea` text NOT NULL,
  `estado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fechareg` datetime NOT NULL,
  `prioridad` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas_alumnos`
--

DROP TABLE IF EXISTS `tareas_alumnos`;
CREATE TABLE IF NOT EXISTS `tareas_alumnos` (
  `ID` smallint(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `CLAVEAL` varchar(12) NOT NULL DEFAULT '',
  `APELLIDOS` varchar(30) NOT NULL DEFAULT '',
  `NOMBRE` varchar(24) NOT NULL DEFAULT '',
  `unidad` varchar(64) NOT NULL,
  `FECHA` date NOT NULL DEFAULT '0000-00-00',
  `FIN` date NOT NULL DEFAULT '0000-00-00',
  `DURACION` smallint(2) NOT NULL DEFAULT '3',
  `PROFESOR` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas_profesor`
--

DROP TABLE IF EXISTS `tareas_profesor`;
CREATE TABLE IF NOT EXISTS `tareas_profesor` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `profesor` varchar(48) NOT NULL DEFAULT '',
  `asignatura` varchar(64) NOT NULL DEFAULT '',
  `tarea` mediumtext NOT NULL,
  `confirmado` char(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temas`
--

DROP TABLE IF EXISTS `temas`;
CREATE TABLE IF NOT EXISTS `temas` (
  `idea` varchar(12) NOT NULL,
  `tema` varchar(64) NOT NULL,
  `fondo` varchar(16) NOT NULL,
  PRIMARY KEY (`idea`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Textos`
--

DROP TABLE IF EXISTS `Textos`;
CREATE TABLE IF NOT EXISTS `Textos` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Autor` varchar(128) DEFAULT NULL,
  `Titulo` varchar(128) NOT NULL DEFAULT '',
  `Editorial` varchar(64) NOT NULL DEFAULT '',
  `Nivel` varchar(64) NOT NULL DEFAULT '',
  `Grupo` mediumtext NOT NULL,
  `Notas` mediumtext,
  `DEPARTAMENTO` varchar(80) DEFAULT NULL,
  `Asignatura` varchar(48) NOT NULL DEFAULT '',
  `Obligatorio` varchar(12) NOT NULL DEFAULT '',
  `Clase` varchar(8) NOT NULL DEFAULT 'Texto',
  `isbn` varchar(18) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `textos_alumnos`
--

DROP TABLE IF EXISTS `textos_alumnos`;
CREATE TABLE IF NOT EXISTS `textos_alumnos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL DEFAULT '0',
  `materia` int(5) NOT NULL DEFAULT '0',
  `estado` char(1) NOT NULL DEFAULT '',
  `devuelto` char(1) DEFAULT '0',
  `fecha` datetime DEFAULT '0000-00-00 00:00:00',
  `curso` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramos`
--

DROP TABLE IF EXISTS `tramos`;
CREATE TABLE IF NOT EXISTS `tramos` (
  `tramo` int(6) UNSIGNED NOT NULL,
  `hora` varchar(80) NOT NULL,
  `horini` int(4) UNSIGNED NOT NULL,
  `horfin` int(4) UNSIGNED NOT NULL,
  `hora_inicio` TIME NOT NULL,
  `hora_fin` TIME NOT NULL,
  `idjornada` int(12) unsigned NOT NULL,
  PRIMARY KEY (`tramo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transito_control`
--

DROP TABLE IF EXISTS `transito_control`;
CREATE TABLE IF NOT EXISTS `transito_control` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `colegio` varchar(128) NOT NULL,
  `pass` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transito_datos`
--

DROP TABLE IF EXISTS `transito_datos`;
CREATE TABLE IF NOT EXISTS `transito_datos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL,
  `tipo` varchar(24) NOT NULL,
  `dato` mediumtext NOT NULL,
  PRIMARY KEY (`id`,`claveal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transito_tipo`
--

DROP TABLE IF EXISTS `transito_tipo`;
CREATE TABLE IF NOT EXISTS `transito_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(24) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;

INSERT INTO `transito_tipo` (`id`, `tipo`) VALUES
(1, 'repeticion'),
(2, 'susp1'),
(3, 'susp2'),
(4, 'susp3'),
(5, 'leng'),
(6, 'mat'),
(7, 'ing'),
(8, 'con'),
(9, 'edfis'),
(10, 'mus'),
(11, 'plas'),
(12, 'asiste'),
(13, 'dificultad'),
(14, 'refuerzo'),
(15, 'necreflen'),
(16, 'necrefmat'),
(17, 'necrefing'),
(18, 'exento'),
(19, 'acompanamiento'),
(20, 'areasadcurrsign'),
(21, 'areasadcurrnosign'),
(22, 'necareasadcurrsign'),
(23, 'necareasadcurrnosign'),
(24, 'PT_AL'),
(25, 'PT_AL_aula'),
(26, 'nacion'),
(27, 'atal'),
(28, 'necatal'),
(29, 'integra'),
(30, 'actitud'),
(31, 'funciona'),
(32, 'relacion'),
(33, 'norelacion'),
(34, 'disruptivo'),
(35, 'expulsion'),
(36, 'observaciones'),
(37, 'orientacion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutoria`
--

DROP TABLE IF EXISTS `tutoria`;
CREATE TABLE IF NOT EXISTS `tutoria` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `claveal` varchar(12) NOT NULL DEFAULT '',
  `apellidos` varchar(42) NOT NULL DEFAULT '',
  `nombre` varchar(24) NOT NULL DEFAULT '',
  `tutor` varchar(48) NOT NULL DEFAULT '',
  `unidad` varchar(64) NOT NULL,
  `observaciones` mediumtext NOT NULL,
  `causa` varchar(42) NOT NULL DEFAULT '',
  `accion` varchar(200) NOT NULL DEFAULT '',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `orienta` tinyint(1) NOT NULL DEFAULT '0',
  `prohibido` tinyint(1) NOT NULL DEFAULT '0',
  `jefatura` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades`
--

DROP TABLE IF EXISTS `unidades`;
CREATE TABLE IF NOT EXISTS `unidades` (
  `idunidad` int(11) UNSIGNED NOT NULL,
  `nomunidad` varchar(10) NOT NULL,
  `idcurso` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`idunidad`, `idcurso`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarioalumno`
--

DROP TABLE IF EXISTS `usuarioalumno`;
CREATE TABLE IF NOT EXISTS `usuarioalumno` (
  `usuario` varchar(18) DEFAULT NULL,
  `pass` varchar(16) NOT NULL DEFAULT '',
  `nombre` varchar(48) DEFAULT NULL,
  `perfil` char(1) NOT NULL DEFAULT '',
  `unidad` varchar(64) NOT NULL DEFAULT '',
  `claveal` varchar(12) NOT NULL DEFAULT '',
  PRIMARY KEY (`claveal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarioprofesor`
--

DROP TABLE IF EXISTS `usuarioprofesor`;
CREATE TABLE IF NOT EXISTS `usuarioprofesor` (
  `usuario` varchar(16) DEFAULT NULL,
  `nombre` varchar(64) DEFAULT NULL,
  `perfil` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`usuario`,`nombre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;
