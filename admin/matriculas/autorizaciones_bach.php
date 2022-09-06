<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed'); 

$pa = explode(", ", $datos_ya->padre);
$papa = "$pa[1] $pa[0]";
$hoy = formatea_fecha(date('Y-m-d'));

$titulo4 = "AUTORIZACIÓN  PARA PUBLICACIÓN DE FOTOS Y GRABACIONES";
$titulo_rgpd = "CONSENTIMIENTO PARA LA PUBLICACIÓN DE IMÁGENES DEL ALUMNO";
if ($foto_alumno==1) {
	$autoriza_imagen = "
	|X| QUIERO
	|_| NO QUIERO";
}
else{
	$autoriza_imagen = "
	|_| QUIERO
	|_| NO QUIERO";
}

$autoriza_fotos="
Alumno/a: $datos_ya->nombre $datos_ya->apellidos

De acuerdo con la Ley de Protección de Datos de Carácter Personal y la Ley de Protección de mi derecho al honor, a mi intimidad personal y familiar y a mi propia imagen y como alumno o alumna del centro de enseñanza ".$config['centro_denominacion'].$autoriza_imagen.".
Que la Secretaría General Técnica de la Consejería de Desarrollo Educativo y Formación Profesional de la Junta de Andalucía publique mi imagen en la página web para la promoción y difusión de las actividades culturales, recreativas, deportivas y sociales en las que participa el propio centro Este consentimiento tendrá validez mientras permanezca escolarizado en el centro de enseñanza.
Tengo derecho a saber, en cualquier momento, qué datos personales míos (incluyendo mi imagen) guarda la Secretaría General Técnica de la Consejería de Desarrollo Educativo y Formación Profesional de la Junta de Andalucía y para qué, modificarlos si éstos han cambiado, o borrarlos (en los casos que ello fuera legalmente posible). Para ello, deberé dirigirme por escrito a la Secretaría General Técnica de la Consejería de Desarrollo Educativo y Formación Profesional de la Junta de Andalucía, con dirección en Avda. Juan Antonio de Vizarrón, s/n, Edificio Torretriana. 41071, Sevilla.
 ";

$titulo5 = "En ".$config['centro_localidad'].", a $hoy


Firmado. D./Dª
";

// Fotos
	$MiPDF->Addpage ();
	#### Cabecera con dirección
	$MiPDF->SetFont ( 'Times', 'B', 11  );
	$MiPDF->SetTextColor ( 0, 0, 0 );
	$MiPDF->SetFillColor(230,230,230);
	#Cuerpo.
	$MiPDF->Image ( '../../img/encabezado2.jpg', 10, 10, 180, '', 'jpg' );
	$MiPDF->Ln ( 12 );
	$MiPDF->Cell(168,5,$titulo4,0,0,'C');
	$MiPDF->SetFont ( 'Times', '', 10  );	
	$MiPDF->Ln ( 4 );
	$MiPDF->Multicell ( 0, 6, $autoriza_fotos, 0, 'L', 0 );
	$MiPDF->Ln ( 1 );
	$MiPDF->Multicell ( 0, 6, $titulo5, 0, 'C', 0 );
	$MiPDF->Ln ( 3 );

$titulo_religion = "SOLICITUD PARA CURSAR LAS ENSEÑANZAS DE RELIGIÓN";
$an = substr($config['curso_actual'],0,4);
$an1 = $an+1;
$an2 = $an+2;
$c_escolar = $an1."/".$an2;
$autoriza_religion="
D./Dª $papa, como padre, madre o tutor legal del alumno/a $row[3] $row[2] del curso ".$n_curso."º de Bachillerato del ".$config['centro_denominacion'].", en desarrollo de la Ley Orgánica 2/2006 de 3 de Mayo, de Educación, modificada por la Ley Orgánica 8/2013, de 9 de diciembre, para la mejora de la calidad educativa.

SOLICITA:
Cursar a partir del curso escolar $c_escolar. mientras no modifique expresamente esta decisión, la enseñanza de Religión:
x $religion
";
$firma_religion = "En ".$config['centro_localidad'].", a $hoy


Firmado. D./Dª";

// Religion

if (substr($religion, 0, 1)=="R") {
	$MiPDF->Cell(168,4,"----------------------------------------------------------------------------------------------------------------------------------------",0,0,'C');
	$MiPDF->Ln ( 10 );
	$MiPDF->SetFont ( 'Times', 'B', 11  );
	$MiPDF->Cell(168,5,$titulo_religion,0,0,'C');
	$MiPDF->SetFont ( 'Times', '', 10  );	
	$MiPDF->Ln ( 3 );
	$MiPDF->Multicell ( 0, 6, $autoriza_religion, 0, 'L', 0 );
	$MiPDF->Ln ( 2 );
	$MiPDF->Multicell ( 0, 6, $firma_religion, 0, 'C', 0 );
	$MiPDF->Ln ( 5 );

}

// Salida de Bachillerato

$titulo_salida = "AUTORIZACIÓN EN LA ENSEÑANZA POSTOBLIGATORIA PARA PODER SALIR DEL CENTRO ";

$autoriza_salida="
D./Dª $papa, como padre, madre o tutor legal del alumno/a ".$datos_ya->nombre." ".$datos_ya->apellidos." del curso ".$n_curso."º de Bachillerato del ".$config['centro_denominacion']." 

AUTORIZA al centro educativo a que el profesor de guardia, ante la ausencia de un profesor, le permita al alumno la salida del centro en la parte final del tramo lectivo.

";
$firma_salida = "En ".$config['centro_localidad'].", a $hoy


Firmado. D./Dª";

// Religion
	if (substr($religion, 0, 1)!=="R") {
	$MiPDF->Cell(168,4,"----------------------------------------------------------------------------------------------------------------------------------------",0,0,'C');
	}
	$MiPDF->Ln ( 10 );
	$MiPDF->SetFont ( 'Times', 'B', 11  );
	$MiPDF->Cell(168,5,$titulo_salida,0,0,'C');
	$MiPDF->SetFont ( 'Times', '', 10  );	
	$MiPDF->Ln ( 8 );
	$MiPDF->Multicell ( 0, 6, $autoriza_salida, 0, 'L', 0 );
	$MiPDF->Ln ( 2 );
	$MiPDF->Multicell ( 0, 6, $firma_salida, 0, 'C', 0 );
	$MiPDF->Ln ( 5 );

	?>