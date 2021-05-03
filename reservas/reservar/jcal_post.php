<?php
require('../../bootstrap.php');

if (isset($_GET['month'])) {
	$month = $_GET['month'];
}
if (isset($_GET['year'])) {
	$year = $_GET['year'];
	}
if (isset($_GET['today'])) {
	$today = $_GET['today'];
	}
if (isset($_GET['servicio'])) {
	$servicio = $_GET['servicio'];
}
for ($i=1;$i<=7;$i++)
{
//echo $_POST['day_event'.$i];
if (isset($_POST['day_event'.$i])) { $day_event{$i} = $_POST['day_event'.$i]; }
elseif (isset($_GET['day_event'.$i])) { $day_event{$i} = $_GET['day_event'.$i]; }
else{$day_event{$i}="";}
}
if (isset($_GET['month'])) { $month = intval($_GET['month']); }
if (isset($_POST['month'])) { $month = intval($_POST['month']); }

if (isset($_GET['year'])) { $year = intval ($_GET['year']); }
if (isset($_POST['year'])) { $year = intval ($_POST['year']); }

if ($year < 1990) { $year = 1990; } if ($year > 2035) { $year = 2035; }

if (isset($_GET['today'])) { $today = intval ($_GET['today']); }
if (isset($_POST['today'])) { $today = intval ($_POST['today']); }


$month = (isset($month)) ? $month : date("n",time());
$year = (isset($year)) ? $year : date("Y",time());
$today = (isset($today))? $today : date("j", time());

$sql_date = "$year-$month-$today";
$semana = date( mktime(0, 0, 0, $month, $today, $year));
$hoy = getdate($semana);
$numero_dia = $hoy['wday'];

$eventQuery = "SELECT id FROM reservas WHERE eventdate = '$sql_date' and servicio = '$servicio'";
$eventExec = mysqli_query($db_con, $eventQuery);
$event_found = "";
while($row = mysqli_fetch_array($eventExec)) {
  //$echo = $row["id"];
  $event_found = 1;
}

$day_event1 = trim($_POST['day_event1']);
$day_event2 = trim($_POST['day_event2']);
$day_event3 = trim($_POST['day_event3']);
$day_event4 = trim($_POST['day_event4']);
$day_event5 = trim($_POST['day_event5']);
$day_event6 = trim($_POST['day_event6']);
$day_event7 = trim($_POST['day_event7']);
$day_event8 = trim($_POST['day_event8']);
$day_event9 = trim($_POST['day_event9']);
$day_event10 = trim($_POST['day_event10']);
$day_event11 = trim($_POST['day_event11']);
$day_event12 = trim($_POST['day_event12']);
$day_event13 = trim($_POST['day_event13']);
$day_event14 = trim($_POST['day_event14']);

if (isset($_POST['day_event1_obs']) && ! empty(trim($_POST['day_event1_obs']))) $day_event1 .= "||" .$_POST['day_event1_obs'];
if (isset($_POST['day_event2_obs']) && ! empty(trim($_POST['day_event2_obs']))) $day_event2 .= "||" .$_POST['day_event2_obs'];
if (isset($_POST['day_event3_obs']) && ! empty(trim($_POST['day_event3_obs']))) $day_event3 .= "||" .$_POST['day_event3_obs'];
if (isset($_POST['day_event4_obs']) && ! empty(trim($_POST['day_event4_obs']))) $day_event4 .= "||" .$_POST['day_event4_obs'];
if (isset($_POST['day_event5_obs']) && ! empty(trim($_POST['day_event5_obs']))) $day_event5 .= "||" .$_POST['day_event5_obs'];
if (isset($_POST['day_event6_obs']) && ! empty(trim($_POST['day_event6_obs']))) $day_event6 .= "||" .$_POST['day_event6_obs'];
if (isset($_POST['day_event7_obs']) && ! empty(trim($_POST['day_event7_obs']))) $day_event7 .= "||" .$_POST['day_event7_obs'];
if (isset($_POST['day_event8_obs']) && ! empty(trim($_POST['day_event8_obs']))) $day_event7 .= "||" .$_POST['day_event8_obs'];
if (isset($_POST['day_event9_obs']) && ! empty(trim($_POST['day_event9_obs']))) $day_event7 .= "||" .$_POST['day_event9_obs'];
if (isset($_POST['day_event10_obs']) && ! empty(trim($_POST['day_event10_obs']))) $day_event7 .= "||" .$_POST['day_event10_obs'];
if (isset($_POST['day_event11_obs']) && ! empty(trim($_POST['day_event11_obs']))) $day_event7 .= "||" .$_POST['day_event11_obs'];
if (isset($_POST['day_event12_obs']) && ! empty(trim($_POST['day_event12_obs']))) $day_event7 .= "||" .$_POST['day_event12_obs'];
if (isset($_POST['day_event13_obs']) && ! empty(trim($_POST['day_event13_obs']))) $day_event7 .= "||" .$_POST['day_event13_obs'];
if (isset($_POST['day_event14_obs']) && ! empty(trim($_POST['day_event14_obs']))) $day_event7 .= "||" .$_POST['day_event14_obs'];


$day_event_safe1 = addslashes($day_event1);
$day_event_safe2 = addslashes($day_event2);
$day_event_safe3 = addslashes($day_event3);
$day_event_safe4 = addslashes($day_event4);
$day_event_safe5 = addslashes($day_event5);
$day_event_safe6 = addslashes($day_event6);
$day_event_safe7 = addslashes($day_event7);
$day_event_safe8 = addslashes($day_event8);
$day_event_safe9 = addslashes($day_event9);
$day_event_safe10 = addslashes($day_event10);
$day_event_safe11 = addslashes($day_event11);
$day_event_safe12 = addslashes($day_event12);
$day_event_safe13 = addslashes($day_event13);
$day_event_safe14 = addslashes($day_event14);
if ($event_found == 1) {
  //UPDATE
    $postQuery = "UPDATE `reservas` SET event1 = '".$day_event_safe1."', event2 = '".$day_event_safe2."', event3 = '".$day_event_safe3."',
    event4 = '".$day_event_safe4."', event5 = '".$day_event_safe5."', event6 = '".$day_event_safe6."', event7 = '".$day_event_safe7."', event8 = '".$day_event_safe8."', event9 = '".$day_event_safe9."', event10 = '".$day_event_safe10."', event11 = '".$day_event_safe11."', event12 = '".$day_event_safe12."', event13 = '".$day_event_safe13."', event14 = '".$day_event_safe14."' WHERE eventdate = '$sql_date' and servicio = '$servicio';";
    $postExec = mysqli_query($db_con, $postQuery) or die("Could not Post UPDATE Event to database!");
    mysqli_query($db_con, "DELETE FROM `reservas` WHERE event1 = '' and event2 = ''  and event3 = ''  and event4 = ''  and event5 = ''  and event6 = ''  and event7 = '' and event8 = '' and event9 = '' and event10 = '' and event11 = '' and event12 = '' and event13 = '' and event14 = '' and servicio = '$servicio'");
mysqli_close($conn);
	header("Location: index.php?servicio=$servicio&year=$year&month=$month&today=$today&mens=actualizar");

} else {
  //INSERT
    $postQuery = "INSERT INTO `reservas` (eventdate,dia,event1,event2,event3,event4,event5,event6,event7,event8,event9,event10,event11,event12,event13,event14,servicio) VALUES ('$sql_date','$numero_dia','".$day_event_safe1."','".$day_event_safe2."','".$day_event_safe3."','".$day_event_safe4."','".$day_event_safe5."','".$day_event_safe6."','".$day_event_safe7."','".$day_event_safe8."','".$day_event_safe9."','".$day_event_safe10."','".$day_event_safe11."','".$day_event_safe12."','".$day_event_safe13."','".$day_event_safe14."', '$servicio')";

    $postExec = mysqli_query($db_con, $postQuery) or die('Error: '.mysqli_error($db_con));
    mysqli_query($db_con, "DELETE FROM `reservas` WHERE event1 = '' and event2 = ''  and event3 = ''  and event4 = ''  and event5 = ''  and event6 = ''  and event7 = '' and event8 = '' and event9 = '' and event10 = '' and event11 = '' and event12 = '' and event13 = '' and event14 = '' and servicio = '$servicio'");
mysqli_close($conn);
    header("Location: index.php?servicio=$servicio&year=$year&month=$month&today=$today&mens=insertar");

}
?>
