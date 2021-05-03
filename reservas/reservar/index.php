<?php
require('../../bootstrap.php');


$pr = $_SESSION['profi'];


include("../../menu.php");
include("../menu.php");

if (isset($_GET['month'])) { $month = $_GET['month']; $month = preg_replace ("/[[:space:]]/", "", $month); $month = preg_replace ("/[[:punct:]]/", "", $month); $month = preg_replace ("/[[:alpha:]]/", "", $month); }
if (isset($_GET['year'])) { $year = $_GET['year']; $year = preg_replace ("/[[:space:]]/", "", $year); $year = preg_replace ("/[[:punct:]]/", "", $year); $year = preg_replace ("/[[:alpha:]]/", "", $year); if ($year < 1990) { $year = 1990; } if ($year > 2035) { $year = 2035; } }
if (isset($_GET['today'])) { $today = $_GET['today']; $today = preg_replace ("/[[:space:]]/", "", $today); $today = preg_replace ("/[[:punct:]]/", "", $today); $today = preg_replace ("/[[:alpha:]]/", "", $today); }

$month = (isset($month)) ? $month : date("n",time());
$year = (isset($year)) ? $year : date("Y",time());
$today = (isset($today))? $today : date("j", time());
$daylong = date("l",mktime(1,1,1,$month,$today,$year));
$monthlong = date("F",mktime(1,1,1,$month,1,$year));
$dayone = date("w",mktime(1,1,1,$month,1,$year))-1;
$numdays = date("t",mktime(1,1,1,$month,1,$year));
$alldays = array('Lun','Mar','Mié','Jue','Vie','Sáb','Dom');
$next_year = $year + 1;
$last_year = $year - 1;
    if ($daylong == "Sunday")
	{$daylong = "Domingo";}
    elseif ($daylong == "Monday")
	{$daylong = "Lunes";}
    elseif ($daylong == "Tuesday")
	{$daylong = "Martes";}
    elseif ($daylong == "Wednesday")
	{$daylong = "Mércoles";}
    elseif ($daylong == "Thursday")
	{$daylong = "Jueves";}
    elseif ($daylong == "Friday")
	{$daylong = "Viernes";}
    elseif ($daylong == "Saturday")
	{$daylong = "Sábado";}


    if ($monthlong == "January")
	{$monthlong = "Enero";}
    elseif ($monthlong == "February")
	{$monthlong = "Febrero";}
    elseif ($monthlong == "March")
	{$monthlong = "Marzo";}
    elseif ($monthlong == "April")
	{$monthlong = "Abril";}
    elseif ($monthlong == "May")
	{$monthlong = "Mayo";}
    elseif ($monthlong == "June")
	{$monthlong = "Junio";}
    elseif ($monthlong == "July")
	{$monthlong = "Julio";}
    if ($monthlong == "August")
	{$monthlong = "Agosto";}
    elseif ($monthlong == "September")
	{$monthlong = "Septiembre";}
    elseif ($monthlong == "October")
	{$monthlong = "Octubre";}
    elseif ($monthlong == "November")
	{$monthlong = "Noviembre";}
    elseif ($monthlong == "December")
	{$monthlong = "Diciembre";}
if ($today > $numdays) { $today--; }

// Estructura de la Tabla
?>

<div class="container">

	<div class="page-header">
	  <h2>Sistema de Reservas <small> Reserva de <?php echo $servicio; ?></small></h2>
	</div>

	<?php if (isset($_GET['mens'])): ?>
	<?php if ($_GET['mens'] == 'actualizar'): ?>
		<div class="alert alert-success">
			La reserva se ha actualizado correctamente.
	  </div>
	<?php elseif ($_GET['mens'] == 'insertar'): ?>
		<div class="alert alert-success">
			La reserva se ha realizado correctamente.
		</div>
	<?php endif; ?>
	<?php endif; ?>

 <div class="row">

	<div class="col-sm-5">
	<?php
	$mes_sig = $month+1;
	$mes_ant = $month-1;
	$ano_ant = $ano_sig = $year;
	if ($mes_ant == 0) {
		$mes_ant = 12;
		$ano_ant = $year-1;
	}
	if ($mes_sig == 13) {
		$mes_sig = 1;
		$ano_sig = $year+1;
	}

	//Nombre del Mes
	echo "<table class=\"table table-bordered table-centered\"><thead><tr>";
	echo "<th><h4><a href=\"".$_SERVER['PHP_SELF']."?servicio=$servicio&year=".$ano_ant."&month=".$mes_ant."\"><span class=\"fas fa-arrow-circle-left fa-fw fa-lg\"></span></a></h4></th>";
	echo "<th colspan=\"5\"><h4>".$monthlong.' '.$year."</h4></th>";
	echo "<th><h4><a href=\"".$_SERVER['PHP_SELF']."?servicio=$servicio&year=".$ano_sig."&month=".$mes_sig."\"><span class=\"fas fa-arrow-circle-right fa-fw fa-lg\"></span></a></h4></th>";
	echo "</tr><tr>";


	//Nombre de DÃ­as
	foreach($alldays as $value) {
	  echo "<th>
	  $value</th>";
	}
	echo "</tr></thead><tbody><tr>";


	//DÃ­as vacÃ­os
	if ($dayone < 0) $dayone = 6;
for ($i = 0; $i < $dayone; $i++) {
	  echo "<td>&nbsp;</td>";
	}

	//DÃ­as
	for ($zz = 1; $zz <= $numdays; $zz++) {
	  if ($i >= 7) {  print("</tr><tr>"); $i=0; }

	  // Enlace
	  $enlace = $_SERVER['PHP_SELF'].'?year='.$year.'&today='.$zz.'&month='.$month.'&servicio='.$servicio;

	  // Mirar a ver si hay alguna ctividad en el dÃ­as
	  $result_found = 0;
	  if ($zz == $today) {
	    echo '<td class="calendar-today"><a href="'.$enlace.'">'.$zz.'</a></td>';
	    $result_found = 1;
	  }

	  // Enlace
	  $enlace = $_SERVER['PHP_SELF'].'?year='.$year.'&today='.$zz.'&month='.$month.'&servicio='.$servicio;

	  if ($result_found != 1) {
			//Buscar actividad para el dóa y marcarla
			$sql_currentday = "$year-$month-$zz";

	    $eventQuery = "SELECT event1, event2, event3, event4, event5, event6, event7, event8, event9, event10, event11, event12, event13, event14 FROM `reservas` WHERE eventdate = '$sql_currentday' and servicio='$servicio';";
					$eventExec = mysqli_query($db_con, $eventQuery );
			if (mysqli_num_rows($eventExec)>0) {
				while ( $row = mysqli_fetch_array ( $eventExec ) ) {
	        echo '<td class="calendar-orange"><a href="'.$enlace.'">'.$zz.'</a></td>';
					$result_found = 1;
				}
			}
			else{
			$sql_currentday = "$year-$month-$zz";
			$fest = mysqli_query($db_con, "select distinct fecha, nombre from $db.festivos WHERE fecha = '$sql_currentday'");
			if (mysqli_num_rows($fest)>0) {
			$festiv=mysqli_fetch_array($fest);
				       echo '<td class="calendar-red">'.$zz.'</td>';
					$result_found = 1;
					}
			}

		}

	  if ($result_found != 1) {
	    echo '<td><a href="'.$enlace.'">'.$zz.'</a></td>';
	  }

	  $i++; $result_found = 0;
	}

	$create_emptys = 7 - (($dayone + $numdays) % 7);
	if ($create_emptys == 7) { $create_emptys = 0; }

	if ($create_emptys != 0) {
	  echo "<td colspan=\"$create_emptys\">&nbsp;</td>";
	}

	echo "</tr></tbody>";
	echo "</table>";
	echo "";
	?>
	</div>

	<div class="col-sm-7">

		<div class="well">
    <?php
  echo "<form method=\"post\" action=\"jcal_post.php?servicio=$servicio&year=$year&today=$today&month=$month\" name=\"jcal_post\">";
	echo "<legend>Reserva para el $daylong, $today de $monthlong</legend><br />";
$sql_date = "$year-$month-$today";
$semana = date( mktime(0, 0, 0, $month, $today, $year));
$hoy = getdate($semana);
$numero_dia = $hoy['wday'];
$eventQuery = "SELECT event1, event2, event3, event4, event5, event6, event7, event8, event9, event10, event11, event12, event13, event14 FROM reservas WHERE eventdate = '$sql_date' and servicio='$servicio'";
$eventExec = mysqli_query($db_con, $eventQuery);
while($row = mysqli_fetch_array($eventExec)) {
  $event_event1 = stripslashes($row["event1"]);
  if (stristr($event_event1, '||') == true) {
    $exp_event_event1 = explode("||", $event_event1);
    $event_event1_profesor = $exp_event_event1[0];
    $event_event1_observacion = $exp_event_event1[1];
  }
  else {
    $event_event1_profesor = $event_event1;
  }
  $event_event2 = stripslashes($row["event2"]);
  if (stristr($event_event2, '||') == true) {
    $exp_event_event2 = explode('||', $event_event2);
    $event_event2_profesor = $exp_event_event2[0];
    $event_event2_observacion = $exp_event_event2[1];
  }
  else {
    $event_event2_profesor = $event_event2;
  }
  $event_event3 = stripslashes($row["event3"]);
  if (stristr($event_event3, '||') == true) {
    $exp_event_event3 = explode('||', $event_event3);
    $event_event3_profesor = $exp_event_event3[0];
    $event_event3_observacion = $exp_event_event3[1];
  }
  else {
    $event_event3_profesor = $event_event3;
  }
  $event_event4 = stripslashes($row["event4"]);
  if (stristr($event_event4, '||') == true) {
    $exp_event_event4 = explode('||', $event_event4);
    $event_event4_profesor = $exp_event_event4[0];
    $event_event4_observacion = $exp_event_event4[1];
  }
  else {
    $event_event4_profesor = $event_event4;
  }
  $event_event5 = stripslashes($row["event5"]);
  if (stristr($event_event5, '||') == true) {
    $exp_event_event5 = explode('||', $event_event5);
    $event_event5_profesor = $exp_event_event5[0];
    $event_event5_observacion = $exp_event_event5[1];
  }
  else {
    $event_event5_profesor = $event_event5;
  }
  $event_event6 = stripslashes($row["event6"]);
  if (stristr($event_event6, '||') == true) {
    $exp_event_event6 = explode('||', $event_event6);
    $event_event6_profesor = $exp_event_event6[0];
    $event_event6_observacion = $exp_event_event6[1];
  }
  else {
    $event_event6_profesor = $event_event6;
  }
  $event_event7 = stripslashes($row["event7"]);
  if (stristr($event_event7, '||') == true) {
    $exp_event_event7 = explode('||', $event_event7);
    $event_event7_profesor = $exp_event_event7[0];
    $event_event7_observacion = $exp_event_event7[1];
  }
  else {
    $event_event7_profesor = $event_event7;
  }
  $event_event8 = stripslashes($row["event8"]);
  if (stristr($event_event8, '||') == true) {
    $exp_event_event8 = explode('||', $event_event8);
    $event_event8_profesor = $exp_event_event8[0];
    $event_event8_observacion = $exp_event_event8[1];
  }
  else {
    $event_event8_profesor = $event_event8;
  }
  $event_event9 = stripslashes($row["event9"]);
  if (stristr($event_event9, '||') == true) {
    $exp_event_event9 = explode('||', $event_event9);
    $event_event9_profesor = $exp_event_event9[0];
    $event_event9_observacion = $exp_event_event9[1];
  }
  else {
    $event_event9_profesor = $event_event9;
  }
  $event_event10 = stripslashes($row["event10"]);
  if (stristr($event_event10, '||') == true) {
    $exp_event_event10 = explode('||', $event_event10);
    $event_event10_profesor = $exp_event_event10[0];
    $event_event10_observacion = $exp_event_event10[1];
  }
  else {
    $event_event10_profesor = $event_event10;
  }
  $event_event11 = stripslashes($row["event11"]);
  if (stristr($event_event11, '||') == true) {
    $exp_event_event11 = explode('||', $event_event11);
    $event_event11_profesor = $exp_event_event11[0];
    $event_event11_observacion = $exp_event_event11[1];
  }
  else {
    $event_event11_profesor = $event_event11;
  }
  $event_event12 = stripslashes($row["event12"]);
  if (stristr($event_event12, '||') == true) {
    $exp_event_event12 = explode('||', $event_event12);
    $event_event12_profesor = $exp_event_event12[0];
    $event_event12_observacion = $exp_event_event12[1];
  }
  else {
    $event_event12_profesor = $event_event12;
  }
  $event_event13 = stripslashes($row["event13"]);
  if (stristr($event_event13, '||') == true) {
    $exp_event_event13 = explode('||', $event_event13);
    $event_event13_profesor = $exp_event_event13[0];
    $event_event13_observacion = $exp_event_event13[1];
  }
  else {
    $event_event13_profesor = $event_event13;
  }
  $event_event14 = stripslashes($row["event14"]);
  if (stristr($event_event14, '||') == true) {
    $exp_event_event14 = explode('||', $event_event14);
    $event_event14_profesor = $exp_event_event14[0];
    $event_event14_observacion = $exp_event_event14[1];
  }
  else {
    $event_event14_profesor = $event_event14;
  }
}

if($_SESSION['profi'] == 'conserje' or stristr($_SESSION['cargo'],'1') == TRUE){$SQL = "select distinct nombre from $db.departamentos order by nombre";}
else{$SQL = "select distinct nombre from $db.departamentos where nombre = '". $_SESSION['profi'] ."'";}



for ($i=1; $i < 15; $i++) { 

  if($servicio){
    $eventQuery2 = "SELECT hora.$i FROM reservas_hor WHERE dia = '$numero_dia' and servicio='$servicio'";
    $reservado0 = mysqli_query($db_con, $eventQuery2);
    if (mysqli_num_rows($reservado0) == 1) {
      $reservado1 = mysqli_fetch_row($reservado0);
  }

  $result_hora_tramo = mysqli_query($db_con, "SELECT `hora_inicio`, `hora_fin` FROM `tramos` WHERE `hora` = '".$i."' LIMIT 1");
  if (mysqli_num_rows($result_hora_tramo)) {
    $row_hora_tramo = mysqli_fetch_array($result_hora_tramo);

    $hora_tramo = substr($row_hora_tramo['hora_inicio'], 0, 5) . " a " . substr($row_hora_tramo['hora_fin'], 0, 5) ." horas";
  }
  else {
    $hora_tramo = "";
  }

  echo '<div class="form-group">';
  if (!(empty($reservado1[0]))) {
    echo "<label>".$i."ª hora - <span class=\"text-muted\">".$hora_tramo."</span></label>";
    echo "<span class='badge badge-warning'>$reservado1[0]</span>"; 
  }
  else {
    if (empty(${event_event.$i._profesor})) { 
      echo "<label>".$i."ª hora - <span class=\"text-muted\">".$hora_tramo."</span></label>";
      echo "<select name=\"day_event".$i."\" class=\"form-control\"><option></option>";
      $result1 = mysqli_query($db_con, $SQL);
      while($row1 = mysqli_fetch_array($result1)){ $profesor = $row1[0];
        echo "<option>" . $profesor . "</option>";
      } 
      echo "</select>";
    }
    else {
      if (mb_strtolower($pr) == mb_strtolower(${event_event.$i._profesor})) {
        echo "<label>".$i."ª Hora - <span class=\"text-muted\">".$hora_tramo."</span></label>";
        echo "<div class=\"form-group\">";
        echo "<input class=\"form-control\" type=\"text\" name=\"day_event".$i."\"  value=\"${event_event.$i._profesor}\">";
        echo "<label><small>Observaciones</small></label>";
        echo "<textarea class=\"form-control\" name=\"day_event".$i."_obs\" rows=\"2\" maxlength=\"190\">${event_event.$i._observacion}</textarea>";
        echo "</div>";
      }
      elseif (acl_permiso($_SESSION['cargo'], array('1'))) {
        echo "<label>".$i."ª hora - <span class=\"text-muted\">".$hora_tramo."</span></label>";
        echo "<div class=\"form-group\">";
        echo "<select name=\"day_event".$i."\" class=\"form-control\"><option></option>";
        $result1 = mysqli_query($db_con, $SQL);
        while($row1 = mysqli_fetch_array($result1)){ $profesor = $row1[0];
          echo "<option value=\"".$profesor."\" ".((${event_event.$i._profesor} == $profesor) ? 'selected' : '').">" . $profesor . "</option>";
        } 
        echo "</select>";
        echo "<label><small>Observaciones</small></label>";
        echo "<textarea class=\"form-control\" name=\"day_event".$i."_obs\" rows=\"2\" maxlength=\"190\">${event_event.$i._observacion}</textarea>";
        echo "</div>";
      }
      else{
        echo "<label>".$i."ª Hora - <span class=\"text-muted\">".$hora_tramo."</span></label>";
        echo "<div class=\"form-group\">";
        echo "<input disabled class=\"form-control\" type=\"text\"  value='${event_event.$i._profesor}'></div>";
        if (isset(${event_event.$i._observacion})){
          echo "<textarea disabled class=\"form-control\" rows=\"2\" maxlength=\"190\">${event_event.$i._observacion}</textarea>";
        }
        echo "</div>";
        echo "<input type=\"hidden\" value=\"${event_event.$i._profesor}\" name=\"day_event".$i."\">";
      }
    }
    echo "</div>";
  }
}
}

echo "<input type=\"hidden\" value=\"$year\" name=\"year\">
      <input type=\"hidden\" value=\"$month\" name=\"month\">
      <input type=\"hidden\" value=\"$today\" name=\"today\">
      <input type=\"submit\" class=\"btn btn-primary\" id=\"formsubmit\" value=\"Reservar\">
    </form>";
echo "</div>";
?>
</div>
</div>
</div>

<?php
include("../../pie.php");
?>
</body>
</html>
