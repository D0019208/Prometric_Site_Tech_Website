<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$record = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$table_name = ltrim(rtrim(filter_input(INPUT_POST, "table_name", FILTER_SANITIZE_STRING)));   
$access_code = ltrim(rtrim(filter_input(INPUT_POST, "access_code", FILTER_SANITIZE_STRING)));   

$query_columns = "(";
$query_values = "(";

$query_stuff = "";
$add_more = true;

$counter_limit;

if($access_code === "1 count")
{
    $counter_limit = 1;
} else if ($access_code === "2 count")
{
    $counter_limit = 2;
} 

$counter = 0; 

foreach ($record["record"] as $key => $value) {
    if ($key !== "recid") {
        $query_columns .= $key; 
        
        if(gettype($value) === "array")
        {  
            $query_values .= "'" . $value["text"] . "'";
        }
        else
        {
            $query_values .= "'" . $value . "'";
        } 
    }

    if ($counter < $counter_limit && $add_more) {
        $query_columns .= ", ";
        $query_values .= ", ";

        $counter++;
    } else if($add_more) {
        $query_columns .= ")";
        $query_values .= ")";
        
        $add_more = false;
    }
}

try {
    $query = "INSERT INTO " . $table_name . $query_columns . " VALUES " . $query_values;    
    $statement = $db->prepare($query); 
    $result = $statement->execute();

    echo $result;
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}