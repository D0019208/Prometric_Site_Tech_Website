<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$json = json_decode($_POST["request"]);
$table_name = $json->table_name;
$where_clause = $json->where_clause;

$selected = json_decode(json_encode($json->selected));

$where_in_checklist_id = "";
$delete_count = count($selected);

for ($i = 0; $i < $delete_count; $i++) {
    $where_in_checklist_id .= "'" . $selected[$i] . "'";

    if ($delete_count > 1 && $i < $delete_count - 1) {
        $where_in_checklist_id .= ", ";
    }
}

try {
    $site_code_query = "SELECT siteCode FROM checklist WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $site_code_statement = $db->prepare($site_code_query);
    $site_code_statement->execute();

    $site_code_results = $site_code_statement->fetchAll(PDO::FETCH_ASSOC);

    $where_in_site_code = "";
    $site_code_count = count($site_code_results);

    for ($i = 0; $i < $site_code_count; $i++) {
        $where_in_site_code .= "'" . $site_code_results[$i]["siteCode"] . "'";

        if ($site_code_count > 1 && $i < $site_code_count - 1) {
            $where_in_site_code .= ", ";
        }
    }

    $site_progress_query = "SELECT status FROM checklist WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $site_progress_statement = $db->prepare($site_progress_query);
    $site_progress_statement->execute();

    $site_progress_results = $site_progress_statement->fetchAll(PDO::FETCH_ASSOC);

    $site_technician_query = "SELECT DISTINCT technician AS 'technician' FROM checklist_technician WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $site_technician_statement = $db->prepare($site_technician_query);
    $site_technician_statement->execute();

    $site_technician_results = $site_technician_statement->fetchAll(PDO::FETCH_ASSOC);

    $overnight_technician_query = "SELECT DISTINCT technician AS 'technician' FROM overnightSupport WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $overnight_technician_statement = $db->prepare($overnight_technician_query);
    $overnight_technician_statement->execute();

    $overnight_technician_results = $overnight_technician_statement->fetchAll(PDO::FETCH_ASSOC);

    $technicians_to_update = implode('\', \'', array_column(array_merge($site_technician_results, $overnight_technician_results), 'technician'));

    delete_checklist_data($db, $where_in_site_code, $where_in_checklist_id);

    $technicians_to_update = "'" . $technicians_to_update . "'";

    $status_count = count($site_progress_results);
    for ($i = 0; $i < $status_count; $i++) {
        if ($site_progress_results[$i]["status"] !== 3) {
            $update_technician_query = "UPDATE technician SET activitiesInProgress = (activitiesInProgress - 1) WHERE technicianFullName IN (" . $technicians_to_update . ")";
            $update_technician_statement = $db->prepare($update_technician_query);
            $update_technician_statement->execute();
        } else {
            $update_technician_query = "UPDATE technician SET activitiesComplete = (activitiesComplete - 1) WHERE technicianFullName IN (" . $technicians_to_update . ")";
            $update_technician_statement = $db->prepare($update_technician_query);
            $update_technician_statement->execute();
        }
    }
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}