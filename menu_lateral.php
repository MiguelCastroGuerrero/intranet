<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed');

switch ($config['centro_provincia']) {
	case 'Almería' : $web_delegacion = 'almeria'; break;
	case 'Cádiz' : $web_delegacion = 'cadiz'; break;
	case 'Córdoba' : $web_delegacion = 'cordoba'; break;
	case 'Granada' : $web_delegacion = 'granada'; break;
	case 'Huelva' : $web_delegacion = 'huelva'; break;
	case 'Jaén' : $web_delegacion = 'jaen'; break;
	case 'Málaga' : $web_delegacion = 'malaga'; break;
	case 'Sevilla' : $web_delegacion = 'sevilla'; break;
}

//	VALORES DEL MENU
//	-----------------------------------------------------------------------------------------------------
//	menu_id					(string) Identificador del menú
//	nombre					(string) Nombre del menú
//	cargos					(array) ID de los cargos con privilegios para visualizar el menu
//	ncargos					(array) ID de los cargos sin privilegios para visualizar el menu
//	modulo					(boolean) Valor del módulo del que depende el menú
//	meses					(array) Número del mes cuando está disponible el menú (sin 0 iniciales)
//	items					(array) Opciones del menú
//	items -> href			(string) URI de la página
//	items -> titulo			(string) Título de la página
//	items -> cargos			(array) ID de los cargos con privilegios para visualizar la opción del menú
//	items -> ncargos		(array) ID de los cargos sin privilegios para visualizar la opción del menú
//	items -> modulo			(boolean) Valor del módulo del que depende la opción del menú
//	items -> meses			(array) Número del mes cuando está disponible la opción del menú (sin 0 iniciales)
//
//	Se puede realizar menus anidados en un item, estos submenus permiten las mismas acciones de control
//	de acceso que el item padre.
//

$menu = array(
	array(
		'menu_id' => 'direccion',
		'nombre'  => 'Dirección del centro',
		'cargos'  => array('1'),
		'items'   => array(
			array(
				'href'   => 'xml/index.php',
				'titulo' => 'Administración de la Intranet',
			),
			array(
				'href'   => 'admin/matriculas/index.php',
				'titulo' => 'Matriculación de alumnos',
				'modulo'  => $config['mod_matriculacion'],
				'meses'	 => array(6, 7, 8, 9),
			),
			array(
				'href'   => 'admin/jefatura/index.php',
				'titulo' => 'Intervenciones',
			),
			array(
				'href'   => 'admin/tutoria/index.php',
				'titulo' => 'Tutorías',
			),
			array(
				'href'   => 'admin/guardias/index_admin.php',
				'titulo' => 'Guardias',
				'modulo' => $config['mod_horarios'],
			),
			array(
				'href'   => 'admin/ausencias/index.php',
				'titulo' => 'Ausencias',
			),
		)
	),
	array(
		'menu_id' => 'extraescolares',
		'nombre'  => 'Extraescolares',
		'cargos'  => array('5'),
		'items'   => array(
			array(
				'href'   => 'admin/actividades/indexextra.php',
				'titulo' => 'Administrar actividades'
			),
			array(
				'href'   => 'calendario/index.php?action=nuevoEvento',
				'titulo' => 'Introducir actividades'
			)
		)
	),
	array(
		'menu_id' => 'orientacion',
		'nombre'  => 'Orientación',
		'cargos'  => array('8'),
		'items'   => array(
			array(
				'href'   => 'admin/orientacion/index.php',
				'titulo' => 'Intervenciones'
			),
			array(
				'href'   => 'admin/tutoria/index.php',
				'titulo' => 'Tutorías'
			),
			array(
				'href'   => 'admin/actividades/indexextra.php',
				'titulo' => 'Actividades extraescolares'
			),
			array(
				'href'   => 'admin/matriculas/index.php',
				'titulo' => 'Matriculación de alumnos',
				'modulo'  => $config['mod_matriculacion'],
				'meses'	 => array(6, 7, 8, 9),
			),
			array(
				'href'   => 'admin/matriculas/consulta_transito.php',
				'titulo' => 'Informes de tránsito',
				'modulo'  => $config['mod_matriculacion'],
			)
		)
	),
	array(
		'menu_id' => 'tutoria',
		'nombre'  => 'Tutoría de '.$_SESSION['mod_tutoria']['unidad'],
		'cargos'  => array('2'),
		'items'   => array(
			array(
				'href'   => 'admin/tutoria/index.php',
				'titulo' => 'Resumen',
			),
			array(
				'href'   => 'admin/datos/datos.php?unidad='.$_SESSION['mod_tutoria']['unidad'],
				'titulo' => 'Datos del grupo',
			),
			array(
				'href'   => 'admin/tutoria/intervencion.php',
				'titulo' => 'Intervenciones',
			),
			array(
				'href'   => 'admin/libros-texto/programa-gratuidad/index.php',
				'titulo' => 'Programa de gratuidad',
				'meses'	 => array(6, 7, 8, 9)
			)
		)
	),
	array(
		'menu_id' => 'biblioteca',
		'nombre'  => 'Biblioteca',
		'modulo'  => $config['mod_biblioteca'],
		'cargos'  => array('c'),
		'items'   => array(
			array(
				'href'   => $config['mod_biblioteca_web'],
				'titulo' => 'Página de la biblioteca',
				'target' => '_blank'
			),
			array(
				'href'   => 'admin/cursos/hor_aulas.php?aula=Biblioteca',
				'titulo' => 'Horario de la biblioteca'
			),
			array(
				'href'   => 'admin/biblioteca/consulta.php',
				'titulo' => 'Gestión de los préstamos'
			),
			array(
				'href'   => 'admin/biblioteca/index.php',
				'titulo' => 'Consultar fondos de la biblioteca'
			),
			array(
				'href'   => 'admin/biblioteca/index_biblio.php',
				'titulo' => 'Importar datos de Abies'
			)
		)
	),
);

$menu_centro = array(
	array(
		'menu_id' => 'centro',
		'nombre'  => 'Centro',
		'items'   => array(
			array(
				'href'   => 'xml/index.php',
				'titulo' => 'Administración de la Intranet',
				'cargos' => array('z'),
				'ncargos' => array('1'),
			),
			array(
				'href'   => '#',
				'titulo' => 'Calendarios',
				'items' => array(
					array(
						'href'   => 'admin/cursos/calendario_escolar.php',
						'titulo' => "Calendario escolar ". $config['curso_actual'],
					),
					array(
						'href'   => 'calendario/index.php',
						'titulo' => 'Calendario de actividades',
					),
					array(
						'href'   => 'calendario/index_unidades.php',
						'titulo' => 'Calendario de los grupos',
						'ncargos' => array('6'),
					),
					array(
						'href'   => 'admin/actividades/indexextra.php',
						'titulo' => 'Actividades extraescolares'
					),
				)
			),
			array(
				'href'   => 'admin/cursos/chorarios.php',
				'titulo' => 'Horarios',
				'modulo' => $config['mod_horarios'],
			),
			array(
				'href'   => '#',
				'titulo' => 'Centro TIC',
				'modulo' => $config['mod_centrotic'],
				'items' => array(
					array(
						'href'   => 'TIC/index.php',
						'titulo' => 'Incidencias TIC',
					),
					array(
						'href'   => 'TIC/incidencia.php',
						'titulo' => 'Nueva incidencia',
					),
					array(
						'href'   => 'TIC/inventario.php',
						'titulo' => 'Inventario TIC',
					),
					array(
						'href'   => 'TIC/mesas_tic_seleccion.php',
						'titulo' => 'Asignación de mesas TIC',
						'ncargo' => array('6', '7', '8')
					),
					array(
						'href'   => 'TIC/perfiles_alumnos.php',
						'titulo' => 'Perfiles de alumnos',
					),
					array(
						'href'   => 'TIC/perfiles_profesores.php',
						'titulo' => 'Perfiles de profesores',
					),
					array(
						'href'   => 'TIC/protocolo.php',
						'titulo' => 'Protocolo de uso',
					),
					array(
						'href'   => 'TIC/estadisticas.php',
						'titulo' => 'Estadísticas de las TIC',
					)
				)
			),
			array(
				'href'   => 'admin/biblioteca/index.php',
				'titulo' => 'Biblioteca',
				'ncargos' => array('c'),
			),
			array(
				'href'   => 'admin/fotos/profes.php',
				'titulo' => 'Fotos de los profesores',
				'cargos' => array('6','7')
			),
			
		)
	)
);

if ($_SERVER['SERVER_NAME'] == "iesmonterroso.org") {
	$monterroso1 = array(
						'href'   => 'varios/Planos_Centro.pdf',
						'titulo' => 'Planos y dependencias',
						'target' => '_blank',
				);			
	$monterroso2 = array(
						'href'   => 'https://iesmonterroso.org/plan-de-centro/',
						'titulo' => 'Plan de centro',
						'target' => '_blank',
				);
	array_push($menu_centro[0]['items'], $monterroso1, $monterroso2);
}

$menu_alumnos = array(
	array(
		'menu_id' => 'alumnos',
		'nombre'  => 'Alumnos',
		'items'   => array(
			array(
				'href'   => 'admin/datos/cdatos.php',
				'titulo' => 'Alumnos y grupos',
			),
			array(
				'href'   => 'admin/fechorias/infechoria.php',
				'titulo' => 'Problemas de convivencia',
				'ncargos' => array('6', '7'),
			),
			array(
				'href'   => 'faltas/index.php',
				'titulo' => 'Faltas de asistencia',
				'modulo' => $config['mod_asistencia'],
				'ncargos' => array('6', '7'),
			),
			array(
				'href'   => '#',
				'titulo' => 'Evaluaciones',
				'ncargos' => array('6', '7'),
				'items' => array(
					array(
						'href'   => 'admin/informes/notas_grupo.php',
						'titulo' => 'Calificaciones'
					),
					array(
						'href'   => 'admin/evaluaciones/actas.php',
						'titulo' => 'Actas de evaluación'
					),
					array(
						'href'   => 'admin/evaluacion_pendientes/index.php',
						'titulo' => 'Evaluación de pendientes',
					),
				),
				array(
						'href'   => 'admin/informes/evaluaciones/index.php',
						'titulo' => 'Estadísticas de las evaluaciones'
					),
			),	
			array(
				'href'   => '#',
				'titulo' => 'Informes',
				'ncargos' => array(6,7),
				'items' => array(
					array(
						'href'   => 'admin/tareas/index.php',
						'titulo' => 'Informes de tareas',
					),
					array(
						'href'   => 'admin/infotutoria/index.php',
						'titulo' => 'Informes de tutoria',
					),
					array(
						'href'   => 'admin/informes/extraordinaria/index.php',
						'titulo' => 'Informes de evaluación extraordinaria',
					),					
					array(
						'href'   => 'admin/informes/pendientes/index.php',
						'titulo' => 'Informes de materias pendientes',
					),
					array(
						'href'   => 'admin/departamento/adaptaciones/index.php',
						'titulo' => 'Adaptaciones curriculares (ACNS)',
					)
				)
			),
			array(
				'href'   => 'admin/fotos/index.php',
				'titulo' => 'Fotos de los alumnos'
			),
			array(
				'href'   => 'admin/matriculas/index.php',
				'titulo' => 'Matriculación',
				'modulo'  => $config['mod_matriculacion'],
				'cargos' => array(7),
				)
			)
		)
	);

$menu_profesores = array(
	array(
		'menu_id' => 'profesores',
		'nombre'  => 'Profesores',
		'ncargos' => array('6', '7'),
		'items'   => array(
			array(
				'href'   => 'admin/departamento/actas/',
				'titulo' => 'Departamento',
			),	
			array(
				'href'   => 'admin/actividades/indexextra.php',
				'titulo' => 'Actividades extraescolares',
			),
			array(
				'href'   => 'admin/guardias/consulta_profesores.php',
				'titulo' => 'Guardias en el aula',
				'modulo' => $config['mod_horarios'],
			),
			array(
				'href'   => 'admin/ausencias/index.php',
				'titulo' => 'Registro de ausencias',
			),
			array(
				'href'   => 'admin/fotos/profes.php',
				'titulo' => 'Fotos de los profesores'
			)
		)
	)
);

$menu_actas = array(
	'menu_id' => 'actas',
	'titulo'  => 'Actas',
	'items'   => array(
		array(
			'href'   => 'admin/departamento/actas/',
			'titulo' => 'Actas del departamento',
			'cargos'  => array('4')
		),
		array(
			'href'   => 'admin/departamento/actas/index.php?organo=DFEIE',
			'titulo' => 'Actas del DFEIE',
			'cargos'  => array('f')
		),
		array(
			'href'   => 'admin/departamento/actas/index.php?organo=ETCP',
			'titulo' => 'Actas del ETCP',
			'cargos'  => array('9','4')
		),
		array(
			'href'   => 'admin/departamento/actas/index.php?organo=Equipo directivo',
			'titulo' => 'Actas del Equipo Directivo',
			'cargos'  => array('1')
		),
		array(
			'href'   => 'admin/departamento/actas/index.php?organo=Coord. Enseñanzas Bilingües',
			'titulo' => 'Actas de Ens. Bilingües',
			'cargos'  => array('a')
		),
		array(
			'href'   => 'admin/departamento/actas/administracion.php',
			'titulo' => 'Administrar actas',
			'cargos'  => array('1','4')
		)
	)
);

if (file_exists('./admin/departamento/actas/config.php')) {
	include('./admin/departamento/actas/config.php');

	$menu_actas_claustro = array(
		'href'   => 'admin/departamento/actas/index.php?organo=Claustro del Centro',
		'titulo' => 'Claustro del Centro',
		'ncargos'  => array('6','7')
	);

	array_push($menu_actas['items'], $menu_actas_claustro);

	if (isset($config['actas_depto']['secretario_aca']) && $pr == $config['actas_depto']['secretario_aca']) {
		$menu_actas_aca = array(
			'href'   => 'admin/departamento/actas/index.php?organo=Área Artística',
			'titulo' => 'Actas del Área Ártística',
			'ncargos'  => array('1',)
		);

		array_push($menu_actas['items'], $menu_actas_aca);
	}

	if (isset($config['actas_depto']['secretario_acct']) && $pr == $config['actas_depto']['secretario_acct']) {
		$menu_actas_acct = array(
			'href'   => 'admin/departamento/actas/index.php?organo=Área Científico-Tecnológica',
			'titulo' => 'Actas del Área Científico-Tecnológica',
			'ncargos'  => array('1')
		);

		array_push($menu_actas['items'], $menu_actas_acct);
	}

	if (isset($config['actas_depto']['secretario_acsl']) && $pr == $config['actas_depto']['secretario_acsl']) {
		$menu_actas_acsl = array(
			'href'   => 'admin/departamento/actas/index.php?organo=Área Social-Lingüística',
			'titulo' => 'Actas del Área Social-Lingüistica',
			'ncargos'  => array('1')
		);

		array_push($menu_actas['items'], $menu_actas_acsl);
	}

	if (isset($config['actas_depto']['secretario_afp']) && $pr == $config['actas_depto']['secretario_afp']) {
		$menu_actas_afp = array(
			'href'   => 'admin/departamento/actas/index.php?organo=Área Formación Profesional',
			'titulo' => 'Actas del Área Formación Profesional',
			'ncargos'  => array('1')
		);

		array_push($menu_actas['items'], $menu_actas_afp);
	}
}

if (count($menu_actas['items'])>0 AND acl_permiso($_SESSION['cargo'], array(1,4,f,9,a))) {
	array_push($menu_profesores[0]['items'], $menu_actas);
}

$menu_utilidades = array(
	array(
		'menu_id' => 'utilidades',
		'nombre'  => 'Utilidades',
		'items'   => array(
			array(
				'href'   => 'usuario.php',
				'titulo' => 'Mis datos',
			),
			array(
				'href'   => 'calendario/index.php',
				'titulo' => 'Agenda personal',
			),
			array(
				'href'   => 'tareas/',
				'titulo' => 'Tareas',
			),
			array(
				'href'   => 'admin/mensajes/redactar.php',
				'titulo' => 'Mensajes y comunicaciones',
			),
			array(
				'href'   => 'documentos/',
				'titulo' => 'Documentos',
			),
			array(
				'href'   => 'reservas/index_aula.php?recurso=aula_grupo',
				'titulo' => 'Sistema de Reservas',
				'ncargos' => array(7),
			),
			array(
				'href'   => '#',
				'titulo' => 'Datos y estadísticas',
				'ncargos' => array('6', '7'),
				'items' => array(
					array(
						'href'   => 'admin/informes/evaluaciones/index.php',
						'titulo' => 'Informe sobre las evaluaciones'
					),
					array(
						'href'   => 'admin/fechorias/informe_convivencia.php',
						'titulo' => 'Informe sobre convivencia'
					),
					array(
						'href'   => 'admin/faltas/informe_grupos.php',
						'titulo' => 'Informe sobre faltas de asistencia',
						'modulo' => $config['mod_asistencia'],
					),
					array(
						'href'   => 'admin/guardias/informe_guardias.php',
						'titulo' => 'Informe sobre guardias',
						'modulo' => $config['mod_horarios'],
					),
					array(
						'href'   => 'admin/ausencias/ausencias_profes.php',
						'titulo' => 'Informe sobre ausencias de profesores',
					),
					array(
						'href'   => 'admin/actividades/informe_actividades.php',
						'titulo' => 'Informe sobre actividades extraescolares',
					)
				)
			),
		
			array(
				'href'   => 'xml/jefe/index_mayores.php',
				'titulo' => 'Alumnos mayores de 18 años',
				'cargos' => array('6'),
			),
			array(
				'href'   => 'admin/noticias/permanentes.php',
				'titulo' => 'Cómo se hace...',
			)
		)
	)
);


$paginas_interes = array(
	array(
		'menu_id' => 'paginas_interes',
		'nombre'  => 'Páginas de interés',
		'items'   => array(
			array(
				'href'   => '//'.$config['dominio'],
				'titulo' => 'Página del '.$config['centro_denominacion'],
				'target' => '_blank',
			),
			array(
				'href'   => 'https://correo.juntadeandalucia.es/',
				'titulo' => 'Correo corporativo',
				'target' => '_blank',
			),
			array(
				'href'   => 'http://www.juntadeandalucia.es/averroes/centros-tic/'.$config['centro_codigo'].'/',
				'titulo' => 'Servidor de contenidos',
				'target' => '_blank',
			),
			array(
				'href'   => 'https://www.educacionyfp.gob.es/',
				'titulo' => 'Ministerio de Educación y FP',
				'target' => '_blank',
			),
			array(
				'href'   => 'http://www.juntadeandalucia.es/educacion/portals/web/ced',
				'titulo' => 'Consejería de Educación y Deporte',
				'target' => '_blank',
			),
			array(
				'href'   => 'http://www.juntadeandalucia.es/educacion/webportal/web/delegacion-'.$web_delegacion.'/',
				'titulo' => 'Delegación de Educación',
				'target' => '_blank',
			),
			array(
				'href'   => 'https://www.juntadeandalucia.es/educacion/portaldocente/',
				'titulo' => 'Portal del Personal Docente',
				'target' => '_blank',
			),
			array(
				'href'   => 'http://www.juntadeandalucia.es/educacion/portalaverroes',
				'titulo' => 'Portal Averroes',
				'target' => '_blank',
			)
		)
	)
);

// Cambios en el menú para otros centros educativos
if (file_exists('menu_lateral_centro.php')) {
	include('menu_lateral_centro.php');
}


$menu = array_merge($menu, $menu_centro, $menu_alumnos, $menu_profesores, $menu_utilidades, $paginas_interes);
?>
<!-- MENU-LATERAL -->

<!-- PHONE SCREENS -->
	<div class="row">
		<?php if (isset($config['mod_asistencia']) && $config['mod_asistencia'] and ($dpto !== "Admin" && $dpto !== "Administracion" && $dpto !== "Conserjeria")): ?>
		<div class="col-xs-3 text-center padmobile">
			<a href="faltas/index.php">
				<span class="far fa-clock fa-2x"></span><br>
				<small>Asistencia</small></a>
		</div>
		<?php endif; ?>
		<?php if ($dpto !== "Admin" && $dpto !== "Administracion" && $dpto !== "Conserjeria"): ?>
		<div class="col-xs-3 text-center padmobile">
			<a href="admin/fechorias/infechoria.php">
				<span class="fas fa-gavel fa-2x"></span><br>
				<small>Convivencia</small></a>
		</div>
		<?php endif; ?>
		<div class="col-xs-3 text-center padmobile">
		<?php if (strstr($_SESSION['cargo'], "2") or strstr($_SESSION['cargo'], "1") or strstr($_SESSION['cargo'], "8")){ ?>

			<a href="admin/tutoria/index.php">
				<span class="fas fa-users fa-2x"></span><br>
				<small>Tutoría</small></a>

		<?php } else{ ?>

		<a href="admin/informes/cinforme.php">
				<span class="far fa-address-book fa-2x"></span><br>
				<small>Informes</small></a>

		<?php } ?>
		</div>
		<div class="col-xs-3 text-center padmobile">
			<a href="admin/cursos/chorarios.php">
				<span class="far fa-calendar fa-2x"></span><br>
				<small>Horarios</small></a>
		</div>
		<div class="col-xs-3 text-center padmobile">
			<a href="admin/mensajes/redactar.php">
				<span class="far fa-envelope fa-2x"></span><br>
				<small>Mensajes</small></a>
		</div>		
		<div class="col-xs-3 text-center padmobile">
			<a href="tareas/index.php">
				<span class="fas fa-tasks fa-2x"></span><br>
				<small>Tareas</small></a>
		</div>
		<div class="col-xs-3 text-center padmobile hidden-xs">
			<a href="calendario/index.php">
				<span class="far fa-calendar fa-2x"></span><br>
				<small>Calendario</small></a>
		</div>
		<div class="col-xs-3 text-center padmobile">
			<a href="reservas/index_aula.php?recurso=aula_grupo">
				<span class="fas fa-key fa-2x"></span><br>
				<small>Reservas</small></a>
		</div>
		<div class="col-xs-3 text-center padmobile hidden-sm hidden-md hidden-lg">
			<a href="#" id="toggleMenu">
				<span class="fas fa-ellipsis-v fa-2x"></span><br>
				<small>Menú</small></a>
		</div>
	</div>
	<br>

<!-- TABLETS / DESKTOPS SCREENS  -->
<div class="panel-group hidden-xs" id="accordion">
<?php $nmenu = 1; ?>
<?php for($i=0 ; $i < count($menu) ; $i++): ?>
<?php if(!isset($menu[$i]['modulo']) || ($menu[$i]['modulo'] == '1')): ?>
<?php if(!isset($menu[$i]['cargos']) || in_array($carg[0], $menu[$i]['cargos']) || in_array($carg[1], $menu[$i]['cargos']) || in_array($carg[2], $menu[$i]['cargos']) || in_array($carg[3], $menu[$i]['cargos']) || in_array($carg[4], $menu[$i]['cargos'])): ?>
<?php if(!isset($menu[$i]['ncargos']) || !in_array($carg[0], $menu[$i]['ncargos']) && !in_array($carg[1], $menu[$i]['ncargos']) && !in_array($carg[2], $menu[$i]['ncargos']) && !in_array($carg[3], $menu[$i]['ncargos']) && !in_array($carg[4], $menu[$i]['ncargos'])): ?>
<?php if(!isset($menu[$i]['meses']) || in_array(date('n'), $menu[$i]['meses'])): ?>
	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h4 class="panel-title">
	      <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $menu[$i]['menu_id']; ?>" style="display: block;">
	      	<span class="fas fa-chevron-down pull-right"></span>
	        <?php echo $menu[$i]['nombre']; ?>
	      </a>
	    </h4>
	  </div>
	  <div id="<?php echo $menu[$i]['menu_id']; ?>" class="panel-collapse collapse">
	    <div class="panel-body">
	    	<?php if(count($menu[$i]['items']) > 0): ?>
				<ul class="nav nav-pills nav-stacked">
					<?php $count=0; ?>
					<?php for($j=0 ; $j < count($menu[$i]['items']) ; $j++): ?>
					<?php if(isset($menu[$i]['items'][$j]['items'])): ?>
					<?php if(!isset($menu[$i]['items'][$j]['modulo']) || ($menu[$i]['items'][$j]['modulo'] == '1')): ?>
					<?php if(!isset($menu[$i]['items'][$j]['cargos']) || in_array($carg[0], $menu[$i]['items'][$j]['cargos']) || in_array($carg[1], $menu[$i]['items'][$j]['cargos']) || in_array($carg[2], $menu[$i]['items'][$j]['cargos']) || in_array($carg[3], $menu[$i]['items'][$j]['cargos']) || in_array($carg[4], $menu[$i]['items'][$j]['cargos'])): ?>
					<?php if(!isset($menu[$i]['items'][$j]['ncargos']) || !in_array($carg[0], $menu[$i]['items'][$j]['ncargos']) && !in_array($carg[1], $menu[$i]['items'][$j]['ncargos']) && !in_array($carg[2], $menu[$i]['items'][$j]['ncargos']) && !in_array($carg[3], $menu[$i]['items'][$j]['ncargos']) && !in_array($carg[4], $menu[$i]['items'][$j]['ncargos'])): ?>
					<?php if(!isset($menu[$i]['items'][$j]['meses']) || in_array($carg, $menu[$i]['items'][$j]['meses'])): ?>
					<li><a data-toggle="collapse" href="#<?php echo $menu[$i]['menu_id']; ?>-submenu<?php echo $count; ?>">
						<span class="fas fa-chevron-down pull-right"></span>
						<?php echo $menu[$i]['items'][$j]['titulo']; ?></a>
					</li>
					<div id="<?php echo $menu[$i]['menu_id']; ?>-submenu<?php echo $count; ?>" class="collapse">
						<ul class="nav nav-pills nav-stacked">
							<?php for($k=0 ; $k < count($menu[$i]['items'][$j]['items']) ; $k++): ?>
							<?php if(!isset($menu[$i]['items'][$j]['items'][$k]['modulo']) || ($menu[$i]['items'][$j]['items'][$k]['modulo'] == '1')): ?>
							<?php if(!isset($menu[$i]['items'][$j]['items'][$k]['cargos']) || in_array($carg[0], $menu[$i]['items'][$j]['items'][$k]['cargos']) || in_array($carg[1], $menu[$i]['items'][$j]['items'][$k]['cargos']) || in_array($carg[2], $menu[$i]['items'][$j]['items'][$k]['cargos']) || in_array($carg[3], $menu[$i]['items'][$j]['items'][$k]['cargos']) || in_array($carg[4], $menu[$i]['items'][$j]['items'][$k]['cargos'])): ?>
							<?php if(!isset($menu[$i]['items'][$j]['items'][$k]['ncargos']) || !in_array($carg[0], $menu[$i]['items'][$j]['items'][$k]['ncargos']) && !in_array($carg[1], $menu[$i]['items'][$j]['items'][$k]['ncargos']) && !in_array($carg[2], $menu[$i]['items'][$j]['items'][$k]['ncargos']) && !in_array($carg[3], $menu[$i]['items'][$j]['items'][$k]['ncargos']) && !in_array($carg[4], $menu[$i]['items'][$j]['items'][$k]['ncargos'])): ?>
							<?php if(!isset($menu[$i]['items'][$j]['items'][$k]['meses']) || in_array(date('n'), $menu[$i]['items'][$j]['items'][$k]['meses'])): ?>
							<li style="margin-left:20px;"><a href="<?php echo $menu[$i]['items'][$j]['items'][$k]['href']; ?>" <?php echo ($menu[$i]['items'][$j]['items'][$k]['target'] == '_blank') ? 'target="_blank"' : ''; ?>><?php echo $menu[$i]['items'][$j]['items'][$k]['titulo']; ?></a></li>
							<?php endif; ?>
							<?php endif; ?>
							<?php endif; ?>
							<?php endif; ?>
							<?php endfor; ?>
						</ul>
					</div>
					<?php $count++; ?>
					<?php endif; ?>
					<?php endif; ?>
					<?php endif; ?>
					<?php endif; ?>
					<?php else: ?>
					<?php if(!isset($menu[$i]['items'][$j]['modulo']) || ($menu[$i]['items'][$j]['modulo'] == '1')): ?>
					<?php if(!isset($menu[$i]['items'][$j]['cargos']) || in_array($carg[0], $menu[$i]['items'][$j]['cargos']) || in_array($carg[1], $menu[$i]['items'][$j]['cargos']) || in_array($carg[2], $menu[$i]['items'][$j]['cargos']) || in_array($carg[3], $menu[$i]['items'][$j]['cargos']) || in_array($carg[4], $menu[$i]['items'][$j]['cargos'])): ?>
					<?php if(!isset($menu[$i]['items'][$j]['ncargos']) || !in_array($carg[0], $menu[$i]['items'][$j]['ncargos']) && !in_array($carg[1], $menu[$i]['items'][$j]['ncargos']) && !in_array($carg[2], $menu[$i]['items'][$j]['ncargos']) && !in_array($carg[3], $menu[$i]['items'][$j]['ncargos']) && !in_array($carg[4], $menu[$i]['items'][$j]['ncargos'])): ?>
					<?php if(!isset($menu[$i]['items'][$j]['meses']) || in_array(date('n'), $menu[$i]['items'][$j]['meses'])): ?>
					<li><a href="<?php echo $menu[$i]['items'][$j]['href']; ?>" <?php echo ($menu[$i]['items'][$j]['target'] == '_blank') ? 'target="_blank"' : ''; ?>><?php echo $menu[$i]['items'][$j]['titulo']; ?></a></li>
					<?php endif; ?>
					<?php endif; ?>
					<?php endif; ?>
					<?php endif; ?>
					<?php endif; ?>
					<?php endfor; ?>
				</ul>
				<?php endif; ?>
	    </div>
	  </div>
	</div>
<?php $nmenu++; ?>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php endfor; ?>
</div>

<?php
// Eliminamos las variables usadas
unset($menu);
unset($paginas_interes);
unset($nmenu);
unset($count);
unset($web_delegacion);
?>

<!-- FIN MENU-LATERAL -->
