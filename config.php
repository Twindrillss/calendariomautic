<?php
//inserire qui le credenziali per il tuo database
 
 $databaseHost = '';
 $databaseName = '';
 $databaseUsername = '';
 $databasePassword = '';
 
$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName) or die('Errore durante la connessione al database'); 
?>
