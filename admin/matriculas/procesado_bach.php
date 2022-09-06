<?php defined('INTRANET_DIRECTORY') OR exit('No direct script access allowed'); 

	if ($_POST) {
		foreach ($_POST as $key=>$val){
			$n_curso = substr($curso, 0, 1);
			$curso_anterior = $n_curso-1;

			$a_bd = substr($config['curso_actual'],0,4)+1;
			$tr = explode("-",$key);
			$id_submit = $tr[1];
			$col = $tr[0];
			if ($col == 'confirmado'){$con.=$id_submit." ";} 
			if ($col == 'revisado'){$revis.=$id_submit." ";}
			if ($col == "grupo_actual"){$val=strtoupper($val);}
			if ($col == 'bilinguismo'){$bili.=$id_submit." ";}
		
			//Promocion	
			if ($col=='promociona' and date('m')>"06"){
				mysqli_query($db_con,"update matriculas_bach set promociona='$val' where id = '$id_submit'");
				//echo "update matriculas_bach set promociona='$val' where id = '$id_submit'<br>";
				$nivel_a = mysqli_query($db_con,"select curso from alma where claveal like (select claveal from matriculas_bach where id = '$id_submit')");
				$nivel_ahora = mysqli_fetch_array($nivel_a);
				$c_ahora=substr($nivel_ahora[0],0,1);

				if ($val=='2' and ($n_curso!==$c_ahora)) {
				
				// Resplado de datos modificados
				$n_promo = mysqli_query($db_con, "select promociona, repite, claveal from matriculas_bach where id = '$id_submit'");
				$n_prom = mysqli_fetch_array($n_promo);

				if ($n_prom[1] != 1) {
				
				if ($curso == "2BACH") {
				// Recolocamos datos porque no promociona.						
				mysqli_query($db_con, "insert into matriculas_bach_backup select * from matriculas_bach where id = '$id_submit'");
				
				$idm2 = mysqli_query($db_con, "select idioma2 from matriculas_bach_".$a_bd." where claveal = '".$claveal."'");
  				$idio2 = mysqli_fetch_array($idm2);
  				$idiom2 = $last['idioma2'];

				$cambia_datos = "update matriculas_bach set curso = '1BACH', promociona='2', idioma2 = '$idiom2' where id = '$id_submit'";
				mysqli_query($db_con, $cambia_datos);
 				}

				elseif($curso == "1BACH"){
				mysqli_query($db_con, "insert into matriculas_bach_backup select * from matriculas_bach where id = '$id_submit'");
				$ret_4 = mysqli_query($db_con, "select * from matriculas_".$a_bd." where claveal like (select claveal from matriculas_bach where id = '$id_submit')");
				$ret = mysqli_fetch_array($ret_4);

				$sql="";				
				$sql = "insert into matriculas VALUES (''";
				for ($i = 1; $i < 69; $i++) {
					$sql.=", '$ret[$i]'";
				}
				$sql.=")";
				$n_afect = mysqli_query($db_con, $sql);

				mysqli_query($db_con, "delete from matriculas_bach where id='$id_submit'");
				$cambia_datos = "update matriculas_bach set promociona='2' where id = '$id_submit'";
				mysqli_query($db_con, $cambia_datos);
				}
				}
				}
				else{

					if ($val == "1" and $n_curso==$c_ahora and date('m')=="09" and $n_curso=='1') {
						mysqli_query($db_con, "delete from matriculas_bach where id='$id_submit'");
						mysqli_query($db_con, "insert into matriculas_bach (select * from matriculas_bach_backup where id = '$id_submit')");
						mysqli_query($db_con, "update matriculas_bach set promociona='1' where id = '$id_submit'");
						mysqli_query($db_con, "delete from matriculas_bach_backup where id='$id_submit'");
					}
					else{
						mysqli_query($db_con, "update matriculas_bach set promociona='$val' where id  = '$id_submit'");
					}
				}
			}
			
			mysqli_query($db_con, "update matriculas_bach set $col = '$val' where id = '$id_submit'");
			mysqli_query($db_con, "update matriculas_bach set confirmado = '' where id = '$id_submit'");
			mysqli_query($db_con, "update matriculas_bach set bilinguismo = '' where id = '$id_submit'");
			mysqli_query($db_con, "update matriculas_bach set revisado = '' where id = '$id_submit'");
		}
		
		$tr_con = explode(" ",$con);
		foreach ($tr_con as $clave){
			mysqli_query($db_con, "update matriculas_bach set confirmado = '1' where id = '$clave'");
		}
		$tr_con5 = explode(" ",$revis);
		foreach ($tr_con5 as $clave_revis){
			mysqli_query($db_con, "update matriculas_bach set revisado = '1' where id = '$clave_revis'");
		}
		$tr_con3 = explode(" ",$bili);
		foreach ($tr_con3 as $clave_bili){
			mysqli_query($db_con, "update matriculas_bach set bilinguismo = 'Si' where id = '$clave_bili'");
		}
	}
	?>
	
	
	
	
	
	