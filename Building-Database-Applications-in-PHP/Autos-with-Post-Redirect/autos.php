<?php
require_once 'pdo.php';

$added = false;
$num = false;
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

if( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) ){
    if( strlen($_POST['make']) >= 1  ){
        if(is_numeric($_POST['year']) && is_numeric($_POST['mileage'])){
            $added = 'Record inserted';
            $sqlq = 'INSERT INTO autos (make,year,mileage) VALUES (:x,:y,:z)';
            $stmt = $pdo -> prepare($sqlq);
            $stmt -> execute(array(
            ':x' => $_POST['make'],
            ':y' => $_POST['year'],
            ':z' => $_POST['mileage']
            ));    
        }else{
            $num = 'Mileage and year must be numeric';
        }
    }else{
        $num = 'Make is required';
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
    <h1>Tracking Autos for <?php echo($_GET['name']); ?></h1>
    <?php
    if($num !== false){
        echo('<p style="color: red;">'.htmlentities($num)."</p>\n");
    }
    if ( $added !== false ) {
        echo('<p style="color: green;">'.htmlentities($added)."</p>\n");
    }
    ?>
    <form method="post">
    <p>Make: <input type="text" name="make" size="60"/></p>
    <p>Year: <input type="text" name="year"/></p>
    <p>Mileage: <input type="text" name="mileage"/></p>
    <input type="submit" value="Add">
    <input type="submit" name="logout" value="Logout">
    </form>

    <h2>Automobiles</h2>
    <?php
    echo('<ul>');
    $stmt = $pdo->query("SELECT * FROM autos");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        echo('<li>');
        echo($row['year'] . ' '.'&lt;b&gt;' . $row['make'] . '&lt;/b&gt;'. ' / ' . $row['mileage']);
        echo('</li>');
        }   
    echo('</ul>');
    ?>
    </div> 
</body>
</html>
