<?php

require_once ("../database/database.php");
require_once ("functions.php");

$checklistID = ltrim(rtrim(filter_input(INPUT_POST, "checklistID", FILTER_SANITIZE_STRING)));
$technician = ltrim(rtrim(filter_input(INPUT_POST, "technician", FILTER_SANITIZE_STRING)));
//$checklistID = 60;
//$technician = "Nichita Postolachi";
if ($technician === "Empty" || $technician === "") {
    echo "Not allowed to edit";
} else {
    try {
        $liveEventQuery = "SELECT DISTINCT checklist.technician FROM checklist INNER JOIN overnightSupport ON overnightSupport.checklistID = :checklistID WHERE checklist.checklistID = :checklistID2 AND checklist.technician = :technician OR overnightSupport.technician = :technician2";
        $liveEventStatement = $db->prepare($liveEventQuery);
        $liveEventStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_INT);
        $liveEventStatement->bindParam(":technician", $technician, PDO::PARAM_STR);
        $liveEventStatement->bindParam(":checklistID2", $checklistID, PDO::PARAM_INT);
        $liveEventStatement->bindParam(":technician2", $technician, PDO::PARAM_STR);
        $liveEventStatement->execute();

        if ($liveEventStatement->rowCount() > 0) {
            echo "Allowed to edit";
        } else {
            echo "Not allowed to edit";
        }
    } catch (PDOException $exception) {
        echo $exception->getMessage();
        exit();
    }
}