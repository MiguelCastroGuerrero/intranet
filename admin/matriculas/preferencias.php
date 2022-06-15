<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1));

function limpiar_string($string)
{
	return trim(htmlspecialchars($string, ENT_QUOTES,'UTF-8'));
}

if (isset($_POST['btnGuardar'])) {
// Optativas ESO
	for ($i=1; $i<8; $i++) {
			if (isset($_POST['opt'.$i.'_0'])) {
				${array_opt.$i}="";
				for ($z=0; $z<9 ; $z++) {
					if (strlen($_POST['opt'.$i.'_'.$z.''])>0) {
						${n_opt.$i}++;
						${array_opt.$i}.= '"'.$_POST['opt'.$i.'_'.$z.''].'",';

					}
				}
				${array_opt.$i}=substr(${array_opt.$i}, 0, -1);
				${c_.$i}=${n_opt.$i};
			}
		}

// Actividades ESO
	for ($i=1; $i<4; $i++) {
			if (isset($_POST['a'.$i.'_0'])) {
				${array_a.$i}="";
				for ($z=0; $z<7 ; $z++) {
					if (strlen($_POST['a'.$i.'_'.$z.''])>0) {
						${n_a.$i}++;
						${array_a.$i}.= '"'.$_POST['a'.$i.'_'.$z.''].'",';
					}
				}
				${array_a.$i}=substr(${array_a.$i}, 0, -1);
				${ca_.$i}=${n_a.$i};
			}
		}

// Itinerarios y optativas de 4 ESO
		for ($i=1; $i<5; $i++) {
			if (isset($_POST['it4'.$i.'_0'])) {
				${array_it4.$i}="";
				for ($z=0; $z<6 ; $z++) {
					if (strlen($_POST['it4'.$i.'_'.$z.''])>0) {
						${n_it4.$i}++;
						${array_it4.$i}.= '"'.$_POST['it4'.$i.'_'.$z.''].'",';
					}
				}
				${array_it4.$i}=substr(${array_it4.$i}, 0, -1);
				${c_it4.$i}=${n_it4.$i};
			}
		}

// Itinerarios 1º Bach.
		if (isset($_POST['it1_0'])) {
			for ($z=0; $z<5 ; $z++) {
				$it1[] = $_POST['it1_'.$z.''];
			}
		}
		for ($i=1; $i<6; $i++) {
		if (isset($_POST['it1'.$i.'_0'])) {
			for ($z=0; $z<6 ; $z++) {
				${it1.$i}[] = $_POST['it1'.$i.'_'.$z.''];
			}
		}
		${cit1_.$i} = count(array_filter(${it1.$i}));
	}

// Optativas de itinerios de 1º Bach
		for ($i=1; $i<7; $i++) {
			if (isset($_POST['opt1'.$i.'_0'])) {
				${array_opt1.$i}="";
				for ($z=0; $z<6 ; $z++) {
					if (strlen($_POST['opt1'.$i.'_'.$z.''])>0) {
						${n_opt1.$i}++;
						$ind1 = iniciales($_POST['opt1'.$i.'_'.$z.'']);
						${array_opt1.$i}.= '"'.$ind1.${n_opt1.$i}.'"=>"'.$_POST['opt1'.$i.'_'.$z.''].'",';
					}
				}
				${array_opt1.$i}=substr(${array_opt1.$i}, 0, -1);
				${c_1.$i}=${n_opt1.$i};
			}
		}
// Itinerarios 2º Bach.
		if (isset($_POST['it2_0'])) {
			for ($z=0; $z<4 ; $z++) {
				$it2[] = $_POST['it2_'.$z.''];
			}
		}
	for ($i=1; $i<6; $i++) {
		if (isset($_POST['it2'.$i.'_0'])) {
			for ($z=0; $z<5 ; $z++) {
				${it2.$i}[] = $_POST['it2'.$i.'_'.$z.''];
			}
		}
		${cit2_.$i} = count(array_filter(${it2.$i}));
	}

// Optativas de itinerarios de 2º Bach
		for ($i=1; $i<5; $i++) {
			if (isset($_POST['opt2'.$i.'_0'])) {
				${array_opt2.$i}="";
				for ($z=0; $z<10 ; $z++) {
					if (strlen($_POST['opt2'.$i.'_'.$z.''])>0) {
						${n_opt2.$i}++;
						$ind = iniciales($_POST['opt2'.$i.'_'.$z.'']);
						${array_opt2.$i}.= '"'.$ind.'"=>"'.$_POST['opt2'.$i.'_'.$z.''].'",';
					}
				}
				${array_opt2.$i}=substr(${array_opt2.$i}, 0, -1);
				${c_2.$i}=${n_opt2.$i};
			}
		}

// Optativas de 2 horas de 2º Bach

		if (isset($_POST['opt_aut2_0'])) {
			for ($z=0; $z<7 ; $z++) {
				$opt_aut2[] = $_POST['opt_aut2_'.$z];
			}
		}

		$array_opt_aut2="";
		for ($z=0; $z<7 ; $z++) {
			if (strlen($_POST['opt_aut2_'.$z])>0) {
				$n_optaut++;
				$array_opt_aut2.= '"opt_aut2'.$n_optaut.'"=>"'.$_POST['opt_aut2_'.$z].'",';
			}
		}
		$array_opt_aut2=substr($array_opt_aut2, 0, -1);
		$c_aut2=$n_optaut;


	$prefInicio	= limpiar_string($_POST['prefInicio']);
	$prefFin	= limpiar_string($_POST['prefFin']);
	$prefInformes	= limpiar_string($_POST['prefInformes']);

	// CREACIÓN DEL ARCHIVO DE CONFIGURACIÓN
	if($file = fopen('config.php', 'w+'))
	{
		fwrite($file, "<?php \r\n");

		fwrite($file, "\r\n// CONFIGURACIÓN MÓDULO DE MATRICULACIÓN\r\n");

		fwrite($file, "\$config['matriculas']['fecha_inicio']\t= '$prefInicio';\r\n");
		fwrite($file, "\$config['matriculas']['fecha_fin']\t= '$prefFin';\r\n");
		fwrite($file, "\$config['matriculas']['transito']\t= '$prefInformes';\r\n");



		fwrite($file, "\$opt1\t= array($array_opt1);\r\n");
		fwrite($file, "\$opt2\t= array($array_opt2);\r\n");
		fwrite($file, "\$opt3\t= array($array_opt3);\r\n");
		fwrite($file, "\$opt4\t= array($array_opt4);\r\n");

		fwrite($file, "\$a1\t= array($array_a1);\r\n");


		fwrite($file, "\$it41\t= array($array_it41);\r\n");
		fwrite($file, "\$it42\t= array($array_it42);\r\n");
		fwrite($file, "\$it43\t= array($array_it43);\r\n");

		fwrite($file, "\$it1\t= array('1'=>'$it1[0]', '2'=>'$it1[1]', '3'=>'$it1[2]', '4'=>'$it1[3]', '5'=>'$it1[4]');\r\n");
		fwrite($file, "\$it2\t= array('1'=>'$it2[0]', '2'=>'$it2[1]', '3'=>'$it2[2]', '4'=>'$it2[3]');\r\n");
		
		fwrite($file, "\$it11\t= array('$it11[0]','$it11[1]','$it11[2]','$it11[3]','$it11[4]','$it11[5]');\r\n");
		fwrite($file, "\$it12\t= array('$it12[0]','$it12[1]','$it12[2]','$it12[3]','$it12[4]','$it12[5]');\r\n");
		fwrite($file, "\$it13\t= array('$it13[0]','$it13[1]','$it13[2]','$it13[3]','$it13[4]','$it13[5]');\r\n");
		fwrite($file, "\$it14\t= array('$it14[0]','$it14[1]','$it14[2]','$it14[3]','$it14[4]','$it14[5]');\r\n");
		fwrite($file, "\$it15\t= array('$it15[0]','$it15[1]','$it15[2]','$it15[3]','$it15[4]','$it15[5]');\r\n");

		fwrite($file, "\$it21\t= array('$it21[0]','$it21[1]','$it21[2]','$it21[3]','$it21[4]');\r\n");
		fwrite($file, "\$it22\t= array('$it22[0]','$it22[1]','$it22[2]','$it22[3]','$it22[4]');\r\n");
		fwrite($file, "\$it23\t= array('$it23[0]','$it23[1]','$it23[2]','$it23[3]','$it23[4]');\r\n");
		fwrite($file, "\$it24\t= array('$it24[0]','$it24[1]','$it24[2]','$it24[3]','$it24[4]');\r\n");

		fwrite($file, "\$opt11\t= array($array_opt11);\r\n");
		fwrite($file, "\$opt12\t= array($array_opt12);\r\n");
		fwrite($file, "\$opt13\t= array($array_opt13);\r\n");
		fwrite($file, "\$opt14\t= array($array_opt14);\r\n");
		fwrite($file, "\$opt15\t= array($array_opt15);\r\n");

		fwrite($file, "\$opt21\t= array($array_opt21);\r\n");
		fwrite($file, "\$opt22\t= array($array_opt22);\r\n");
		fwrite($file, "\$opt23\t= array($array_opt23);\r\n");
		fwrite($file, "\$opt24\t= array($array_opt24);\r\n");

		fwrite($file, "\$opt_aut2\t= array($array_opt_aut2);\r\n");

		fwrite($file, "\$count_1\t= '$c_1';\r\n");
		fwrite($file, "\$count_2\t= '$c_2';\r\n");
		fwrite($file, "\$count_3\t= '$c_3';\r\n");
		fwrite($file, "\$count_4\t= '$c_4';\r\n");

		fwrite($file, "\$count_a1\t= '$ca_1';\r\n");
		fwrite($file, "\$count_a2\t= '$ca_2';\r\n");
		fwrite($file, "\$count_a3\t= '$ca_3';\r\n");

		fwrite($file, "\$count_11\t= '$c_11';\r\n");
		fwrite($file, "\$count_12\t= '$c_12';\r\n");
		fwrite($file, "\$count_13\t= '$c_13';\r\n");
		fwrite($file, "\$count_14\t= '$c_14';\r\n");
		fwrite($file, "\$count_15\t= '$c_15';\r\n");

		fwrite($file, "\$count_21\t= '$c_21';\r\n");
		fwrite($file, "\$count_22\t= '$c_22';\r\n");
		fwrite($file, "\$count_23\t= '$c_23';\r\n");
		fwrite($file, "\$count_24\t= '$c_24';\r\n");

		fwrite($file, "\$count_2b2\t= '$c_aut2';\r\n");


		fwrite($file, "\r\n\r\n// Fin del archivo de configuración");

		fclose($file);

		$msg_success = "Las preferencias han sido guardadas correctamente.";
	}

}

if (file_exists('config.php')) {
	include('config.php');
}

for ($i=1; $i<6; $i++) {
	${ar_opt1.$i} = array_values(${opt1.$i});
}

for ($i=1; $i<5; $i++) {
	${ar_opt2.$i} = array_values(${opt2.$i});
}

$ar_opt_aut2 = array_values($opt_aut2);


include("../../menu.php");
include("menu.php");
?>

<div class="container">

	<div class="page-header">
		<h2>Matriculación de alumnos <small>Preferencias</small></h2>
	</div>

	<!-- MENSAJES -->
	<?php if (isset($msg_error)): ?>
	<div class="alert alert-danger alert-fadeout">
		<?php echo $msg_error; ?>
	</div>
	<?php endif; ?>

	<?php if (isset($msg_success)): ?>
	<div class="alert alert-success alert-fadeout">
		<?php echo $msg_success; ?>
	</div>
	<?php endif; ?>

			<form class="form-horizontal" method="post" action="preferencias.php">

					<fieldset>
						<div class="well">
						<h3>Fechas de Matriculación para los alumnos del Centro<br></h3>

							<p class="help-block">Las fechas de Inicio y Fin indican a partir de qué momento se activa y cuando se desactiva el formulario de matriculación en la página pública del Centro para que los alumnos puedan comenzar a registrar los datos.</p>
						<div class="form-group">
							<label for="prefInicio" class="col-sm-3 control-label">Fecha de inicio de Matriculación</label>
							<div class="col-sm-3" id="datetimepicker1">
							<div class="input-group">
							<input name="prefInicio" type="text"
								class="form-control" value="<?php echo $config['matriculas']['fecha_inicio']; ?>" data-date-format="YYYY-MM-DD" id="prefInicio">
								<span class="input-group-addon"><i class="far fa-calendar"></i></span>
							</div>
							</div>
						</div>

						<div class="form-group">
							<label for="prefFin" class="col-sm-3 control-label">Fecha de Fin de Matriculación</label>
							<div class="col-sm-3" id="datetimepicker2">
							<div class="input-group">
							<input name="prefFin" type="text"
								class="form-control" value="<?php echo $config['matriculas']['fecha_fin']; ?>" data-date-format="YYYY-MM-DD" id="prefFin">
								<span class="input-group-addon"><i class="far fa-calendar"></i></span>
							</div>
							</div>
						</div>
					</div>

					<br>

					<div class="well">
					<h3>Informes de Tránsito para alumnos de Primaria<br></h3>
						<p class="help-block">El módulos de Informes de Tránsito lo utilizan los tutores de colegios de Primaria para rellenar los datos que luego gestionan el Dpto. de Orientación y Jefatura de Estudios. Supone que hemos importado los datos de los alumnos que nos han dado los directores de los colegios. Los tutores acceden al página en la dirección http://dominio_del_centro.com/transito, y usan la contraseña que les indique el director del Instituto.</p>
						<div class="form-group">
							<label for="prefInformes" class="col-sm-4 control-label">El Centro utiliza el módulo de Informes de Tránsito</label>
							<div class="col-sm-3">
  							<div class="checkbox">
								<input name="prefInformes" type="checkbox" value="1" <?php if ($config['matriculas']['transito']==1) { echo "checked"; }  ?> id="prefInformes">
							</div>
							</div>
						</div>
						</div>

					<br>

					<div class="well">
					<h3>Optativas, Refuerzos, Ampliaciones, Itinerarios<br></h3>
					<br>
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab1" data-toggle="tab">1º de ESO</a></li>
						<li><a href="#tab2" data-toggle="tab">2º de ESO</a></li>
						<li><a href="#tab3" data-toggle="tab">3º de ESO</a></li>
						<li><a href="#tab4" data-toggle="tab">4º de ESO</a></li>
						<li><a href="#tab5" data-toggle="tab">1º Bachillerato</a></li>
						<li><a href="#tab6" data-toggle="tab">2º Bachillerato</a></li>
					</ul>

					<div class="tab-content" style="padding-bottom: 9px;">

					<div class="tab-pane fade in active" id="tab1">

						<h3>1º de E.S.O.</h3>
						<br>
						<table class='table table-bordered' style='border:none;'>
							<tr>
								<td>
									<legend>Asignaturas Optativas</legend>
						<div class="form-group">
							<label for="opt1_0" class="col-sm-4 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt1_0" type="text"
								class="form-control" value="<?php echo (isset($opt1[0])) ? $opt1[0] : 'Alemán 2º Idioma'; ?>" id="opt1_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt1_1" class="col-sm-4 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt1_1" type="text"
								class="form-control" value="<?php echo (isset($opt1[1])) ? $opt1[1] : 'Cambios Sociales y Género'; ?>" id="opt1_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt1_2" class="col-sm-4 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt1_2" type="text"
								class="form-control" value="<?php echo (isset($opt1[2])) ? $opt1[2] : 'Francés 2º Idioma'; ?>" id="opt1_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt1_3" class="col-sm-4 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt1_3" type="text"
								class="form-control" value="<?php echo (isset($opt1[3])) ? $opt1[3] : 'Tecnología Aplicada'; ?>" id="opt1_3">
							</div>
						</div>
						<div class="form-group">
							<label for="opt1_4" class="col-sm-4 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt1_4" type="text"
								class="form-control" value="<?php echo (isset($opt1[4])) ? $opt1[4] : ''; ?>" id="opt1_4">
							</div>
						</div>
						
						<div class="form-group">
							<label for="opt1_5" class="col-sm-4 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt1_5" type="text"
								class="form-control" value="<?php echo (isset($opt1[5])) ? $opt1[5] : ''; ?>" id="opt1_5">
							</div>
						</div>
						<div class="form-group">
							<label for="opt1_6" class="col-sm-4 control-label">Optativa 7</label>
							<div class="input-group col-sm-5">
							<input name="opt1_6" type="text"
								class="form-control" value="<?php echo (isset($opt1[6])) ? $opt1[6] : ''; ?>" id="opt1_6">
							</div>
						</div>
								</td>
								<td>
									<legend>Ampliaciones y Refuerzos</legend>
						<div class="form-group">
							<label for="a1_0" class="col-sm-4 control-label">Actividad 1</label>
							<div class="input-group col-sm-5">
							<input name="a1_0" type="text"
								class="form-control" value="<?php echo (isset($a1[0])) ? $a1[0] : ''; ?>" id="a1_0">
							</div>
						</div>
						<div class="form-group">
							<label for="a1_1" class="col-sm-4 control-label">Actividad 2</label>
							<div class="input-group col-sm-5">
							<input name="a1_1" type="text"
								class="form-control" value="<?php echo (isset($a1[1])) ? $a1[1] : ''; ?>" id="a1_1">
							</div>
						</div>
						<div class="form-group">
							<label for="a1_2" class="col-sm-4 control-label">Actividad 3</label>
							<div class="input-group col-sm-5">
							<input name="a1_2" type="text"
								class="form-control" value="<?php echo (isset($a1[2])) ? $a1[2] : ''; ?>" id="a1_2">
							</div>
						</div>
						<div class="form-group">
							<label for="a1_3" class="col-sm-4 control-label">Actividad 4</label>
							<div class="input-group col-sm-5">
							<input name="a1_3" type="text"
								class="form-control" value="<?php echo (isset($a1[3])) ? $a1[3] : ''; ?>" id="a1_3">
							</div>
						</div>
						<div class="form-group">
							<label for="a1_4" class="col-sm-4 control-label">Actividad 5</label>
							<div class="input-group col-sm-5">
							<input name="a1_4" type="text"
								class="form-control" value="<?php echo (isset($a1[4])) ? $a1[4] : ''; ?>" id="a1_4">
							</div>
						</div>
						<div class="form-group">
							<label for="a1_5" class="col-sm-4 control-label">Actividad 6</label>
							<div class="input-group col-sm-5">
							<input name="a1_5" type="text"
								class="form-control" value="<?php echo (isset($a1[5])) ? $a1[5] : ''; ?>" id="a1_5">
							</div>
						</div>
						<div class="form-group">
							<label for="a1_6" class="col-sm-4 control-label">Actividad 7</label>
							<div class="input-group col-sm-5">
							<input name="a1_6" type="text"
								class="form-control" value="<?php echo (isset($a1[6])) ? $a1[6] : ''; ?>" id="a1_6">
							</div>
						</div>
								</td>
							</tr>
						</table>

					</div>

					<div class="tab-pane fade in" id="tab2">

					<h3>2º de E.S.O.</h3>
					<br>
						<table class='table table-bordered' style='border:none;'>
							<tr>
								<td>
									<legend>Asignaturas Optativas</legend>
						<div class="form-group">
							<label for="opt2_0" class="col-sm-4 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt2_0" type="text"
								class="form-control" value="<?php echo (isset($opt2[0])) ? $opt2[0] : 'Alemán 2º Idioma'; ?>" id="opt2_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt2_1" class="col-sm-4 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt2_1" type="text"
								class="form-control" value="<?php echo (isset($opt2[1])) ? $opt2[1] : 'Cambios Sociales y Género'; ?>" id="opt2_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt2_2" class="col-sm-4 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt2_2" type="text"
								class="form-control" value="<?php echo (isset($opt2[2])) ? $opt2[2] : 'Francés 2º Idioma'; ?>" id="opt2_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt2_3" class="col-sm-4 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt2_3" type="text"
								class="form-control" value="<?php echo (isset($opt2[3])) ? $opt2[3] : ''; ?>" id="opt2_3">
							</div>
						</div>
						<div class="form-group">
							<label for="opt2_4" class="col-sm-4 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt2_4" type="text"
								class="form-control" value="<?php echo (isset($opt2[4])) ? $opt2[4] : ''; ?>" id="opt2_4">
							</div>
						</div>
						<div class="form-group">
							<label for="opt2_5" class="col-sm-4 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt2_5" type="text"
								class="form-control" value="<?php echo (isset($opt2[5])) ? $opt2[5] : ''; ?>" id="opt2_5">
							</div>
						</div>
						<div class="form-group">
							<label for="opt2_6" class="col-sm-4 control-label">Optativa 7</label>
							<div class="input-group col-sm-5">
							<input name="opt2_6" type="text"
								class="form-control" value="<?php echo (isset($opt2[6])) ? $opt2[6] : ''; ?>" id="opt2_6">
							</div>
						</div>
						<div class="form-group">
							<label for="opt2_7" class="col-sm-4 control-label">Optativa 8</label>
							<div class="input-group col-sm-5">
							<input name="opt2_7" type="text"
								class="form-control" value="<?php echo (isset($opt2[7])) ? $opt2[7] : ''; ?>" id="opt2_7">
							</div>
						</div>
								</td>
								
							</tr>
						</table>
					</div>

					<div class="tab-pane fade in" id="tab3">

						<h3>3º de E.S.O.</h3>
						<br>
						<table class='table table-bordered' style='border:none;'>
							<tr>
								<td>
									<legend>Asignaturas Optativas</legend>
						<div class="form-group">
							<label for="opt3_0" class="col-sm-4 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt3_0" type="text"
								class="form-control" value="<?php echo (isset($opt3[0])) ? $opt3[0] : 'Alemán 2º Idioma'; ?>" id="opt3_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt3_1" class="col-sm-4 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt3_1" type="text"
								class="form-control" value="<?php echo (isset($opt3[1])) ? $opt3[1] : 'Francés 2º Idioma'; ?>" id="opt3_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt3_2" class="col-sm-4 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt3_2" type="text"
								class="form-control" value="<?php echo (isset($opt3[2])) ? $opt3[2] : 'Cambios Sociales y Género'; ?>" id="opt3_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt3_3" class="col-sm-4 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt3_3" type="text"
								class="form-control" value="<?php echo (isset($opt3[3])) ? $opt3[3] : 'Cultura Clásica'; ?>" id="opt3_3">
							</div>
						</div>
						<div class="form-group">
							<label for="opt3_4" class="col-sm-4 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt3_4" type="text"
								class="form-control" value="<?php echo (isset($opt3[4])) ? $opt3[4] : ''; ?>" id="opt3_4">
							</div>
						</div>
						<div class="form-group">
							<label for="opt3_5" class="col-sm-4 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt3_5" type="text"
								class="form-control" value="<?php echo (isset($opt3[5])) ? $opt3[5] : ''; ?>" id="opt3_5">
							</div>
						</div>
						<div class="form-group">
							<label for="opt3_6" class="col-sm-4 control-label">Optativa 7</label>
							<div class="input-group col-sm-5">
							<input name="opt3_6" type="text"
								class="form-control" value="<?php echo (isset($opt3[6])) ? $opt3[6] : ''; ?>" id="opt3_6">
							</div>
						</div>
						<div class="form-group">
							<label for="opt3_7" class="col-sm-4 control-label">Optativa 8</label>
							<div class="input-group col-sm-5">
							<input name="opt3_7" type="text"
								class="form-control" value="<?php echo (isset($opt3[7])) ? $opt3[7] : ''; ?>" id="opt3_7">
							</div>
						</div>
						<div class="form-group">
							<label for="opt3_8" class="col-sm-4 control-label">Optativa 9</label>
							<div class="input-group col-sm-5">
							<input name="opt3_8" type="text"
								class="form-control" value="<?php echo (isset($opt3[8])) ? $opt3[8] : ''; ?>" id="opt3_8">
							</div>
						</div>

								</td>
								
							</tr>
						</table>

					</div>

					<div class="tab-pane fade in" id="tab4">

						<h3>4º de E.S.O.</h3>
						<br>
						<table class='table table-bordered' style='border:none;'>
							<tr>
								<td>
									<legend>Asignaturas Optativas</legend>
						<div class="form-group">
							<label for="opt4_0" class="col-sm-2 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt4_0" type="text"
								class="form-control" value="<?php echo (isset($opt4[0])) ? $opt4[0] : 'Alemán 2º Idioma'; ?>" id="opt4_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt4_1" class="col-sm-2 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt4_1" type="text"
								class="form-control" value="<?php echo (isset($opt4[1])) ? $opt4[1] : 'Francés 2º Idioma'; ?>" id="opt4_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt4_2" class="col-sm-2 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt4_2" type="text"
								class="form-control" value="<?php echo (isset($opt4[2])) ? $opt4[2] : 'TIC'; ?>" id="opt4_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt4_3" class="col-sm-2 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt4_3" type="text"
								class="form-control" value="<?php echo (isset($opt4[3])) ? $opt4[3] : 'Educación Plástica Visual'; ?>" id="opt4_3">
							</div>
						</div>
						<div class="form-group">
							<label for="opt4_4" class="col-sm-2 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt4_4" type="text"
								class="form-control" value="<?php echo (isset($opt4[4])) ? $opt4[4] : 'Música'; ?>" id="opt4_4">
							</div>
						</div>
								</td>
							</tr>
							<tr>
								<td>

						<legend>Itinerarios 4º ESO</legend>

						<table class='table' style="background-color:transparent;">
						<tr>
							<td>
								<legend>Itinerario 1</legend>
						<div class="form-group">
							<label for="it41_0" class="col-sm-4 control-label">Descripción</label>
							<div class="input-group col-sm-5">
							<input name="it41_0" type="text"
								class="form-control" value="<?php echo (isset($it41[0])) ? $it41[0] : '(Bachillerato de Ciencias)'; ?>" id="it41_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it41_1" class="col-sm-4 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it41_1" type="text"
								class="form-control" value="<?php echo (isset($it41[1])) ? $it41[1] : ''; ?>" id="it41_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it41_2" class="col-sm-4 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it41_2" type="text"
								class="form-control" value="<?php echo (isset($it41[2])) ? $it41[2] : ''; ?>" id="it41_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it41_3" class="col-sm-4 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it41_3" type="text"
								class="form-control" value="<?php echo (isset($it41[3])) ? $it41[3] : ''; ?>" id="it41_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it41_4" class="col-sm-4 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="it41_4" type="text"
								class="form-control" value="<?php echo (isset($it41[4])) ? $it41[4] : ''; ?>" id="it41_4">
							</div>
						</div>
						<div class="form-group">
							<label for="it41_5" class="col-sm-4 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="it41_5" type="text"
								class="form-control" value="<?php echo (isset($it41[5])) ? $it41[5] : ''; ?>" id="it41_5">
							</div>
						</div>
							</td>

							<td>
						<legend>Itinerario 2</legend>
						<div class="form-group">
							<label for="it42_0" class="col-sm-4 control-label">Descripción</label>
							<div class="input-group col-sm-5">
							<input name="it42_0" type="text"
								class="form-control" value="<?php echo (isset($it42[0])) ? $it42[0] : '(Bachillerato de Humanidades y Ciencias Sociales)'; ?>" id="it42_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it42_1" class="col-sm-4 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it42_1" type="text"
								class="form-control" value="<?php echo (isset($it42[1])) ? $it42[1] : 'Matemáticas Académicas'; ?>" id="it42_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it42_2" class="col-sm-4 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it42_2" type="text"
								class="form-control" value="<?php echo (isset($it42[2])) ? $it42[2] : 'Latín'; ?>" id="it42_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it42_3" class="col-sm-4 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it42_3" type="text"
								class="form-control" value="<?php echo (isset($it42[3])) ? $it42[3] : 'Economía'; ?>" id="it42_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it42_4" class="col-sm-4 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="it42_4" type="text"
								class="form-control" value="<?php echo (isset($it42[4])) ? $it42[4] : ''; ?>" id="it42_4">
							</div>
						</div>
						<div class="form-group">
							<label for="it42_5" class="col-sm-4 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="it42_5" type="text"
								class="form-control" value="<?php echo (isset($it42[5])) ? $it42[5] : ''; ?>" id="it42_5">
							</div>
						</div>
								</td>

								<td>
						<legend>Itinerario 3</legend>
						<div class="form-group">
							<label for="it43_0" class="col-sm-4 control-label">Descripción</label>
							<div class="input-group col-sm-5">
							<input name="it43_0" type="text"
								class="form-control" value="<?php echo (isset($it43[0])) ? $it43[0] : '(Ciclos Formativos y Mundo Laboral)'; ?>" id="it43_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it43_1" class="col-sm-4 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it43_1" type="text"
								class="form-control" value="<?php echo (isset($it43[1])) ? $it43[1] : 'Matemáticas Aplicadas'; ?>" id="it43_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it43_2" class="col-sm-4 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it43_2" type="text"
								class="form-control" value="<?php echo (isset($it43[2])) ? $it43[2] : 'Tecnología'; ?>" id="it43_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it43_3" class="col-sm-4 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it43_3" type="text"
								class="form-control" value="<?php echo (isset($it43[3])) ? $it43[3] : 'Ciencias Aplicadas a la Actividad Profesional'; ?>" id="it43_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it43_4" class="col-sm-4 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="it43_4" type="text"
								class="form-control" value="<?php echo (isset($it43[4])) ? $it43[4] : 'Iniciación a la Actividad Emprendedora y Empresarial'; ?>" id="it43_4">
							</div>
						</div>
								</td>
							</tr>
						</table>


						</td>
						</tr>
						</table>
						</div>

						<div class="tab-pane fade in" id="tab5">

						<h3>1º de Bachillerato</h3>
						<br>
						<table class='table table-bordered' style='border:none;'>
							<tr>
								<td colspan=3>
									<legend>Itinerarios</legend>
						<div class="form-group">
							<label for="it1_0" class="col-sm-2 control-label">Itinerario 1</label>
							<div class="input-group col-sm-5">
							<input name="it1_0" type="text"
								class="form-control" value="<?php echo (isset($it1[1])) ? $it1[1] : 'Ciencias e Ingeniería y Arquitectura'; ?>" id="it1_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it1_1" class="col-sm-2 control-label">Itinerario 2</label>
							<div class="input-group col-sm-5">
							<input name="it1_1" type="text"
								class="form-control" value="<?php echo (isset($it1[2])) ? $it1[2] : 'Ciencias y Ciencias de la Salud'; ?>" id="it1_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it1_2" class="col-sm-2 control-label">Itinerario 3</label>
							<div class="input-group col-sm-5">
							<input name="it1_2" type="text"
								class="form-control" value="<?php echo (isset($it1[3])) ? $it1[3] : 'Humanidades'; ?>" id="it1_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it1_3" class="col-sm-2 control-label">Itinerario 4</label>
							<div class="input-group col-sm-5">
							<input name="it1_3" type="text"
								class="form-control" value="<?php echo (isset($it1[4])) ? $it1[4] : 'Ciencias Sociales y Jurídicas'; ?>" id="it1_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it1_4" class="col-sm-2 control-label">Itinerario 5</label>
							<div class="input-group col-sm-5">
							<input name="it1_4" type="text"
								class="form-control" value="<?php echo (isset($it1[5])) ? $it1[5] : 'General'; ?>" id="it1_4">
							</div>
						</div>
								</td>
							</tr>
							<tr>
								<td colspan=3>
									<legend>Nombre y Asignaturas obligatorias de los distintos itinerarios</legend>
						<table class='table' style="background-color:transparent;">
						<tr>
							<td>
								<legend>Itinerario 1</legend>
						<div class="form-group">
							<label for="it11_0" class="col-sm-5 control-label">Nombre</label>
							<div class="input-group col-sm-5">
							<input name="it11_0" type="text"
								class="form-control" value="<?php echo (isset($it11[0])) ? $it11[0] : 'Bachillerato de Ciencias'; ?>" id="it11_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it11_1" class="col-sm-5 control-label">Modalidad</label>
							<div class="input-group col-sm-5">
							<input name="it11_1" type="text"
								class="form-control" value="<?php echo (isset($it11[1])) ? $it11[1] : 'Arquitectura e Ingeniería y Ciencias'; ?>" id="it11_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it11_2" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it11_2" type="text"
								class="form-control" value="<?php echo (isset($it11[2])) ? $it11[2] : 'Matemáticas'; ?>" id="it11_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it11_3" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it11_3" type="text"
								class="form-control" value="<?php echo (isset($it11[3])) ? $it11[3] : 'Física y Química'; ?>" id="it11_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it11_4" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it11_4" type="text"
								class="form-control" value="<?php echo (isset($it11[4])) ? $it11[4] : 'Dibujo Técnico'; ?>" id="it11_4">
							</div>
						</div>
						<div class="form-group">
							<label for="it11_5" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="it11_5" type="text"
								class="form-control" value="<?php echo (isset($it11[5])) ? $it11[5] : 'Tecnología Industrial'; ?>" id="it11_5">
							</div>
						</div>

							</td>

								<td>
						<legend>Itinerario 2</legend>
						<div class="form-group">
							<label for="it12_0" class="col-sm-5 control-label">Nombre</label>
							<div class="input-group col-sm-5">
							<input name="it12_0" type="text"
								class="form-control" value="<?php echo (isset($it12[0])) ? $it12[0] : 'Bachillerato de Ciencias'; ?>" id="it12_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it12_1" class="col-sm-5 control-label">Modalidad</label>
							<div class="input-group col-sm-5">
							<input name="it12_1" type="text"
								class="form-control" value="<?php echo (isset($it12[1])) ? $it12[1] : 'Ciencias y Ciencias de la Salud'; ?>" id="it12_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it12_2" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it12_2" type="text"
								class="form-control" value="<?php echo (isset($it12[2])) ? $it12[2] : 'Matemáticas'; ?>" id="it12_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it12_3" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it12_3" type="text"
								class="form-control" value="<?php echo (isset($it12[3])) ? $it12[3] : 'Física y Química'; ?>" id="it12_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it12_4" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it12_4" type="text"
								class="form-control" value="<?php echo (isset($it12[4])) ? $it12[4] : 'Biología y Geología'; ?>" id="it12_4">
							</div>
						</div>
						<div class="form-group">
							<label for="it12_5" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="it12_5" type="text"
								class="form-control" value="<?php echo (isset($it12[5])) ? $it12[5] : 'Anatomía Aplicada'; ?>" id="it12_5">
							</div>
						</div>
								</td>

								<td>
						<legend>Itinerario 3</legend>
						<div class="form-group">
							<label for="it13_0" class="col-sm-5 control-label">Nombre</label>
							<div class="input-group col-sm-5">
							<input name="it13_0" type="text"
								class="form-control" value="<?php echo (isset($it13[0])) ? $it13[0] : 'Bachillerato de Humanidades'; ?>" id="it13_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it13_1" class="col-sm-5 control-label">Modalidad</label>
							<div class="input-group col-sm-5">
							<input name="it13_1" type="text"
								class="form-control" value="<?php echo (isset($it13[1])) ? $it13[1] : 'Humanidades'; ?>" id="it13_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it13_2" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it13_2" type="text"
								class="form-control" value="<?php echo (isset($it13[2])) ? $it13[2] : 'Latín'; ?>" id="it13_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it13_4" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it13_4" type="text"
								class="form-control" value="<?php echo (isset($it13[4])) ? $it13[4] : 'Griego'; ?>" id="it13_4">
							</div>
						</div>
						<div class="form-group">
							<label for="it13_3" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it13_3" type="text"
								class="form-control" value="<?php echo (isset($it13[3])) ? $it13[3] : 'Patrimonio Cultural y Artístico'; ?>" id="it13_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it13_5" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="it13_5" type="text"
								class="form-control" value="<?php echo (isset($it13[5])) ? $it13[5] : 'TIC'; ?>" id="it13_5">
							</div>
						</div>
								</td>
							<td>
						<legend>Itinerario 4</legend>
						<div class="form-group">
							<label for="it14_0" class="col-sm-5 control-label">Nombre</label>
							<div class="input-group col-sm-5">
							<input name="it14_0" type="text"
								class="form-control" value="<?php echo (isset($it14[0])) ? $it14[0] : 'Bachillerato de Ciencias Sociales'; ?>" id="it14_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it14_1" class="col-sm-5 control-label">Modalidad</label>
							<div class="input-group col-sm-5">
							<input name="it14_1" type="text"
								class="form-control" value="<?php echo (isset($it14[1])) ? $it14[1] : 'Ciencias Sociales y Jurídicas'; ?>" id="it14_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it14_2" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it14_2" type="text"
								class="form-control" value="<?php echo (isset($it14[2])) ? $it14[2] : 'Matemáticas de las Ciencias Sociales II'; ?>" id="it14_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it14_3" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it14_3" type="text"
								class="form-control" value="<?php echo (isset($it14[3])) ? $it14[3] : 'Economía'; ?>" id="it14_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it14_4" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it14_4" type="text"
								class="form-control" value="<?php echo (isset($it14[4])) ? $it14[4] : 'Cultura Emprendedora'; ?>" id="it14_4">
							</div>
						</div>
						<div class="form-group">
							<label for="it14_5" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="it14_5" type="text"
								class="form-control" value="<?php echo (isset($it14[5])) ? $it14[5] : 'TIC'; ?>" id="it14_5">
							</div>
						</div>

							</td>
							<td>
								
						<legend>Itinerario 5</legend>
						<div class="form-group">
							<label for="it15_0" class="col-sm-5 control-label">Nombre</label>
							<div class="input-group col-sm-5">
							<input name="it15_0" type="text"
								class="form-control" value="<?php echo (isset($it15[0])) ? $it15[0] : 'Bachillerato General'; ?>" id="it15_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it15_1" class="col-sm-5 control-label">Modalidad</label>
							<div class="input-group col-sm-5">
							<input name="it15_1" type="text"
								class="form-control" value="<?php echo (isset($it15[1])) ? $it15[1] : 'General'; ?>" id="it15_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it15_2" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it15_2" type="text"
								class="form-control" value="<?php echo (isset($it15[2])) ? $it15[2] : 'Latín'; ?>" id="it15_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it15_3" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it15_3" type="text"
								class="form-control" value="<?php echo (isset($it15[3])) ? $it15[3] : 'Patrimonio Cultural y Artístico'; ?>" id="it15_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it15_4" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it15_4" type="text"
								class="form-control" value="<?php echo (isset($it15[4])) ? $it15[4] : 'Griego'; ?>" id="it15_4">
							</div>
						</div>
						<div class="form-group">
							<label for="it15_5" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="it15_5" type="text"
								class="form-control" value="<?php echo (isset($it15[5])) ? $it15[5] : 'TIC'; ?>" id="it15_5">
							</div>
						</div>
								</td>
						</tr>
					</table>
				</td>
			</tr>
							<tr>
								<td>
									<legend>Optativas de los distintos itinerarios</legend>
						<table class='table' style="background-color:transparent;">
						<tr>
							<td>
								<legend>Itinerario 1</legend>
						<div class="form-group">
							<label for="opt11_0" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt11_0" type="text"
								class="form-control" value="<?php echo (isset($ar_opt11[0])) ? $ar_opt11[0] : 'Cultura Científica 1'; ?>" id="opt11_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt11_1" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt11_1" type="text"
								class="form-control" value="<?php echo (isset($ar_opt11[1])) ? $ar_opt11[1] : 'Tecnologías de Información y Comunicación 1'; ?>" id="opt11_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt11_2" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt11_2" type="text"
								class="form-control" value="<?php echo (isset($ar_opt11[2])) ? $ar_opt11[2] : 'Robótica 1'; ?>" id="opt11_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt11_3" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt11_3" type="text"
								class="form-control" value="<?php echo (isset($ar_opt11[3])) ? $ar_opt11[3] : ''; ?>" id="opt11_3">
							</div>
						</div>
								<div class="form-group">
							<label for="opt11_4" class="col-sm-5 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt11_4" type="text"
								class="form-control" value="<?php echo (isset($ar_opt11[4])) ? $ar_opt11[4] : ''; ?>" id="opt11_4">
							</div>
						</div>
								<div class="form-group">
							<label for="opt11_5" class="col-sm-5 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt11_5" type="text"
								class="form-control" value="<?php echo (isset($ar_opt11[5])) ? $ar_opt11[5] : ''; ?>" id="opt11_5">
							</div>
						</div>
							</td>

								<td>
						<legend>Itinerario 2</legend>
						<div class="form-group">
							<label for="opt12_0" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt12_0" type="text"
								class="form-control" value="<?php echo (isset($ar_opt12[0])) ? $ar_opt12[0] : 'Cultura Científica 2'; ?>" id="opt12_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt12_1" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt12_1" type="text"
								class="form-control" value="<?php echo (isset($ar_opt12[1])) ? $ar_opt12[1] : 'Tecnologías de Información y Comunicación 2'; ?>" id="opt12_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt12_2" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt12_2" type="text"
								class="form-control" value="<?php echo (isset($ar_opt12[2])) ? $ar_opt12[2] : ''; ?>" id="opt12_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt12_3" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt12_3" type="text"
								class="form-control" value="<?php echo (isset($ar_opt12[3])) ? $ar_opt12[3] : ''; ?>" id="opt12_3">
							</div>
						</div>
									<div class="form-group">
							<label for="opt12_4" class="col-sm-5 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt12_4" type="text"
								class="form-control" value="<?php echo (isset($ar_opt12[4])) ? $ar_opt12[4] : ''; ?>" id="opt12_4">
							</div>
						</div><div class="form-group">
							<label for="opt12_5" class="col-sm-5 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt12_5" type="text"
								class="form-control" value="<?php echo (isset($ar_opt12[5])) ? $ar_opt12[5] : ''; ?>" id="opt12_5">
							</div>
						</div>
								</td>

								<td>
						<legend>Itinerario 3</legend>
						<div class="form-group">
							<label for="opt13_0" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt13_0" type="text"
								class="form-control" value="<?php echo (isset($ar_opt13[0])) ? $ar_opt13[0] : 'Literatura Universal 3'; ?>" id="opt13_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt13_1" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt13_1" type="text"
								class="form-control" value="<?php echo (isset($ar_opt13[1])) ? $ar_opt13[1] : 'Historia del Mundo Contemporáneo 3'; ?>" id="opt13_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt13_2" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt13_2" type="text"
								class="form-control" value="<?php echo (isset($ar_opt13[2])) ? $ar_opt13[2] : ''; ?>" id="opt13_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt13_3" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt13_3" type="text"
								class="form-control" value="<?php echo (isset($ar_opt13[3])) ? $ar_opt13[3] : ''; ?>" id="opt13_3">
							</div>
							</div>								
							<div class="form-group">
							<label for="opt13_4" class="col-sm-5 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt13_4" type="text"
								class="form-control" value="<?php echo (isset($ar_opt13[4])) ? $ar_opt13[4] : ''; ?>" id="opt13_4">
							</div>							
							</div>
							<div class="form-group">
							<label for="opt13_5" class="col-sm-5 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt13_5" type="text"
								class="form-control" value="<?php echo (isset($ar_opt13[5])) ? $ar_opt13[5] : ''; ?>" id="opt13_5">
							</div>
							</div>
								</td>
							<td>
						<legend>Itinerario 4</legend>
						<div class="form-group">
							<label for="opt14_0" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt14_0" type="text"
								class="form-control" value="<?php echo (isset($ar_opt14[0])) ? $ar_opt14[0] : 'Literatura Universal 4'; ?>" id="opt14_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt14_1" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt14_1" type="text"
								class="form-control" value="<?php echo (isset($ar_opt14[1])) ? $ar_opt14[1] : 'Historia del Mundo Contemporáneo 4'; ?>" id="opt14_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt14_2" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt14_2" type="text"
								class="form-control" value="<?php echo (isset($ar_opt14[2])) ? $ar_opt14[2] : ''; ?>" id="opt14_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt14_3" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt14_3" type="text"
								class="form-control" value="<?php echo (isset($ar_opt14[3])) ? $ar_opt14[3] : ''; ?>" id="opt14_3">
							</div>
						</div>
								<div class="form-group">
							<label for="opt14_4" class="col-sm-5 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt14_4" type="text"
								class="form-control" value="<?php echo (isset($ar_opt14[4])) ? $ar_opt14[4] : ''; ?>" id="opt14_4">
							</div>
						</div>
								<div class="form-group">
							<label for="opt14_5" class="col-sm-5 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt14_5" type="text"
								class="form-control" value="<?php echo (isset($ar_opt14[5])) ? $ar_opt14[5] : ''; ?>" id="opt14_5">
							</div>
						</div>
						</td>
						<td>
						<legend>Itinerario 5</legend>
						<div class="form-group">
							<label for="opt15_0" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt15_0" type="text"
								class="form-control" value="<?php echo (isset($ar_opt15[0])) ? $ar_opt15[0] : 'Optativa 1'; ?>" id="opt15_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt15_1" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt15_1" type="text"
								class="form-control" value="<?php echo (isset($ar_opt15[1])) ? $ar_opt15[1] : 'Optativa 2'; ?>" id="opt15_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt15_2" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt15_2" type="text"
								class="form-control" value="<?php echo (isset($ar_opt15[2])) ? $ar_opt15[2] : ''; ?>" id="opt15_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt15_3" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt15_3" type="text"
								class="form-control" value="<?php echo (isset($ar_opt15[3])) ? $ar_opt15[3] : ''; ?>" id="opt15_3">
							</div>
						</div>
													<div class="form-group">
							<label for="opt15_4" class="col-sm-5 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt15_4" type="text"
								class="form-control" value="<?php echo (isset($ar_opt15[4])) ? $ar_opt15[4] : ''; ?>" id="opt15_4">
							</div>
						</div>
													<div class="form-group">
							<label for="opt15_5" class="col-sm-5 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt15_5" type="text"
								class="form-control" value="<?php echo (isset($ar_opt15[5])) ? $ar_opt15[5] : ''; ?>" id="opt15_5">
							</div>
						</div>
								</td>
							</tr>
						</table>
								</td>
							</tr>
						</table>

					</div>

					<div class="tab-pane fade in" id="tab6">

						<h3>2º de Bachillerato</h3>
						<br>
						<table class='table table-bordered' style='border:none;'>
							<tr>
								<td colspan=3 align=left>
									<legend>Itinerarios</legend>
						<div class="form-group">
							<label for="it2_0" class="col-sm-2 control-label">Itinerario 1</label>
							<div class="input-group col-sm-5">
							<input name="it2_0" type="text"
								class="form-control" value="<?php echo (isset($it2[1])) ? $it2[1] : 'Ciencias e Ingeniería y Arquitectura'; ?>" id="it2_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it2_1" class="col-sm-2 control-label">Itinerario 2</label>
							<div class="input-group col-sm-5">
							<input name="it2_1" type="text"
								class="form-control" value="<?php echo (isset($it2[2])) ? $it2[2] : 'Ciencias y Ciencias de la Salud'; ?>" id="it2_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it2_2" class="col-sm-2 control-label">Itinerario 3</label>
							<div class="input-group col-sm-5">
							<input name="it2_2" type="text"
								class="form-control" value="<?php echo (isset($it2[3])) ? $it2[3] : 'Humanidades'; ?>" id="it2_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it2_3" class="col-sm-2 control-label">Itinerario 4</label>
							<div class="input-group col-sm-5">
							<input name="it2_3" type="text"
								class="form-control" value="<?php echo (isset($it2[4])) ? $it2[4] : 'Ciencias Sociales y Jurídicas'; ?>" id="it2_3">
							</div>
						</div>
								</td>
							</tr>
							<tr>
								<td>
									<legend>Nombre y Asignaturas obligatorias de los distintos itinerarios</legend>
						<table class='table' style="background-color:transparent;">
						<tr>
							<td>
								<legend>Itinerario 1</legend>
						<div class="form-group">
							<label for="it21_0" class="col-sm-5 control-label">Nombre</label>
							<div class="input-group col-sm-5">
							<input name="it21_0" type="text"
								class="form-control" value="<?php echo (isset($it21[0])) ? $it21[0] : 'Bachillerato de Ciencias'; ?>" id="it21_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it21_1" class="col-sm-5 control-label">Modalidad</label>
							<div class="input-group col-sm-5">
							<input name="it21_1" type="text"
								class="form-control" value="<?php echo (isset($it21[1])) ? $it21[1] : 'Arquitectura e Ingeniería y Ciencias'; ?>" id="it21_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it21_2" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it21_2" type="text"
								class="form-control" value="<?php echo (isset($it21[2])) ? $it21[2] : 'Matemáticas II'; ?>" id="it21_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it21_3" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it21_3" type="text"
								class="form-control" value="<?php echo (isset($it21[3])) ? $it21[3] : 'Física'; ?>" id="it21_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it21_4" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it21_4" type="text"
								class="form-control" value="<?php echo (isset($it21[4])) ? $it21[4] : 'Dibujo Técnico II'; ?>" id="it21_4">
							</div>
						</div>
							</td>

								<td>
						<legend>Itinerario 2</legend>
						<div class="form-group">
							<label for="it22_0" class="col-sm-5 control-label">Nombre</label>
							<div class="input-group col-sm-5">
							<input name="it22_0" type="text"
								class="form-control" value="<?php echo (isset($it22[0])) ? $it22[0] : 'Bachillerato de Ciencias'; ?>" id="it22_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it22_1" class="col-sm-5 control-label">Modalidad</label>
							<div class="input-group col-sm-5">
							<input name="it22_1" type="text"
								class="form-control" value="<?php echo (isset($it22[1])) ? $it22[1] : 'Ciencias y Ciencias de la Salud'; ?>" id="it22_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it22_2" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it22_2" type="text"
								class="form-control" value="<?php echo (isset($it22[2])) ? $it22[2] : 'Matemáticas II'; ?>" id="it22_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it22_3" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it22_3" type="text"
								class="form-control" value="<?php echo (isset($it22[3])) ? $it22[3] : 'Química'; ?>" id="it22_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it22_4" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it22_4" type="text"
								class="form-control" value="<?php echo (isset($it22[4])) ? $it22[4] : 'Biología'; ?>" id="it22_4">
							</div>
						</div>
								</td>

								<td>
						<legend>Itinerario 3</legend>
						<div class="form-group">
							<label for="it23_0" class="col-sm-5 control-label">Nombre</label>
							<div class="input-group col-sm-5">
							<input name="it23_0" type="text"
								class="form-control" value="<?php echo (isset($it23[0])) ? $it23[0] : 'Bachillerato de Humanidades'; ?>" id="it23_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it23_1" class="col-sm-5 control-label">Modalidad</label>
							<div class="input-group col-sm-5">
							<input name="it23_1" type="text"
								class="form-control" value="<?php echo (isset($it23[1])) ? $it23[1] : 'Humanidades'; ?>" id="it23_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it23_2" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it23_2" type="text"
								class="form-control" value="<?php echo (isset($it23[2])) ? $it23[2] : 'Latín II'; ?>" id="it23_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it23_3" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it23_3" type="text"
								class="form-control" value="<?php echo (isset($it23[3])) ? $it23[3] : 'Historia del Arte'; ?>" id="it23_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it23_4" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it23_4" type="text"
								class="form-control" value="<?php echo (isset($it23[4])) ? $it23[4] : 'Griego II'; ?>" id="it23_4">
							</div>
						</div>
								</td>
							<td>
						<legend>Itinerario 4</legend>
						<div class="form-group">
							<label for="it24_0" class="col-sm-5 control-label">Nombre</label>
							<div class="input-group col-sm-5">
							<input name="it24_0" type="text"
								class="form-control" value="<?php echo (isset($it24[5])) ? $it24[5] : 'Bachillerato de Ciencias Sociales'; ?>" id="it24_0">
							</div>
						</div>
						<div class="form-group">
							<label for="it24_1" class="col-sm-5 control-label">Modalidad</label>
							<div class="input-group col-sm-5">
							<input name="it24_1" type="text"
								class="form-control" value="<?php echo (isset($it24[1])) ? $it24[1] : 'Ciencias Sociales y Jurídicas'; ?>" id="it24_1">
							</div>
						</div>
						<div class="form-group">
							<label for="it24_2" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="it24_2" type="text"
								class="form-control" value="<?php echo (isset($it24[2])) ? $it24[2] : 'Matemáticas de las Ciencias Sociales II'; ?>" id="it24_2">
							</div>
						</div>
						<div class="form-group">
							<label for="it24_3" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="it24_3" type="text"
								class="form-control" value="<?php echo (isset($it24[3])) ? $it24[3] : 'Geografía'; ?>" id="it24_3">
							</div>
						</div>
						<div class="form-group">
							<label for="it24_4" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="it24_4" type="text"
								class="form-control" value="<?php echo (isset($it24[4])) ? $it24[4] : 'Economía'; ?>" id="it24_4">
							</div>
						</div>

							</td>
						</tr>
						<tr>
							<td colspan=3>
									<legend>Optativas de Modalidad de los distintos itinerarios</legend>
							</td>
						<tr>
						<tr>
							<td>
								<legend>Itinerario 1</legend>
						<div class="form-group">
							<label for="opt21_0" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt21_0" type="text"
								class="form-control" value="<?php echo (isset($ar_opt21[0])) ? $ar_opt21[0] : 'Inglés 2º Idioma 1'; ?>" id="opt21_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt21_1" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt21_1" type="text"
								class="form-control" value="<?php echo (isset($ar_opt21[1])) ? $ar_opt21[1] : 'Francés 2º Idioma 1'; ?>" id="opt21_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt21_2" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt21_2" type="text"
								class="form-control" value="<?php echo (isset($ar_opt21[2])) ? $ar_opt21[2] : 'Alemán 2º Idioma 1'; ?>" id="opt21_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt21_3" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt21_3" type="text"
								class="form-control" value="<?php echo (isset($ar_opt21[3])) ? $ar_opt21[3] : 'Tecnología Industrial 1 II'; ?>" id="opt21_3">
							</div>
						</div>
						<div class="form-group">
							<label for="opt21_4" class="col-sm-5 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt21_4" type="text"
								class="form-control" value="<?php echo (isset($ar_opt21[4])) ? $ar_opt21[4] : 'Ciencias de la Tierra y del Medio Ambiente 1'; ?>" id="opt21_4">
							</div>
						</div>
						<div class="form-group">
							<label for="opt21_5" class="col-sm-5 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt21_5" type="text"
								class="form-control" value="<?php echo (isset($ar_opt21[5])) ? $ar_opt21[5] : 'Psicología 1'; ?>" id="opt21_5">
							</div>
						</div>
						<div class="form-group">
							<label for="opt21_6" class="col-sm-5 control-label">Optativa 7</label>
							<div class="input-group col-sm-5">
							<input name="opt21_6" type="text"
								class="form-control" value="<?php echo (isset($ar_opt21[6])) ? $ar_opt21[6] : 'Geología 1'; ?>" id="opt21_6">
							</div>
						</div>
						<div class="form-group">
							<label for="opt21_7" class="col-sm-5 control-label">Optativa 8</label>
							<div class="input-group col-sm-5">
							<input name="opt21_7" type="text"
								class="form-control" value="<?php echo (isset($ar_opt21[7])) ? $ar_opt21[7] : 'TIC 1 II'; ?>" id="opt21_7">
							</div>
						</div>
						<div class="form-group">
							<label for="opt21_8" class="col-sm-5 control-label">Optativa 9</label>
							<div class="input-group col-sm-5">
							<input name="opt21_8" type="text"
								class="form-control" value="<?php echo (isset($ar_opt21[8])) ? $ar_opt21[8] : ''; ?>" id="opt21_8">
							</div>
						</div>
							</td>

									<td>
								<legend>Itinerario 2</legend>
						<div class="form-group">
							<label for="opt22_0" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt22_0" type="text"
								class="form-control" value="<?php echo (isset($ar_opt22[0])) ? $ar_opt22[0] : 'Inglés 2º Idioma 2'; ?>" id="opt22_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt22_1" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt22_1" type="text"
								class="form-control" value="<?php echo (isset($ar_opt22[1])) ? $ar_opt22[1] : 'Francés 2º Idioma 2'; ?>" id="opt22_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt22_2" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt22_2" type="text"
								class="form-control" value="<?php echo (isset($ar_opt22[2])) ? $ar_opt22[2] : 'Alemán 2º Idioma 2'; ?>" id="opt22_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt22_3" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt22_3" type="text"
								class="form-control" value="<?php echo (isset($ar_opt22[3])) ? $ar_opt22[3] : 'Tecnología Industrial 2 II'; ?>" id="opt22_3">
							</div>
						</div>
						<div class="form-group">
							<label for="opt22_4" class="col-sm-5 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt22_4" type="text"
								class="form-control" value="<?php echo (isset($ar_opt22[4])) ? $ar_opt22[4] : 'Ciencias de la Tierra y del Medio Ambiente 2'; ?>" id="opt22_4">
							</div>
						</div>
						<div class="form-group">
							<label for="opt22_5" class="col-sm-5 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt22_5" type="text"
								class="form-control" value="<?php echo (isset($ar_opt22[5])) ? $ar_opt22[5] : 'Psicología 2'; ?>" id="opt22_5">
							</div>
						</div>
						<div class="form-group">
							<label for="opt22_6" class="col-sm-5 control-label">Optativa 7</label>
							<div class="input-group col-sm-5">
							<input name="opt22_6" type="text"
								class="form-control" value="<?php echo (isset($ar_opt22[6])) ? $ar_opt22[6] : 'Geología 2'; ?>" id="opt22_6">
							</div>
						</div>
						<div class="form-group">
							<label for="opt22_7" class="col-sm-5 control-label">Optativa 8</label>
							<div class="input-group col-sm-5">
							<input name="opt22_7" type="text"
								class="form-control" value="<?php echo (isset($ar_opt22[7])) ? $ar_opt22[7] : 'TIC 2 II'; ?>" id="opt22_7">
							</div>
						</div>
						<div class="form-group">
							<label for="opt22_8" class="col-sm-5 control-label">Optativa 9</label>
							<div class="input-group col-sm-5">
							<input name="opt22_8" type="text"
								class="form-control" value="<?php echo (isset($ar_opt22[8])) ? $ar_opt22[8] : 'Física 2'; ?>" id="opt22_8">
							</div>
						</div>
							</td>
								<td>
								<legend>Itinerario 3</legend>
						<div class="form-group">
							<label for="opt23_0" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt23_0" type="text"
								class="form-control" value="<?php echo (isset($ar_opt23[0])) ? $ar_opt23[0] : 'Inglés 2º Idioma 3'; ?>" id="opt23_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt23_1" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt23_1" type="text"
								class="form-control" value="<?php echo (isset($ar_opt23[1])) ? $ar_opt23[1] : 'Francés 2º Idioma 3'; ?>" id="opt23_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt23_2" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt23_2" type="text"
								class="form-control" value="<?php echo (isset($ar_opt23[2])) ? $ar_opt23[2] : 'Alemán 2º Idioma 3'; ?>" id="opt23_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt23_3" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt23_3" type="text"
								class="form-control" value="<?php echo (isset($ar_opt23[3])) ? $ar_opt23[3] : 'TIC II 3'; ?>" id="opt23_3">
							</div>
						</div>
						<div class="form-group">
							<label for="opt23_4" class="col-sm-5 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt23_4" type="text"
								class="form-control" value="<?php echo (isset($ar_opt23[4])) ? $ar_opt23[4] : ''; ?>" id="opt23_4">
							</div>
						</div>
						<div class="form-group">
							<label for="opt23_5" class="col-sm-5 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt23_5" type="text"
								class="form-control" value="<?php echo (isset($ar_opt23[5])) ? $ar_opt23[5] : ''; ?>" id="opt23_5">
							</div>
						</div>
						<div class="form-group">
							<label for="opt23_6" class="col-sm-5 control-label">Optativa 7</label>
							<div class="input-group col-sm-5">
							<input name="opt23_6" type="text"
								class="form-control" value="<?php echo (isset($ar_opt23[6])) ? $ar_opt23[6] : ''; ?>" id="opt23_6">
							</div>
						</div>
						<div class="form-group">
							<label for="opt23_7" class="col-sm-5 control-label">Optativa 8</label>
							<div class="input-group col-sm-5">
							<input name="opt23_7" type="text"
								class="form-control" value="<?php echo (isset($ar_opt23[7])) ? $ar_opt23[7] : ''; ?>" id="opt23_7">
							</div>
						</div>
						<div class="form-group">
							<label for="opt23_8" class="col-sm-5 control-label">Optativa 9</label>
							<div class="input-group col-sm-5">
							<input name="opt23_8" type="text"
								class="form-control" value="<?php echo (isset($ar_opt23[8])) ? $ar_opt23[8] : ''; ?>" id="opt23_8">
							</div>
						</div>
							</td>
								<td>
								<legend>Itinerario 4</legend>
						<div class="form-group">
							<label for="opt24_0" class="col-sm-5 control-label">Optativa 1</label>
							<div class="input-group col-sm-5">
							<input name="opt24_0" type="text"
								class="form-control" value="<?php echo (isset($ar_opt24[0])) ? $ar_opt24[0] : 'Inglés 2º Idioma 4'; ?>" id="opt24_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt24_1" class="col-sm-5 control-label">Optativa 2</label>
							<div class="input-group col-sm-5">
							<input name="opt24_1" type="text"
								class="form-control" value="<?php echo (isset($ar_opt24[1])) ? $ar_opt24[1] : 'Francés 2º Idioma 4'; ?>" id="opt24_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt24_2" class="col-sm-5 control-label">Optativa 3</label>
							<div class="input-group col-sm-5">
							<input name="opt24_2" type="text"
								class="form-control" value="<?php echo (isset($ar_opt24[2])) ? $ar_opt24[2] : 'Alemán 2º Idioma 4'; ?>" id="opt24_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt24_3" class="col-sm-5 control-label">Optativa 4</label>
							<div class="input-group col-sm-5">
							<input name="opt24_3" type="text"
								class="form-control" value="<?php echo (isset($ar_opt24[3])) ? $ar_opt24[3] : 'TIC II 4'; ?>" id="opt24_3">
							</div>
						</div>
						<div class="form-group">
							<label for="opt24_4" class="col-sm-5 control-label">Optativa 5</label>
							<div class="input-group col-sm-5">
							<input name="opt24_4" type="text"
								class="form-control" value="<?php echo (isset($ar_opt24[4])) ? $ar_opt24[4] : 'Fundamentos de Administración y Gestión 4'; ?>" id="opt24_4">
							</div>
						</div>
						<div class="form-group">
							<label for="opt24_5" class="col-sm-5 control-label">Optativa 6</label>
							<div class="input-group col-sm-5">
							<input name="opt24_5" type="text"
								class="form-control" value="<?php echo (isset($ar_opt24[5])) ? $ar_opt24[5] : ''; ?>" id="opt24_5">
							</div>
						</div>
						<div class="form-group">
							<label for="opt24_6" class="col-sm-5 control-label">Optativa 7</label>
							<div class="input-group col-sm-5">
							<input name="opt24_6" type="text"
								class="form-control" value="<?php echo (isset($ar_opt24[6])) ? $ar_opt24[6] : ''; ?>" id="opt24_6">
							</div>
						</div>
						<div class="form-group">
							<label for="opt24_7" class="col-sm-5 control-label">Optativa 8</label>
							<div class="input-group col-sm-5">
							<input name="opt24_7" type="text"
								class="form-control" value="<?php echo (isset($ar_opt24[7])) ? $ar_opt24[7] : ''; ?>" id="opt24_7">
							</div>
						</div>
						<div class="form-group">
							<label for="opt24_8" class="col-sm-5 control-label">Optativa 9</label>
							<div class="input-group col-sm-5">
							<input name="opt24_8" type="text"
								class="form-control" value="<?php echo (isset($ar_opt24[8])) ? $ar_opt24[8] : ''; ?>" id="opt24_8">
							</div>
						</div>
							</td>
							</tr>

							<tr>
								<td colspan=3>
								<legend>Optativas de 2 horas de 2º de Bachillerato</legend>
						<div class="form-group">
							<label for="opt_aut2_0" class="col-sm-3 control-label">Optativa de 2 horas 1</label>
							<div class="input-group col-sm-5">
							<input name="opt_aut2_0" type="text"
							class="form-control" value="<?php echo (isset($ar_opt_aut2[0])) ? $ar_opt_aut2[0] : ''; ?>" id="opt_aut2_0">
							</div>
						</div>
						<div class="form-group">
							<label for="opt_aut2_1" class="col-sm-3 control-label">Optativa de 2 horas 2</label>
							<div class="input-group col-sm-5">
							<input name="opt_aut2_1" type="text"
								class="form-control" value="<?php echo (isset($ar_opt_aut2[1])) ? $ar_opt_aut2[1] : ''; ?>" id="opt_aut2_1">
							</div>
						</div>
						<div class="form-group">
							<label for="opt_aut2_2" class="col-sm-3 control-label">Optativa de 2 horas 3</label>
							<div class="input-group col-sm-5">
							<input name="opt_aut2_2" type="text"
								class="form-control" value="<?php echo (isset($ar_opt_aut2[2])) ? $ar_opt_aut2[2] : ''; ?>" id="opt_aut2_2">
							</div>
						</div>
						<div class="form-group">
							<label for="opt_aut2_3" class="col-sm-3 control-label">Optativa de 2 horas 4</label>
							<div class="input-group col-sm-5">
							<input name="opt_aut2_3" type="text"
								class="form-control" value="<?php echo (isset($ar_opt_aut2[3])) ? $ar_opt_aut2[3] : ''; ?>" id="opt_aut2_3">
							</div>
						</div>
						<div class="form-group">
							<label for="opt_aut2_4" class="col-sm-3 control-label">Optativa de 2 horas 5</label>
							<div class="input-group col-sm-5">
							<input name="opt_aut2_4" type="text"
								class="form-control" value="<?php echo (isset($ar_opt_aut2[4])) ? $ar_opt_aut2[4] : ''; ?>" id="opt_aut2_4">
							</div>
						</div>
						<div class="form-group">
							<label for="opt_aut2_5" class="col-sm-3 control-label">Optativa de 2 horas 6</label>
							<div class="input-group col-sm-5">
							<input name="opt_aut2_5" type="text"
								class="form-control" value="<?php echo (isset($ar_opt_aut2[5])) ? $ar_opt_aut2[5] : ''; ?>" id="opt_aut2_5">
							</div>
						</div>
						<div class="form-group">
							<label for="opt_aut2_6" class="col-sm-3 control-label">Optativa de 2 horas 7</label>
							<div class="input-group col-sm-5">
							<input name="opt_aut2_6" type="text"
								class="form-control" value="<?php echo (isset($ar_opt_aut2[6])) ? $ar_opt_aut2[6] : ''; ?>" id="opt_aut2_6">
							</div>
						</div>
						<div class="form-group">
							<label for="opt_aut2_7" class="col-sm-3 control-label">Optativa de 2 horas 8</label>
							<div class="input-group col-sm-5">
							<input name="opt_aut2_7" type="text"
								class="form-control" value="<?php echo (isset($ar_opt_aut2[7])) ? $ar_opt_aut2[7] : ''; ?>" id="opt_aut2_7">
							</div>
						</div>
								</td>
							</tr>
						</table>
								</td>
							</tr>
						</table>

					</div>

					</div>
				</fieldset>

				<button type="submit" class="btn btn-primary" name="btnGuardar">Guardar cambios</button>
				<?php if (isset($_GET['esAdmin']) && $_GET['esAdmin'] == 1): ?>
				<a href="../../../xml/index.php" class="btn btn-default">Volver</a>
				<?php else: ?>
				<a href="index.php" class="btn btn-default">Volver</a>
				<?php endif; ?>

			</form>
		</div>
	</div>

<?php include("../../pie.php"); ?>
<script>
$(function ()
{
	$('#datetimepicker1').datetimepicker({
		language: 'es',
		pickTime: false
	});

	$('#datetimepicker2').datetimepicker({
		language: 'es',
		pickTime: false
	});
});
</script>
</body>
</html>
