<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1,7));

if (file_exists('config.php')) {
	include('config.php');
}

require("../../pdf/pdf_js.php");
//require("../pdf/mc_table.php");

class PDF_AutoPrint extends PDF_JavaScript
{
function AutoPrint($dialog=false)
{
    //Open the print dialog or start printing immediately on the standard printer
    $param=($dialog ? 'true' : 'false');
    $script="print($param);";
    $this->IncludeJS($script);
}

function AutoPrintToPrinter($server, $printer, $dialog=false)
{
    //Print on a shared printer (requires at least Acrobat 6)
    $script = "var pp = getPrintParams();";
    if($dialog)
        $script .= "pp.interactive = pp.constants.interactionLevel.full;";
    else
        $script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
    $script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
    $script .= "print(pp);";
    $this->IncludeJS($script);
}
}
define ( 'FPDF_FONTPATH', '../../pdf/font/' );
# creamos el nuevo objeto partiendo de la clase ampliada
$MiPDF = new PDF_AutoPrint();
$MiPDF->SetMargins ( 20, 20, 20 );
# ajustamos al 100% la visualizaciÃ³n
$MiPDF->SetDisplayMode ( 'fullpage' );
// Consulta  en curso. 
if (substr($curso, 0, 1) == '1') {
	$mas = ", colegio";
}
$n_curso = substr($curso, 0, 1);
$result0 = mysqli_query($db_con, "select distinct id_matriculas from matriculas_bach_temp, matriculas_bach where id=id_matriculas order by curso".$mas.", letra_grupo, apellidos, nombre" );
while ($id_ar = mysqli_fetch_array($result0)) {
$id = $id_ar[0];
$result = mysqli_query($db_con, "select * from matriculas_bach where id = '$id'");
if ($datos_ya = mysqli_fetch_object ( $result )) {

$naci = explode("-",$datos_ya->nacimiento);
$nacimiento = "$naci[2]-$naci[1]-$naci[0]";
$apellidos = $datos_ya->apellidos; $id = $datos_ya->id; $nombre = $datos_ya->nombre; $nacido = $datos_ya->nacimiento; $provincia = $datos_ya->provincia; $domicilio = $datos_ya->domicilio; $localidad = $datos_ya->localidad; $dni = $datos_ya->dni; $padre = $datos_ya->padre; $dnitutor = $datos_ya->dnitutor; $madre = $datos_ya->madre; $dnitutor2 = $datos_ya->dnitutor2; $telefono1 = $datos_ya->telefono1; $telefono2 = $datos_ya->telefono2; $colegio = $datos_ya->colegio; $correo = $datos_ya->correo; $otrocolegio = $datos_ya->otrocolegio; $letra_grupo = $datos_ya->letra_grupo; $religion = $datos_ya->religion; $observaciones = $datos_ya->observaciones; $promociona = $datos_ya->promociona; $transporte = $datos_ya->transporte; $ruta_este = $datos_ya->ruta_este; $ruta_oeste = $datos_ya->ruta_oeste; $sexo = $datos_ya->sexo; $hermanos = $datos_ya->hermanos; $nacionalidad = $datos_ya->nacionalidad; $claveal = $datos_ya->claveal; $curso = $datos_ya->curso;  $itinerario1 = $datos_ya->itinerario1; $itinerario2 = $datos_ya->itinerario2; $optativa1 = $datos_ya->optativa1; $optativa2 = $datos_ya->optativa2; $optativa2b1 = $datos_ya->optativa2b1; $optativa2b2 = $datos_ya->optativa2b2; $optativa2b3 = $datos_ya->optativa2b3; $optativa2b4 = $datos_ya->optativa2b4; $optativa2b5 = $datos_ya->optativa2b5; $optativa2b6 = $datos_ya->optativa2b6; $optativa2b7 = $datos_ya->optativa2b7; $optativa2b8 = $datos_ya->optativa2b8; $optativa2b9 = $datos_ya->optativa2b9; $optativa2b10 = $datos_ya->optativa2b10; $repetidor = $datos_ya->repite;$revisado = $datos_ya->revisado; $confirmado = $datos_ya->confirmado; $grupo_actual = $datos_ya->grupo_actual; $idioma1 = $datos_ya->idioma1; $idioma2 = $datos_ya->idioma2; $bilinguismo = $datos_ya->bilinguismo; $optativa151 = $datos_ya->optativa151; $optativa152 = $datos_ya->optativa152; $optativa121 = $datos_ya->optativa121;
	
	if (stristr($optativa2, "Empresa")) { $optativa2 ="- ECE";} elseif (stristr($optativa2, "Griego")) { $optativa2 ="- GRI";} else{$optativa2 ="";}
	$apellidos = "Apellidos del Alumno: ". $apellidos;
	 $nombre= "Nombre: ".$nombre;
	 $nacido= "Nacido en: ".$nacido;
	 $provincia= "Provincia de: ".$provincia;
	 $fecha_nacimiento= "Fecha de Nacimiento: $nacimiento";
	 $domicilio= "Domicilio: ".$domicilio;
	 $localidad= "Localidad: ".$localidad;
	 $dni= "DNI del alumno: ".$dni;
	 $padre= "Apellidos y nombre del Tutor legal 1: ".$padre;
	 $dnitutor= "DNI: ".$dnitutor;
	 $madre= "Apellidos y nombre del Tutor legal 2: ".$madre;
	 $dnitutor2= "DNI: ".$dnitutor2;
	 $telefono1= "Teléfono Casa: ".$telefono1;
	 $telefono2= "Teléfono Móvil: ".$telefono2;
	 $telefonos="$telefono1\n   $telefono2";

	 if ($datos_ya->colegio == "Otro Centro") { $colegio= "".$datos_ya->otrocolegio; }else{	 $colegio= "".$datos_ya->colegio." (".$datos_ya->letra_grupo.")"; }
	 $correo= "Correo electrónico de padre o madre: ".$datos_ya->correo;
	 // Optativas y refuerzos
	 $n_curso = substr($curso, 0, 1);
	 $n_curso2 = $n_curso-1;

	$observaciones= "OBSERVACIONES: ".$observaciones;
	 $texto_exencion= "El alumno solicita la exención de la Asignatura Optativa";
	 $texto_bilinguismo= "El alumno solicita participar en el Programa de Bilingüismo";
	 $fecha_total = $fecha;
	 $texto_transporte = "Transporte escolar: $ruta_este$ruta_oeste.";
	 if ($hermanos == '' or $hermanos == '0') { $hermanos = ""; }
	 
}
$fech = explode(" ",$fecha_total);
$fecha = $fech[0];
$datos_junta = "PROTECCIÓN DE DATOS.\n En cumplimiento de lo dispuesto en la Ley Orgánica 15/1999, de 13 de Diciembre, de Protección de Datos de Carácter Personal, la Consejería de Educación y Deporte le informa que los datos personales obtenidos mediante la cumplimentación de este formulario y demás documentación que se adjunta van a ser incorporados, para su tratamiento, al fichero 'Séneca. Datos personales y académicos del alumnado', con la finalidad de recoger los datos personales y académicos del alumnado que cursa estudios en centros dependientes de la Conserjería de Educación, así como de las respectivas unidades familiares.\n De acuerdo con lo previsto en la Ley, puede ejercer los derechos de acceso, rectificación, cancelación y oposición dirigiendo un escrito a la Secretaría General Técnica de la Conserjería de Educación de la Junta de Andalucía en Avda. Juan Antonio de Vizarrón, s/n, Edificio Torretriana 41071 SEVILLA";

// Formulario de la junta	
for($i=1;$i<2;$i++){
	$MiPDF->Addpage ();
	#### Cabecera con dirección
	$MiPDF->SetFont ( 'Times', 'B', 10  );
	$MiPDF->SetTextColor ( 0, 0, 0 );
	$MiPDF->SetFillColor(230,230,230);
	$MiPDF->Image ( '../../img/encabezado2.jpg', 10, 10, 180, '', 'jpg' );
	$MiPDF->Ln ( 15 );
	$titulo1 = "SOLICITUD DE MATRÍCULA EN ".$n_curso."º DE BACHILLERATO";
	$MiPDF->Multicell ( 0, 4, $titulo1, 0, 'C', 0 );
	$MiPDF->Ln ( 5 );
	$MiPDF->SetFont ( 'Times', '', 10 );
	$MiPDF->Cell(5,6,"1",1,0,'C',1);
	$MiPDF->Cell(163,6,"DATOS PERSONALES DEL ALUMNO",1,0,'C',1);
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(84,5,"APELLIDOS",0,0,"C");
	$MiPDF->Cell(84,5,"NOMBRE",0,0,"C");
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(84,5,$datos_ya->apellidos,1,0,'C');
	$MiPDF->Cell(84,5,$datos_ya->nombre,1,0,'C');
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(40,5,"FECHA NACIMIENTO",0,0,"C");
	$MiPDF->Cell(26,5,"DNI/NIE",0,0,"C");
	$MiPDF->Cell(26,5,"TELÉFONO",0,0,"C");
	$MiPDF->Cell(35,5,"NACIONALIDAD",0,0,"C");
	$MiPDF->Cell(21,5,"HERMANOS",0,0,"C");
	$MiPDF->Cell(20,5,"SEXO",0,0,"C");
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(40,5,$nacimiento,1,0,'C');
	$MiPDF->Cell(26,5,$datos_ya->dni,1,0,'C');
	$MiPDF->Cell(26,5,$datos_ya->telefono1,1,0,'C');
	$MiPDF->Cell(35,5,$nacionalidad,1,0,'C');
	$MiPDF->Cell(21,5,$hermanos,1,0,'C');
	$MiPDF->Cell(20,5,$sexo,1,0,'C');
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(100,5,"DOMICILIO",0,0,"C");
	$MiPDF->Cell(25,5,"LOCALIDAD",0,0,"C");
	$MiPDF->Cell(15,5,"C.P.",0,0,"C");
	$MiPDF->Cell(28,5,"PROVINCIA",0,0,"C");
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(100,5,$datos_ya->domicilio,1,0,'C');
	$MiPDF->Cell(25,5,$datos_ya->localidad,1,0,'C');
	$MiPDF->Cell(15,5,$config['centro_codpostal'],1,0,'C');
	$MiPDF->Cell(28,5,$config['centro_provincia'],1,0,'C');
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(168,5,"CORREO ELECTRÓNICO DE CONTACTO",0,0,'C');
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(168,5,$datos_ya->correo,1,0,'C');
	
	$MiPDF->Ln ( 9 );
	$MiPDF->Cell(5,6,"2",1,0,'C',1);
	$MiPDF->Cell(163,6,"DATOS DE LOS REPRESENTANTES LEGALES DEL ALUMNO",1,0,'C',1);
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(140,5,"APELLIDOS Y NOMBRE DEL REPRESENTANTE LEGAL 1(con quien este convive)",0,0,"C");
	$MiPDF->Cell(28,5,"DNI/NIE",0,0,"C");
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(140,5,$datos_ya->padre,1,0,'C');
	$MiPDF->Cell(28,5,$datos_ya->dnitutor,1,0,'C');
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(140,5,"APELLIDOS Y NOMBRE DEL REPRESENTANTE LEGAL 2",0,0,"C");
	$MiPDF->Cell(28,5,"DNI/NIE",0,0,"C");
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(140,5,$datos_ya->madre,1,0,'C');
	$MiPDF->Cell(28,5,$datos_ya->dnitutor2,1,0,'C');

	
	$MiPDF->Ln ( 9 );
	$MiPDF->Cell(5,6,"3",1,0,'C',1);
	$MiPDF->Cell(163,6,"DATOS DE MATRÍCULA",1,0,'C',1);
	$MiPDF->Ln ( 8 );
	$MiPDF->Cell(76,5,"CENTRO DE ORIGEN",0,0,"C");
	$MiPDF->Cell(46,5,"LOCALIDAD",0,0,"C");
	$MiPDF->Cell(46,5,"CODIGO",0,0,"C");
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(76,5,$colegio,1,0,'C');
	$MiPDF->Cell(46,5,$config['centro_localidad'],1,0,'C');
	$MiPDF->Cell(46,5,$config['centro_codigo'],1,0,'C');
	$MiPDF->Ln ( 10 );

	if(stristr($religion,"Valores")==TRUE){$religion="Educación para la Ciudadanía";}

	if ($curso=="2BACH") {
	$MiPDF->Cell(168,6,"RELIGIÓN O ALTERNATIVA",0,0,'C');
	$MiPDF->Ln ( 6);
	$MiPDF->Cell(168,5,$religion,1,0,'C');	
	}
	else{
	$MiPDF->Cell(168,6,"RELIGIÓN O ALTERNATIVA",0,0,'C');
	$MiPDF->Ln ( 6);
	$MiPDF->Cell(168,5,$religion,1,0,'C');
	}

	$MiPDF->Ln ( 7 );	//echo $itinerario;
	if ($n_curso == '4') { $extra="4ESO (It. $itinerario)";}else{$extra=$curso;}
	$MiPDF->Cell(70,5,"CURSO EN QUE SE MATRICULA",0,0,"C");
	$MiPDF->Cell(98,5,"MATERIAS DE LA MODALIDAD",0,0,"C");
	$MiPDF->Ln ( 5 );
	
	
	
	// Optativas extra de 1 de bach.
	$opt="";
	$opta1="";
	$opta2="";
	$nombre_opta="";
	$nombre_opt="";
	$bil="";
    if($bilinguismo=="Si"){$bil = "Enseñanza Bilingüe\n";}
    
         	if ($n_curso=="1") {
                foreach($it1 as $num_it=>$nombre_it){
                    if($num_it==$itinerario1){
                        $mod_registro = $nombre_it;
                    }
                }
                    
				for ($i = 1; $i < 6; $i++) {
                    if($itinerario1 == $i){
					foreach(${opt1.$i} as $abrev_opt=>$nombre_opt){ 
                         if($optativa1==$abrev_opt){
                                $opt = $nombre_opt;
                         }
                    }	
                    $opt=$opta1.$opt;				
                    }
	            }
				if($itinerario1==5){
					$opt = $opt.", ".$optativa151.", ".$optativa152;
				}
				if($itinerario1==2){
					$opt = $opt.", ".$optativa121;
				}
			}
	
	if ($n_curso=="2") {
		$mod_registro = ${it2.$itinerario2}[1];
		$n_z="";
		$opt_2b = "";
		$opt2b = "";
			foreach (${opt2.$itinerario2} as $key=>$val){
				$n_z+=1;		
				$opt_b = mysqli_query($db_con, "select optativa2b$n_z from matriculas_bach where id = '$id'");
				$o_b = mysqli_fetch_array($opt_b);
				$opt2b .= $o_b[0].": ".$val."; ";				
				}

		// Optativas extra de 2 de bach.
				$num_z="";
				foreach ($opt_aut2 as $key=>$val){
				$num_z+=1;		
				$opt_bach = mysqli_query($db_con, "select opt_aut2".$num_z." from matriculas_bach where id = '$id'");
				//echo "select opt_aut2".$num_z." from matriculas_bach where id = '$id'<br>";
				$o_bach = mysqli_fetch_array($opt_bach);
				$opt_2b .= $o_bach[0].": ".$val."; ";				
				}
			}
		//$opt_o = "\nOptativas de Bachillerato".$opt_2b;
	if ($curso=="1BACH") {
	$MiPDF->Cell(80,5,$n_curso."º DE BACH. ( ".$mod_registro." )",1,0,'C');
	$MiPDF->MultiCell(88,5,$bil.$opt,1);
	$MiPDF->Ln ( 2 );
	}

	if ($curso=="2BACH") {
	$MiPDF->Cell(80,5,$n_curso."º DE BACH. ( ".$mod_registro." ".$optativa2." )",1,0,'C');
	$MiPDF->MultiCell(88,5,$bil.$opt2b,1);
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(165,5,"MATERIAS OPTATIVAS",0,0,"C");
	$MiPDF->Ln ( 5 );
	$MiPDF->MultiCell(168,5,$opt_2b,1);
	$MiPDF->Ln ( 8 );

	if(!$repetidor){
	$MiPDF->Cell(165,5,"MODALIDAD Y OPTATIVAS DE 1º BACHILLERATO",0,0,"C");
	$MiPDF->Ln ( 5 );
	$MiPDF->Cell(80,5,"".$mod_registro." ",1,0,'C');
	$MiPDF->MultiCell(88,5,$bil.$optativa1,1);
	$MiPDF->Ln ( 2 );
	}
	}
  }
}
   $MiPDF->AutoPrint(true);     
   $MiPDF->Output ();

?>