<?php

require_once ("../database/database.php");
require_once ("../backend/functions.php");

$json = json_decode($_POST["request"]);
$key = $json->key; 
$tabName = $json->tab;
$summary = $json->summary;
//$key = 57;
//$tabName = "Comms Backend";

try {
    //MANDATORY tabs
    $commsBackendQuery = "SELECT ID, optionalTaskName, taskCompleted FROM checklist_checklistOptionalTasks WHERE checklistID = :checklistID AND optionalTabName = :tab";
    $commsBackendStatement = $db->prepare($commsBackendQuery);
    $commsBackendStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $commsBackendStatement->bindParam(":tab", $tabName, PDO::PARAM_STR);
    $commsBackendStatement->execute();

    $commsBackendArray = $commsBackendStatement->fetchAll(PDO::FETCH_NUM);
} catch (PDOException $exception) {
    echo $exception->getMessage();
}

$jsonArray = [];
//JSON columns
$jsonColumnsArray = array("recid", "taskName", "complete");

//Add JSON columns
for ($j = 0; $j < count($jsonColumnsArray); $j++) {
    $key = $jsonColumnsArray[$j];
    $value = '';

    $jsonArray[$key] = $value;
} 

//Build the JSON string
$jsonToSendBack = '{ "records" : [';
$idUsed = false;
$taskUsed = false;
$completeUsed = false;
$completedCount = 0;

$counter = 0; 
$commsBackendArrayCountIf = count($commsBackendArray) - 1;
$innerCommsArray = 0;

//Summary
$notStarted = false;
$inProgress = false;
$complete = false; 

for ($i = 0; $i < count($commsBackendArray); $i++) {
    foreach ($jsonArray as $key => $value) 
    { 
        if ($idUsed === false) { 
            $jsonArray[$key] = $commsBackendArray[$i][$innerCommsArray];
            $idUsed = true; 
            $innerCommsArray++;
        } else if ($taskUsed === false) {
            $jsonArray[$key] = $commsBackendArray[$i][$innerCommsArray];
            $taskUsed = true;
            $innerCommsArray++;
        }
        else if($completeUsed === false)
        {  
            if($commsBackendArray[$i][$innerCommsArray] == "true")
            {
                $jsonArray[$key] = '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" checked>'; 
                $inProgress = true;
                $completedCount++;
            }
            else
            {
                $notStarted = true;
                $jsonArray[$key] = '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" unchecked>'; 
            }
            
            if(count($commsBackendArray) === $completedCount)
            {
                $complete = true;
            }
            
            $completeUsed = true;
            
        }
        
        if ($counter == count($jsonArray) - 1) {

            //If loop has ended (all columns filled) Reset
            $idUsed = false;
            $taskUsed = false; 
            $completeUsed = false;
            $innerCommsArray = 0;
            
            $encodedJsonArray = json_encode($jsonArray);
            $jsonToSendBack .= $encodedJsonArray;

            if ($i < $commsBackendArrayCountIf) {
                $jsonToSendBack .= ',';
            } else { 
                if($complete)
                {
                   $jsonToSendBack .= ",{w2ui: {summary: true},recid: 'S-1', taskName: '" . $summary . "', complete: '<span>Complete</span>' }]}"; 
                }
                else if($inProgress)
                {
                    $jsonToSendBack .= ",{w2ui: {summary: true},recid: 'S-1', taskName: '" . $summary . "', complete: '<span>In Progress</span>' }]}";
                }
                else if($notStarted)
                {
                    $jsonToSendBack .= ",{w2ui: {summary: true},recid: 'S-1', taskName: '" . $summary . "', complete: '<span>Not Started</span>' }]}";
                }
            }
            $counter = 0;
        } else {

            $counter++;
        }
    }
}
 
echo $jsonToSendBack; 