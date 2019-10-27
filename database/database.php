<?php

$dsn = "mysql:host=mysql-d00192082.alwaysdata.net;dbname=d00192082_sitetechwebsite";
$username = "___________";
$password = "___________";
//$siteName = "http://localhost/SiteTechWebsite";
$siteName = "___________";
$ftp_host = '___________'; /* host */
$ftp_user_name = '___________'; /* username */
$ftp_user_pass = '___________'; /* password */

try {
    $db = new PDO($dsn, $username, $password);
    //set up error reporting on server
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_reporting(E_ALL);
} catch (PDOException $exception) {
    //Error message
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}
