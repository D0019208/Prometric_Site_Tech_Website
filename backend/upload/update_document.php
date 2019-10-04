<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$technician = $directory = ltrim(rtrim(filter_input(INPUT_POST, "technician", FILTER_SANITIZE_STRING)));
$document_id = ltrim(rtrim(filter_input(INPUT_POST, "document_id", FILTER_SANITIZE_NUMBER_INT)));
$directory = ltrim(rtrim(filter_input(INPUT_POST, "current_directory", FILTER_SANITIZE_STRING)));
$old_document = ltrim(rtrim(filter_input(INPUT_POST, "current_file_name", FILTER_SANITIZE_STRING)));

$pdf = $_FILES['pdf']['tmp_name'];
$pdfName = str_replace('.pdf', '', $_FILES["pdf"]["name"]); 

if (!empty($pdf)) {
    $filepath = "../../images/temp/" . $_FILES["pdf"]["name"];
    $remote_file = "SiteTechWebsiteServer/documents/" . $directory . "/" . $_FILES["pdf"]["name"];
    move_uploaded_file($pdf, $filepath);
    //^^^^^^^^^^^^^^^^^^^^SANITIZE THIS^^^^^^^^^^^^^^^^^^^^    

    $connection = ftp_connect($ftp_host);

    /* Login to FTP */
    $login_result = ftp_login($connection, $ftp_user_name, $ftp_user_pass);
    if ($login_result) {
        ftp_pasv($connection, true);
        /* Send $local_file to FTP */
        ftp_delete($connection, "SiteTechWebsiteServer/documents/" . $directory . "/" . $old_document);
        ftp_put($connection, $remote_file, $filepath, FTP_BINARY);
        unlink($filepath);
    }
    /* Close the connection */
    ftp_close($connection);

    try {
        $update_technician_query = "UPDATE technician SET documentsUpdated = documentsUpdated + 1 WHERE technicianFullName = :technician";
        $update_technician_statement = $db->prepare($update_technician_query);
        $update_technician_statement->bindParam(":technician", $technician, PDO::PARAM_STR);
        $update_technician_statement->execute();

        $update_document_query = "UPDATE documents INNER JOIN technician ON technician.technicianFullName = :technicianFullName SET modifiedBy = technician.technicianID, documentName = :document_name, documentURL = 'd00192082.alwaysdata.net/SiteTechWebsiteServer/documents/" . $directory . "/" . $_FILES["pdf"]["name"] . "' WHERE documentID = :documentID";
        $update_document_statement = $db->prepare($update_document_query);
        $update_document_statement->bindParam(":document_name", $pdfName, PDO::PARAM_STR);
        $update_document_statement->bindParam(":technicianFullName", $technician, PDO::PARAM_STR);
        $update_document_statement->bindParam(":documentID", $document_id, PDO::PARAM_INT);

        $update_document_statement->execute();

        if ($update_document_statement == true) {
            $old_document = str_replace('.pdf', '', $old_document);
            
            if($pdfName !== $old_document)
            {
                $event = $technician . " has updated the " . $old_document . " document. It is now called " . $pdfName;
            }
            else
            {
                $event = $technician . " has updated the " . $old_document . " document.";
            }
            
            $date = date("Y-m-d");
            $time = date("H:i:s");

            $query = "INSERT INTO documentEvents (event, technician, date, time, image, link, allDayEvent, event_type) VALUES ('" . $event . "', '" . $technician . "', '" . $date . "', '" . $time . "', 'images/SiteIcons/DocumentIcons/DocumentUpdated.png', 'https://" . $siteName . $remote_file . "', 0, 'documents')";
            $statement = $db->prepare($query);
            $statement->execute(); 

            $query_updated_count = "SELECT documentsUpdated FROM technician WHERE technicianFullName = :technician";
            $statement_updated_count = $db->prepare($query_updated_count);
            $statement_updated_count->bindParam(":technician", $technician, PDO::PARAM_STR);
            $statement_updated_count->execute();

            $result_updated_count = $statement_updated_count->fetchAll(PDO::FETCH_ASSOC);

            if ($result_updated_count[0]["documentsUpdated"] % 10 == 0) {
                $event = $technician . " has updated " . $result_updated_count[0]["documentsDeleted"] . " documents! Congratulations " . explode(' ', $technician)[0] . "!";
                $date = date("Y-m-d");
                $time = date("H:i:s");

                $query = "INSERT INTO documentEvents (event, technician, date, time, image, link, allDayEvent, event_type) VALUES ('" . $event . "', '" . $technician . "', '" . $date . "', '" . $time . "', 'images/SiteIcons/ModalIcons/ActivityType.png', '#', 0, 'other')";
                $statement = $db->prepare($query);
                $statement->execute();
            }
        }
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
}


