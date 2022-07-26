<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed'); 

$pa = explode(", ", $row[10]);
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
Alumno/a: $row[3] $row[2]

De acuerdo con la Ley de Protección de Datos de Carácter Personal y la Ley de Protección de mi derecho al honor, a mi intimidad personal y familiar y a mi propia imagen y como alumno o alumna del centro de enseñanza ".$config['centro_denominacion'].$autoriza_imagen.".
Que la Secretaría General Técnica de la Consejería de Desarrollo Educativo y Formación Profesional de la Junta de Andalucía publique mi imagen en la página web para la promoción y difusión de las actividades culturales, recreativas, deportivas y sociales en las que participa el propio centro Este consentimiento tendrá validez mientras permanezca escolarizado en el centro de enseñanza.
Tengo derecho a saber, en cualquier momento, qué datos personales míos (incluyendo mi imagen) guarda la Secretaría General Técnica de la Consejería de Desarrollo Educativo y Formación Profesional de la Junta de Andalucía y para qué, modificarlos si éstos han cambiado, o borrarlos (en los casos que ello fuera legalmente posible). Para ello, deberé dirigirme por escrito a la Secretaría General Técnica de la Consejería de Desarrollo Educativo y Formación Profesional de la Junta de Andalucía, con dirección en Avda. Juan Antonio de Vizarrón, s/n, Edificio Torretriana. 41071, Sevilla.
 ";

$autoriza_rgpd="
De conformidad con lo establecido en los artículos 6.1 y 11.1 de la Ley Orgánica 15/1999, de 13 de diciembre, de Protección de Datos de Carácter Personal y en el art. 2.2 de la Ley Orgánica 1/1982, de 5 de mayo, de protección civil del derecho al honor, a la intimidad personal y familiar y a la propia imagen,

CONSIENTE EXPRESAMENTE:
A la Secretaría General Técnica de la Consejería de Desarrollo Educativo y Formación Profesional de la Junta de Andalucía, con dirección en Avda. Juan Antonio de Vizarrón, s/n, Edificio Torretriana. 41071, Sevilla, a proceder a la publicación de la imagen de su hijo/a o menor cuya representación legal ostenta, en la página web del centro de enseñanza, con la finalidad de promoción y difusión en los sitios web de los centros y servicios educativos de las actividades culturales, recreativas, deportivas y sociales en las que participa el propio centro. Este consentimiento tendrá validez mientras su hijo/a permanezca escolarizado en el centro de enseñanza.
De igual manera, reconoce haber sido informado de la posibilidad de ejercitar los correspondientes derechos de acceso, rectificación, cancelación y oposición, de conformidad con lo establecido en la Ley Orgánica 15/1999, de 13 de diciembre, de Protección de Datos de Carácter Personal, en el Real Decreto 1720/2007, de 21 de diciembre, por el que se aprueba el Reglamento de desarrollo de la Ley Orgánica 15/1999, de 13 de diciembre de Protección de Datos de Carácter Personal, y en la Instrucción 1/1998, de 19 de enero, de la Agencia Española de Protección de Datos, relativa al ejercicio de los derechos de acceso, rectificación, cancelación y oposición.
El responsable del citado tratamiento es la Secretaría General Técnica de la Consejería de Desarrollo Educativo y Formación Profesional de la Junta de Andalucía, con dirección en Avda. Juan Antonio de Vizarrón, s/n, Edificio Torretriana. 41071, Sevilla. 
";

$titulo5 = "En ".$config['centro_localidad'].", a $hoy

Firmado. D./Dª
";

// Edad del alumno
$menores_14 = date('Y-m-d',strtotime('-14 year'));
$fecha_nacimiento = cambia_fecha($nacimiento);

//echo "$fecha_nacimiento >= $hoy";
$result = mysqli_query($db_con, "SELECT apellidos, nombre, nacimiento, CONCAT( curso, '-', grupo_actual ) FROM matriculas_bach where date(nacimiento) < '$hoy' order by apellidos, nombre, curso, grupo_actual");
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
	if ($fecha_nacimiento <= $menores_14) {
		$MiPDF->Multicell ( 0, 6, $autoriza_fotos, 0, 'L', 0 );
	}
	else{
		$MiPDF->Multicell ( 0, 6, $autoriza_rgpd, 0, 'L', 0 );
	}
	
	$MiPDF->Ln ( 1 );
	$MiPDF->Multicell ( 0, 6, $titulo5, 0, 'C', 0 );
	$MiPDF->Ln ( 3 );

$titulo_religion = "SOLICITUD PARA CURSAR LAS ENSEÑANZAS DE RELIGIÓN";
$an = substr($config['curso_actual'],0,4);
$an1 = $an+1;
$an2 = $an+2;
$c_escolar = $an1."/".$an2;
$autoriza_religion="
D./Dª $papa, como padre, madre o tutor legal del alumno/a $row[3] $row[2] del curso ".$n_curso."º de ESO del ".$config['centro_denominacion'].", en desarrollo de la Ley Orgánica 2/2006 de 3 de Mayo, de Educación, modificada por la Ley Orgánica 8/2013, de 9 de diciembre, para la mejora de la calidad educativa.

SOLICITA:
Cursar a partir del curso escolar $c_escolar. mientras no modifique expresamente esta decisión, la enseñanza de $religion";
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
	?>