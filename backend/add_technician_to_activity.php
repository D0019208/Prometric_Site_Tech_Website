<?php

require_once ("../database/database.php");
require_once ("functions.php");

$user_name = ltrim(rtrim(filter_input(INPUT_POST, "userName", FILTER_SANITIZE_STRING)));

if (isset($_POST["technicians"])) {
    $technicians = $_POST["technicians"];
} else {
    echo "Ooops, it seems like you forgot to select a technician!";
    exit();
}

if (isset($_POST["activities"])) {
    $activities = $_POST["activities"];
} else {
    echo "Ooops, it seems like you forgot to select an activity!";
    exit();
}

print_r($technicians);
print_r($activities);

$technicians_count = count($technicians);
$activities_count = count($activities);

$technicians_string = "";
$activities_string = "";

for($i = 0; $i < $technicians_count; $i++)
{ 
    $technicians_string .= "'" . $technicians[$i] . "'";
    
    if($i < $technicians_count - 1 && $technicians_count > 1)
    {
        $technicians_string .= ", ";
    }
}

for($i = 0; $i < $activities_count; $i++)
{ 
    $activities_string .= "'" . $activities[$i] . "'";
    
    if($i < $activities_count - 1 && $activities_count > 1)
    {
        $activities_string .= ", ";
    }
}

echo $technicians_string;

try {
    $check_query = "SELECT COUNT(checklist.technician) AS 'count' FROM checklist WHERE checklist.checklistID = IN(" . $activities_string . ") AND checklist.technician IN(" . $technicians_string . ")";
    
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}