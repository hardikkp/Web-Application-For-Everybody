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

if( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage']) ){
    if( strlen($_POST['make']) >= 1 && strlen($_POST['model']) >= 1 && strlen($_POST['year']) >= 1 && strlen($_POST['mileage']) >= 1){
        if( is_numeric($_POST['year']) ){
            if( is_numeric($_POST['mileage']) ){
                
                $sqlq = 'INSERT INTO autos (make,model,year,mileage) VALUES (:x,:y,:z,:w)';
                $stmt = $pdo -> prepare($sqlq);
                $stmt -> execute(array(
                ':x' => $_POST['make'],
                ':y' => $_POST['model'],
                ':z' => $_POST['year'],
                ':w' => $_POST['mileage']
                ));    
                $_SESSION['success'] = 'Record added';
                header('Location: index.php'); 
                return;
            }else{
                $_SESSION['error'] = 'Mileage must be an integer';
                header('Location: add.php');
                return;
            }
        }else{
            $_SESSION['error'] = 'Year must be an integer';
            header('Location: add.php');
            return;
        }
    }else{
        $_SESSION['error'] = 'All fields are required';
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
    if( isset($_SESSION['error']) ){
        echo('<p style="color: red">'. htmlentities($_SESSION['error']) ."</p>");
        unset($_SESSION['error']); 
    }
    
    ?>

    <form method="post">
    <p>Make: <input type="text" name="make" size="42"/></p>
    <p>Model: <input type="text" name="model" size="42"/></p>
    <p>Year: <input type="text" name="year"/></p>
    <p>Mileage: <input type="text" name="mileage"/></p>
    <input type="submit" value="Add">
    <input type="submit" name="cancel" value="Cancel">
    </form>

</div> 
</body>
</html>