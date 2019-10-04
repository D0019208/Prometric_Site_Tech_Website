<?php
session_start();

require_once ("../database/database.php");
require_once ("functions.php");
require_once ("class_lib.php");

$email = ltrim(rtrim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)));
if(empty($email))
{
    echo "Ooops, the email seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit();
}

$password = ltrim(rtrim(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING)));
if(empty($password))
{
    echo "Ooops, the password seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit();
}

$technician;

$technicianData = loginTechnician($db, $email, $password);

//foreach ($technicianData as $row)
//{
//    $_SESSION["loggedIn"] = true;
//    $_SESSION["name"] = $row["technicianFullName"];
//    $_SESSION["avatar"] = $row["avatar"];
//    
//    //$technician = new Technician($row["technicianFullName"], $row["experience"], $row["workingSince"], $row["activitiesComplete"], $row["activitiesInProgress"], $row["documentsUpdated"], $row["documentsCreated"], $row["accessLevel"]);
//}

echo json_encode($technicianData);

