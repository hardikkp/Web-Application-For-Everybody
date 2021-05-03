<?php
require_once 'pdo.php';
session_start();

?>
<!DOCTYPE html>
<html>
<head>
<title>hardik patel - Autos Database</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h2>Welcome to the Automobiles Database</h2>
<?php
if ( ! isset($_SESSION['email']) || strlen($_SESSION['email']) < 1  ) {
    echo('<p><a href="login.php">Please log in</a></p>');
    echo('<p>Attempt to <a href="add.php">add data</a> without logging in</p>');
}
?>

<?php
if ( isset($_SESSION['email']) ){
    if ( isset($_SESSION['success']) ) {
        echo('<p style="color: green">'. htmlentities($_SESSION['success']) ."</p>");
        unset($_SESSION['success']);
    }
    echo('<table border="1"> <thead><tr>
    <th>Make</th>
    <th>Model</th>
    <th>Year</th>
    <th>Mileage</th>
    <th>Action</th>
    </tr></thead>');
    $stmt = $pdo->query("SELECT * FROM autos");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        echo('<tr><td>');
        echo(htmlentities($row['make']));
        echo('</td><td>');
        echo(htmlentities($row['model']));
        echo('</td><td>');
        echo(htmlentities($row['year']));
        echo('</td><td>');
        echo(htmlentities($row['mileage']));
        echo('</td><td>');
        echo('<a href="edit.php?autos_id='.$row['autos_id'].'"> Edit </a> / ');
        echo('<a href="delete.php?autos_id='.$row['autos_id'].'"> Delete </a>');
        echo('</td></tr>');
    }
    echo('</table>');
    echo('<p><a href="add.php">Add New Entry</a></p>');
    echo('<p><a href="logout.php">Logout</a></p>');
}
?>

</div>
</body>
</html>