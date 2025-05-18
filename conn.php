<?php

$host = "localhost";
$name = "root";
$password = "";
$db = "college_tutor2";

try {
    $conn = mysqli_connect($host, $name, $password, $db);
}
catch(\Exception $ex) {
    die("Connect Error: ".$ex->getMessage());
}


?>