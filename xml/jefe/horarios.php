
// Recorremos la tabla Profesores bajada de Séneca
$tabla_profes =mysqli_query($db_con,"select * from profesores");
if (mysqli_num_rows($tabla_profes) > 0) {
	$hay_profes=1;
}
else{
	$nohay_profes=1;
	$pro =mysqli_query($db_con,"select distinct c_asig, a_grupo, prof from horw where c_asig not like '2' order by prof");
	while ($prf =mysqli_fetch_array($pro)) {
		$mat = mysqli_query($db_con,"select nombre from asignaturas where codigo = '$prf[0]' limit 1");
		$mater= mysqli_fetch_array($mat);
		$materia = $mater[0];
		$grupo = $prf[1];
		$profesor = $prf[2];
		$niv =mysqli_query($db_con,"select distinct curso from alma where unidad = '$grupo'");
		$nive =mysqli_fetch_array($niv);
		$nivel = $nive[0];

		mysqli_query($db_con,"INSERT INTO  profesores (
`nivel` ,
`materia` ,
`grupo` ,
`profesor`
) VALUES ('$nivel', '$materia', '$grupo', '$profesor')");
	}
	mysqli_query($db_con,"delete from profesores WHERE nivel = ''");
}
