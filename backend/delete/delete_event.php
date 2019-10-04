<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$json = json_decode($_POST["request"]);
$table_name = $json->table_name;
$where_clause = $json->where_clause;

$selected = json_decode(json_encode($json->selected));

$where_in = "";
$delete_count = count($selected);

for ($i = 0; $i < $delete_count; $i++) {
    $where_in .= $selected[$i];

    if ($delete_count > 1 && $i < $delete_count - 1) {
        $where_in .= ", ";
    }
} 

try { 
    $delete_query = "DELETE FROM " . $table_name . " WHERE " . $where_clause . " IN (" . $where_in . ")";
    $delete_statement = $db->prepare($delete_query);
    $delete_statement->execute(); 
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}