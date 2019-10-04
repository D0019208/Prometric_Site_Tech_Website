<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$userName = ltrim(rtrim(filter_input(INPUT_POST, "userName", FILTER_SANITIZE_STRING)));

$image = $_FILES['image']['tmp_name'];
$imageName = $_FILES["image"]["name"];
$imageName = $userName . $imageName;

$imageType = substr($_FILES['image']["type"], strpos($_FILES['image']["type"], "/") + 1);

$imageName = $userName . "." . $imageType;
$imageName = str_replace(' ', '', $imageName);

$filepath = "";
//print_r($_FILES['image']); exit();

if (!empty($image)) {
    $filepath = "../../images/temp/$imageName";
    $remote_file = "../SiteTechWebsiteServer/avatar/$imageName";
    move_uploaded_file($image, $filepath);
    //^^^^^^^^^^^^^^^^^^^^SANITIZE THIS^^^^^^^^^^^^^^^^^^^^    

    $connection = ftp_connect($ftp_host);

    /* Login to FTP */
    $login_result = ftp_login($connection, $ftp_user_name, $ftp_user_pass);
    if ($login_result) {
        ftp_pasv($connection, true);
        /* Send $local_file to FTP */
        ftp_put($connection, $remote_file, $filepath, FTP_BINARY);
        unlink($filepath);
    }
    else
    {
        echo "Error connecting to FTP Client.";
        exit();
    }
    /* Close the connection */
    ftp_close($connection);
}

try {
    $imageName = "https://d00192082.alwaysdata.net/SiteTechWebsiteServer/avatar/" . $imageName;

    $updateAvatarQuery = "UPDATE technician SET avatar = :avatar WHERE technicianFullName = :name";
    $updateAvatarStatement = $db->prepare($updateAvatarQuery);
    $updateAvatarStatement->bindParam(":avatar", $imageName, PDO::PARAM_STR);
    $updateAvatarStatement->bindParam(":name", $userName, PDO::PARAM_STR);
    $updated = $updateAvatarStatement->execute();

    if ($updated) {
        echo $imageName;
    } else {
        echo "<b>Fatal error</b>:";
    }
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}