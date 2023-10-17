<?php

require("config.php");

$id = $_GET['id'];
$data = $_GET['data'];

$dataaggiunta = $data . ' 13:00:00';

$result = mysqli_query($mysqli, "UPDATE lead_notes SET date_time='$dataaggiunta' WHERE id=$id");

?>
