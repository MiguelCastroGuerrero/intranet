<?php 
require('../../bootstrap.php');

$jsondata = array();

// Verificación lectura de mensajes de profesores
if (isset($_POST['idp'])) {
        
    $idp = $_POST['idp'];

    if (isset($_POST['esTarea']) && $_POST['esTarea'] == true) {
        $result = mysqli_query($db_con, "SELECT mens_texto.asunto, mens_texto.texto, mens_texto.origen FROM mens_profes JOIN mens_texto ON mens_profes.id_texto = mens_texto.id WHERE mens_profes.id_profe = $idp LIMIT 1");
        $row = mysqli_fetch_array($result);

        $result_profesor = mysqli_query($db_con, "SELECT nombre FROM departamentos WHERE idea = '".$row['origen']."' LIMIT 1");
        $row_profesor = mysqli_fetch_array($result_profesor);

        $titulo = $row['asunto'];
        $fechareg = date('Y-m-d H:i:s');
        $enlace = '//'.$config['dominio'].'/intranet/admin/mensajes/redactar.php?profes=1&origen='.$row['origen'].'&asunto=RE:%20'.$titulo;
        $tarea = htmlspecialchars_decode($row['texto']).'<p><br></p><p>Enviado por: '.$row_profesor['nombre'].'</p><p><a id="enlace_respuesta" href="'.$enlace.'"></a>';
        

        mysqli_query($db_con, "INSERT tareas (idea, titulo, tarea, estado, fechareg, prioridad) VALUES ('".$idea."', '".$titulo."', '".$tarea."', 0, '".$fechareg."', 0)");
    }
    
    $result = mysqli_query($db_con, "UPDATE mens_profes SET recibidoprofe = 1 WHERE id_profe = $idp LIMIT 1");

    if($result) {
        $jsondata['status'] = true;
    } else {
        $jsondata['status'] = false;
    }
    
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($jsondata);
    exit();

}

// Verificación lectura de mensajes de familias
if (isset($_POST['idf'])) {
        
    $idf = $_POST['idf'];
    
       if (isset($_POST['esTarea']) && $_POST['esTarea'] == true && isset($_POST['idf'])) {
        $result = mysqli_query($db_con, "SELECT ahora, asunto, texto, mensajes.claveal FROM mensajes JOIN alma ON mensajes.claveal = alma.claveal WHERE mensajes.id = $idf ORDER BY ahora DESC");
                    
        $origen = $_SESSION['ide']; 
        $row = mysqli_fetch_array($result);
            
        $datos_al = mysqli_query($db_con,"select apellidos, nombre from alma where claveal = '".$row['claveal']."'");
        $datos_an = mysqli_fetch_array($datos_al);

        $titulo = $row['asunto'];
        $fechareg = date('Y-m-d H:i:s');
            
        $enlace = '//'.$config['dominio'].'/intranet/admin/mensajes/redactar.php?profes=1&origen='.$datos_an['nombre'].' '.$datos_an['apellidos'].' &asunto=RE:%20'.$titulo;
            
        $tarea = htmlspecialchars_decode($row['texto']).'<p><br></p><p>Enviado por: '.$datos_an['nombre'].' '.$datos_an['apellidos'].'</p><p><a id="enlace_respuesta" href="'.$enlace.'"></a>';
        
        mysqli_query($db_con, "INSERT tareas (idea, titulo, tarea, estado, fechareg, prioridad) VALUES ('".$origen."', '".$titulo."', '".$tarea."', 0, '".$fechareg."', 0)");
    }
        
    $result = mysqli_query($db_con, "UPDATE mensajes SET recibidotutor = 1 WHERE id = $idf LIMIT 1");
        
    if($result) {
        $jsondata['status'] = true;
    } else {
        $jsondata['status'] = false;
    }
    
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($jsondata);
    exit();

}
?>