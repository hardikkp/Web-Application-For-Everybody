<?php
require_once 'pdo.php';
session_start();

if ( ! isset($_SESSION['email']) || strlen($_SESSION['email']) < 1  ) {
    die('ACCESS DENIED');
}

if( isset($_POST['delete']) && isset($_POST['autos_id'])){
    $sqlq = 'DELETE FROM autos WHERE autos_id = :zip';
    $stmt = $pdo -> prepare($sqlq);
    $stmt -> execute(array(
        ':zip' => $_POST['autos_id']
    ));
    $_SESSION['success'] = 'Record deleted';
    header('Location: index.php'); //////////////////
    return;
}


// Person changes in url id for that play safe
if( ! isset($_GET['autos_id']) ){
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
}

$sqlq = 'SELECT make,autos_id FROM autos WHERE autos_id = :xyz';
$stmt = $pdo -> prepare($sqlq);
$stmt -> execute(array(
    ':xyz' => $_GET['autos_id']));
$row = $stmt -> fetch(PDO::FETCH_ASSOC);
if( $row === false){
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
}

?>
<p> Confirm: Deleting <?= htmlentities($row['make']) ?> </p>
<form method = 'post'>
<input type='hidden' name='autos_id' value = '<?= $row['autos_id']?>'>
<input type = 'submit' value = 'Delete' name='delete'>
<a href='index.php'> Cancel </a>
</form>

