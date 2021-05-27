<?php
require_once 'pdo.php';
require_once "utils.php";
session_start();
accessDenied();
canCel();

if( ! isset($_GET['profile_id']) ){
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
}

$sqlq = 'SELECT * FROM profile WHERE profile_id = :xyz and user_id = :uid';
$stmt = $pdo -> prepare($sqlq);
$stmt -> execute(array(
    ':uid' => $_SESSION['user_id'],
    ':xyz' => $_GET['profile_id']));
$row = $stmt -> fetch(PDO::FETCH_ASSOC);
if( $row == false){
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $_REQUEST['profile_id']; 

$positions = loadPos($pdo, $_REQUEST['profile_id']);
$educations = loadEdu($pdo, $_REQUEST['profile_id']);
?>

<!DOCTYPE html>
<html>
<head>
<title>hardik patel's Profile View</title>
<?php require_once 'head.php' ?>
</head>
<body>
<div class="container">
<h1>Profile information</h1>
<p>First Name: <?= $fn ?></p>
<p>Last Name: <?= $ln ?></p>
<p>Email: <?= $em ?></p>
<p>Headline:<br/> <?= $he ?></p>
<p>Summary:<br/><?= $su ?></p>

<?php
    if( !$educations == false ){
        echo('<p> Education </p> <ul>');

        foreach($educations as $education){
            echo '<li>';
            echo $education['year'].': '.$education['name'];
            echo '</li>';
        }
        echo '</ul>';
    }

    if( !$positions == false ){
        echo('<p> Position </p> <ul>');

        foreach($positions as $position){
            echo '<li>';
            echo $position['year'].': '.$position['description'];
            echo '</li>';
        }
        echo '</ul>';
    }

    

?>

<p>
<a href="index.php">Done</a>
</p>
</div>