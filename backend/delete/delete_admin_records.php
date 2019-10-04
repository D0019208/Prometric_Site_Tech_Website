<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$json = json_decode($_POST["request"]);
$table_name = $json->table_name;
$where_clause = $json->where_clause;

$selected = json_decode(json_encode($json->selected)); 

$where_append = "";
$delete_count = count($selected);

for($i = 0; $i < $delete_count; $i++)
{
    $where_append .= $where_clause . " = " . $selected[$i];
    
    if($i < $delete_count - 1 && $delete_count > 1)
    {
        $where_append .= " OR ";
    }
}

try {
    $query = "DELETE FROM " . $table_name . " WHERE " . $where_append;
    $statement = $db->prepare($query);
    $result = $statement->execute();
    
    echo $result;
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}