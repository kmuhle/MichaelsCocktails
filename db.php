<?php

// set to my database
$host = 'localhost';
$username = 'umxymifo0rhvg';
$password = 'et65ksexu9ll';
$dbname = 'db2nwwdbgaa6va';

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_errno) {
    die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;