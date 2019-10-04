<?php

require_once ("../database/database.php");
require_once ("functions.php");

$all_technicians = json_decode($_POST["technicians"]);
$all_technicians_count = count($all_technicians);

$whereClause;
$activities_count_array = [];

if ($all_technicians_count < 1) {
    $whereClause = "";
} else {
    $whereClause = "WHERE ";
}

for ($i = 0; $i < $all_technicians_count; $i++) {
    $activities_count_array = array_merge($activities_count_array, array($all_technicians[$i] => array("activities_complete" => 0, "activities_in_progress" => 0, "upcoming_activities" => 0)));

    $whereClause .= "technician.technicianFullName = '" . $all_technicians[$i] . "'";

    if ($i < $all_technicians_count - 1) {
        $whereClause .= " OR ";
    }
}

for ($i = 0; $i < $all_technicians_count; $i++) {
    $whereClause .= " OR overnightSupport.technician = '" . $all_technicians[$i] . "'";
}

try {
    //First Query (Completed Sites)
    $activities_complete_query = "SELECT checklist.technician FROM checklist WHERE complete = 'true'";
    $activities_complete_statement = $db->prepare($activities_complete_query);
    $activities_complete_statement->execute();

    $activities_complete_result = $activities_complete_statement->fetchAll(PDO::FETCH_ASSOC);
    $activities_complete_count = count($activities_complete_result);

    //First Query (Completed Sites) - Overnight Support Seperate Query
    $activities_complete_overnight_query = "SELECT overnightSupport.technician AS 'overnightSupport' FROM overnightSupport INNER JOIN checklist ON checklist.checklistID = overnightSupport.checklistID WHERE checklist.complete = 'true'";
    $activities_complete_overnight_statement = $db->prepare($activities_complete_overnight_query);
    $activities_complete_overnight_statement->execute();

    $activities_complete_overnight_result = $activities_complete_overnight_statement->fetchAll(PDO::FETCH_ASSOC);
    $activities_complete_overnight_count = count($activities_complete_overnight_result);

    //Second Query (In Progress Sites)
    $activities_in_progress_query = "SELECT checklist.technician, liveSiteEvents.checklistID FROM liveSiteEvents INNER JOIN checklist AS checklist ON checklist.checklistID = liveSiteEvents.checklistID INNER JOIN technician as technician ON checklist.technician = technician.technicianFullName";
    $activities_in_progress_statement = $db->prepare($activities_in_progress_query);
    $activities_in_progress_statement->execute();

    $activities_in_progress_result = $activities_in_progress_statement->fetchAll(PDO::FETCH_ASSOC);
    $activities_in_progress_count = count($activities_in_progress_result);
    
    $checklist_ids_search = "";
    $counter = 0;
    foreach ($activities_in_progress_result as $value) {
        $checklist_ids_search .= $value["checklistID"];
        $counter++;
        
        if($counter < $activities_in_progress_count && $activities_in_progress_count > 1)
        {
            $checklist_ids_search .= ", ";
        }
    } 
    
    //Second Query (In Progress Sites) - Overnight Support Seperate Query
    $activities_in_progress_overnight_query = "SELECT overnightSupport.technician AS 'overnightSupport' FROM overnightSupport INNER JOIN checklist AS checklist ON checklist.checklistID = overnightSupport.checklistID INNER JOIN liveSiteEvents ON liveSiteEvents.checklistID = checklist.checklistID WHERE liveSiteEvents.checklistID IN($checklist_ids_search)";
    $activities_in_progress_overnight_statement = $db->prepare($activities_in_progress_overnight_query);
    $activities_in_progress_overnight_statement->execute();

    $activities_in_progress_overnight_result = $activities_in_progress_overnight_statement->fetchAll(PDO::FETCH_ASSOC);
    $activities_in_progress_overnight_count = count($activities_in_progress_overnight_result);

    //Third Query (Upcoming Sites)
    $upcoming_activities_query = "SELECT technician FROM upcomingSiteEvents";
    $upcoming_activities_statement = $db->prepare($upcoming_activities_query);
    $upcoming_activities_statement->execute();

    $upcoming_activities_statement_result = $upcoming_activities_statement->fetchAll(PDO::FETCH_ASSOC);
    $upcoming_activities_statement_count = count($upcoming_activities_statement_result);

    //Other needed data - Optional Categories
    $other_data_query1 = "SELECT optionalCategoryName FROM checklistOptionalCategories";
    $other_data_statement1 = $db->prepare($other_data_query1);
    $other_data_statement1->execute();

    $other_data_statement_result1 = $other_data_statement1->fetchAll(PDO::FETCH_NUM);

    //Other needed data - Categories
    $other_data_query2 = "SELECT categoryName FROM checklistCategories WHERE categories_identifier != 'Contact'";
    $other_data_statement2 = $db->prepare($other_data_query2);
    $other_data_statement2->execute();

    $other_data_statement_result2 = $other_data_statement2->fetchAll(PDO::FETCH_NUM);

    //Other needed data - Optional Tabs
    $other_data_query3 = "SELECT optionalTabName FROM checklistOptionalTabs";
    $other_data_statement3 = $db->prepare($other_data_query3);
    $other_data_statement3->execute();

    $other_data_statement_result3 = $other_data_statement3->fetchAll(PDO::FETCH_NUM);

    //Other needed data - Tabs
    $other_data_query4 = "SELECT tabName FROM checklistTabs WHERE tabs_identifier != 'Contact Tab' AND tabs_identifier != 'Overview Tab'";
    $other_data_statement4 = $db->prepare($other_data_query4);
    $other_data_statement4->execute();

    $other_data_statement_result4 = $other_data_statement4->fetchAll(PDO::FETCH_NUM);
} catch (Exception $ex) {
    echo $exception->getMessage();
    exit();
}
//echo "Complete ";
//print_r($activities_complete_result);
//print_r($activities_complete_overnight_result);
//
//echo "In progress ";
//print_r($activities_in_progress_result);
//print_r($activities_in_progress_overnight_result);

$main_looper_in_progress;
if ($activities_in_progress_count === 0) {
    $main_looper_in_progress = $activities_in_progress_overnight_count;
} else {
    $main_looper_in_progress = $activities_in_progress_count;
}

//echo $main_looper_in_progress;
//print_R($activities_in_progress_result);
//print_R($activities_in_progress_overnight_result); exit();

if ($activities_in_progress_result !== 0) {
    foreach ($activities_count_array as $key => $value) {
        for ($i = 0; $i < $activities_in_progress_count; $i++) {
            if ($key === $activities_in_progress_result[$i]["technician"]) {
                $activities_count_array[$key]["activities_in_progress"] = $activities_count_array[$key]["activities_in_progress"] + 1; 
            }
        }
    }
}

if($activities_in_progress_overnight_count !== 0)
{
    foreach ($activities_count_array as $key => $value) {
        for ($i = 0; $i < $activities_in_progress_overnight_count; $i++) {
            if ($key === $activities_in_progress_overnight_result[$i]["overnightSupport"]) {
                $activities_count_array[$key]["activities_in_progress"] = $activities_count_array[$key]["activities_in_progress"] + 1; 
            }
        }
    }
}

//foreach ($activities_count_array as $key => $value) {
//    for ($i = 0; $i < $main_looper_in_progress; $i++) {
//        if (isset($activities_in_progress_result[$i]["technician"])) {
//            if ($key === $activities_in_progress_result[$i]["technician"]) {
//                $activities_count_array[$key]["activities_in_progress"] = $activities_count_array[$key]["activities_in_progress"] + 1; 
//            } else {
//                for ($j = 0; $j < $activities_in_progress_overnight_count; $j++) {
//                    if ($key === $activities_in_progress_overnight_result[$j]["overnightSupport"]) {
//                        $activities_count_array[$key]["activities_in_progress"] = $activities_count_array[$key]["activities_in_progress"] + 1;
//                    }
//                }
//            }
//        } else {
//            for ($j = 0; $j < $activities_in_progress_overnight_count; $j++) {
//                if ($key === $activities_in_progress_overnight_result[$j]["overnightSupport"]) {
//                    $activities_count_array[$key]["activities_in_progress"] = $activities_count_array[$key]["activities_in_progress"] + 1;
//                }
//            }
//        }
//    }
//}

$main_looper_complete;
if ($activities_complete_count === 0) {
    $main_looper_complete = $activities_complete_overnight_count;
} else {
    $main_looper_complete = $activities_complete_count;
}

foreach ($activities_count_array as $key => $value) {
    for ($i = 0; $i < $main_looper_complete; $i++) {
        if (isset($activities_complete_result[$i]["technician"])) {
            if ($key === $activities_complete_result[$i]["technician"]) {
                $activities_count_array[$key]["activities_complete"] = $activities_count_array[$key]["activities_complete"] + 1;
            } else {
                for ($j = 0; $j < $activities_complete_overnight_count; $j++) {
                    if ($key === $activities_complete_overnight_result[$j]["overnightSupport"]) {
                        $activities_count_array[$key]["activities_complete"] = $activities_count_array[$key]["activities_complete"] + 1;
                    }
                }
            }
        } else {
            for ($j = 0; $j < $activities_complete_overnight_count; $j++) {
                if ($key === $activities_complete_overnight_result[$j]["overnightSupport"]) {
                    $activities_count_array[$key]["activities_complete"] = $activities_count_array[$key]["activities_complete"] + 1;
                }
            }
        }
    }
}

foreach ($activities_count_array as $key => $value) {
    for ($i = 0; $i < $upcoming_activities_statement_count; $i++) {
        if ($key === $upcoming_activities_statement_result[$i]["technician"]) {
            $activities_count_array[$key]["upcoming_activities"] = $activities_count_array[$key]["upcoming_activities"] + 1;
        }
    }
}
//print_r($activities_count_array);
//exit();
echo json_encode(array_merge(array($activities_count_array), array($other_data_statement_result1), array($other_data_statement_result2), array($other_data_statement_result3), array($other_data_statement_result4)));
