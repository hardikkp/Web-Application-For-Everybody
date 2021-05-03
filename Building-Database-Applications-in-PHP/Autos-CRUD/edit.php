<?php
require_once 'pdo.php';
session_start();

if ( ! isset($_SESSION['email']) || strlen($_SESSION['email']) < 1  ) {
    die('ACCESS DENIED');
}

// If the user requested cancel go back to add.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage']) && isset($_POST['autos_id']) ){
    if( strlen($_POST['make']) >= 1 && strlen($_POST['model']) >= 1 && strlen($_POST['year']) >= 1 && strlen($_POST['mileage']) >= 1){
        if( is_numeric($_POST['year']) ){
            if( is_numeric($_POST['mileage']) ){
                
                $sqlq = 'UPDATE autos SET make = :make , model = :model , year = :year ,
                 mileage = :mileage WHERE autos_id = :autos_id';
                $stmt = $pdo -> prepare($sqlq);
                $stmt -> execute(array(
                ':make' => $_POST['make'],
                ':model' => $_POST['model'],
                ':year' => $_POST['year'],
                ':mileage' => $_POST['mileage'],
                ':autos_id' => $_POST['autos_id']
                ));    
                $_SESSION['success'] = 'Record edited';
                header('Location: index.php'); 
                return;
                
            }else{
                $_SESSION['error'] = 'Mileage must be an integer';
                header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
                return;
            }
        }else{
            $_SESSION['error'] = 'Year must be an integer';
            header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
            return;
        }
    }else{
        $_SESSION['error'] = 'All fields are required';
        header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
        return;
    }
}


// Person changes in url id for that play safe
if( ! isset($_GET['autos_id']) ){
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
}

$sqlq = 'SELECT * FROM autos WHERE autos_id = :xyz';
$stmt = $pdo -> prepare($sqlq);
$stmt -> execute(array(
    ':xyz' => $_GET['autos_id']));
$row = $stmt -> fetch(PDO::FETCH_ASSOC);
if( $row === false){
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
}

$ma = htmlentities($row['make']);
$mo = htmlentities($row['model']);
$y = htmlentities($row['year']);
$mi = htmlentities($row['mileage']);
$autos_id = $row['autos_id']; 
?>
<html>
<head>
<title>hardik patel Automobile Tracker</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Editing Automobile</h1>
<?php
if( isset($_SESSION['error']) ){
    echo('<p style="color:red">' .  $_SESSION['error'] . '</p>');
    unset($_SESSION['error']);
}
?>
<form method="post">
<p>Make<input type="text" name="make" size="40" value="<?= $ma ?>"/></p>
<p>Model<input type="text" name="model" size="40" value="<?= $mo ?>"/></p>
<p>Year<input type="text" name="year" size="10" value="<?= $y ?>"/></p>
<p>Mileage<input type="text" name="mileage" size="10" value="<?= $mi ?>"/></p>
<input type="hidden" name="autos_id" value="<?= $autos_id ?>">
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
</div>
</body>
</html>
