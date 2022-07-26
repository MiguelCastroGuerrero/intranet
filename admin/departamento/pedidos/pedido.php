<?php
require('../../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array(1, 4));

if (file_exists('config.php')) {
	include('config.php');
}


// GUARDAR O ENTREGAR PEDIDO
if (isset($_POST['btnGuardar'])) {

	if (isset($_GET['id']) && intval($_GET['id'])) {
		$id = $_GET['id'];
	}

	$form_departamento	= mysqli_real_escape_string($db_con, $_POST['departamento']);
	$form_responsable	= mysqli_real_escape_string($db_con, $_POST['responsable']);
	$form_numacta		= mysqli_real_escape_string($db_con, $_POST['numacta']);
	$form_justificacion	= mysqli_real_escape_string($db_con, trim($_POST['justificacion']));
	$form_numarticulos	= mysqli_real_escape_string($db_con, $_POST['numarticulos']);
	$form_incidencias	= mysqli_real_escape_string($db_con, trim($_POST['incidencias']));
	$form_condiciones	= mysqli_real_escape_string($db_con, $_POST['condiciones']);
	$form_fechareg		= date('Y-m-d H:i:s');

	if (empty($form_departamento) || empty($form_responsable) || empty($form_numacta) || empty($form_justificacion)) {

		if (empty($form_departamento)) $departamento_has_error = 1;
		if (empty($form_responsable)) $responsable_has_error = 1;
		if (empty($form_numacta)) $numacta_has_error = 1;
		if (empty($form_justificacion)) $justificacion_has_error = 1;

		$msg_error = "Debe cumplimentar los campos marcados en rojo.";

	}
	elseif ($form_condiciones != 1) {
		$msg_error .= "Debe leer y aceptar las Condiciones de compras de los Departamentos";
	}
	else {

		if (isset($id)) {
			// ACTUALIZACION EN LA BASE DE DATOS
			$result = mysqli_query($db_con, "UPDATE depto_pedidos SET responsable = '$form_responsable', id_acta = '$form_numacta', justificacion = '$form_justificacion', incidencias = '$form_incidencias', condiciones = '$form_condiciones' WHERE id = '$id' LIMIT 1");

			if (! $result) {
				$msg_error = "Ha ocurrido un error al modificar el pedido. Error: ".mysqli_error($db_con);
			}
			else {
				$msg_success = "El pedido ha sido modificado correctamente.";
			}
		}
		else {
			// REGISTRO EN LA BASE DE DATOS
			$result = mysqli_query($db_con, "INSERT INTO depto_pedidos (departamento, responsable, id_acta, justificacion, incidencias, condiciones, fechaRegistro) VALUES ('$form_departamento', '$form_responsable', '$form_numacta', '$form_justificacion', '$form_incidencias', '$form_condiciones', '$form_fechareg')");

			if (! $result) {
				$msg_error = "Ha ocurrido un error al registrar el pedido. Error: ".mysqli_error($db_con);
			}
			else {
				$id_pedido = mysqli_insert_id($db_con);
				header('Location:'.'pedido.php?id='.$id_pedido.'&msg=registrado');
			}
		}

		// DETALLES DEL PEDIDO
		$id_pedido = mysqli_insert_id($db_con);

		for ($i = 1; $i < $form_numarticulos+1; $i++) {
			$form_articulo	= mysqli_real_escape_string($db_con, $_POST['articulo'.$i]);
			$form_cantidad	= mysqli_real_escape_string($db_con, $_POST['cantidad'.$i]);
			$form_importe	= mysqli_real_escape_string($db_con, $_POST['importe'.$i]);

			// REGISTRO DETALLES DEL PEDIDO EN LA BASE DE DATOS
			if (! empty($form_articulo) && $form_cantidad > 0 && $form_importe >= 0) {

				if (isset($id) && isset($_POST['id_articulo'.$i])) {
					$form_idarticulo	= mysqli_real_escape_string($db_con, $_POST['id_articulo'.$i]);

					$result = mysqli_query($db_con, "UPDATE depto_pedidos_detalles SET articulo = '$form_articulo', cantidad = '$form_cantidad', importe = '$form_importe' WHERE id = '$form_idarticulo' LIMIT 1");
				}
				elseif (isset($id) && ! isset($_POST['id_articulo'.$i])) {
					$result = mysqli_query($db_con, "INSERT INTO depto_pedidos_detalles (id_pedido, articulo, cantidad, importe) VALUES ('$id', '$form_articulo', '$form_cantidad', '$form_importe')");
				}
				else {
					$result = mysqli_query($db_con, "INSERT INTO depto_pedidos_detalles (id_pedido, articulo, cantidad, importe) VALUES ('$id_pedido', '$form_articulo', '$form_cantidad', '$form_importe')");
				}
			}
		}
	}

}

if (isset($_GET['id']) && intval($_GET['id'])) {

	$id = $_GET['id'];

	$result_datos_pedido = mysqli_query($db_con, "SELECT * FROM depto_pedidos WHERE id = '$id'");
	$datos_pedido = mysqli_fetch_array($result_datos_pedido);

	if (! acl_permiso($_SESSION['cargo'], array(1)) && $datos_pedido['departamento'] != $dpto) {
		$msg_error = "El pedido que intenta editar no es de su departamento.";

		unset($id);
		unset($datos_pedido);
	}
	else {

		if ($datos_pedido['entregado'] == 1) $campoBloqueado = 1;
		elseif ($datos_pedido['entregado'] == 1 && acl_permiso($_SESSION['cargo'], array(1))) $campoBloqueado = 0;
		else $campoBloqueado = 0;

		$result_articulos_pedido = mysqli_query($db_con, "SELECT * FROM depto_pedidos_detalles WHERE id_pedido = '$id'");
		$num_articulos_pedido = mysqli_num_rows($result_articulos_pedido);

	}

}

include("../../../menu.php");
include("../menu.php");
include("menu.php");
?>

<div class="container">

	<div class="page-header">
		<h2>Pedido de material <small><?php echo (isset($_GET['id'])) ? 'Editar pedido' : 'Nuevo pedido'; ?></small></h2>
	</div>

	<!-- MENSAJES -->
	<?php if (isset($msg_error)): ?>
	<div class="alert alert-danger">
		<?php echo $msg_error; ?>
	</div>
	<?php endif; ?>

	<?php if (isset($msg_success)): ?>
	<div class="alert alert-success">
		<?php echo $msg_success; ?>
	</div>
	<?php endif; ?>

	<?php if (isset($_GET['msg'])): ?>
	<div class="alert alert-success">
		El pedido ha sido registrado correctamente.
	</div>
	<?php endif; ?>


	<div class="row">

		<div class="col-sm-12">

			<form id="pedido" method="post" action="">

				<div class="row">

					<div class="col-sm-4">

						<div class="form-group<?php echo (isset($departamento_has_error)) ? ' has-error' : ''; ?>">
							<label for="departamento">Departamento</label>
							<input type="text" class="form-control" id="departamento" name="departamento" value="<?php echo (isset($datos_pedido['departamento'])) ? $datos_pedido['departamento'] : $dpto; ?>" <?php echo (isset($campoBloqueado) && $campoBloqueado == 1) ? 'disabled' : 'readonly'; ?>>
						</div>

					</div>

					<div class="col-sm-4">

						<div class="form-group<?php echo (isset($responsable_has_error)) ? ' has-error' : ''; ?>">
							<label for="responsable">Responsable del departamento / compra</label>
							<select class="form-control" id="responsable" name="responsable" <?php echo (isset($campoBloqueado) && $campoBloqueado == 1) ? 'disabled' : ''; ?>>
								<?php if (isset($datos_pedido['departamento'])): ?>
								<?php $result = mysqli_query($db_con, "SELECT idea, nombre, cargo FROM departamentos WHERE departamento = '".$datos_pedido['departamento']."'"); ?>
								<?php else: ?>
								<?php $result = mysqli_query($db_con, "SELECT idea, nombre, cargo FROM departamentos WHERE departamento = '$dpto'"); ?>
								<?php endif; ?>
								<?php while ($row = mysqli_fetch_array($result)): ?>
								<option value="<?php echo $row['idea']; ?>" <?php if (isset($datos_pedido['responsable']) && $datos_pedido['responsable'] == $row['idea']) echo 'selected'; elseif (stristr($row['cargo'], '4') == TRUE) echo 'selected'; ?>><?php echo $row['nombre']; ?></option>
								<?php endwhile; ?>
							</select>
						</div>

					</div>

					<div class="col-sm-4">

						<div class="form-group<?php echo (isset($numacta_has_error)) ? ' has-error' : ''; ?>" id="datetimepicker1">
							<label for="numacta">Nº Acta de toma de acuerdo</label>
							<select class="form-control" id="numacta" name="numacta" <?php echo (isset($campoBloqueado) && $campoBloqueado == 1) ? 'disabled' : ''; ?>>
								<option value=""></option>
								<?php if (isset($datos_pedido['departamento'])): ?>
								<?php $result = mysqli_query($db_con, "SELECT id, numero, fecha FROM r_departamento WHERE departamento = '".$datos_pedido['departamento']."' ORDER BY fecha DESC"); ?>
								<?php else: ?>
								<?php $result = mysqli_query($db_con, "SELECT id, numero, fecha FROM r_departamento WHERE departamento = '$dpto' ORDER BY fecha DESC"); ?>
								<?php endif; ?>
								<?php while ($row = mysqli_fetch_array($result)): ?>
								<option value="<?php echo $row['id']; ?>"<?php if (isset($datos_pedido['id_acta']) && $datos_pedido['id_acta'] == $row['id']) echo 'selected'; elseif (isset($form_numacta) && $form_numacta == $row['id']) echo ' selected'; ?>>Acta <?php echo $row['numero']; ?> - Fecha: <?php echo $row['fecha']; ?></option>
								<?php endwhile; ?>
							</select>
						</div>

					</div>

				</div>

				<div class="form-group<?php echo (isset($justificacion_has_error)) ? ' has-error' : ''; ?>">
					<label for="justificacion">Justificación de la necesidad</label>
					<textarea class="form-control" id="justificacion" name="justificacion" rows="3" <?php echo (isset($campoBloqueado) && $campoBloqueado == 1) ? 'disabled' : ''; ?>><?php echo (isset($datos_pedido['justificacion'])) ? $datos_pedido['justificacion'] : $form_justificacion; ?></textarea>
				</div>

				<div class="row header hidden-xs">

					<div class="col-sm-8">
						<label>
							Artículo
						</label>
					</div>

					<div class="col-sm-2">
						<label>
							Unidades
						</label>
					</div>

					<div class="col-sm-2">
						<label>
							Precio
						</label>
					</div>

				</div>


				<div id="listaArticulos">

				<?php $cont = 0; ?>
				<?php while ($row_articulo = mysqli_fetch_array($result_articulos_pedido)): ?>
				<?php $cont++; ?>
				<div class="visible-xs" style="margin-top: 15px;"><label>Artículo <?php echo $cont; ?></label></div>

				<div class="row grupo" style="margin-bottom: 8px;">

					<div class="col-sm-8">
						<input type="hidden" name="id_articulo<?php echo $cont; ?>" value="<?php echo $row_articulo['id']; ?>" />
						<input type="text" class="form-control" name="articulo<?php echo $cont; ?>" value="<?php echo $row_articulo['articulo']; ?>" <?php echo (isset($campoBloqueado) && $campoBloqueado == 1) ? 'disabled' : ''; ?> />
					</div>

					<div class="col-sm-2">
						<input type="number" class="form-control" name="cantidad<?php echo $cont; ?>" min="0" value="<?php echo $row_articulo['cantidad']; ?>" <?php echo (isset($campoBloqueado) && $campoBloqueado == 1) ? 'disabled' : ''; ?> />
					</div>

					<div class="col-sm-2">
						<div class="input-group">
							<input type="number" class="form-control" name="importe<?php echo $cont; ?>" min="0" step="0.01" value="<?php echo $row_articulo['importe']; ?>" <?php echo (isset($campoBloqueado) && $campoBloqueado == 1) ? 'disabled' : ''; ?> />
							<span class="input-group-addon">&euro;</span>
						</div>

					</div>

				</div>
				<?php endwhile; ?>

				</div>

				<input type="hidden" id="numArticulos" name="numarticulos" value="1">

				<?php if (! (isset($campoBloqueado) && $campoBloqueado == 1)): ?>
				<div class="row">
					<div class="col-xs-12 col-sm-2">
						<button type="button" id="nuevoArticulo" class="btn btn-success btn-block">Añadir artículo</button>
					</div>
				</div>
				<?php endif; ?>

				<br>


				<div class="form-group">
					<label for="incidencias">Incidencias</label>
					<textarea class="form-control" id="incidencias" name="incidencias" rows="3" <?php echo (isset($campoBloqueado) && $campoBloqueado == 1) ? 'disabled' : ''; ?>><?php echo (isset($datos_pedido['incidencias'])) ? $datos_pedido['incidencias'] : $form_incidencias; ?></textarea>
				</div>

				<div class="form-group">
					<div class="checkbox">
					  <label for="condiciones">
					    <input type="checkbox" id="condiciones" name="condiciones" value="1" <?php echo (isset($datos_pedido['condiciones']) && $datos_pedido['condiciones'] == 1) ? 'checked' : ''; ?> <?php echo (isset($campoBloqueado) && $campoBloqueado == 1) ? 'disabled' : ''; ?>>
					    He leído y acepto las <a href="#" data-toggle="modal" data-target="#modalCondiciones">Condiciones de compras de los Departamentos</a>.
					  </label>
					</div>
				</div>

				<br>

				<div class="form-group">
					<?php if (isset($campoBloqueado) && $campoBloqueado == 1): ?>
					<a href="index.php" class="btn btn-default">Volver</a>
					<?php else: ?>
					<button type="submit" class="btn btn-primary" name="btnGuardar">Guardar pedido</button>
					<?php endif; ?>
				</div>

			</form>

		</div>

	</div>

</div>


<div id="modalCondiciones" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Condiciones de compras de los Departamentos</h4>
      </div>
      <div class="modal-body">
      	<?php if (isset($config['pedidos']['condiciones'])): ?>
      	<?php echo htmlspecialchars_decode($config['pedidos']['condiciones']); ?>
      	<?php else: ?>
        <p>El presupuesto del centro determinará la asignación de partidas específicas a cada Departamento en función del número de miembros del Departamento y de necesidades fundamentadas y para ello, cada Departamento entregará en Secretaría un proyecto escrito de presupuesto de funcionamiento anual.</p>

        <p>En él deberán detallarse las partidas que componen el gasto desglosado por conceptos (material general, fungible, bibliografía, actividades, repografía, etc.) y valorarlas con la mayor exactitud posible (I.V.A. incluido).</p>

        <p>Para la adquisición de material se sigue el siguiente protocolo:</p>

        <ol>
        	<li>El primer paso es recoger en acta el acuerdo del Departamento</li>
        	<li>Todo gasto deberá contar previamente con la conformidad del Secretario/a y del Director/a mediante petición por escrito</li>
        	<li>Se solicitará en el momento de la compra una factura con todos los requisitos legales oportunos:
        		<ul>
        			<li>Factura a nombre del <?php echo $config['centro_denominacion']; ?></li>
        			<li>NIF de la Consejería de Desarrollo Educativo y Formación Profesional de la Junta de Andalucía: S4111001F</li>
        			<li>CIF o NIF del proveedor</li>
        			<li>Nombre del proveedor</li>
        			<li>Fecha y número de la factura</li>
        			<li>Firma y sello de la empresa proveedora</li>
        		</ul>
        	</li>
        	<li>El original del albarán o factura se entregará al Secretario/a, quedando una copia en poder del Jefe de Departamento correspondiente</li>
        	<li>El Secretario/a sólo entregará a los Jefes de Departamento el valor nominal de la compra efectuada</li>
        </ol>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Entendido</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php include("../../../pie.php"); ?>

<?php if (! (isset($campoBloqueado) && $campoBloqueado == 1)): ?>
<script>
$(document).ready(function(){

    var num = <?php echo (isset($num_articulos_pedido)) ? $num_articulos_pedido : 0; ?>;
    function nuevoArticulo() {
    	num++;
    	$('#listaArticulos').append('<div class="visible-xs" style="margin-top: 15px;"><label>Artículo '+num+'</label></div><div class="row grupo" style="margin-bottom: 8px;"><div class="col-sm-8"><input type="text" class="form-control" name="articulo'+num+'" value="" /></div><div class="col-sm-2"><input type="number" class="form-control" name="cantidad'+num+'" min="0" value="0" /></div><div class="col-sm-2"><div class="input-group"><input type="number" class="form-control" name="importe'+num+'" min="0" step="0.01" value="0.00"/><span class="input-group-addon">&euro;</span></div></div></div>');

    	$('#numArticulos').val(num);
    }

    nuevoArticulo();

    $("#nuevoArticulo").click(function() {
    	<?php if (isset($config['pedidos']['limite_articulos']) && $config['pedidos']['limite_articulos'] > 0): ?>
    	if (num < <?php echo $config['pedidos']['limite_articulos']; ?>) {
    	<?php endif; ?>
    	nuevoArticulo();
    	<?php if (isset($config['pedidos']['limite_articulos']) && $config['pedidos']['limite_articulos'] > 0): ?>
    	}

    	if (num == <?php echo $config['pedidos']['limite_articulos']; ?>) {
    		$('#nuevoArticulo').prop( "disabled", true);
    	}
    	else {
    		$('#nuevoArticulo').prop( "disabled", false);
    	}
    	<?php endif; ?>
    });

});
</script>
<?php endif; ?>

</body>
</html>
