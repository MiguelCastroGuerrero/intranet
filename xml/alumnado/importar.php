<?php
require('../../bootstrap.php');

acl_acceso($_SESSION['cargo'], array('z', '1'));

function normalize_regalum_header($header) {
    $vocal_chars = array('a','e','i','o','u');
    $consonant_chars = array('n','c');
    
    $ilegal_integer_chars_acent = array('0','1','2','3','4','5','6','7','8','9');
    $ilegal_vocal_chars_acent = array('á','é','í','ó','ú');
    $ilegal_voval_chars_grave_accent = array('à','è','ì','ò','ù');
    $ilegal_vocal_chars_dieresis = array('ä','ë','ï','ö','ü');
    $ilegal_vocal_chars_circunflejo = array('â','ê','î','ô','û');
    $ilegal_consonant_chars = array('ñ','ç');
    $ilegal_simbol_chars = array(' ','/','-','+');
    $ilegal_prepositions = array("_de_los_","_de_las_","_del_","_de_","_a_","_en_","_esta_","_este_","_esto_","_las_","_la_","_les_","_los_","_a_");
    $ilegal_chars = array('º','ª','.',',',';','<','>','[',']','{','}','(',')','"','\'','·','$','%','&','=','?','¿','!','¡');
    
    $header = mb_convert_case($header, MB_CASE_LOWER, "UTF-8");
    $header = str_replace($ilegal_integer_chars_acent, '', $header);
    $header = str_replace($ilegal_vocal_chars_acent, $vocal_chars, $header);
    $header = str_replace($ilegal_voval_chars_grave_accent, $vocal_chars, $header);
    $header = str_replace($ilegal_vocal_chars_dieresis, $vocal_chars, $header);
    $header = str_replace($ilegal_vocal_chars_circunflejo, $vocal_chars, $header);
    $header = str_replace($ilegal_consonant_chars, $consonant_chars, $header);
    $header = str_replace($ilegal_simbol_chars, '_', $header);
    $header = str_replace($ilegal_prepositions, '_', $header);
    $header = str_replace($ilegal_chars, '', $header);
    $header = str_replace("alumno_a", "alumno", $header);
    $header = str_replace("correo_electronico", "correo", $header);
    $header = preg_replace('/[_]+/', '_', $header);
    
    return $header;
}

function create_normalize_table($table_name, $csv_header) {
    global $db_con;

    $columns = count($csv_header);

    $column_definitions = array();

    for ($column = 0; $column < $columns; $column++) {

        $csv_header[$column] = utf8_encode($csv_header[$column]);
        $column_name = normalize_regalum_header($csv_header[$column]);

        // Default values
        $column_type = "varchar";
        $column_length = "120";
        $attribs = NULL;
        $is_null = TRUE;
        $is_autoincrement = FALSE;
        $constraints = NULL;
        $is_primary_key = FALSE;
        $comment = $csv_header[$column];

        if ($column_name === "alumno") {
            $column_type = "varchar";
            $column_length = "250";
            $is_null = FALSE;
        }

        if ($column_name === "n_id_escolar") {
            $column_type = "varchar";
            $column_length = "12";
            $is_null = FALSE;
            $is_primary_key = TRUE;
        }

        if (strpos($column_name, "dni_pasaporte") !== FALSE) {
            $column_type = "char";
            $column_length = "9";
        }

        if (strpos($column_name, "direccion") !== FALSE) {
            $column_length = "250";
        }

        if ($column_name === "codigo_postal") {
            $column_type = "char";
            $column_length = "5";
        }

        if (strpos($column_name, "fecha") !== FALSE) {
            $column_type = "char";
            $column_length = "10";
        }

        if (strpos($column_name, "curso") !== FALSE) {
            $column_length = "250";
        }

        if ($column_name === "n_expediente_centro") {
            $column_type = "varchar";
            $column_length = "15";
            $is_null = FALSE;
        }

        if ($column_name === "primer_apellido") {
            $is_null = FALSE;
        }

        if ($column_name === "nombre") {
            $is_null = FALSE;
        }

        if (strpos($column_name, "nombre") !== FALSE) {
            $column_length = "60";
        }

        if (strpos($column_name, "telefono") !== FALSE) {
            $column_type = "char";
            $column_length = "9";
        }

        if (strpos($column_name, "sexo") !== FALSE) {
            $column_type = "char";
            $column_length = "1";
        }

        if ($column_name === "unidad") {
            $column_type = "varchar";
            $column_length = "25";
        }

        if (strpos($column_name, "apellido") !== FALSE) {
            $column_type = "varchar";
            $column_length = "60";
        }

        if ($column_name === "ano_matricula") {
            $column_type = "char";
            $column_length = "4";
            $is_null = FALSE;
        }

        if ($column_name === "edad_ano_matricula") {
            $column_type = "tinyint";
            $column_length = "2";
            $attribs = "UNSIGNED";
            $is_null = FALSE;
        }

        if ($column_name === "n_matriculas_curso") {
            $column_type = "tinyint";
            $column_length = "1";
            $attribs = "UNSIGNED";
            $is_null = FALSE;
        }

        if ($column_name === "nsegsocial") {
            $column_type = "char";
            $column_length = "12";
        }

        if ($column_name === "padece_alguna_enfermedad") {
            $column_type = "char";
            $column_length = "2";
        }

        if ($column_name === "sigue_tratamiento") {
            $column_type = "char";
            $column_length = "2";
        }

        if ($column_name === "alergia_medicamentos") {
            $column_type = "char";
            $column_length = "2";
        }

        if ($column_name === "intolerancias_alimenticias") {
            $column_type = "char";
            $column_length = "2";
        }

        if ($column_name === "tipo_familia_numerosa") {
            $column_type = "varchar";
            $column_length = "30";
        }

        if ($column_name === "custodia") {
            $column_length = "250";
        }

        $column_definition = array(
            "column_name" => $column_name,
            "column_type" => $column_type,
            "column_length" => $column_length,
            "attribs" => $attribs,
            "is_null" => $is_null,
            "is_autoincrement" => $is_autoincrement,
            "constraints" => $constraints,
            "is_primary_key" => $is_primary_key,
            "comment" => $comment
        );

        array_push($column_definitions, $column_definition);
    }

    mysqli_query($db_con, "DROP TABLE `$table_name`;");

    $sql_statement = "CREATE TABLE IF NOT EXISTS `$table_name` (";
    foreach ($column_definitions as $column_def) {
        $column_name = $column_def["column_name"];
        $column_type = mb_convert_case($column_def["column_type"], MB_CASE_LOWER, "UTF-8");
        $column_length = $column_def["column_length"];
        $attribs = $column_def["attribs"] ? $column_def["attribs"] : '';
        $is_null = $column_def["is_null"] ? "DEFAULT NULL" : "NOT NULL";
        $is_autoincrement = $column_def["column_type"] == "int" && $column_def["autoincrement"] ? "AUTO_INCREMENT" : "";
        $is_primary_key = $column_def["is_primary_key"] ? "PRIMARY KEY": "";
        $comment = $column_def["comment"] ? "COMMENT '".htmlspecialchars($column_def["comment"])."'" : "";

        $sql_statement .= "`$column_name` $column_type($column_length) $attribs $is_null $is_primary_key $comment ,";
    }
    $sql_statement = rtrim($sql_statement, ',');
    $sql_statement .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci ;";

    $result = mysqli_query($db_con, $sql_statement);

    if (! $result) {
        return Null;
    }
    else {
        return $sql_statement;
    }
}

if (! isset($_POST['submit'])) {
    echo "Direct access no allowed.";
}
 
// Allowed mime types
$file_mimes = array(
    'text/x-comma-separated-values',
    'text/comma-separated-values',
    'application/octet-stream',
    'application/vnd.ms-excel',
    'application/x-csv',
    'text/x-csv',
    'text/csv',
    'application/csv',
    'application/excel',
    'application/vnd.msexcel',
    'text/plain'
);

// Validate whether selected file is a CSV file
if (empty($_FILES['file']['name']) || ! in_array($_FILES['file']['type'], $file_mimes)) {
    echo "Please select valid file";
}

// Open uploaded CSV file with read-only mode
$csv_file = fopen($_FILES['file']['tmp_name'], 'r');

$csv_header = fgetcsv($csv_file, 10000, ",");
$sql = create_normalize_table("alumnado", $csv_header);

if (! $sql) {
    echo "Oh noo!";
}
else {
    echo "Creada la base de datos";
}


// Parse data from CSV file line by line
while (($get_data = fgetcsv($csv_file, 10000, ",")) !== FALSE) {
    // Get row data
    $name = $get_data[0];
    $email = $get_data[1];
    $phone = $get_data[2];
    $status = $get_data[3];


}

// Close opened CSV file
fclose($csv_file);
