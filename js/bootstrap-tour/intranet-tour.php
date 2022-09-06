<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed'); ?>

<script>
// Instance the tour
var tour = new Tour({

	onEnd: function() {
		localStorage.removeItem('tour_current_step');
	  return window.location.href = '//<?php echo $config['dominio']; ?>/intranet/index.php';
	},

	keyboard: true,

  steps: [
  {
    title: "<h1 class=\"text-center\">Primeros pasos</h1>",
    content: "<p>Antes de comenzar, realiza un tour por la página de Inicio de la Intranet para conocer las características de los módulos que la componen y la información de la que dispone.</p><p>Haz clic en <strong>Siguiente</strong> para continuar o haz clic en <strong>Omitir</strong> para saltarse el tour.",
    container: "body",
    template: "<div class='popover tour' style='max-width: 670px !important;'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='end'>Omitir</button><button class='btn btn-primary' data-role='next'>Siguiente »</button></div></div>",
    orphan: true,
    backdrop: true,
  },
  {
    element: "#bs-tour-usermenu",
    title: "Menú de usuario",
    content: "Desde este menú podrás volver a cambiar la contraseña, correo electrónico y fotografía.",
    container: "body",
    placement: "bottom",
    template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button></div></div>",
    backdrop: false,
  },
	{
    element: "#bs-tour-tareas",
    title: "Tareas pendientes",
    content: "Consulta las tareas que hayas marcado como pendientes. Puedes crear tareas o marcar mensajes recibidos como tarea para realizarla en cualquier momento. En el caso de que tengas tareas pendientes, este icono cambiará de color. Para ver las tareas pendientes y realizadas, haz clic en Ver tareas.",
    container: "body",
    placement: "bottom",
    template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button></div></div>",
    backdrop: false,
  },
  {
    element: "#bs-tour-mensajes",
    title: "Mensajes",
    content: "Consulta los últimos mensajes recibidos. Cuando recibas un mensaje, el icono cambiará de color para avisarte. Para leer el mensaje haz clic en este icono o dirígete a la página de Inicio de la Intranet para ver todos los avisos.",
    container: "body",
    placement: "bottom",
    template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button></div></div>",
    backdrop: false,
  },
  {
    element: "#bs-tour-consejeria",
    title: "Consejería de Desarrollo Educativo y Formación Profesional",
    content: "Consulta las últimas novedades de la Consejería de Desarrollo Educativo y Formación Profesional de la Junta de Andalucía o accede al portal Séneca.",
    container: "body",
    placement: "bottom",
    template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button></div></div>",
    backdrop: false,
  },
  {
    element: "#bs-tour-menulateral",
    title: "Menú de opciones",
    content: "Según tu perfil de trabajo tendrás un menú con las opciones que necesitas en tu día a día.<br>Desde el menú <strong>Consultas</strong> tendrás acceso a la información de los alumnos, horarios, estadísticas y fondos de la Biblioteca del centro.<br>El menú <strong>Trabajo</strong> contiene las acciones de registro de Problemas de Convivencia, Faltas de Asistencia, Informes de tareas, Informes de tutoría, Reservas de aulas y medios audiovisuales, Ausencias, etc.<br>El menú <strong>Departamento</strong> contiene las opciones necesarias para la gestión de tu departamento.<br>Y por último, <strong>Páginas de interes</strong> contiene enlaces a páginas externas de información y recursos educativos.",
    container: "body",
    placement: "right",
    template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button></div></div>",
    backdrop: true,
  },
  {
    element: "#bs-tour-ausencias",
    title: "Profesores de baja",
    content: "Este módulo ofrece información sobre los profesores ausentes en el día. Si el profesor ha registrado tareas para los alumnos aparecerá marcado con el icono chequeado. Para registrar una ausencia debes dirigirte al menú <strong>Trabajo</strong>, <strong>Profesores ausentes</strong>.",
    container: "body",
    placement: "right",
    template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button></div></div>",
    backdrop: true,
  },
  {
    element: "#bs-tour-tareas-2",
    title: "Tareas pendientes",
    content: "Consulta las tareas que hayas marcado como pendientes. Este módulo solo aparece en la página de inicio. Puedes crear tareas o marcar mensajes recibidos como tarea para realizarla en cualquier momento. Para ver las tareas pendientes y realizadas, haz clic en Ver tareas.",
    container: "body",
    placement: "right",
    template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button></div></div>",
    backdrop: true,
  },
  {
    element: "#bs-tour-pendientes",
    title: "Notificaciones",
    content: "Aquí aparecerán las notificaciones de mensajes sin leer, informes de tareas y tutorías pendientes de rellenar.",
    container: "body",
    placement: "bottom",
    template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button></div></div>",
    backdrop: true,
  },
  {
    element: "#bs-tour-buscar",
    title: "Buscar alumnos y noticias",
    content: "Este buscador te permite buscar alumnos para consultar su expediente o realizar alguna acción como registrar un Problema de Convivencia o Intervención. Puedes buscar tanto por nombre como apellidos. <br>Si presionas la tecla <kbd>Intro</kbd> buscará coincidencias en las noticias publicadas.",
    container: "body",
    placement: "left",
    template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button></div></div>",
    backdrop: true,
  },
  {
    element: "#bs-tour-calendario",
    title: "Calendario",
    content: "El calendario mostrará información sobre los eventos del Centro, Actividades extraescolares y tus anotaciones personales. Cada evento está identificado con una bola de color; al pasar el ratón por encima aparecerá la descripción del evento. Debajo del calendario aparerán los eventos programados para el día de hoy. Para programar un evento haz clic en <strong>Ver calendario</strong> o dirígite al menú <strong>Trabajo</strong>, <strong>Calendario</strong>, <strong>Ver calendario</strong>.",
    container: "body",
    placement: "left",
    <?php if($config['mod_horarios'] and ($n_curso > 0)): ?>
    template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button></div></div>",
    <?php else: ?>
    template: "<div class='popover tour' style='max-width: 600px !important;'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button><button class='btn btn-primary' data-role='end'>Entendido</button></div></div>",
    <?php endif; ?>
    backdrop: true,
  },
  <?php if($config['mod_horarios'] and ($n_curso > 0)): ?>
  {
    element: "#bs-tour-horario",
    title: "Horario y cuaderno de notas",
    content: "Por último, el horario contiene enlaces para acceder al <strong>Cuaderno de notas</strong>, si se trata de una asignatura, o a la <strong>Gestión de guardias</strong> si es un Servicio de guardia.",
    container: "body",
    placement: "left",
    template: "<div class='popover tour' style='max-width: 600px !important;'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Anterior</button>&nbsp;<button class='btn btn-default' data-role='next'>Siguiente »</button><button class='btn btn-primary' data-role='end'>Entendido</button></div></div>",
    backdrop: true,
  }
  <?php endif; ?>
  ],
 	});



// Initialize the tour
tour.init();

// Start the tour
tour.start(true);
</script>
