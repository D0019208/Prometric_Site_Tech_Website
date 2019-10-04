<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$json = json_decode($_POST["request"]);
$table_name = $json->data->table_name;
$select_data = $json->data->select_data;

if (isset($json->access_code)) {
    $access_code = $json->access_code;
} else {
    $access_code = $json->data->access_code;
}