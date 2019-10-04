<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$json = json_decode($_POST["request"]);
$table_name = $json->data->table_name;
$select_data = $json->data->select_data;
$changes = json_decode(json_encode($json->changes), true);

if (isset($json->access_code)) {
    $access_code = $json->access_code;
} else {
    $access_code = $json->data->access_code;
}

$updateFields = "";
$changes_count = count($changes);
$select_data_count = count($select_data);

$keys_array = array();
foreach ($changes[0] as $key => $value) {
    if ($key !== "recid") {
        array_push($keys_array, $key);
    }
}

$key_count = count($keys_array);
$counter = 0;



for ($i = 0; $i < $changes_count; $i++) {
    $inside_changes_count = count($changes[$i]) - 1;

    for ($j = 0; $j < $inside_changes_count; $j++) {
        $updateFields .= $keys_array[$counter] . " = CASE WHEN " . $select_data[0] . " = " . $changes[$i]["recid"] . " THEN '" . $changes[$i][$keys_array[$counter]] . "' ELSE " . $keys_array[$counter] . " END";

        if ($j < $inside_changes_count - 1 || $changes_count > 1 && $i < $changes_count - 1) {
            $updateFields .= ", ";
        }

        if ($counter === $key_count - 1) {
            $counter = 0;
        } else {
            $counter++;
        }
    }
}

try {
    $query = "UPDATE " . $table_name . " SET " . $updateFields;
    $statement = $db->prepare($query);
    $result = $statement->execute();

    if ($access_code === "admin_save_tech") {
        $original_technician = json_decode(json_encode($json->original_technician), true);
        $original_technician_count = count($original_technician);

        //print_r($original_technician);
        $special_query = "UPDATE checklist SET " . create_technician_replacement_query("technician", $changes_count, $changes, $original_technician_count, $original_technician);
//        echo $special_query;
//        exit();
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

        $special_query = "UPDATE checklist SET " . create_technician_replacement_query("overNightSupport", $changes_count, $changes, $original_technician_count, $original_technician);
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

        $special_query = "UPDATE checklist_technician SET " . create_technician_replacement_query("technician", $changes_count, $changes, $original_technician_count, $original_technician);
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

        $special_query = "UPDATE documentEvents SET " . create_technician_replacement_query("technician", $changes_count, $changes, $original_technician_count, $original_technician);
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

        $special_query = "UPDATE liveSiteEvents SET " . create_technician_replacement_query("technician", $changes_count, $changes, $original_technician_count, $original_technician);
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

        $special_query = "UPDATE overnightSupport SET " . create_technician_replacement_query("technician", $changes_count, $changes, $original_technician_count, $original_technician);
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

        $special_query = "UPDATE site SET " . create_technician_replacement_query("siteTech", $changes_count, $changes, $original_technician_count, $original_technician);
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

        $special_query = "UPDATE siteContact SET " . create_technician_replacement_query("name", $changes_count, $changes, $original_technician_count, $original_technician);
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

        $special_query = "UPDATE siteNews SET " . create_technician_replacement_query("technician", $changes_count, $changes, $original_technician_count, $original_technician);
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

        $special_query = "UPDATE site_technician SET " . create_technician_replacement_query("technician", $changes_count, $changes, $original_technician_count, $original_technician);
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

        $special_query = "UPDATE upcomingSiteEvents SET " . create_technician_replacement_query("technician", $changes_count, $changes, $original_technician_count, $original_technician);
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

        echo $result;
    } else if ($access_code === "admin_save_categories") {
        $original_categories = json_decode(json_encode($json->original_categoy_names), true);
        
        $special_query = "UPDATE checklistTasks SET " . create_category_replacement_query($changes, $original_categories, "categoryName"); 
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();

//        //OPTIONAL
//        $special_query = "UPDATE checklist_checklistCategories SET " . create_category_replacement_query($changes, $original_categories, "categoryName"); 
//        $special_statement = $db->prepare($special_query);
//        $special_statement->execute();
//        
//        //OPTIONAL
//        $special_query = "UPDATE checklist_checklistTasks SET " . create_category_replacement_query($changes, $original_categories, "categoryName"); 
//        $special_statement = $db->prepare($special_query);
//        $special_statement->execute(); 
        echo $result;
    } else if ($access_code === "admin_save_optional_categories")
    {
        $original_categories = json_decode(json_encode($json->original_category_names), true);
        
        $special_query = "UPDATE checklistOptionalTasks SET " . create_category_replacement_query($changes, $original_categories, "optionalCategoryName"); 
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();
        
        $special_query = "UPDATE checklistOptionalTabs SET " . create_category_replacement_query($changes, $original_categories, "optionalCategoryName"); 
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();
        
        echo $result;
    }
    else if($access_code === "admin_save_optional_tasks_tabs")
    {
        $original_tasks = json_decode(json_encode($json->original_tasks), true);
        
        $special_query = "UPDATE checklistOptionalTabs SET " . create_category_replacement_query($changes, $original_tasks, "optionalTabName"); 
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();
        
        echo $result;
    }
    else if($access_code === "admin_save_tasks_tabs")
    {
        $original_tasks = json_decode(json_encode($json->original_tasks), true);
        
        $special_query = "UPDATE checklistTabs SET " . create_category_replacement_query($changes, $original_tasks, "tabName"); 
        $special_statement = $db->prepare($special_query);
        $special_statement->execute();
        
        echo $result;
    }
//    else if ($access_code === "admin_save_checklist") {
//        $original_technician = json_decode(json_encode($json->original_technician), true);
//        $original_technician_count = count($original_technician);
//        $update_string = "";
//
//
//        foreach ($changes as $value) {
//            $checklist_id = $value["recid"];
//            $ot = explode(',', $value["overNightSupport"]);
//            $overnigthTechs = array_map('trim', $ot);
//            $overnigthTechs_count = count($overnigthTechs);
//
////            print_r($overnigthTechs);
////            print_r($original_technician);
////            print_r($changes);
////            exit();
//
////            print_r($original_technician);exit();
//            for ($i = 0; $i < $overnigthTechs_count; $i++) {
//                $update_string .= "technician = CASE WHEN checklistID = " . $checklist_id . " AND technician = '" . $original_technician[$counter]["technician_name"][$i] . "' THEN '" . $overnigthTechs[$i] . "' ELSE technician END";
//            }
//
//            $overnight_count = count($overnigthTechs);
//            $original_technician_inside_count = count($original_technician[$counter]["technician_name"]);
//
//            $update_string = "";
//            $counter = 0;
//            $second_counter = 0;
//
//            for ($i = 0; $i < $overnight_count; $i++) {
//
//
//                if ($counter < $original_technician_count) {
//                    for ($j = 0; $j < $original_technician_inside_count; $j++) {
//                        $update_string .= "technician = CASE WHEN checklistID = " . $checklist_id . " AND technician = '" . $original_technician[$counter]["technician_name"][$j] . "' THEN '" . $overnigthTechs[$i] . "' ELSE technician END";
//
//                        if ($j < $original_technician_inside_count - 1 && $j > 1) {
//                            $update_string .= ", ";
//                        }
//                    }
//
//                    $counter++;
//                    $second_counter++;
//                }
//
//                if ($i < $overnight_count - 1 && $overnight_count > 1) {
//                    $update_string .= ", ";
//                }
//            }
//        }
//
//        $update_overnightSupport_query = "UPDATE overnightSupport SET " . $update_string;
//        echo $update_overnightSupport_query;
//        exit();
//        $update_overnightSupport_statement = $db->prepare($update_overnightSupport_query);
//        $update_overnightSupport_statement->execute();
//
//        echo $result;
//    } 
    else {
        echo $result;
    }
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}