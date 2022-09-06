<?php

// header("Content-Type","application/json");
// header('Content-type: application/json; charset=UTF-8');
 
include_once "../bin/connect/DatabaseCanais.php";

$DB = new DatabaseCanais();

echo json_encode($DB->canais());


?>