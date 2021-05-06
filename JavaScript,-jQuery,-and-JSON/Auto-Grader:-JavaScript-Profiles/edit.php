<?php
require_once 'pdo.php';
session_start();

if ( !isset($_SESSION['user_id']) || !isset($_SESSION['name']) || strlen($_SESSION['user_id']) < 1 ){
    die('Not logged in');
}

// If the user requested cancel go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');  
    return;
}

if( isset($_POST['save'])  && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['profile_id']) ){
    if( strlen($_POST['first_name']) >= 1 && strlen($_POST['last_name']) >= 1 && strlen($_POST['email']) >= 1 && strlen($_POST['headline']) >= 1 ){
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){

            $sqlq = ' UPDATE profile SET 
            first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
            WHERE profile_id = :xyz AND user_id = :uid ';
            $stmt = $pdo -> prepare($sqlq);
            $stmt->execute(array(
                ':uid' => $_SESSION['user_id'],
                ':xyz' => $_POST['profile_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary'])
            );
            $_SESSION['success'] = 'Record edited';
            header('Location: index.php');  
            return;

        }
        else{
            $_SESSION['error'] = 'Email address must contain @';
            header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
            return;
        }      
    }
    else{
        $_SESSION['error'] = 'All fields are required';
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        return;
    }
}


// Person changes in url id for that play safe
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
if( $row === false){
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $row['profile_id']; 

?>

<!DOCTYPE html>
<html>
<head>
<title>hardik patel's Profile Edit</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Editing Profile for <?php echo($_SESSION['name']); ?></h1>
    
    <?php
    if( isset($_SESSION['error'])){
        echo('<p style="color: red">'. htmlentities($_SESSION['error']) ."</p>");
        unset($_SESSION['error']); 
    }
    ?>

    <form method="post">
    <p>First Name:
    <input type="text" name="first_name" size="60" value="<?= $fn ?>"/></p>
    <p>Last Name:
    <input type="text" name="last_name" size="60" value="<?= $ln ?>"/></p>
    <p>Email:
    <input type="text" name="email" size="30" value="<?= $em ?>"/></p>
    <p>Headline:<br/>
    <input type="text" name="headline" size="80" value="<?= $he ?>"/></p>
    <p>Summary:<br/>
    <textarea name="summary" rows="8" cols="80" value="<?= $su ?>"></textarea>
    <p>
    <input type="hidden" name="profile_id" value="<?= $profile_id ?>"/>
    <input type="submit" name = "save" value="Save">
    <input type="submit" name="cancel" value="Cancel">
    </p>
    </form>
</div>
</body>
</html>