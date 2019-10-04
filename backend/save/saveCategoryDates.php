<?php

require_once ("../../database/database.php");

$json = json_decode($_POST["request"]);
$key = $json->key;
$changesObject = $json->changes;
$changes = json_decode(json_encode($changesObject), true); 
 
$changesCount = count($changes);
$optionalCategories = [];
$updateOptionalFieldsExpected = "";
$updateOptionalFieldsComplete = "";
$updateFieldsExpected = "";
$updateFieldsComplete = "";
$optionalFieldExists = false;

$siteTypeSiteRegionQuery = "SELECT optionalCategoryName, ID FROM checklist_checklistOptionalCategories WHERE checklistID = :checklistID";
$siteTypeSiteRegionStatement = $db->prepare($siteTypeSiteRegionQuery);
$siteTypeSiteRegionStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
$siteTypeSiteRegionStatement->execute();
$comparatorArray = $siteTypeSiteRegionStatement->fetchAll(PDO::FETCH_ASSOC);

if ($siteTypeSiteRegionStatement->rowCount() > 0) { 
    for ($i = 0; $i < $changesCount; $i++) {
        foreach ($comparatorArray as $value) {
            if ($value["ID"] === $changes[$i]["recid"] && isset($changes[$i]["expected"])) {
                $updateOptionalFieldsExpected .= "WHEN ID = '" . $changes[$i]["recid"] . "' THEN '" . $changes[$i]["expected"] . "' ";
                $optionalFieldExists = true;
                array_push($optionalCategories, $value["ID"]);
            }
            if ($value["ID"] === $changes[$i]["recid"] && isset($changes[$i]["completedOn"])) {
                $updateOptionalFieldsComplete .= "WHEN ID = '" . $changes[$i]["recid"] . "' THEN '" . $changes[$i]["completedOn"] . "' ";
                $optionalFieldExists = true;
                array_push($optionalCategories, $value["ID"]);
            }
        }
    }

    if ($optionalFieldExists) {
        if ($updateOptionalFieldsExpected !== "" && $updateOptionalFieldsComplete !== "") {
            $updateOptionalQuery = "UPDATE checklist_checklistOptionalCategories SET expectedCompletionTime = CASE " . $updateOptionalFieldsExpected . "ELSE expectedCompletionTime END, completedOn = CASE " . $updateOptionalFieldsComplete . "ELSE completedOn END";
            $updateOptionalStatement = $db->prepare($updateOptionalQuery);
            $response = $updateOptionalStatement->execute();
        } else if ($updateOptionalFieldsExpected !== "") {
            $updateOptionalQuery = "UPDATE checklist_checklistOptionalCategories SET expectedCompletionTime = CASE " . $updateOptionalFieldsExpected . "ELSE expectedCompletionTime END";
            $updateOptionalStatement = $db->prepare($updateOptionalQuery);
            $response = $updateOptionalStatement->execute();
        } else if ($updateOptionalFieldsComplete !== "") {
            $updateOptionalQuery = "UPDATE checklist_checklistOptionalCategories SET completedOn = CASE " . $updateOptionalFieldsComplete . "ELSE completedOn END";
            $updateOptionalStatement = $db->prepare($updateOptionalQuery);
            $response = $updateOptionalStatement->execute();
        }
    }
}

if (!empty($optionalCategories)) {
    for ($i = 0; $i < $changesCount; $i++) {
        for ($j = 0; $j < count($optionalCategories); $j++) {
            if ($optionalCategories[$j] !== $changes[$i]["recid"] && isset($changes[$i]["expected"])) {
                $updateFieldsExpected .= "WHEN ID = '" . $changes[$i]["recid"] . "' THEN '" . $changes[$i]["expected"] . "' ";
            }
            if ($optionalCategories[$j] !== $changes[$i]["recid"] && isset($changes[$i]["completedOn"])) {
                $updateFieldsComplete .= "WHEN ID = '" . $changes[$i]["recid"] . "' THEN '" . $changes[$i]["completedOn"] . "' ";
            }
        }
    }
} else {
    for ($i = 0; $i < $changesCount; $i++) {
        if (isset($changes[$i]["expected"])) {
            $updateFieldsExpected .= "WHEN ID = '" . $changes[$i]["recid"] . "' THEN '" . $changes[$i]["expected"] . "' ";
        }
        if (isset($changes[$i]["completedOn"])) {
            $updateFieldsComplete .= "WHEN ID = '" . $changes[$i]["recid"] . "' THEN '" . $changes[$i]["completedOn"] . "' ";
        }
    }
}

//print_r($changes);
//print_r("Expected = " . $updateFieldsExpected);
//print_r("Completed = " . $updateFieldsComplete);
//exit();

if ($updateFieldsExpected !== "" && $updateFieldsComplete !== "") {
    $updateQuery = "UPDATE checklist_checklistCategories SET expectedCompletionTime = CASE " . $updateFieldsExpected . "ELSE expectedCompletionTime END, completedOn = CASE " . $updateFieldsComplete . "ELSE completedOn END";
    $updateStatement = $db->prepare($updateQuery);
    $response = $updateStatement->execute();
} else if ($updateFieldsComplete !== "") {
    $updateQuery = "UPDATE checklist_checklistCategories SET completedOn = CASE " . $updateFieldsComplete . "ELSE completedOn END";
    $updateStatement = $db->prepare($updateQuery);
    $response = $updateStatement->execute();
} else if ($updateFieldsExpected !== "") { 
    $updateQuery = "UPDATE checklist_checklistCategories SET expectedCompletionTime = CASE " . $updateFieldsExpected . "ELSE expectedCompletionTime END";
    $updateStatement = $db->prepare($updateQuery);
    $response = $updateStatement->execute();
}

echo $response;