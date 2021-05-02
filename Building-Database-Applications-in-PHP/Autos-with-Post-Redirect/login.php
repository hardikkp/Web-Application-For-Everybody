<?php // Do not put any HTML above this line
require_once 'pdo.php';
session_start();
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123


// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['failure'] = "User name and password are required";
        header('Location: login.php');
        return;
    } else {
        unset($_SESSION['email']);
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $check = hash('md5', $salt.$_POST['pass']);
            if ( $check == $stored_hash ) {
            // Redirect the browser to game.php
            $_SESSION['email'] = $_POST['email'];
            $_SESSION['success'] = 'Logged in';
            error_log("Login success ".$_POST['email']);
            header('Location: view.php');
            return;
            } else {
                $_SESSION['failure'] = 'Incorrect Password...';        
                $failure = "Incorrect password";
                error_log("Login fail ".$_POST['email']." $check");
                header('Location: login.php');
                return;
            }
        }else{
            $_SESSION['failure'] = "Email must have an at-sign (@)"; 
            header('Location: login.php');
            return;   
        }
        
    }
}

// Fall through into the View
?>
<!DOCTYPE html>
<html>
    <head>
    <?php  require_once "bootstrap.php"; ?> 
    <title>hardik patel Login Page</title>
    </head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( isset($_SESSION['failure']) ) {
    // Look closely at the use of single and double quotes
    echo('<p style="color:red">'. htmlentities($_SESSION['failure']) ."</p>");
    unset($_SESSION['failure']);
}
?>

<form method="POST">
<label for="email">Email</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<label for="Log In">Log In</label>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the three character name of the 
programming language used in this class (all lower case) 
followed by 123. -->
</p>
</div>
</body>
</html>