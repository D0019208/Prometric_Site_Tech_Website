<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$technician = $directory = ltrim(rtrim(filter_input(INPUT_POST, "technician", FILTER_SANITIZE_STRING)));
$document_id = ltrim(rtrim(filter_input(INPUT_POST, "document_id", FILTER_SANITIZE_NUMBER_INT)));
$directory = ltrim(rtrim(filter_input(INPUT_POST, "current_directory", FILTER_SANITIZE_STRING)));
$old_document = ltrim(rtrim(filter_input(INPUT_POST, "current_file_name", FILTER_SANITIZE_STRING)));

$connection = ftp_connect($ftp_host);

/* Login to FTP */
$login_result = ftp_login($connection, $ftp_user_name, $ftp_user_pass);
if ($login_result) {
    ftp_pasv($connection, true);
    ftp_delete($connection, "SiteTechWebsiteServer/documents/" . $directory . "/" . $old_document);
}
/* Close the connection */
ftp_close($connection);

try {
    $query_delete_document = "DELETE FROM documents WHERE documentID = :documentID";
    $statement_delete_document = $db->prepare($query_delete_document);
    $statement_delete_document->bindParam(":documentID", $document_id, PDO::PARAM_INT);
    $statement_delete_document->execute();

    $query_update_tech = "UPDATE technician SET documentsDeleted = documentsDeleted + 1 WHERE technicianFullName = :technician";
    $statement_update_tech = $db->prepare($query_update_tech);
    $statement_update_tech->bindParam(":technician", $technician, PDO::PARAM_STR);
    $statement_update_tech->execute();

    $date = date("Y-m-d");
    $time = date("H:i:s");

    if ($statement_update_tech == true) {
        $event = $technician . " has deleted the " . str_replace('.pdf', '', $old_document) . " document.";  
        $query = "INSERT INTO documentEvents (event, technician, date, time, image, link, allDayEvent, event_type) VALUES ('" . $event . "', '" . $technician . "', '" . $date . "', '" . $time . "', 'images/SiteIcons/DocumentIcons/DocumentEmergency.png', '#', 0, 'documents')";
        $statement = $db->prepare($query);
        $statement->execute();

        $query_deleted_count = "SELECT documentsDeleted FROM technician WHERE technicianFullName = :technician";
        $statement_deleted_count = $db->prepare($query_deleted_count);
        $statement_deleted_count->bindParam(":technician", $technician, PDO::PARAM_STR);
        $statement_deleted_count->execute();

        $result_deleted_count = $statement_deleted_count->fetchAll(PDO::FETCH_ASSOC);

        if ($result_deleted_count[0]["documentsDeleted"] % 10 == 0) {
            $event = $technician . " has deleted " . $result_deleted_count[0]["documentsDeleted"] . " documents! Congratulations " . explode(' ', $technician)[0] . "!"; 

            $query = "INSERT INTO documentEvents (event, technician, date, time, image, link, allDayEvent, event_type) VALUES ('" . $event . "', '" . $technician . "', '" . $date . "', '" . $time . "', 'images/SiteIcons/ModalIcons/ActivityType.png', '#', 0, 'documents')";
            $statement = $db->prepare($query);
            $statement->execute();
        }
    }
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}