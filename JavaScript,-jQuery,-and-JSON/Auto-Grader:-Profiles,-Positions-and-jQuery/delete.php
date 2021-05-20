<?php
require_once 'pdo.php';
require_once "bootstrap.php";
require_once "utils.php";
session_start();
accessDenied();
canCel();

if( isset($_POST['delete']) && isset($_POST['profile_id'])){
    $sqlq = 'DELETE FROM profile WHERE profile_id = :zip AND user_id = :uid ';
    $stmt = $pdo -> prepare($sqlq);
    $stmt -> execute(array(
        ':uid' => $_SESSION['user_id'],
        ':zip' => $_POST['profile_id']
    ));
    $_SESSION['success'] = 'Record deleted';
    header('Location: index.php'); 
    return;
}


// Person changes in url id for that play safe
if( ! isset($_GET['profile_id']) ){
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
}

$sqlq = 'SELECT first_name, last_name,profile_id FROM profile WHERE profile_id = :xyz AND user_id = :uid';
$stmt = $pdo -> prepare($sqlq);
$stmt -> execute(array(
    ':uid' => $_SESSION['user_id'],
    ':xyz' => $_GET['profile_id']));
$row = $stmt -> fetch(PDO::FETCH_ASSOC);
if( $row === false){
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>hardik patel's Profile Delete</title>

</head>
<body>
<div class="container">
<h1>Deleteing Profile</h1>
    <form method="post">
    <p>First Name: <?= htmlentities($row['first_name']) ?> </p>
    <p>Last Name: <?= htmlentities($row['last_name']) ?> </p>
    <input type="hidden" name="profile_id" value = '<?= $row['profile_id']?> '/>
    <input type="submit" name="delete" value="Delete">
    <input type="submit" name="cancel" value="Cancel">
    </p>
    </form>
</div>
</body>
</html>