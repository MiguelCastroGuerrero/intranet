<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed'); 

foreach($_POST as $val)
{
if (strlen($val)>0) {
	$n+=1;
}
}
if ($n>2) {
	$mostrar_filtro = ' in';
}
?>
<br>
<form action="consultas_bach.php" method="post" name="form2">
<div class="well well-sm hidden-print">

<div class="row">
	<div class="col-sm-2">
<?php
if (file_exists(INTRANET_DIRECTORY . '/config_datos.php')): ?>
  <div class="form-group">
    <label for="c_escolar">Curso escolar</label>
    
    <select class="form-control" id="c_escolar" name="c_escolar" onchange="submit()">
    	<?php $exp_c_escolar = explode("/", $config['curso_actual']); ?>
    	<?php for($i=0; $i<3; $i++): ?>
    	<?php $anio_escolar = $exp_c_escolar[0] - $i; ?>
    	<?php $anio_escolar_sig = substr(($exp_c_escolar[0] - $i + 1), 2, 2); ?>
    	<?php if($i == 0 || (isset($config['db_host_c'.$anio_escolar]) && $config['db_host_c'.$anio_escolar] != "")): ?>
    	<option value="<?php echo $anio_escolar.'/'.$anio_escolar_sig; ?>" <?php if($anio_escolar.'/'.$anio_escolar_sig == $c_escolar) echo "selected";?>><?php echo ($anio_escolar).'/'.($anio_escolar_sig); ?></option>
    	<?php endif; ?>
    	<?php endfor; ?>
    </select>
  </div>
 <?php endif; ?>
</div>
</div>

<div class="row">
<div class="col-sm-4">
<div class="form-group" align="left">
<label>Curso:</label>
<select class="form-control" name="curso" id="curso" onChange="submit()" style="width:272px;">
	<option><?php echo $curso;?></option>
	<option>1BACH</option>
	<option>2BACH</option>
</select>
</div>
</div>
<div class="col-sm-8">
<div class="form-group">	
<label>Unidades:</label>
<div class="checkbox">
<?php					
$tipo0 = "select distinct grupo_actual from matriculas_bach where curso = '$curso' order by grupo_actual";
$tipo10 = mysqli_query($db_con, $tipo0);
  while($tipo20 = mysqli_fetch_array($tipo10))
        {	
        	if ($tipo20[0]=="") {
        		$tipo20[0]="Sin asignar";
        	}

if ($_POST['grupo_actua']) {			
		foreach ($_POST['grupo_actua'] as $grup_actua){
			  if ($grup_actua==$tipo20[0]) {
			  	$chk = " checked ";
			  }
			  else{
			  	$chk = "";
			  }
		}	
	}
?>

	<label>
		<input name='grupo_actua[]' type='checkbox' value='<?php echo $tipo20[0]; ?>' <?php echo $chk; ?>>
<strong class="text-info"><?php echo $tipo20[0]; ?></strong>
</label>
&nbsp;&nbsp;
<?php
        }
						
	?>
    </div>
    </div>
    </div>
	</div>
	
			<div class="panel-group" id="filter">
			  <div class="panel panel-default">
			    <div class="panel-heading">
			      <h4 class="panel-title" align="left">
			        <a data-toggle="collapse" data-parent="#filter" href="#avanzado">
			          <span class="fa fa-filter"></span> Búsqueda avanzada
			        </a>
			      </h4>
			    </div>
			    <div id="avanzado" class="panel-collapse collapse<?php echo $mostrar_filtro;?>">
			      <div class="panel-body">
			      
<div class="row">
<div class="col-sm-3">
<div class="form-group"><label>
		DNI </label><input type="text" class="form-control" name="dn" 
		<?php
		if ($dn) {
			echo "value='$dn'";
		}
		?>
		 />
         </div>
</div>
<div class="col-sm-3">
		<div class="form-group"><label>
		Apellidos </label><input type="text" class="form-control" name="apellid" 
		<?php
		if ($apellid) {
			echo "value='$apellid'";
		}
		?>
		 />
</div>
</div>
<div class="col-sm-3">
<div class="form-group"><label>
		Nombre </label><input type="text" class="form-control" name="nombr" 
		<?php
		if ($nombr) {
			echo "value='$nombr'";
		}
		?>
		 />
         </div>
</div>
<div class="col-sm-3">
<div class="form-group"><label>Modalidad </label><select class="form-control"  name="itinerari">
		<?php
		if ($itinerari) {
			echo "<option>$itinerari</option>";
		}
		?>
			<option></option>
			<option>1</option>
			<option>2</option>
			<option>3</option>
			<option>4</option>	
			<option>5</option>		
		</select>
		</div>
</div>
</div>

<div class="row">

<div class="col-sm-3">
<div class="form-group"><label>Optativas Modalidad </label><select class="form-control"  name="optativ">
		<?php
		if ($optativ) {
			for ($i = 1; $i < 6; $i++) {
			foreach(${opt.$n_curso.$i} as $key=>$val){
			if ($optativ == $key) {
				echo "<option value='$optativ'>$val</option>";
			}
		}			
		}			
		}
		?>
			<option></option>
		<?php
		for ($i = 1; $i < 6; $i++) {
			foreach(${opt.$n_curso.$i} as $key=>$val){
			echo '<option value="'.$key.'">'.$val.'</option>';
		}			
		}
		if($n_curso=="1"){
		for ($i = 1; $i < 2; $i++) {
			foreach(${opt15.$i} as $key=>$val){
			echo '<option value="'.$key.'">'.$val.'</option>';
		}
		}
		}

			
		?>	
		</select>
		</div>
</div>



<?php if($curso=="2BACH"){ ?>
<div class="col-sm-3">
<div class="form-group"><label>Otras Optativas </label><select class="form-control"  name="optativ2">
		<?php


		if ($optativ2) {
			echo '<option value="'.$optativ2.'">'.$optativ2.'</option>';
		}
		?>
			<option></option>
			<option>Economia de la Empresa</option>
			<option>Griego II</option>

		</select></div></div><?php }
else{
	?>
	<div class="col-sm-3">
	<div class="form-group"><label>Optativa 2 (It. 2) </label><select class="form-control"  name="optativ121" >
			<?php
			if ($optativa121) {
				echo "<option>$optativa121</option>";
			}
	?>
			<option></option>
	<?php
			foreach($opt121 as $key=>$val){
				echo '<option value="'.$key.'">'.$val.'</option>';
			}
			?>	
			</select>
			</div>
	</div>
	<div class="col-sm-3">
	<div class="row">
	<div class="col-sm-6">
	<center>
	<div class="form-group"><label>Idioma 1 </label><select class="form-control"  name="idiom1">
		<?php
		if ($idiom1) {
			echo "<option>$idiom1</option>";
		}
		?>
			<option></option>
			<option>Inglés</option>
			<option>Francés</option>
		</select>
</div>
</div>
<div class="col-sm-6">
<div class="form-group">
	<label>Idioma 2 </label><select class="form-control"  name="idiom2">
		<?php
		if ($idiom2) {
			echo "<option>$idiom2</option>";
		}
		?>
			<option></option>
			<option>Alemán</option>
			<option>Francés</option>
		</select>
		</div>
		</div>
	</center>
		</div>
		</div>	
<?php }
		?>

<div class="col-sm-3">
<div class="form-group"><label>Grupo de Origen </label><select class="form-control"  name="letra_grup">
		<?php
		if ($letra_grup) {
			echo "<option>$letra_grup</option>";
		}
		?>
			<option></option>
			<option>A</option>
			<option>B</option>
			<option>C</option>
			<option>D</option>
			<option>E</option>
			<option>F</option>
			<option>G</option>
			<option>H</option>
			<option>I</option>
		</select>
		</div>
</div>
</div>

<div class="row">

<div class="col-sm-3">
<div class="form-group"><label>Grupo Actual </label><select class="form-control"  name="grupo_actua_seg">
		<?php
		if ($grupo_actua_seg) {
			echo "<option>$grupo_actua_seg</option>";
		}
		?>
			<option></option>
			<option>Ninguno</option>
			<option>A</option>
			<option>B</option>
			<option>C</option>
			<option>D</option>
			<option>E</option>
			<option>F</option>
			<option>G</option>
			<option>H</option>
			<option>I</option>
			</select>
			</div>
</div>

<div class="col-sm-3">
<div class="form-group"><label>Transporte escolar </label><select class="form-control"  name="transport">
		<?php
		if ($transport) {
			echo "<option>$transport</option>";
		}
		?>
			<option></option>
			<option>ruta_este</option>
			<option>ruta_oeste</option>
		</select>
		</div>
</div>
<div class="col-sm-3">
<div class="form-group"><label>Religión</span> </label><select class="form-control"  name="religio" id="religion">
		<?php
		if ($religio) {
			echo "<option>$religio</option>";
		}
		?>
			<option></option>
			<option>Religi&oacute;n Cat&oacute;lica</option>
			<option>Religión Islámica</option>
			<option>Religión Judía</option>
			<option>Religión Evangélica</option>
			<option>Cultura Científica</option>
			<option value="Valores Éticos">Educación para la Ciudadanía y los Derechos Humanos</option>
		</select>
		</div>
</div>
<div class="col-sm-3">
<div class="form-group"><label>Centro Origen </label><select class="form-control"  name="colegi">
		<?php
		if ($colegi) {
			echo "<option>$colegi</option>";
		}
		?>
		<option></option>
		<?php 
		$coleg=mysqli_query($db_con, "select distinct colegio from matriculas_bach order by colegio");
		while ($cole=mysqli_fetch_array($coleg)) {
			echo "<option>$cole[0]</option>";
		}
		?>
		</select></div>
</div>
</div>

<div class="row">

<div class="col-sm-4">
<div class="form-group">
	<label >Bilinguismo </label><select class="form-control" name="bilinguism">
		<?php if ($bilinguism) {
			echo "<option>$bilinguism</option>";
		}
		?>
			<option></option>
			<option>Si</option>
			<option>No</option>
		</select>
</div>
</div>

<?php if($curso=="2BACH"){ ?>
<div class="col-sm-4">
<div class="form-group">
<label>Optativa 2Bach. 2h</label><select class="form-control"  name="opt_2h">
		<?php
		if ($opt_2h) {
			echo "<option>$opt_2h</option>";
		}
		?>
			<option></option>
			<option>1</option>
			<option>2</option>
			<option>3</option>
			<option>4</option>
			<option>5</option>
			<option>6</option>
		</select>
</div>
</div>
<?php } ?>

<div class="col-sm-4">
<div class="form-group">
	<label >Problemas de Convivencia </label><select class="form-control" name="fechori">
		<?php if ($fechori) {
			echo "<option>$fechori</option>";
		}
		?>
			<option></option>
			<option>Sin problemas</option>
			<option>1 --> 5</option>
			<option>5 --> 15</option>
			<option>15 --> 1000</option>
		</select>
</div>
</div>
	<div class="col-sm-4">
	<div class="form-group"><label>Promoción </label><select class="form-control"  name="promocion" >
			<?php
			if ($promocion) {
				echo "<option>$promocion</option>";
			}
			?>
				<option></option>
				<option>SI</option>
				<option>NO</option>			
			</select>
			</div>
	</div>

</div>	
<div class="row">
<div class="col-sm-12" align=left>
<strong>Criterio de ordenación<br></strong>
<div class="radio">
<label class="radio-inline">
  <input type="radio" name="op_orden" value="promociona"> Promociona
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="bilinguismo"> Bilingues
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="letra_grupo"> Grupo de origen
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="grupo_actual"> Grupo actual
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="itinerario1"> Itinerario 1 Bach
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="itinerario2"> Itinerario 2 Bach
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="opt_orden"> Optativas
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="religion"> Religion
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="colegio"> Centro escolar
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="idioma1"> Idioma1
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="idioma2"> Idioma2
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="confirmado"> Confirmados
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="repite"> Repite</label>
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="foto"> Foto
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="enfermedad"> Enfermedad
</label>
<label class="radio-inline">
  <input type="radio" name="op_orden" value="divorcio"> Divorcio
</label>
</div>
</div>
</div>
		      
</div>			      

</div>
</div>
</div>
</div>
	
	<input type="submit" name="consulta" value="Ver matrículas" alt="Introducir" class="btn btn-primary hidden-print" />
	
</form>
<br />
</div>
