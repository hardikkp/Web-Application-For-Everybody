<?php
require_once 'pdo.php';
require_once "bootstrap.php";
require_once "utils.php";
session_start();

?>

<!DOCTYPE html>
<html>
<head>
<title>hardik patel's Resume Registry</title>
</head>
<body>
<div class="container">
<h1>hardik patel's Resume Registry</h1>
<?php

if(! isset($_SESSION['user_id']) || strlen($_SESSION['name']) < 1){
    echo('<p><a href="login.php">Please log in</a></p>');

    $sqlq = 'SELECT first_name, last_name,headline,profile_id FROM profile';
    $stmt = $pdo -> query($sqlq);
    $i  = 0;
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if($row != false && $i<1){
            echo('<table border="1" > <tr><th>Name</th><th>Headline</th></tr>');
            $i++;
        }
    echo('<tr><td>');
    echo( htmlentities($row['first_name'] . ' ' .  $row['last_name']));
    echo('</td><td>');
    echo(htmlentities($row['headline']));
    echo('</td></tr>');
    }
    echo('</table>');

}else{
    
    flashMessages();
    echo('<p><a href="logout.php">Logout</a></p>');
    
    $sqlq = 'SELECT first_name, last_name,headline,profile_id FROM profile';
    $stmt = $pdo -> query($sqlq);
    $i  = 0;
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if($row != false && $i<1){
            echo('<table border="1" > <tr><th>Name</th><th>Headline</th><th>Action</th></tr>');
            $i++;
        }
    echo('<tr><td>');
    echo('<a href="view.php?profile_id='.$row['profile_id'].'">');
    echo( htmlentities($row['first_name'] . ' ' .  $row['last_name']));
    echo('</a></td><td>');
    echo(htmlentities($row['headline']));
    echo('</td><td>');
    echo('<a href="edit.php?profile_id='.$row['profile_id'].'"> Edit </a> / ');
    echo('<a href="delete.php?profile_id='.$row['profile_id'].'"> Delete </a>');
    echo('</td></tr>');
    }
    echo('</table>');
    echo('<p><a href="add.php">Add New Entry</a></p>');

}
?>
</div>
</body>
</html>