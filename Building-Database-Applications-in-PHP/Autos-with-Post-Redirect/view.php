<?php
require_once 'pdo.php';
session_start();
if ( ! isset($_SESSION['email']) || strlen($_SESSION['email']) < 1  ) {
    die('Not logged in');
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
    <h1>Tracking Autos for <?php  echo($_SESSION['email']) ?></h1>
    <?php
    if ( isset($_SESSION['added']) ) {
        echo('<p style="color: green">'. htmlentities($_SESSION['added']) ."</p>");
        unset($_SESSION['added']);
    }
    ?>
    <h2>Automobiles</h2>
    <?php
    echo('<ul>');
    $stmt = $pdo->query("SELECT * FROM autos");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        echo('<li>');
        echo($row['year'] . ' ' . $row['make'] . ' / ' . $row['mileage']);
        echo('</li>');
        }   
    echo('</ul>');
    ?>
    <p>
    <a href="add.php">Add New</a> |
    <a href="logout.php">Logout</a>
    </p>
    </div> 
</body>
</html>
