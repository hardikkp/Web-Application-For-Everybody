<?php

function accessDenied(){
    if ( !isset($_SESSION['user_id']) || !isset($_SESSION['name']) || strlen($_SESSION['user_id']) < 1 ){
        die('ACCESS DENIED');
    }
}

function canCel(){
    // If the user requested cancel go back to index.php
    if ( isset($_POST['cancel']) ) {
    header('Location: index.php');  
    return;
    }
}

function flashMessages(){
    if ( isset($_SESSION['success']) ) {
        echo('<p style="color: green">'. htmlentities($_SESSION['success']) ."</p>");
        unset($_SESSION['success']);
    }
    if( isset($_SESSION['error'])){
        echo('<p style="color: red">'. htmlentities($_SESSION['error']) ."</p>");
        unset($_SESSION['error']); 
    }
}

function validatePos() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
       
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
        }

        if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
        }
    }
    return true;
   
}


function validatePro(){
    if( strlen($_POST['first_name']) < 1 && strlen($_POST['last_name']) < 1 && strlen($_POST['email']) < 1 && strlen($_POST['headline']) < 1 && strlen($_POST['summary']) < 1 ){
        return 'All fields are required';
    }
    
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        // (strpos($_POST['email'],'@') === false)
        return 'Email address must contain @';
    }

    return true; 
}

function loadPos($pdo , $profile_id){
    $sqlq = 'SELECT * FROM Position WHERE profile_id = :xyz ORDER BY rank';
    $stmt = $pdo -> prepare($sqlq);
    $stmt -> execute(array(
        ':xyz' => $profile_id));
    $position = array();    
    while( $row = $stmt -> fetch(PDO::FETCH_ASSOC)){
        $position[] = $row;
    }
    return $position;
}