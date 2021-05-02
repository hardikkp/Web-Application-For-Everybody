<?php
require_once 'pdo.php';
session_start();

if ( ! isset($_SESSION['email']) || strlen($_SESSION['email']) < 1  ) {
    die('Name parameter missing');
}

// If the user requested cancel go back to add.php
if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
    return;
}

if( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) ){
    if( strlen($_POST['make']) >= 1  ){
        if(is_numeric($_POST['year']) && is_numeric($_POST['mileage'])){
            $_SESSION['added'] = 'Record inserted';
            $sqlq = 'INSERT INTO autos (make,year,mileage) VALUES (:x,:y,:z)';
            $stmt = $pdo -> prepare($sqlq);
            $stmt -> execute(array(
            ':x' => $_POST['make'],
            ':y' => $_POST['year'],
            ':z' => $_POST['mileage']
            ));    
            header('Location: view.php');
            return;
        }else{
            $_SESSION['num'] = 'Mileage and year must be numeric';
            header('Location: add.php');
            return;
        }
    }else{
        $_SESSION['num'] = 'Make is required';
        header('Location: add.php');
        return;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<title>hardik patel Automobile Tracker</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
    <div class="container">
    <h1>Tracking Autos for <?php echo($_SESSION['email']); ?></h1>
    <?php
    if( isset($_SESSION['num']) ){
        echo('<p style="color: red">'. htmlentities($_SESSION['num']) ."</p>");
        unset($_SESSION['num']); 
    }
    
    ?>

    <form method="post">
    <p>Make: <input type="text" name="make" size="60"/></p>
    <p>Year: <input type="text" name="year"/></p>
    <p>Mileage: <input type="text" name="mileage"/></p>
    <input type="submit" value="Add">
    <input type="submit" name="cancel" value="Cancel">
    </form>

</div> 
</body>
</html>