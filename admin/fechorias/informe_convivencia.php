<?php
require('../../bootstrap.php');

if (file_exists('config.php')) {
    include('config.php');
}
    include("../../menu.php");

$numCursosAnteriores = 5; //num de años a considerar en la consulta
?>

<script src="../../js/ChartJS/Chart.min.js"></script>
<script type="text/javascript">
window.chartColors = {
    red: 'rgb(244, 67, 54)',
    pink: 'rgb(255, 64, 129)',
    purple: 'rgb(156, 39, 176)',
    deeppurple: 'rgb(124, 77, 255)',
    indigo: 'rgb(63, 81, 181)',
    blue: 'rgb(68, 138, 255)',
    lightblue: 'rgb(3, 169, 244)',
    cyan: 'rgb(0, 188, 212)',
    teal: 'rgb(0, 150, 136)',
    green: 'rgb(76, 175, 80)',
    lightgreen: 'rgb(139, 195, 74)',
    lime: 'rgb(205, 220, 57)',
    yellow: 'rgb(255, 235, 59)',
    amber: 'rgb(255, 193, 7)',
    orange: 'rgb(255, 152, 0)',
    deeporange: 'rgb(255, 87, 34)',
    brown: 'rgb(121, 85, 72)',
    grey: 'rgb(158, 158, 158)',
    bluegrey: 'rgb(96, 125, 139)'
};
</script>

<div class="container">

    <div class="page-header">
        <h2>Jefatura de Estudios <small> Informe de Problemas de Convivencia</small></h2>
    </div>

    <div class="text-center" id="t_larga_barra">
        <span class="lead">Cargando los datos. El proceso puede tardar un poco...</span><br><br><span class="fas fa-spinner fa-spin fa-5x"></span>
    </div>
    <div id='t_larga' style='display:none' >
        <div>
            <ul class="nav nav-tabs">
               <li class="active"><a href="#tab1" data-toggle="tab">Resumen general</a></li>
               <li><a href="#tab2" data-toggle="tab">Resumen por Nivel</a></li>
               <li><a href="#tab3" data-toggle="tab">Resumen por Grupo</a></li>
               <?php
               if(stristr($_SESSION['cargo'],'1') == TRUE or stristr($_SESSION['cargo'],'8') == TRUE)
               {
                   echo '<li><a href="#tab4" data-toggle="tab">Informe por Profesor</a></li>';
               }
               ?>
               <li><a href="#tab5" data-toggle="tab">Informe por Tipo</a></li>
            </ul>

            <div class="tab-content" style="padding-bottom: 9px;">

                <div class="tab-pane fade in active" id="tab1">
                    <br /><h3>Resumen General</h3><br />

                    <?php
                    $cur = substr($config['curso_inicio'],0,4);
                    for ($i = 0; $i < $numCursosAnteriores; $i++)    
                    {
                        $anio_escolar = $cur - $i;
                        $haydatos = 0;
                        
                        if ($i == 0)
                        {
                            $db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']) or die('error');
                            mysqli_query($db_con,"SET NAMES 'utf8'");
                            $haydatos = 1;
                        }
                        
                        if($i > 0 && ! empty($config['db_host_c'.$anio_escolar]))
                        {
                            $db_con = mysqli_connect($config['db_host_c'.$anio_escolar], $config['db_user_c'.$anio_escolar], $config['db_pass_c'.$anio_escolar], $config['db_name_c'.$anio_escolar]);
                            mysqli_query($db_con,"SET NAMES 'utf8'");
                            $haydatos = 1;
                        }

                        if($haydatos)
                        { ?>
                           

                            <?php $chart_n = 0; ?>
                    
                            <h4 class="text-info">Curso <?php echo $anio_escolar; echo "-".($anio_escolar+1);?></h4>
                            
                                <?php
                                
                                $consulta = mysqli_fetch_assoc(mysqli_query($db_con, "select min(fecha) as minNav, max(fecha) as maxNav from festivos where nombre like '%Navidad%'"));
                                $minNavidad = $consulta['minNav'];
                                $maxNavidad = $consulta['maxNav'];
                                
                                $consulta = mysqli_fetch_assoc(mysqli_query($db_con, "select min(fecha) as minSanta, max(fecha) as maxSanta from festivos where nombre like '%Santa%'"));
                                $minSanta = $consulta['minSanta'];
                                $maxSanta = $consulta['maxSanta'];
                            
                                //echo '[1T desde '.$minNavidad. ' hasta '.$maxNavidad;
                                //echo '][2T desde '.$minSanta. ' hasta '.$maxSanta.']';
                            
                                $SQL = "select count(*) as total from Fechoria where month(fecha) >='09' and fecha <= '$minNavidad'";          
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_conv1 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where fecha >= '$maxNavidad' and fecha <= '$minSanta'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_conv2 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where fecha >='$maxSanta' and fecha <= '".$config['curso_fin']."'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_conv3 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where grave = 'leve' and month(fecha) >='09' and fecha <= '$minNavidad'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_leves1 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where grave = 'leve' and fecha >= '$maxNavidad' and fecha <= '$minSanta'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_leves2 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where grave = 'leve' and fecha >='$maxSanta' and fecha <= '".$config['curso_fin']."'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_leves3 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where grave = 'grave' and month(fecha) >='09' and fecha <= '$minNavidad'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_graves1 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where grave = 'grave' and fecha >= '$maxNavidad' and fecha <= '$minSanta'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_graves2 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where grave = 'grave' and fecha >='$maxSanta' and fecha <= '".$config['curso_fin']."'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_graves3 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where grave = 'muy grave' and month(Fechoria.fecha) >='09' and fecha <= '$minNavidad'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_muygraves1 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where grave = 'muy grave' and fecha >= '$maxNavidad' and fecha <= '$minSanta'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_muygraves2 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where grave = 'muy grave' and fecha >='$maxSanta' and fecha <= '".$config['curso_fin']."'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_muygraves3 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where expulsion > '0' and month(fecha) >='09' and fecha <= '$minNavidad' and asunto not like '%covid%'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_expulsion1 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where expulsion > '0' and fecha >= '$maxNavidad' and fecha <= '$minSanta' and asunto not like '%covid%'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_expulsion2 = $result['total'];
                                
                                $SQL = "select count(*) as total from Fechoria where expulsion > '0' and fecha >='$maxSanta' and fecha <= '".$config['curso_fin']."' and asunto not like '%covid%'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_expulsion3 = $result['total'];
                                
                                $SQL = "select count(distinct claveal) as total from Fechoria where expulsion > '0' and month(fecha) >='09' and fecha <= '$minNavidad' and asunto not like '%covid%'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_expulsados1 = $result['total'];
                                
                                $SQL = "select count(distinct claveal) as total from Fechoria where expulsion > '0' and fecha >= '$maxNavidad' and fecha <= '$minSanta' and asunto not like '%covid%'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_expulsados2 = $result['total'];
                                
                                $SQL = "select count(distinct claveal) as total from Fechoria where expulsion > '0' and fecha >='$maxSanta' and fecha <= '".$config['curso_fin']."' and asunto not like '%covid%'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_expulsados3 = $result['total'];
                                
                                $SQL = "select count(distinct claveal) as total from Fechoria where expulsionaula = '1' and month(fecha) >='09' and fecha <= '$minNavidad'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_expulsadosaula1 = $result['total'];
                                
                                $SQL = "select count(distinct claveal) as total from Fechoria where expulsionaula = '1' and fecha >= '$maxNavidad' and fecha <= '$minSanta'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_expulsadosaula2 = $result['total'];
                                
                                $SQL = "select count(distinct claveal) as total from Fechoria where expulsionaula = '1' and Fechoria.fecha >='$maxSanta' and fecha <= '".$config['curso_fin']."'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_expulsadosaula3 = $result['total'];
                                
                                $SQL = "select count(distinct id) as total from infotut_alumno where month(F_ENTREV) >='09' and date(F_ENTREV) <= '$minNavidad' ";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_informes1 = $result['total'];
                                
                                $SQL = "select count(distinct id) as total from infotut_alumno where F_ENTREV >= '$maxNavidad' and date(F_ENTREV) <= '$minSanta' ";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_informes2 = $result['total'];
                                
                                $SQL = "select count(distinct id) as total from infotut_alumno where date(F_ENTREV) >='$maxSanta' and F_ENTREV <= '".$config['curso_fin']."' ";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_informes3 = $result['total'];
                                
                                $chk = mysqli_query($db_con,"select id from tutoria");
                                if (mysqli_num_rows($chk)<=0)
                                  mysqli_query($db_con,"ALTER TABLE `tutoria` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");

                                $SQL = "select count(distinct id) as total from tutoria where month(fecha) >='09' and fecha <= '$minNavidad' and causa not like 'Retraso en la asistencia al aula' and causa not like 'Falta de asistencia a primera hora'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_acciones1 = $result['total'];
                                
                                $SQL = "select count(distinct id) as total from tutoria where fecha >= '$maxNavidad' and fecha <= '$minSanta' and causa not like 'Retraso en la asistencia al aula' and causa not like 'Falta de asistencia a primera hora'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_acciones2 = $result['total'];
                                
                                $SQL = "select count(distinct id) as total from tutoria where fecha >='$maxSanta' and month(fecha) <= '06'  and causa not like 'Retraso en la asistencia al aula' and causa not like 'Falta de asistencia a primera hora'";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_acciones3 = $result['total'];
                                
                                $SQL = "select count(distinct id) as total from tutoria where (causa like '%Asistencia%' or causa like 'Retraso%') and month(fecha) >='09' and fecha <= '$minNavidad' ";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_comunica1 = $result['total'];
                                
                                $SQL = "select count(distinct id) as total from tutoria where (causa like '%Asistencia%' or causa like 'Retraso%') and fecha >= '$maxNavidad' and fecha <= '$minSanta' ";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_comunica2 = $result['total'];
                                
                                $SQL = "select count(distinct id) as total from tutoria where (causa like '%Asistencia%' or causa like 'Retraso%') and fecha >='$maxSanta' and month(fecha) <= '06' ";
                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                $num_comunica3 = $result['total'];

                                // Control de Absentismo.

                                $absent = mysqli_query($db_con,"select * from absentismo");

                                $menor_edad=date("Y-m-d", strtotime ("-16years"));   

                                if (mysqli_num_rows($absent)>0) {
                                    $faltas = "select distinct claveal from absentismo where claveal in (select claveal from alma where  STR_TO_DATE( fecha,  '%d/%m/%Y' ) > DATE( '$menor_edad' ) )";
                                    $faltas0 = mysqli_query($db_con, $faltas);
                                    $num_faltas = mysqli_num_rows($faltas0);
                                }
                                else{
                                    $SQL = "select count(distinct t1.claveal) as total from (SELECT claveal, unidad, month(fecha) as mes, count(*) AS numero FROM FALTAS where falta = 'F' and claveal in (select claveal from alma) group by claveal, month(fecha) having numero > 25) as t1";                               
                                    $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                    $num_faltas = $result['total'];
                                }
                               
                               // Fin de control de Absentismo

                                $num_conv = $num_conv1 + $num_conv2 + $num_conv3;
                                $num_leves = $num_leves1 + $num_leves2 + $num_leves3;
                                $num_graves = $num_graves1 + $num_graves2 + $num_graves3;
                                $num_muygraves = $num_muygraves1 + $num_muygraves2 + $num_muygraves3;
                                $num_expulsion = $num_expulsion1 + $num_expulsion2 + $num_expulsion3;
                                $num_expulsados = $num_expulsados1 + $num_expulsados2 + $num_expulsados3;
                                $num_expulsadosaula = $num_expulsadosaula1 + $num_expulsadosaula2 + $num_expulsadosaula3;
                                $num_acciones = $num_acciones1 + $num_acciones2 + $num_acciones3;
                                $num_informes = $num_informes1 + $num_informes2 + $num_informes3;
                                $num_comunica = $num_comunica1 + $num_comunica2 + $num_comunica3;
                                ?>


                                <?php $chart_n++; ?>

                                <canvas id="chart_<?php echo $chart_n; ?>_<?php echo $anio_escolar; ?>" width="200" height="100"></canvas>
                                <script>
                                var ctx = document.getElementById("chart_<?php echo $chart_n; ?>_<?php echo $anio_escolar; ?>");
                                
                                var myChart = new Chart(ctx, {
                                    responsive: true,
                                    type: 'horizontalBar',
                                    data: {
                                      labels: [
                                        "Absentismo",
                                        "Convivencia",
                                        <?php if (isset($config['convivencia']['convivencia_seneca']) && $config['convivencia']['convivencia_seneca']): ?>
                                        "Otras conductas",
                                        "Conductas contrarias",
                                        "Conductas graves",
                                        <?php else: ?>
                                        "Leves",
                                        "Graves",
                                        "Muy Graves",
                                        <?php endif; ?>
                                        "Expulsiones",
                                        "Alumnos Expulsados",
                                        "Expulsión del Aula",
                                        "Informes",
                                        "Comunicaciones"
                                      ],
                                      datasets: [{
                                            label: 'Primer trimestre',
                                            backgroundColor: window.chartColors.red,
                                            data: [
                                                <?php echo $num_faltas; ?>,
                                                <?php echo $num_conv1; ?>, 
                                                <?php echo $num_leves1; ?>, 
                                                <?php echo $num_graves1; ?>, 
                                                <?php echo $num_muygraves1; ?>, 
                                                <?php echo $num_expulsion1; ?>, 
                                                <?php echo $num_expulsados1; ?>,
                                                <?php echo $num_expulsadosaula1; ?>,
                                                <?php echo $num_informes1; ?>,
                                                <?php echo $num_comunica1; ?>
                                            ]
                                        }, {
                                            label: 'Segundo trimestre',
                                            backgroundColor: window.chartColors.blue,
                                            data: [
                                                <?php echo $num_faltas; ?>,
                                                <?php echo $num_conv2; ?>, 
                                                <?php echo $num_leves2; ?>, 
                                                <?php echo $num_graves2; ?>, 
                                                <?php echo $num_muygraves2; ?>, 
                                                <?php echo $num_expulsion2; ?>, 
                                                <?php echo $num_expulsados2; ?>,
                                                <?php echo $num_expulsadosaula2; ?>,
                                                <?php echo $num_informes2; ?>,
                                                <?php echo $num_comunica2; ?>
                                            ]
                                        }, {
                                            label: 'Tercer trimestre',
                                            backgroundColor: window.chartColors.green,
                                            data: [
                                                <?php echo $num_faltas; ?>,
                                                <?php echo $num_conv3; ?>, 
                                                <?php echo $num_leves3; ?>, 
                                                <?php echo $num_graves3; ?>, 
                                                <?php echo $num_muygraves3; ?>, 
                                                <?php echo $num_expulsion3; ?>, 
                                                <?php echo $num_expulsados3; ?>,
                                                <?php echo $num_expulsadosaula3; ?>,
                                                <?php echo $num_informes3; ?>,
                                                <?php echo $num_comunica3; ?>
                                            ]
                                        }]
                                    },
                                    options: {
                                        events: false,
                                        legend: {
                                            display: false
                                        },
                                        scales: {
                                            xAxes: [{
                                                position: 'top',
                                                ticks: {
                                                    stepSize: 0
                                                }
                                            }],
                                            yAxes: [{
                                                
                                            }]
                                        },
                                    animation: {
                                            animateScale: true,
                                            animateRotate: true,
                                            onComplete: function () {
                                                var chartInstance = this.chart;
                                                var ctx2 = chartInstance.ctx;
                                                console.log(chartInstance);
                                                var height = chartInstance.controller.boxes[0].bottom;
                                                ctx2.textAlign = "center";
                                                Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
                                                  var meta = chartInstance.controller.getDatasetMeta(i);
                                                  Chart.helpers.each(meta.data.forEach(function (bar, index) {
                                                    if(dataset.data[index]>0) ctx2.fillText(dataset.data[index], bar._model.x+12, height - ((height - bar._model.y)-4));
                                                  }),this)
                                                }),this);
                                            }
                                        }
                                    }
                                });
                                </script>
                        <?php
                        } //cierre if ($hayDatos)
                        echo "<br><br>";
                    } //cierre del for
                    ?>
                </div>
                <div class="tab-pane fade in" id="tab2">



                    <br /><h3>Información por Nivel</h3>
                    <br />

                    <div class="tabbable" style="margin-bottom: 18px;">
                        <ul class="nav nav-tabs">
                            <?php
                            $cur = substr($config['curso_inicio'],0,4);
                            for ($a = 0; $a < $numCursosAnteriores; $a++)
                            {
                                $anio_escolar = $cur - $a;
                                $haydatos = 0;

                                if ($a == 0)
                                {
                                    $db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']) or die('error');
                                    mysqli_query($db_con,"SET NAMES 'utf8'");
                                    $haydatos = 1;
                                }
                                
                                if($a > 0 && ! empty($config['db_host_c'.$anio_escolar]))
                                {
                                    $db_con = mysqli_connect($config['db_host_c'.$anio_escolar], $config['db_user_c'.$anio_escolar], $config['db_pass_c'.$anio_escolar], $config['db_name_c'.$anio_escolar]);
                                    mysqli_query($db_con,"SET NAMES 'utf8'");
                                    $haydatos = 1;
                                }

                                if($haydatos)
                                {
                            ?>
                                    <li<?php echo ($a == 0) ? ' class="active"' : '';?>><a href="#n<?php echo $a+1;?>" data-toggle="tab">Curso <?php echo $anio_escolar."-".($anio_escolar+1);?></a></li>
                            <?php
                                }
                            }
                            ?>
                        </ul>

                        <div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">

                            <?php
                            $num="";
                            $cur = substr($config['curso_inicio'],0,4);
                            for ($i = 0; $i < $numCursosAnteriores; $i++)
                            {
                                $num +=1;
                                $num == '1' ? $activ=" active" : $activ='';
                                $anio_escolar = $cur - $i;
                                $haydatos = 0;

                                if ($i == 0)
                                {
                                    $db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']) or die('error');
                                    mysqli_query($db_con,"SET NAMES 'utf8'");
                                    $haydatos = 1;
                                }
                                
                                if($i > 0 && ! empty($config['db_host_c'.$anio_escolar]))
                                {
                                    $db_con = mysqli_connect($config['db_host_c'.$anio_escolar], $config['db_user_c'.$anio_escolar], $config['db_pass_c'.$anio_escolar], $config['db_name_c'.$anio_escolar]);
                                    mysqli_query($db_con,"SET NAMES 'utf8'");
                                    $haydatos = 1;
                                }

                                if($haydatos)
                                {
                                    $chart_n = 0;

                                    $consulta = mysqli_fetch_assoc(mysqli_query($db_con, "select min(fecha) as minNav, max(fecha) as maxNav from festivos where nombre like '%Navidad%'"));
                                    $minNavidad = $consulta['minNav'];
                                    $maxNavidad = $consulta['maxNav'];
                                
                                    $consulta = mysqli_fetch_assoc(mysqli_query($db_con, "select min(fecha) as minSanta, max(fecha) as maxSanta from festivos where nombre like '%Santa%'"));
                                    $minSanta = $consulta['minSanta'];
                                    $maxSanta = $consulta['maxSanta'];
                                    ?>
                                    <div class="tab-pane fade in <?php echo $activ;?>" id="<?php echo "n".$num;?>">
                                        <br>
                                        <?php
                                        $nivel0 = "select nomcurso, idcurso from cursos order by nomcurso";
                                        $nivel1 = mysqli_query($db_con, $nivel0);
                                        while($nivel = mysqli_fetch_array($nivel1))
                                        {
                                            $nivel = $nivel[0];
                                            $idcurso = $nivel[1];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                                            //echo $SQL."<br>";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_conv1 = $result['total'];

                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_conv2 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and Fechoria.fecha >='$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_conv3 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and grave = 'leve' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_leves1 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and grave = 'leve' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_leves2 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and grave = 'leve' and Fechoria.fecha >='$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_leves3 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and grave = 'grave' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_graves1 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and grave = 'grave' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_graves2 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and grave = 'grave' and Fechoria.fecha >='$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_graves3 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and grave = 'muy grave' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_muygraves1 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and grave = 'muy grave' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_muygraves2 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and grave = 'muy grave' and Fechoria.fecha >='$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_muygraves3 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and expulsion > '0' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_expulsion1 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and expulsion > '0' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_expulsion2 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and expulsion > '0' and Fechoria.fecha >='$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_expulsion3 = $result['total'];
                                            
                                            $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and expulsion > '0' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_expulsados1 = $result['total'];
                                            
                                            $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and expulsion > '0' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_expulsados2 = $result['total'];
                                            
                                            $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and expulsion > '0' and Fechoria.fecha >='$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_expulsados3 = $result['total'];
                                            
                                            $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and expulsionaula = '1' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_expulsadosaula1 = $result['total'];
                                            
                                            $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and expulsionaula = '1' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_expulsadosaula2 = $result['total'];
                                            
                                            $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel'  and expulsionaula = '1' and Fechoria.fecha >='$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_expulsadosaula3 = $result['total'];
                                            
                                            $SQL = "select count(distinct id) as total from infotut_alumno inner join alma on alma.claveal = infotut_alumno.claveal where curso = '$nivel' and month(F_ENTREV) >='09' and date(F_ENTREV) <= '$minNavidad'";
                                            //echo $SQL."<br>";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_informes1 = $result['total'];
                                            
                                            $SQL = "select count(distinct id) as total from infotut_alumno inner join alma on alma.claveal = infotut_alumno.claveal where curso = '$nivel' and F_ENTREV >= '$maxNavidad' and date(F_ENTREV) <= '$minSanta'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_informes2 = $result['total'];
                                            
                                            $SQL = "select count(distinct id) as total from infotut_alumno inner join alma on alma.claveal = infotut_alumno.claveal where curso = '$nivel' and date(F_ENTREV) >= '$maxSanta' and F_ENTREV <= '".$config['curso_fin']."'";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_informes3 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and month(tutoria.fecha) >='09' and date(tutoria.fecha) <= '$minNavidad' ";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_acciones1 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and tutoria.fecha >= '$maxNavidad' and date(tutoria.fecha) <= '$minSanta' ";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_acciones2 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and date(tutoria.fecha) >= '$maxSanta' and tutoria.fecha <= '".$config['curso_fin']."' ";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_acciones3 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and causa = 'Faltas de Asistencia' and month(tutoria.fecha) >='09' and date(tutoria.fecha) <= '$minNavidad' ";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_comunica1 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and causa = 'Faltas de Asistencia' and tutoria.fecha >= '$maxNavidad' and date(tutoria.fecha) <= '$minSanta' ";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_comunica2 = $result['total'];
                                            
                                            $SQL = "select count(*) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and causa = 'Faltas de Asistencia' and date(tutoria.fecha) >= '$maxSanta' and tutoria.fecha <= '".$config['curso_fin']."' ";
                                            $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                            $num_comunica3 = $result['total'];
                                            

                                            if (mysqli_num_rows($absent)>0) {
                                                $faltas = "select distinct claveal from absentismo where claveal in (select claveal from alma where  STR_TO_DATE( fecha,  '%d/%m/%Y' ) > DATE( '$menor_edad' ) and curso = '$nivel')";
                                                $faltas0 = mysqli_query($db_con, $faltas);
                                                $num_faltas = mysqli_num_rows($faltas0);
                                            }
                                            else{
                                                $SQL = "select count(distinct t1.claveal) as total from (select f.claveal, month(fecha) as mes, count(*) as numero from FALTAS f, unidades u, cursos c where c.idcurso = u.idcurso and f.unidad = u.nomunidad and f.falta = 'F' and c.nomcurso = '$nivel' group by f.claveal, mes having numero > 25) as t1";                   
                                                $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                                                $num_faltas = $result['total'];
                                            }
                                  
                                            $num_conv = $num_conv1 + $num_conv2 + $num_conv3;
                                            $num_leves = $num_leves1 + $num_leves2 + $num_leves3;
                                            $num_graves = $num_graves1 + $num_graves2 + $num_graves3;
                                            $num_muygraves = $num_muygraves1 + $num_muygraves2 + $num_muygraves3;
                                            $num_expulsion = $num_expulsion1 + $num_expulsion2 + $num_expulsion3;
                                            $num_expulsados = $num_expulsados1 + $num_expulsados2 + $num_expulsados3;
                                            $num_expulsadosaula = $num_expulsadosaula1 + $num_expulsadosaula2 + $num_expulsadosaula3;
                                            $num_acciones = $num_acciones1 + $num_acciones2 + $num_acciones3;
                                            $num_informes = $num_informes1 + $num_informes2 + $num_informes3;
                                            $num_comunica = $num_comunica1 + $num_comunica2 + $num_comunica3;
                                            ?>
                                            
                                
                                <?php $chart_n++; ?>
                                <div class="col-sm-6">
                                    <h4 class="badge badge-success"><?php echo $nivel; ?></h4>
                                            <br>
                                <canvas id="chart_<?php echo $chart_n; ?>_<?php echo $anio_escolar; ?>_<?php echo $idcurso; ?>" width="200" height="260"></canvas>
                                <script>
                                var ctx = document.getElementById("chart_<?php echo $chart_n; ?>_<?php echo $anio_escolar; ?>_<?php echo $idcurso; ?>");
                                
                                var myChart = new Chart(ctx, {
                                    responsive: true,
                                    type: 'horizontalBar',
                                    data: {
                                      labels: [
                                        "Absentismo",
                                        "Convivencia",
                                        <?php if (isset($config['convivencia']['convivencia_seneca']) && $config['convivencia']['convivencia_seneca']): ?>
                                        "Otras conductas",
                                        "Conductas contrarias",
                                        "Conductas graves",
                                        <?php else: ?>
                                        "Leves",
                                        "Graves",
                                        "Muy Graves",
                                        <?php endif; ?>
                                        "Expulsiones",
                                        "Alumnos Expulsados",
                                        "Expulsión del Aula",
                                        "Informes",
                                        "Comunicaciones"
                                      ],
                                      datasets: [{
                                            label: 'Primer trimestre',
                                            backgroundColor: window.chartColors.red,
                                            data: [
                                                <?php echo $num_faltas; ?>,
                                                <?php if($num_conv1>0){echo $num_conv1;}else{echo "";} ?>, 
                                                <?php echo $num_leves1; ?>, 
                                                <?php echo $num_graves1; ?>, 
                                                <?php echo $num_muygraves1; ?>, 
                                                <?php echo $num_expulsion1; ?>, 
                                                <?php echo $num_expulsados1; ?>,
                                                <?php echo $num_expulsadosaula1; ?>,
                                                <?php echo $num_informes1; ?>,
                                                <?php echo $num_comunica1; ?>
                                            ]
                                        }, {
                                            label: 'Segundo trimestre',
                                            backgroundColor: window.chartColors.blue,
                                            data: [
                                                <?php echo $num_faltas; ?>,
                                                <?php echo $num_conv2; ?>, 
                                                <?php echo $num_leves2; ?>, 
                                                <?php echo $num_graves2; ?>, 
                                                <?php echo $num_muygraves2; ?>, 
                                                <?php echo $num_expulsion2; ?>, 
                                                <?php echo $num_expulsados2; ?>,
                                                <?php echo $num_expulsadosaula2; ?>,
                                                <?php echo $num_informes2; ?>,
                                                <?php echo $num_comunica2; ?>
                                            ]
                                        }, {
                                            label: 'Tercer trimestre',
                                            backgroundColor: window.chartColors.green,
                                            data: [
                                                <?php echo $num_faltas; ?>,
                                                <?php echo $num_conv3; ?>, 
                                                <?php echo $num_leves3; ?>, 
                                                <?php echo $num_graves3; ?>, 
                                                <?php echo $num_muygraves3; ?>, 
                                                <?php echo $num_expulsion3; ?>, 
                                                <?php echo $num_expulsados3; ?>,
                                                <?php echo $num_expulsadosaula3; ?>,
                                                <?php echo $num_informes3; ?>,
                                                <?php echo $num_comunica3; ?>
                                            ]
                                        }]
                                    },
                                    options: {
                                        events: false,
                                        legend: {
                                            display: false
                                        },
                                        scales: {
                                            xAxes: [{
                                                position: 'top',
                                                ticks: {
                                                    stepSize: 0
                                                }
                                            }],
                                            yAxes: [{
                                                
                                            }]
                                        },
                                    animation: {
                                            animateScale: true,
                                            animateRotate: true,
                                            onComplete: function () {
                                                var chartInstance = this.chart;
                                                var ctx2 = chartInstance.ctx;
                                                console.log(chartInstance);
                                                var height = chartInstance.controller.boxes[0].bottom;
                                                ctx2.textAlign = "center";
                                                Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
                                                  var meta = chartInstance.controller.getDatasetMeta(i);
                                                  Chart.helpers.each(meta.data.forEach(function (bar, index) {
                                                    if(dataset.data[index]>0) ctx2.fillText(dataset.data[index], bar._model.x+12, height - ((height - bar._model.y)-4));
                                                  }),this)
                                                }),this);
                                            }
                                        }                                        
                                    }
                                });
                                </script>
                            </div>
                                        <?php
                                        }
                                        ?>
                            </div>
                            <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade in" id="tab3">
                    <br />
                    <h3>Información por Grupo</h3>
                    <br>
                    <?php
                    $db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']) or die('error');
                    mysqli_query($db_con,"SET NAMES 'utf8'");
                    $cursos0 = "select distinct curso, unidad from alma order by curso";
                    $cursos1 = mysqli_query($db_con, $cursos0);
                    
                    $consulta = mysqli_fetch_assoc(mysqli_query($db_con, "select min(fecha) as minNav, max(fecha) as maxNav from festivos where nombre like '%Navidad%'"));
                    $minNavidad = $consulta['minNav'];
                    $maxNavidad = $consulta['maxNav'];
                                
                    $consulta = mysqli_fetch_assoc(mysqli_query($db_con, "select min(fecha) as minSanta, max(fecha) as maxSanta from festivos where nombre like '%Santa%'"));
                    $minSanta = $consulta['minSanta'];
                    $maxSanta = $consulta['maxSanta'];
                    
                    while($cursos = mysqli_fetch_array($cursos1))
                    {
                        $nivel = $cursos[0];
                        $grupo = $cursos[1];
                        $unidad = $cursos[0]." -- ".$cursos[1];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_conv1 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_conv2 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and Fechoria.fecha >= '$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_conv3 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and grave = 'leve' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_leves1 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and grave = 'leve' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_leves2 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and grave = 'leve' and Fechoria.fecha >= '$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_leves3 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and grave = 'grave' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_graves1 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and grave = 'grave' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_graves2 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and grave = 'grave' and Fechoria.fecha >= '$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_graves3 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and grave = 'muy grave' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_muygraves1 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and grave = 'muy grave' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_muygraves2 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and grave = 'muy grave' and Fechoria.fecha >= '$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_muygraves3 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and expulsion > '0' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsion1 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and expulsion > '0' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsion2 = $result['total'];
                        
                        $SQL = "select count(*) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where curso = '$nivel' and unidad = '$grupo' and expulsion > '0' and Fechoria.fecha >= '$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsion3 = $result['total'];
                        
                        $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and unidad = '$grupo' and expulsion > '0' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsados1 = $result['total'];
                        
                        $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and unidad = '$grupo' and expulsion > '0' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsados2 = $result['total'];
                        
                        $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and unidad = '$grupo' and expulsion > '0' and Fechoria.fecha >= '$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsados3 = $result['total'];
                        
                        $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and unidad = '$grupo' and expulsion > '0' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsados1 = $result['total'];
                        
                        $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and unidad = '$grupo' and expulsion > '0' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsados2 = $result['total'];
                        
                        $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and unidad = '$grupo' and expulsion > '0' and Fechoria.fecha >= '$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsados3 = $result['total'];
                        
                        $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and unidad = '$grupo' and expulsionaula = '1' and month(Fechoria.fecha) >='09' and Fechoria.fecha <= '$minNavidad'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsadosaula1 = $result['total'];
                        
                        $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and unidad = '$grupo' and expulsionaula = '1' and Fechoria.fecha >= '$maxNavidad' and Fechoria.fecha <= '$minSanta'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsadosaula2 = $result['total'];
                        
                        $SQL = "select count(distinct Fechoria.claveal) as total from Fechoria inner join alma on alma.claveal = Fechoria.claveal where alma.curso = '$nivel' and unidad = '$grupo' and expulsionaula = '1' and Fechoria.fecha >= '$maxSanta' and Fechoria.fecha <= '".$config['curso_fin']."'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_expulsadosaula3 = $result['total'];
                        
                        $SQL = "select count(distinct id) as total from infotut_alumno inner join alma on alma.claveal = infotut_alumno.claveal where curso = '$nivel' and infotut_alumno.unidad = '$grupo' and month(F_ENTREV) >='09' and date(F_ENTREV) <= '$minNavidad'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_informes1 = $result['total'];
                        
                        $SQL = "select count(distinct id) as total from infotut_alumno inner join alma on alma.claveal = infotut_alumno.claveal where curso = '$nivel' and infotut_alumno.unidad = '$grupo' and F_ENTREV >= '$maxNavidad' and date(F_ENTREV) <= '$minSanta'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_informes2 = $result['total'];
                        
                        $SQL = "select count(distinct id) as total from infotut_alumno inner join alma on alma.claveal = infotut_alumno.claveal where curso = '$nivel' and infotut_alumno.unidad = '$grupo' and date(F_ENTREV) >= '$maxSanta' and month(F_ENTREV) >= '06'";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_informes3 = $result['total'];
                        
                        $SQL = "select count(distinct id) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and tutoria.unidad = '$grupo' and month(tutoria.fecha) >='09' and date(tutoria.fecha) <= '$minNavidad' ";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_acciones1 = $result['total'];
                        
                        $SQL = "select count(distinct id) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and tutoria.unidad = '$grupo' and tutoria.fecha >= '$maxNavidad' and date(tutoria.fecha) <= '$minSanta' ";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_acciones2 = $result['total'];
                        
                        $SQL = "select count(distinct id) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and tutoria.unidad = '$grupo' and date(tutoria.fecha) >= '$maxSanta' and tutoria.fecha <= '".$config['curso_fin']."' ";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_acciones3 = $result['total'];
                        
                        $SQL = "select count(distinct id) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and tutoria.unidad = '$grupo' and causa = 'Faltas de Asistencia' and month(tutoria.fecha) >='09' and date(tutoria.fecha) <= '$minNavidad' ";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_comunica1 = $result['total'];
                        
                        $SQL = "select count(distinct id) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and tutoria.unidad = '$grupo' and causa = 'Faltas de Asistencia' and tutoria.fecha >= '$maxNavidad' and date(tutoria.fecha) <= '$minSanta' ";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_comunica2 = $result['total'];
                        
                        $SQL = "select count(distinct id) as total from tutoria inner join alma on tutoria.claveal = alma.claveal where curso = '$nivel' and tutoria.unidad = '$grupo' and causa = 'Faltas de Asistencia' and date(tutoria.fecha) >= '$maxSanta' and tutoria.fecha <= '".$config['curso_fin']."' ";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_comunica3 = $result['total'];

                        /*
                        $faltas = "select count(distinct absentismo.claveal) as total from absentismo inner join alma on alma.claveal = absentismo.claveal where curso = '$nivel' and absentismo.unidad = '$grupo'";
                        $faltas0 = mysqli_fetch_assoc(mysqli_query($db_con, $faltas));
                        $num_faltas = $result['total'];
                        */
                        $SQL = "select count(distinct t1.claveal) as total from (SELECT claveal, unidad, month(fecha) as mes, count(*) AS numero FROM FALTAS where falta = 'F' group by claveal, mes having numero > 25 and unidad = '$grupo') as t1";
                        $result = mysqli_fetch_assoc(mysqli_query($db_con, $SQL));
                        $num_faltas = $result['total'];
                        
                        $num_conv = $num_conv1 + $num_conv2 + $num_conv3;
                        $num_leves = $num_leves1 + $num_leves2 + $num_leves3;
                        $num_graves = $num_graves1 + $num_graves2 + $num_graves3;
                        $num_muygraves = $num_muygraves1 + $num_muygraves2 + $num_muygraves3;
                        $num_expulsion = $num_expulsion1 + $num_expulsion2 + $num_expulsion3;
                        $num_expulsados = $num_expulsados1 + $num_expulsados2 + $num_expulsados3;
                        $num_expulsadosaula = $num_expulsadosaula1 + $num_expulsadosaula2 + $num_expulsadosaula3;
                        $num_acciones = $num_acciones1 + $num_acciones2 + $num_acciones3;
                        $num_informes = $num_informes1 + $num_informes2 + $num_informes3;
                        $num_comunica = $num_comunica1 + $num_comunica2 + $num_comunica3;
                    ?>
                        <h4  class="badge"><?php echo $unidad;?></h4>
                        <br />
                        <div class="row">
                            
                            <div class="col-sm-10">

                        <?php $chart_n++; ?>

                        <canvas id="chart_<?php echo $chart_n; ?>_<?php echo $anio_escolar; ?>_<?php echo $grupo; ?>" width="200" height="100"></canvas>
                        <script>
                        var ctx = document.getElementById("chart_<?php echo $chart_n; ?>_<?php echo $anio_escolar; ?>_<?php echo $grupo; ?>");
                        
                        var myChart = new Chart(ctx, {
                            responsive: true,
                            type: 'horizontalBar',
                            data: {
                              labels: [
                                "Absentismo",
                                "Convivencia",
                                <?php if (isset($config['convivencia']['convivencia_seneca']) && $config['convivencia']['convivencia_seneca']): ?>
                                "Otras conductas",
                                "Conductas contrarias",
                                "Conductas graves",
                                <?php else: ?>
                                "Leves",
                                "Graves",
                                "Muy Graves",
                                <?php endif; ?>
                                "Expulsiones",
                                "Alumnos Expulsados",
                                "Expulsión del Aula",
                                "Informes",
                                "Comunicaciones"
                              ],
                              datasets: [{
                                    label: 'Primer trimestre',
                                    backgroundColor: window.chartColors.red,
                                    data: [
                                        <?php echo $num_faltas; ?>,
                                        <?php echo $num_conv1; ?>, 
                                        <?php echo $num_leves1; ?>, 
                                        <?php echo $num_graves1; ?>, 
                                        <?php echo $num_muygraves1; ?>, 
                                        <?php echo $num_expulsion1; ?>, 
                                        <?php echo $num_expulsados1; ?>,
                                        <?php echo $num_expulsadosaula1; ?>,
                                        <?php echo $num_informes1; ?>,
                                        <?php echo $num_comunica1; ?>
                                    ]
                                }, {
                                    label: 'Segundo trimestre',
                                    backgroundColor: window.chartColors.blue,
                                    data: [
                                        <?php echo $num_faltas; ?>,
                                        <?php echo $num_conv2; ?>, 
                                        <?php echo $num_leves2; ?>, 
                                        <?php echo $num_graves2; ?>, 
                                        <?php echo $num_muygraves2; ?>, 
                                        <?php echo $num_expulsion2; ?>, 
                                        <?php echo $num_expulsados2; ?>,
                                        <?php echo $num_expulsadosaula2; ?>,
                                        <?php echo $num_informes2; ?>,
                                        <?php echo $num_comunica2; ?>
                                    ]
                                }, {
                                    label: 'Tercer trimestre',
                                    backgroundColor: window.chartColors.green,
                                    data: [
                                        <?php echo $num_faltas; ?>,
                                        <?php echo $num_conv3; ?>, 
                                        <?php echo $num_leves3; ?>, 
                                        <?php echo $num_graves3; ?>, 
                                        <?php echo $num_muygraves3; ?>, 
                                        <?php echo $num_expulsion3; ?>, 
                                        <?php echo $num_expulsados3; ?>,
                                        <?php echo $num_expulsadosaula3; ?>,
                                        <?php echo $num_informes3; ?>,
                                        <?php echo $num_comunica3; ?>
                                    ]
                                }]
                            },
                            options: {
                                events: false,
                                legend: {
                                    display: false
                                },
                                animation: {
                                            animateScale: true,
                                            animateRotate: true,
                                            onComplete: function () {
                                                var chartInstance = this.chart;
                                                var ctx2 = chartInstance.ctx;
                                                console.log(chartInstance);
                                                var height = chartInstance.controller.boxes[0].bottom;
                                                ctx2.textAlign = "center";
                                                Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
                                                  var meta = chartInstance.controller.getDatasetMeta(i);
                                                  Chart.helpers.each(meta.data.forEach(function (bar, index) {
                                                    if(dataset.data[index]>0) ctx2.fillText(dataset.data[index], bar._model.x+12, height - ((height - bar._model.y)-4));
                                                  }),this)
                                                }),this);
                                            }
                                        },                               
                                scales: {
                                    xAxes: [{
                                        position: 'top',
                                        ticks: {
                                            stepSize: 0
                                        }
                                    }],
                                    yAxes: [{
                                        
                                    }]
                                }
                            }
                        });
                        </script>

                        <br />

                    </div>
                </div>
                        <?php
                        /*$tabla = 'tmp_'.$grupo;
                        $temp = mysqli_query($db_con, "CREATE TEMPORARY TABLE `$tabla` SELECT Fechoria.asunto FROM Fechoria, alma WHERE Fechoria.claveal = alma.claveal and alma.unidad = '$grupo'");
                        $ini0 = mysqli_query($db_con, "SELECT distinct asunto, COUNT(*) FROM  `$tabla` group by asunto");
                        */
                        $ini0 = mysqli_query($db_con, "SELECT f.asunto, count(*) as total FROM Fechoria f, alma a WHERE f.claveal = a.claveal and a.unidad = '$grupo' group by asunto order by total desc");
                            
                        if (mysqli_num_rows($ini0)):
                            ?>
                            <div class="row">
                            <div class="col-sm-10">

                            <table class="table table-striped table-hover" align="left">
                                <tr>
                                    <th>Tipo de Problema</th>
                                    <th>Número</th>
                                </tr>
                                <?php
                                while ($ini = mysqli_fetch_array($ini0))
                                {
                                ?>
                                    <tr>
                                        <td><?php  echo $ini[0];?></td>
                                        <td class="text-danger"><b><?php  echo $ini[1];?></b></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                    <?php
                    endif;
                    //mysqli_query($db_con, "DROP TABLE `$tabla`");
                }
                        ?>
                </div>

                <?php
                if(stristr($_SESSION['cargo'],'1') == TRUE or stristr($_SESSION['cargo'],'8') == TRUE)
                {
                ?>
                    <div class="tab-pane fade in" id="tab4">

                        <br /><h3>Información por Profesor</h3>
                        <br />

                        <div class="tabbable" style="margin-bottom: 18px;">
                            <ul class="nav nav-tabs">
                            <?php
                                $cur = substr($config['curso_inicio'],0,4);
                                for ($b = 0; $b < $numCursosAnteriores; $b++)
                                {
                                    $anio_escolar = $cur - $b;
                                    $haydatos = 0;

                                    if ($b == 0)
                                    {
                                        $db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']) or die('error');
                                        mysqli_query($db_con,"SET NAMES 'utf8'");
                                        $haydatos = 1;
                                    }

                                    if($b > 0 && ! empty($config['db_host_c'.$anio_escolar]))
                                    {
                                        $db_con = mysqli_connect($config['db_host_c'.$anio_escolar], $config['db_user_c'.$anio_escolar], $config['db_pass_c'.$anio_escolar], $config['db_name_c'.$anio_escolar]);
                                        mysqli_query($db_con,"SET NAMES 'utf8'");
                                        $haydatos = 1;
                                    }

                                    if($haydatos)
                                    {
                                    ?>
                                        <li<?php echo ($b == 0) ? ' class="active"' : '';?>><a href="#m<?php echo $b+1;?>" data-toggle="tab">Curso <?php echo $anio_escolar."-".($anio_escolar+1);?></a>
                                        </li>
                                        <?php
                                    }
                                }
                            ?>
                            </ul>

                            <div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">

                                <?php
                                $num="";
                                $cur = substr($config['curso_inicio'],0,4);
                                for ($i = 0; $i < $numCursosAnteriores; $i++)
                                {
                                    $num += 1;
                                    $num == '1' ? $activ=" active" : $activ='';

                                    $anio_escolar = $cur - $i;
                                    $haydatos = 0;

                                    if ($i == 0)
                                    {
                                        $db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']) or die('error');
                                        mysqli_query($db_con,"SET NAMES 'utf8'");
                                        $haydatos = 1;
                                    }
                                    
                                    if($i > 0 && ! empty($config['db_host_c'.$anio_escolar]))
                                    {
                                        $db_con = mysqli_connect($config['db_host_c'.$anio_escolar], $config['db_user_c'.$anio_escolar], $config['db_pass_c'.$anio_escolar], $config['db_name_c'.$anio_escolar]);
                                        mysqli_query($db_con,"SET NAMES 'utf8'");
                                        $haydatos = 1;
                                    }

                                    if($haydatos)
                                    {
                                    ?>
                                    <?php $chart_n = 0; ?>

                                        <div class="tab-pane fade in <?php echo $activ;?>" id="<?php echo "m".$num;?>">
                                        <br /><br />
                                        
                                        <?php
                                            $tot0 = '';
                                            $profesor = '';
                                            $numero = '';
                                            //$tot1 = mysqli_query($db_con, "create table fech_temp select informa, count(*) as numeros from Fechoria group by informa");
                                            //$tot0 = mysqli_query($db_con, "select informa, numeros from fech_temp order by numeros desc");
                                        
                                            $tot0 = mysqli_query($db_con, "select informa, count(*) as numeros from Fechoria group by informa order by numeros desc");
                                        
                                           
                                    ?>

                                     <?php  while ($total0 = mysqli_fetch_array($tot0))
                                            { ?>
                                            <?php $profesor.='"'.$total0[0].'",'; ?>
                                           <?php $numero.="$total0[1],"; ?>
                                        <?php } ?>
                                               <?php $chart_n++; ?>

                                <canvas id="chart_<?php echo $chart_n; ?>_<?php echo $anio_escolar; ?>_prof" width="100" height="120"></canvas>
                                <script>
                                var ctx = document.getElementById("chart_<?php echo $chart_n; ?>_<?php echo $anio_escolar; ?>_prof");
                                
                                var myChart = new Chart(ctx, {
                                    responsive: true,
                                    type: 'horizontalBar',
                                    data: {
                                      labels: [
                                        <?php echo $profesor; ?>
                                      ],
                                      datasets: [{
                                        backgroundColor: window.chartColors.green,
                                        data: [
                                            <?php echo $numero; ?>
                                        ]
                                      }]
                                    },
                                    options: {
                                        responsive: true,
                                         scales: {
                                            xAxes: [{
                                                position: 'top'
                                            }]
                                        },
                                        events: false,
                                        legend: {
                                            display: false,
                                        },
                                        animation: {
                                            animateScale: true,
                                            animateRotate: true,
                                            onComplete: function () {
                                                var chartInstance = this.chart;
                                                var ctx2 = chartInstance.ctx;
                                                console.log(chartInstance);
                                                var height = chartInstance.controller.boxes[0].bottom;
                                                ctx2.textAlign = "center";
                                                Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
                                                  var meta = chartInstance.controller.getDatasetMeta(i);
                                                  Chart.helpers.each(meta.data.forEach(function (bar, index) {
                                                     if(dataset.data[index]>0) ctx2.fillText(dataset.data[index], bar._model.x+12, height - ((height - bar._model.y)-4));
                                                  }),this)
                                                }),this);
                                            }
                                        }
                                    }
                                });
                                </script>
                                     
                                        <?php
                                        //mysqli_query($db_con, "drop table fech_temp");
                                    }
                                    ?>
                                        </div>
                                    <?php
                                }
                        ?>
                        </div>
                        </div>
                    </div>
                <?php
                }
                ?>  
                <div class="tab-pane fade in" id="tab5">
                        <br /><h3>Informe por Tipo de problema</h3><br />
                            <div class="tabbable" style="margin-bottom: 18px;">
                                <ul class="nav nav-tabs">
                                    <?php
                                    $cur = substr($config['curso_inicio'],0,4);
                                    for ($c = 0; $c < $numCursosAnteriores; $c++)
                                    {
                                        $anio_escolar = $cur - $c;
                                        $haydatos = 0;

                                        if ($c == 0)
                                        {
                                            $db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']) or die('error');
                                            mysqli_query($db_con,"SET NAMES 'utf8'");
                                            $haydatos = 1;
                                        }

                                        if($c > 0 && ! empty($config['db_host_c'.$anio_escolar]))
                                        {
                                            $db_con = mysqli_connect($config['db_host_c'.$anio_escolar], $config['db_user_c'.$anio_escolar], $config['db_pass_c'.$anio_escolar], $config['db_name_c'.$anio_escolar]);
                                            mysqli_query($db_con,"SET NAMES 'utf8'");
                                            $haydatos = 1;
                                        }

                                        if($haydatos)
                                        {
                                        ?>
                                            <li<?php echo ($c == 0) ? ' class="active"' : '';?>><a href="#p<?php echo $c+1;?>" data-toggle="tab">Curso <?php echo $anio_escolar."-".($anio_escolar+1);?></a></li>
                                        <?php
                                        }
                                    }
                                ?>
                                </ul>

                                <div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">

                                <?php
                                $num="";
                                $cur = substr($config['curso_inicio'],0,4);
                                for ($i = 0; $i < $numCursosAnteriores; $i++)
                                {
                                    $num+=1;
                                    $num == '1' ? $activ=" active" : $activ='';
                                    $anio_escolar = $cur - $i;
                                    $haydatos = 0;

                                    if ($i == 0)
                                    {
                                        $db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']) or die('error');
                                        mysqli_query($db_con,"SET NAMES 'utf8'");
                                        $haydatos = 1;
                                    }

                                    if($i > 0 && ! empty($config['db_host_c'.$anio_escolar]))
                                    {
                                        $db_con = mysqli_connect($config['db_host_c'.$anio_escolar], $config['db_user_c'.$anio_escolar], $config['db_pass_c'.$anio_escolar], $config['db_name_c'.$anio_escolar]);
                                        mysqli_query($db_con,"SET NAMES 'utf8'");
                                        $haydatos = 1;
                                    }
                                    
                                    if($haydatos)
                                    {
                                        $tipo = '';
                                        $numero = '';
                                    ?>
                                    <?php $chart_n = 0; ?>
                                        <div class="tab-pane fade in <?php echo $activ;?>" id="<?php echo "p".$num;?>">
                                            <br />
                                                <?php
                                                    $tot = '';
                                                    $tot = mysqli_query($db_con, "select asunto, count(*) as total, grave from Fechoria group by grave, asunto order by total desc");
                                                    while ($total = mysqli_fetch_array($tot))
                                                    { ?>

                                                            <?php if (isset($config['convivencia']['convivencia_seneca']) && $config['convivencia']['convivencia_seneca']): ?>
                                                            <?php
                                                            switch($total[2]) {
                                                                case 'leve' : $nom_gravedad = "Otra conducta"; break;
                                                                case 'grave' : $nom_gravedad = "Conducta contraria"; break;
                                                                case 'muy grave' : $nom_gravedad = "Conducta grave"; break;
                                                            }
                                                            ?>
                                                            <?php endif; ?>   
                                                            <?php $tipo.='"'.substr($total[0],0,80).'... ('.$total[2].')",'; ?>
                                                            <?php $numero.="$total[1],"; ?>
                                                    <?php
                                                    }
                                                    ?>
                                <?php $chart_n++; ?>

                                <canvas id="chart_<?php echo $chart_n; ?>_<?php echo $anio_escolar; ?>_tipo" width="100" height="120"></canvas>
                                <script>
                                var ctx = document.getElementById("chart_<?php echo $chart_n; ?>_<?php echo $anio_escolar; ?>_tipo");
                                
                                var myChart = new Chart(ctx, {
                                    responsive: true,
                                    type: 'horizontalBar',
                                    data: {
                                      labels: [
                                        <?php echo $tipo; ?>
                                      ],
                                      datasets: [{
                                        backgroundColor: window.chartColors.orange,
                                        data: [
                                            <?php echo $numero; ?>
                                        ]
                                      }]
                                    },
                                    options: {
                                        responsive: true,
                                        scales: {
                                            xAxes: [{
                                                position: 'top'
                                            }]
                                        },
                                        events: false,
                                        legend: {
                                            display: false,
                                        },
                                        animation: {
                                            animateScale: true,
                                            animateRotate: true,
                                            onComplete: function () {
                                                var chartInstance = this.chart;
                                                var ctx2 = chartInstance.ctx;
                                                console.log(chartInstance);
                                                var height = chartInstance.controller.boxes[0].bottom;
                                                ctx2.textAlign = "center";
                                                Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
                                                  var meta = chartInstance.controller.getDatasetMeta(i);
                                                  Chart.helpers.each(meta.data.forEach(function (bar, index) {
                                                     if(dataset.data[index]>0) ctx2.fillText(dataset.data[index], bar._model.x+15, height - ((height - bar._model.y)-3));
                                                  }),this)
                                                }),this);
                                            }
                                        }
                                    }
                                });
                                </script>
                                        </div>
                                    <?php
                                    }
                                }
                                ?>
                            </div>
                            </div>
                </div>   
            </div>
        </div>
    </div>
</div>
<?php
$db_con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
mysqli_query($db_con,"SET NAMES 'utf8'");
include("../../pie.php");?>
<script>
function espera( ) {
        document.getElementById("t_larga").style.display = '';
        document.getElementById("t_larga_barra").style.display = 'none';
}
window.onload = espera;
</script>
