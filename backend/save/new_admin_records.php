<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$record = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$table_name = ltrim(rtrim(filter_input(INPUT_POST, "table_name", FILTER_SANITIZE_STRING)));

$query_columns = "(";
$query_values = "(";

$query_stuff = "";
$add_more = true;
$counter = 0;
//$record = ltrim(rtrim(filter_input(INPUT_POST, "record", FILTER_SANITIZE_STRING)));

$columns_array = array("avatar" => "https://d00192082.alwaysdata.net/SiteTechWebsiteServer/avatar/base.png", "password" => "Prometric1", "accessLevel" => 1);

//print_r($record);
foreach ($columns_array as $columns_key => $columns_value) {
    $query_columns .= $columns_key;
    $query_values .= "'" . $columns_value . "'";

    if ($counter < 3) {
        $query_columns .= ", ";
        $query_values .= ", ";

        $counter++;
    }
}

$counter = 0;

foreach ($record["record"] as $key => $value) {
    if ($key !== "recid") {
        $query_columns .= $key;
        $query_values .= "'" . $value . "'";
    }

    if ($counter < 3 && $add_more) {
        $query_columns .= ", ";
        $query_values .= ", ";

        $counter++;
    } else if ($add_more) {
        $query_columns .= ")";
        $query_values .= ")";

        $add_more = false;
    }
}

try {
    $query = "INSERT INTO " . $table_name . $query_columns . " VALUES " . $query_values;
    $statement = $db->prepare($query);
    $result = $statement->execute();  
    
    if ($table_name === "technician" && $result) {
        $new_technician = $record['record']['technicianFullName'];
        $event = $new_technician . " has just joined the Site Technician Team. Everybody welcome " . $new_technician . " to the team!";
 
        $time = date("H:i:s"); 
        
        $query = "INSERT INTO other_events (event, start, technician, link, event_type, image, allDayEvent) VALUES ('" . $event . "', '" . $record['record']['workingSince'] . " " . $time . "', '" . $new_technician . "', '#', 'other', 'images/SiteIcons/ModalIcons/ActivityType.png', 0)";
        $statement = $db->prepare($query);
        $statement->execute();
    }

    echo $result;
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}