<?php

if( ! isset($_GET['term']) ){
    die('Missing require parameters');
}

//Let's not start session unless we already have one
if( ! isset($_COOKIE[session_name()]) ){
    die('Must be logged in..');
}
session_start();

if( ! isset($_SESSION['user_id']) ){
    die('Access Denied');
}

//Don't even make database connection untill we are happy
require_once 'pdo.php';
$term = $_GET['term'];
error_log('Looking up typeahead term'.$term);

$stmt = $pdo -> prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
$stmt -> execute(array(
    ':prefix' => $_REQUEST['term']."%"
));

$retval = array();
while( $row = $stmt -> fetch(PDO::FETCH_ASSOC) ){
    $retval [] = $row['name'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));
