<?php 
require('../../bootstrap.php');

$auth = smsLogin($config['mod_sms_user'], $config['mod_sms_pass']);
$history = smsHistory($auth);
$credits = smsCredit($auth);

include('../../menu.php');
?>

	<div class="container">
	
		<div class="page-header">
			<h2>Información de envíos SMS</h2>
		</div>
		
		
		<div class="row">
			<?php foreach ($credits->sms as $credit_info): ?>
			<div class="col-sm-4 text-center">
				<h3>
					<span class="lead text-info"><?php echo $credit_info->quantity; ?></span><br>
					<small class="text-uppercase text-muted"><?php echo $credit_info->type; ?></small>
				</h3>
			</div>
			<?php endforeach; ?>
		</div><!-- /.row -->
		
		<br>
		
		<h3>Histórico de envíos <small>(<?php echo count($history->smshistory); ?> envíos en los últimos 3 días)</small></h3>
				
		<div class="row">
		
			<div class="col-sm-12">
				
				<?php if (count($history->smshistory) > 0): ?>
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>ID Orden</th>
							<th>Tipo SMS</th>
							<th>Fecha de envío</th>
							<th>Destinatarios</th>
							<th>Estado</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($history->smshistory as $history_item): ?>
						<?php $smsState = smsState($auth, $history_item->order_id); ?>
						<tr>
							<td><?php echo $history_item->order_id; ?></td>
							<td><?php echo $history_item->message_type; ?></td>
							<td><?php echo $history_item->create_time; ?></td>
							<td>
								<?php foreach ($smsState->recipients as $recipient_info): ?>
								<?php echo $recipient_info->destination; ?><br>
								<?php endforeach; ?>
							</td>
							<td>
								<?php foreach ($smsState->recipients as $recipient_info): ?>
								<?php echo smsStatus($recipient_info->status); ?><br>
								<?php endforeach; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php else: ?>

				<br><br>
				<div class="text-center">
					
					<span class="far fa-mobile fa-4x text-muted"></span>
					<p class="lead text-muted">No se han enviado mensajes en esta semana</p>
					
				</div>
				<br><br>
				<?php endif; ?>

			
			</div><!-- /.col-sm-12 -->
			
		</div><!-- /.row -->

		<div class="row">
			
			<div class="col-sm-12">
				<a href="../index.php" class="btn btn-default">Volver</a>
				<a href="https://extranet.trendoo.es/s/user/login" class="btn btn-danger" target="_blank">Ir a Trendoo</a>
			</div><!-- /.col-sm-12 -->
			
		</div><!-- /.row -->
		
		
	</div><!-- /.container -->

	<?php include('../../pie.php'); ?>
</body>
</html>