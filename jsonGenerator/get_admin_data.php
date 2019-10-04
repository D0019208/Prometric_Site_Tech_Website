<?php

require_once ("../database/database.php");
require_once ("../backend/functions.php");

$json = json_decode($_POST["request"]);
$table_name = $json->data->table_name;
$select_data = $json->data->select_data;
$recid_set = false;

if (isset($json->access_code)) {
    $access_code = $json->access_code;
} else {
    $access_code = $json->data->access_code;
}

$select_string = "";
$inner_join = "";
$select_data_count = count($select_data);

$conditions_array = array("taskID", "optionalTaskID", "tabID", "optionalTabID", "optionalCategoryID", "categoryID", "technicianID", "checklistID", "statusID", "activityID", "regionID", "siteCode", "event_id", "upcomingSiteEventID", "documentEventID");

for ($i = 0; $i < $select_data_count; $i++) { 
    if (in_array($select_data[$i], $conditions_array) && $recid_set === false) {
        $select_string .= $select_data[$i] . " AS recid";
        $recid_set = true; 
        
    } else {
        $select_string .= $select_data[$i];
    }
    
    if ($i < $select_data_count - 1) {
        $select_string .= ", ";
    }
    
    if($select_data[$i] === "status.status" && $table_name === "checklist")
    {
        $inner_join .= " INNER JOIN status ON checklist.status = status.statusID";
    }
} 

try {
    $query = "SELECT " . $select_string . " FROM " . $table_name . $inner_join .  " ORDER BY recid";   
    $statement = $db->prepare($query);
    $statement->execute();

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($access_code === "checklist_load") {
        $append = "";
        
        foreach ($result as $key => $value_checklist) { 
            $overNightSupportQuery = "SELECT technician, siteCode, checklistID FROM overnightSupport WHERE checklistID = :checklistID ORDER BY checklistID";
            $overNightSupportStatement = $db->prepare($overNightSupportQuery);
            $overNightSupportStatement->bindParam(":checklistID", $result[$key]["recid"], PDO::PARAM_STR);
            $overNightSupportStatement->execute();

            $overNightSupportArray = $overNightSupportStatement->fetchAll(PDO::FETCH_ASSOC);
           
            $overnight_count = count($overNightSupportArray);
            
            for($i = 0; $i < $overnight_count; $i++)
            {
                $append .= $overNightSupportArray[$i]["technician"];
                
                if($overnight_count > 1 && $i < $overnight_count - 1)
                {
                    $append .= ", ";
                } 
            } 
            
            $result[$key]["overNightSupport"] = $append;
            $append = "";
        }



        //print_r($overNightSupportArray);
        //print_r($result);
        
        echo json_encode($result);
    }
    else
    {
        echo json_encode($result);
    } 
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}