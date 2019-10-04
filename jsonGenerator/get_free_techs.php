<?php

require_once ("../database/database.php");
require_once ("../backend/functions.php");  

try {
    $query = "SELECT technicianID AS 'recid', technicianFullName AS 'name' FROM technician WHERE activitiesInProgress = 0"; 
    $statement = $db->prepare($query);
    $statement->execute();

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}