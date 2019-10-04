<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$json = json_decode($_POST["request"]);
$key = $json->key;
$changesObject = $json->changes;
$category = $json->category;
$siteCode = $json->siteCode;
      
$technician = $json->technician;
$technician = ltrim(rtrim(filter_var($technician, FILTER_SANITIZE_STRING)));
$overnightSupport = $json->overnightSupport;
$email = $json->email;

$timezone = $json->timezone; 

date_default_timezone_set($timezone);
$time = date('Y/m/d h:i:s', time());

$numberOfChanges = count($changesObject);
$updateFields = "";
$phoneNumberFields = '';
$nameFields = '';
$contactNameSet = false;
$contactNumberSet = false;
$contactCompleteSet = false;

$changes = json_decode(json_encode($changesObject), True);

for ($i = 0; $i < $numberOfChanges; $i++) {
    
}

for ($i = 0; $i < $numberOfChanges; $i++) {
    if (isset($changes[$i]["contactNumber"])) {
        $phoneNumberFields .= "WHEN contactID = '" . $changes[$i]["recid"] . "' THEN '" . $changes[$i]["contactNumber"] . "' ";
        $contactNumberSet = true;
    }

    if (isset($changes[$i]["contactName"])) {
        $nameFields .= "WHEN contactID = '" . $changes[$i]["recid"] . "' THEN '" . $changes[$i]["contactName"] . "' ";
        $contactNameSet = true;
    }

    if (isset($changes[$i]["complete"])) {
        if (preg_match("~\bchecked\b~", $changes[$i]["complete"])) {
            $updateFields .= "WHEN contactID = '" . $changes[$i]["recid"] . "' THEN 'true' ";
        } else {
            $updateFields .= "WHEN contactID = '" . $changes[$i]["recid"] . "' THEN 'false' ";
        }

        $contactCompleteSet = true;
    }
}



if ($contactCompleteSet) {
    $updateQuery1 = "UPDATE siteContact SET taskCompleted = CASE " . $updateFields . "ELSE taskCompleted END";
    $updateStatement1 = $db->prepare($updateQuery1);
//$updateStatement1->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $updateStatement1->execute();
}

if($contactNumberSet) {
    $updateQuery2 = "UPDATE siteContact SET phoneNumber = CASE " . $phoneNumberFields . "ELSE phoneNumber END";
    $updateStatement2 = $db->prepare($updateQuery2);
//$updateStatement2->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $updateResponse2 = $updateStatement2->execute();
}

if ($contactNameSet) {
    $updateQuery3 = "UPDATE siteContact SET name = CASE " . $nameFields . "ELSE name END";
    $updateStatement3 = $db->prepare($updateQuery3);
//$updateStatement3->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $updateStatement3->execute();
}

$categoryCountQuery = "SELECT COUNT(*) FROM siteContact WHERE checklistID = :checklistID";
$categoryCountStatement = $db->prepare($categoryCountQuery);
$categoryCountStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
$categoryCountStatement->execute();

$results = $categoryCountStatement->fetchAll(PDO::FETCH_NUM);
$categoryCount = $results[0][0];

$categoryCountQueryTrue = "SELECT COUNT(*) FROM siteContact WHERE  checklistID = :checklistID AND taskCompleted = 'true'";
$categoryCountStatementTrue = $db->prepare($categoryCountQueryTrue);
$categoryCountStatementTrue->bindParam(":checklistID", $key, PDO::PARAM_STR);
$categoryCountStatementTrue->execute();

$results = $categoryCountStatementTrue->fetchAll(PDO::FETCH_NUM);
$categoryTrueCount = $results[0][0];

if ($categoryTrueCount === 0) {
    $categoryStatus = 1;
} else if ($categoryTrueCount >= 1 && $categoryTrueCount < $categoryCount) {
    $categoryStatus = 2;
} else if ($categoryTrueCount === $categoryCount) {
    $categoryStatus = 3;
}    

$updateCategoryStatusQuery = "UPDATE checklist_checklistCategories SET categoryStatus = '" . $categoryStatus . "' WHERE categoryName = :categoryName AND checklistID = :checklistID";
$updateCategoryStatusStatement = $db->prepare($updateCategoryStatusQuery);
$updateCategoryStatusStatement->bindParam(":categoryName", $category, PDO::PARAM_STR);
$updateCategoryStatusStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
$response = $updateCategoryStatusStatement->execute();

echo setStatus($db, $siteCode, $key, $technician, $overnightSupport, $time, false, $key);