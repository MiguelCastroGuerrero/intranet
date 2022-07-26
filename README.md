# [Intranet del Monterroso](http://iesmonterroso.github.io/intranet/)

Lo que aquí llamamos la Intranet del Monterroso es una aplicación Web creada y probada a lo largo de los años en nuestro centro. Nació dentro del Programa de Autoevaluación y Mejora de la Consejería de Desarrollo Educativo y Formación Profesional de la Junta de Andalucía, y ha sido pensada para facilitar y simplificar el trabajo diario de los profesores en general.

En realidad se trata de un conjunto de módulos interconectados que responden a tareas específicas que debe realizar un profesor. Los módulos han ido ido surgiendo de necesidades concretas planteadas por los propios profesores a lo largo de los años. Precisamente por eso, debe quedar claro que la aplicación solo expresa un punto de vista, el nuestro, acerca de cómo se lleva un instituto (Problemas e convivencia, comunicación entre los profesores, trabajo de los tutores, reservas de aulas y material, actividades extraescolares, etc.).

La Intranet se ha ajustado a las exigencias y necesidades del IES Monterroso, pero a lo largo de los años hemos podido comprobar que tanto las tareas como los procedimientos para resolverlas son en general comunes a la mayoría de los institutos de Andalucía, razón por la que hemos decidido ofrecer esta aplicación.


## Requisitos

* Servidor Web: [Apache (versión 2 o superior)](http://httpd.apache.org/)
* Versión de PHP: [PHP 7.2 o 7.3](http://www.php.net/)
* Base de datos: [MySQL 5.5](http://www.php.net/)

## Módulos

La Intranet funciona a partir de un conjunto de datos que son importados desde Séneca: profesores, alumnos, asignaturas, etc. La compatibilidad con Séneca es tan alta como lo permite la propia aplicación de la Junta de Andalucía.

Dispone de un sencillo proceso de instalación que permite ajustar los datos esenciales a un centro educativo determinado, y un sistema de importación de datos a partir de Séneca que en pocos minutos la pone a funcionar.

Los módulos que contiene son los siguientes:

* Consulta que presentan datos de los alumnos, listas de grupos y horarios tanto de grupos como de profesores y aulas
* Registro de problemas de convivencia, expulsiones, etc. (con posibilidad de envío de correo electrónico y SMS a padres o tutores legales)
* Registro y justificaciones de faltas de asistencia de los alumnos, que posteriormente pueden ser exportadas a Séneca (con posibilidad de envío de correo electrónico y SMS a padres o tutores legales)
* Informes de tutoría para el tutor que recibe la visita de los padres de un alumno, y pide a los miembros de su equipo educativo que le digan cómo va el alumno en su asignatura
* Informes de tareas para un alumno que va a ser expulsado del centro durante un tiempo, activado por un tutor o el jefe de estudios
* Página de control de tutorias de un grupo, donde el tutor puede ver todos los datos relevantes de los alumnos de su tutoría (faltas de asistencia, problemas de convivencia, tareas por expulsión, visitas de padres, actividades extraescolares, etc.), así como registrar las distintas intervenciones que realiza en su tarea de tutor. Todo ello se presenta en un informe de tutoría que solo tendrá que imprimir a final de curso y presentar a la Dirección
* Mensajería interna entre los usuarios de la Intranet que permite enviar y recibir mensajes
* Sistema de envío de mensajes SMS, que se activará automáticamente en caso de problemas de convivencia, faltas de asistencia continuadas, o que se podrá utilizar para poner en contacto rápido al tutor o la Dirección con los padres o tutores legales de un alumno
* Administración de actividades extraescolares
* Administración de libros de texto. Permite crear la lista de los libros de texto por materia para los distintos niveles educativos. También permite  la importación y administración de los libros de textos del Programa de Gratuidad de Libros de Textos de la Junta de Andalucía.
* Generación de memorias para jefatura de estudios y tutores a final de curso
* Generación de actas de departamento y evaluaciones.
* Gestión de guardias y registro de bajas de los profesores
* Gestión de pedidos de material de los departamentos
* Inventario de material de los departamentos
* Sistema de reservas para las aulas, carros de portátiles y medios audiovisuales
* Gestión de incidencias con los ordenadores
* Gestión de morosos de la Biblioteca (con posibilidad de envío de correo electrónico y SMS a padres o tutores legales)
* Generación de los usuarios TIC para el alta masiva en Gesuser, en la plataforma educativa Moodle, Google Suite y Microsoft 365
* Informes y estadísticas de evaluaciones, problemas de convivencia, faltas de asistencia, guardias, ausencias de profesores y uso de los recursos TIC
* Cuaderno de notas, calendario personal y registro de tareas pendientes
* Repositorio de documentos públicos del centro y privados del profesor

## Instalación

* Creamos una carpeta llamada **intranet** en el directorio raíz del servidor web Apache y copiamos los archivos de la Intranet dentro de esta.
* Abrimos el navegador y nos dirigimos a la dirección Web de nuestro dominio, por ejemplo: [https://iesmonterroso.org/intranet/](https://iesmonterroso.org/intranet/). No se recomienda acceder utilizando la dirección IP local del servidor o nombre de equipo, a no ser que el acceso a la Intranet sea únicamente local dentro de su centro educativo.
* Es importante que el alojamiento o hosting Web donde vaya a instalar la Intranet cuente con certificado SSL para acceder mediante protocolo HTTPS. Aunque la aplicación funciona aunque no utilice certificado SSL, el centro educativo es responsable de cumplir con el Reglamento General de Protección de Datos y Ley Orgánica de Protección de Datos.
* La primera vez que accedemos a la Intranet se ejecuta el asistente de instalación, donde se comprobarán los siguientes requisitos: versión de PHP, límite de memoria, límite de subida de archivos, mostrar información de errores y permisos sobre creación y modificación de archivos y directorios. Si se cumplen estos requisitos podrá introducir los datos del centro educativo, conexión a la base de datos MySQL y configurar los módulos que desea utilizar. Al teminar la creación de la base de datos se generará una contraseña aleatoria para el usuario Administrador de la Intranet, que por defecto, el nombre de usuario es **admin**.

## Importación de datos

* En el menú lateral, diríjase a Administración de la Intranet y proceda a importar los datos en el orden que aparecen en el apartado **A principio de curso...**. Si necesita actualizar los datos utilice los enlaces del apartado **Actualización**. Es recomendable realizar una copia de seguridad para evitar pérdidas de información; dispone de un enlace en el apartado **Bases de datos** para tal fin.
* La importación de horarios es opcional para el uso de la Intranet, aunque sus funcionalidades se verán reducidas si decide no importarlos. La aplicación acepta archivos XML compatibles con Séneca, generados por cualquier generador de horarios; o archivo en formato DEL generado con [http://www.horw.es/index.php](HorW). También existe la posibilidad de crear los horarios manualmente en la Intranet si no dispone de los archivos de importación.
* Dentro del apartado **Personal del centro** encontrará las opciones para asignar los perfiles de los profesores, asignar y combinar las especialidades del personal en los Departamentos del centro, asignar las sustituciones, entre otras opciones.
* Los miembros Administradores de la Intranet tienen la posibilidad de poder cambiar de perfil en cualquier instante, con el objetivo de comprobar y depurar errores que los usuarios vayan encontrando. No debe asignarse este perfil al equipo directivo o persona que no sea encargada de administrar la Intranet con la intención de supervisar a los usuarios, ya que este comportamiento compromete la privacidad y está considerado delito de acceso ilícito.
* Los miembros del equipo directivo tienen acceso a cualquier módulo de la Intranet.
* La primera vez que los usuarios accedan a la Intranet deberán utilizar como usuario el usuario IdEA y contraseña de Séneca. La plataforma pedirá a registrar una dirección de correo electrónico y teléfono móvil que se utilizará para el envío de notificaciones.

## Privacidad y condiciones de uso

La aplicación registra la actividad de los usuarios según lo establecido en el Reglamento General de Protección de Datos (RGPD) y Ley Orgánica de Protección de Datos (LOPD).
* Los accesos a la Intranet serán almacenados en la tabla **reg_intranet** y contiene la siguiente información: identificador único de sesión, fecha y hora de inicio de sesión, nombre de usuario, dirección IP y agente de usuario con información sobre el dispositivo, nombre y versión del sistema operativo, nombre y versión del navegador Web.
* El registro de actividad será almacenado en la tabla **reg_paginas** y contiene la siguiente información: identificador único de registro de actividad, identificador único de sesión asociado al profesor, dirección URL del módulo de la Intranet al que ha accedido.
* Los usuarios podrán consultar los registros de actividad en la página **Consultar accesos**, dentro del menú de usuario, en la barra superior donde aparece el usuario IdEA.
* Los datos de actividad almacenados en las bases de datos de los centros no son cedidos a los autores de la Intranet, ni terceras personas o empresas.
* El centro educativo es responsable de responder a los derechos de acceso, rectificación, cancelación y oposición, conocidos por su acrónimo ARCO, sobre el control de los datos de su comunidad educativa, eximiendo de responsabilidad a los autores de la Intranet.


## Autores

* [Miguel Ángel García González](https://github.com/mgarcia39)
* [Rubén López Herrera](https://github.com/rubenlh91)
