<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$json = json_decode($_POST["request"]);
$key = $json->key;
$table = $json->table;
$changesObject = $json->changes;
$siteCode = $json->siteCode;
$category = $json->category;

//User data
$technician = $json->technician;
$technician = ltrim(rtrim(filter_var($technician, FILTER_SANITIZE_STRING)));
$overnightSupport = $json->overnightSupport;
$email = $json->email;

$timezone = $json->timezone; 

date_default_timezone_set($timezone);
$time = date('Y/m/d h:i:s', time());

$categoryStatus;
$numberOfChanges = count($changesObject);
$updateFields = "";

$changes = json_decode(json_encode($changesObject), True);


for ($i = 0; $i < $numberOfChanges; $i++) {
    if (preg_match("~\bchecked\b~", $changes[$i]["complete"])) {
        $updateFields .= "WHEN ID = '" . $changes[$i]["recid"] . "' THEN 'true' ";
    } else {
        $updateFields .= "WHEN ID = '" . $changes[$i]["recid"] . "' THEN 'false' ";
    }
}

$updateQuery = "UPDATE " . $table . " SET taskCompleted = CASE " . $updateFields . "ELSE taskCompleted END";
$updateStatement = $db->prepare($updateQuery);
$updateResponse = $updateStatement->execute(); 

$categoryCountQuery = "SELECT COUNT(*) FROM checklist_checklistTasks WHERE categoryName = :category AND checklistID = :checklistID AND taskName <> 'Server Prebuilt by 3rd Party'";
$categoryCountStatement = $db->prepare($categoryCountQuery);
$categoryCountStatement->bindParam(":category", $category[0], PDO::PARAM_STR);
$categoryCountStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
$categoryCountStatement->execute();

$results = $categoryCountStatement->fetchAll(PDO::FETCH_NUM);
$categoryCount = $results[0][0];

$categoryCountQueryTrue = "SELECT COUNT(*) FROM checklist_checklistTasks WHERE categoryName = :category AND checklistID = :checklistID AND taskCompleted = 'true' AND taskName <> 'Server Prebuilt by 3rd Party'";
$categoryCountStatementTrue = $db->prepare($categoryCountQueryTrue);
$categoryCountStatementTrue->bindParam(":category", $category[0], PDO::PARAM_STR);
$categoryCountStatementTrue->bindParam(":checklistID", $key, PDO::PARAM_STR);
$categoryCountStatementTrue->execute();

$results = $categoryCountStatementTrue->fetchAll(PDO::FETCH_NUM);
$categoryTrueCount = $results[0][0];

if ($categoryTrueCount < 1) {
    $categoryStatus = 1;
} else if ($categoryTrueCount >= 1 && $categoryTrueCount < $categoryCount) {
    $categoryStatus = 2;
} else if ($categoryTrueCount === $categoryCount) {
    $categoryStatus = 3;
}  

$updateCategoryStatusQuery = "UPDATE checklist_checklistCategories SET categoryStatus = '" . $categoryStatus . "' WHERE categoryName = :categoryName AND checklistID = :checklistID";
$updateCategoryStatusStatement = $db->prepare($updateCategoryStatusQuery);
$updateCategoryStatusStatement->bindParam(":categoryName", $category[0], PDO::PARAM_STR);
$updateCategoryStatusStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
$response = $updateCategoryStatusStatement->execute();

echo setStatus($db, $siteCode, $key, $technician, $overnightSupport, $time, false, $key);