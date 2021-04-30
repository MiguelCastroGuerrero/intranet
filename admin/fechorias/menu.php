<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed');

$activo1="";
$activo2="";
$activo3="";
$activo4="";
$activo5="";
$activo6="";
$activo7="";
if (strstr($_SERVER['REQUEST_URI'],'cfechorias.php')==TRUE) {$activo1 = ' class="active" ';}
if (strstr($_SERVER['REQUEST_URI'],'infechoria.php')==TRUE){ $activo2 = ' class="active" ';}
if (strstr($_SERVER['REQUEST_URI'],'lfechorias.php')==TRUE){ $activo3 = ' class="active" ';}
if (strstr($_SERVER['REQUEST_URI'],'expulsados.php')==TRUE){ $activo4 = ' class="active" ';}
if (strstr($_SERVER['REQUEST_URI'],'convivencia.php')==TRUE){ $activo5 = ' class="active" ';}
if (strstr($_SERVER['REQUEST_URI'],'lfechorias3')==TRUE){ $activo6 = ' class="active" ';}
?>
<div class="container hidden-print">

	<?php if (acl_permiso($carg, array('1'))): ?>
	<a href="preferencias.php" class="btn btn-sm btn-default pull-right"><span class="fas fa-cog fa-lg"></span></a>
	<?php endif; ?>

	<ul class="nav nav-tabs">
		<li <?php echo $activo2;?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/fechorias/infechoria.php">
		Registrar Problema</a></li>
		<li <?php echo $activo1;?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/fechorias/cfechorias.php">
		Consultar Problemas</a></li>
		<li <?php echo $activo3;?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/fechorias/lfechorias.php">
		Últimos Problemas</a></li>
		<li <?php echo $activo4;?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/fechorias/expulsados.php">
		Alumnos expulsados</a></li>

		<?php if (isset($config['convivencia']['compromiso_convivencia']) && $config['convivencia']['compromiso_convivencia']): ?>
		<li<?php echo (strstr($_SERVER['REQUEST_URI'],'compromisos.php') == TRUE) ? ' class="active"' : ''; ?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/fechorias/compromisos.php">Compromisos de convivencia</a></li>
		<?php endif; ?>
		</li>
		<?php
		$pr_conv = $_SESSION['profi'];
		$conv = mysqli_query($db_con, "SELECT DISTINCT nombre FROM departamentos WHERE cargo like '%b%' AND nombre = '$pr_conv'");
		// echo "select distinct prof from horw where a_asig = 'GUCON' and prof = '$pr'";
		if (mysqli_num_rows($conv) > '0' and $config['mod_convivencia']==1) {
			?>
		<li <?php echo $activo5;?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/fechorias/convivencia.php">Aula
		de Convivencia</a></li>
		<?php
		}
		?>
		<?php if(stristr($_SESSION['cargo'],'1') == TRUE and $config['mod_convivencia']==1){ ?>
		<li <?php echo $activo5;?>><a href="//<?php echo $config['dominio']; ?>/intranet/admin/fechorias/convivencia_jefes.php">Aula de Convivencia</a></li>
		<?php } ?>


		<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"> Más... <span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
			<li><a href="informe_convivencia.php">Informes sobre Convivencia</a></li>
			<li><a href="lfechorias3.php">Ranking general</a></li>
			<li><a href="lfechorias3b.php">Ranking tras última Expulsión</a></li>			
		</ul>
		</li>



	</ul>
</div>
