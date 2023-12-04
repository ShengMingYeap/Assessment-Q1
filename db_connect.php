<?php 

$db_hostname = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "photo_sharing";

$dblink = mysqli_connect($db_hostname, $db_username, $db_password, $db_name);
if(mysqli_connect_errno()){
    echo mysqli_connect_error();
    die();
}

mysqli_query($dblink, "SET NAMES 'utf8mb4'");
date_default_timezone_set("Asia/Kuala_Lumpur");

?>