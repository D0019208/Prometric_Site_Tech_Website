<?php
require_once ("../database/database.php");
require_once ("../backend/functions.php");

$json = json_decode($_POST["request"]);
$key = $json->key; 
//$key = 69;
$summary = "Contact Complete";
try {

    $checklistQuery = "SELECT contactID, type, name, phoneNumber, taskCompleted FROM siteContact WHERE checklistID = :checklistID";
    $checklistStatement = $db->prepare($checklistQuery);
    $checklistStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $checklistStatement->execute();

    $checklistArray = $checklistStatement->fetchAll(PDO::FETCH_NUM);
} catch (PDOException $exception) {
    return $exception->getMessage();
} 

$jsonArray = [];
//JSON columns
$jsonColumnsArray = array("recid", "contactType", "contactName", "contactNumber", "complete");

//Add JSON columns
for ($j = 0; $j < count($jsonColumnsArray); $j++) {
    $key = $jsonColumnsArray[$j];
    $value = '';

    $jsonArray[$key] = $value;
} 

//{recid: 1, contactType: 'TCA', contactName: 'John Smith', contactNumber: '089 985 4571', complete: '<input type="checkbox" unchecked>'},
//{recid: 2, contactType: 'Main Tech', contactName: 'Nichita Postolachi', contactNumber: '089 985 4571', complete: '<input type="checkbox" checked>'},
//{recid: 3, contactType: 'Overnight Support', contactName: 'Shane Dollard', contactNumber: '089 985 4571', complete: '<input type="checkbox" checked>'}

//Build the JSON string
$jsonToSendBack = '{ "records" : [';
$idUsed = false;
$contactTypeUsed = false;
$contactNameUsed = false;
$contactNumberUsed = false;
$completeUsed = false;

$completedCount = 0;

$counter = 0; 
$checklistArrayCountIf = count($checklistArray) - 1;
$innerCommsArray = 0;

//Summary
$notStarted = false;
$inProgress = false;
$complete = false; 

for ($i = 0; $i < count($checklistArray); $i++) {
    foreach ($jsonArray as $key => $value) 
    { 
        if ($idUsed === false) { 
            $jsonArray[$key] = $checklistArray[$i][$innerCommsArray];
            $idUsed = true; 
            $innerCommsArray++;
        } else if ($contactTypeUsed === false) {
            $jsonArray[$key] = $checklistArray[$i][$innerCommsArray];
            $contactTypeUsed = true;
            $innerCommsArray++;
        } else if ($contactNameUsed === false) {
            $jsonArray[$key] = $checklistArray[$i][$innerCommsArray];
            $contactNameUsed = true;
            $innerCommsArray++;
        } else if ($contactNumberUsed === false) {
            $jsonArray[$key] = $checklistArray[$i][$innerCommsArray];
            $contactNumberUsed = true;
            $innerCommsArray++;
        }
        else if($completeUsed === false)
        {  
            if($checklistArray[$i][$innerCommsArray] == "true")
            {
                $jsonArray[$key] = '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" checked>'; 
                $inProgress = true;
                $completedCount++;
            }
            else
            {
                $notStarted = true;
                $jsonArray[$key] = '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" unchecked>'; 
            }
            
            if(count($checklistArray) === $completedCount)
            {
                $complete = true;
            }
            
            $completeUsed = true;
            
        }
        
        if ($counter == count($jsonArray) - 1) {

            //If loop has ended (all columns filled) Reset
            $idUsed = false;
            $contactTypeUsed = false; 
            $contactNumberUsed = false;
            $contactNameUsed = false;
            $completeUsed = false;
            
            $innerCommsArray = 0;
            
            $encodedJsonArray = json_encode($jsonArray);
            $jsonToSendBack .= $encodedJsonArray;

            if ($i < $checklistArrayCountIf) {
                $jsonToSendBack .= ',';
            } else { 
                if($complete)
                {
                   $jsonToSendBack .= ",{w2ui: {summary: true},recid: 'S-1', contactType: '" . $summary . "', complete: '<span>Complete</span>' }]}"; 
                }
                else if($inProgress)
                {
                    $jsonToSendBack .= ",{w2ui: {summary: true},recid: 'S-1', contactType: '" . $summary . "', complete: '<span>In Progress</span>' }]}";
                }
                else if($notStarted)
                {
                    $jsonToSendBack .= ",{w2ui: {summary: true},recid: 'S-1', contactType: '" . $summary . "', complete: '<span>Not Started</span>' }]}";
                }
            }
            $counter = 0;
        } else {

            $counter++;
        }
    }
}
 
echo $jsonToSendBack; 
