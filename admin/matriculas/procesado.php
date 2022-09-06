<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed'); 

	if ($_POST) {
		foreach ($_POST as $key=>$val){
			$n_curso = substr($curso, 0, 1);
			$curso_anterior = $n_curso-1;
			//echo "$key --> $val<br>";
			$tr = explode("-",$key);
			$id_submit = $tr[1];
			$col = $tr[0];
			if ($col == 'confirmado'){$con.=$id_submit." ";}
			if ($col == 'exencion'){$exen.=$id_submit." ";}
			if ($col == 'bilinguismo'){$bili.=$id_submit." ";}
			if ($col == 'diversificacion'){$diver.=$id_submit." ";}
			if ($col == 'revisado'){$revis.=$id_submit." ";}
			if ($col == "grupo_actual"){$val=strtoupper($val);}

			// PromociÃ³n
			if ($col=='promociona' and date('m')>"06" and $n_curso>1){

				$nivel_a = mysqli_query($db_con,"select curso, claveal from alma where claveal like (select claveal from matriculas where id = '$id_submit')");
				$nivel_ahora = mysqli_fetch_array($nivel_a);
				$c_ahora=substr($nivel_ahora[0],0,1);
				$clave_al = $nivel_ahora[1];
				if ($val=='2' and ($n_curso!==$c_ahora)) {					
					
				// Resplado de datos modificados
				mysqli_query($db_con, "insert into matriculas_backup select * from matriculas where id = '$id_submit'");
				$promo = "select optativa21, optativa22, optativa23, optativa24, optativa25, optativa26, optativa27, act21 from matriculas where id = '$id_submit'";
				$prom = mysqli_query($db_con, $promo);
				$pro = mysqli_fetch_array($prom);
				$cambia_datos = "update matriculas set optativa1 = '$pro[0]', optativa2 = '$pro[1]', optativa3 = '$pro[2]', optativa4 = '$pro[3]', optativa5 = '$pro[4]', optativa6 = '$pro[5]', optativa7 = '$pro[6]', curso = '".$curso_anterior."ESO' ";
				if ($curso=="4ESO") {
				$cambia_datos.=", itinerario = '', optativas4 = '', ciencias4 = '' ";
				}
				else{
					$cambia_datos.=", act1 = '$pro[7]'";
				}
				$cambia_datos.=" where id = '$id_submit'";
				mysqli_query($db_con, $cambia_datos);
				}
				else{

					if ($val == "1" and $n_curso==$c_ahora and date('m')=="09" and $n_curso<'4') {
						mysqli_query($db_con, "delete from matriculas where id='$id_submit'");
						mysqli_query($db_con, "insert into matriculas (select * from matriculas_backup where id = '$id_submit')");
						mysqli_query($db_con, "update matriculas set promociona='1' where id = '$id_submit'");
						mysqli_query($db_con, "delete from matriculas_backup where id='$id_submit'");
					}
					elseif($val == "1" and $n_curso==$c_ahora and date('m')>"06" and $n_curso=='4'){
						mysqli_query($db_con, "delete from matriculas where id='$id_submit'");
						mysqli_query($db_con, "insert into matriculas_bach (select * from matriculas_bach_backup where claveal = '$clave_al')");
						mysqli_query($db_con, "update matriculas_bach set promociona='0' where claveal = '$clave_al'");
						mysqli_query($db_con, "delete from matriculas_bach_backup where claveal='$clave_al'");
					}
					else{
						mysqli_query($db_con, "update matriculas set promociona='$val' where id  = '$id_submit'");
					}
				}
			}
			
			mysqli_query($db_con, "update matriculas set $col = '$val' where id = '$id_submit'");
			mysqli_query($db_con, "update matriculas set confirmado = '' where id = '$id_submit'");
			mysqli_query($db_con, "update matriculas set exencion = '' where id = '$id_submit'");
			mysqli_query($db_con, "update matriculas set bilinguismo = '' where id = '$id_submit'");
			mysqli_query($db_con, "update matriculas set diversificacion = '' where id = '$id_submit'");	
			mysqli_query($db_con, "update matriculas set revisado = '' where id = '$id_submit'");
		}
		
		$tr_con = explode(" ",$con);
		foreach ($tr_con as $clave){
			mysqli_query($db_con, "update matriculas set confirmado = '1' where id = '$clave'");
		}
		$tr_con2 = explode(" ",$exen);
		foreach ($tr_con2 as $clave_exen){
			mysqli_query($db_con, "insert into matriculas_backup select * from matriculas where id = '$clave_exen'");
			mysqli_query($db_con, "update matriculas set exencion = '1' where id = '$clave_exen'");
		}
	
		$tr_con3 = explode(" ",$bili);
		foreach ($tr_con3 as $clave_bili){
			mysqli_query($db_con, "update matriculas set bilinguismo = 'Si' where id = '$clave_bili'");
		}
		$tr_con4 = explode(" ",$diver);
		foreach ($tr_con4 as $clave_diver){
			mysqli_query($db_con, "insert into matriculas_backup select * from matriculas where id = '$clave_diver'");
			mysqli_query($db_con, "update matriculas set diversificacion = '1', optativa1 = '', optativa2 = '', optativa3 = '', optativa4 = '', optativa5 = '', optativa6 = '', optativa7 = '', act1='', itinerario = '', optativas4 = '' where id = '$clave_diver'");
		}
		$tr_con5 = explode(" ",$revis);
		foreach ($tr_con5 as $clave_revis){
			mysqli_query($db_con, "update matriculas set revisado = '1' where id = '$clave_revis'");
		}
	}
	?>
	
	
	
	
	
	