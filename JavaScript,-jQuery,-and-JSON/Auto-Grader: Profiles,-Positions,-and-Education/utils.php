<?php

function accessDenied(){
    if( !isset($_SESSION['user_id']) && !isset($_SESSION['name']) ){
        die('ACCESS DENIED');
    }
}

function canCel(){
    // if the user requested cancel go back to index.php
    if( isset($_POST['cancel']) ){
        header('Location: index.php');
        return;
    }
}

function flashMessages(){
    if( isset($_SESSION['success']) ){
        echo('<p style="color:green">' . $_SESSION['success']). '</p>';
        unset($_SESSION['success']);
    }
    if( isset($_SESSION['error']) ){
        echo('<p style="color:red">' . $_SESSION['error']). '</p>';
        unset($_SESSION['error']);
    }
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

function validatePos(){
    for($i=1;$i<=9;$i++){
        if( ! isset($_POST['year'.$i]) ) continue;
        if( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        
        if( strlen($year) == 0 || strlen($desc) == 0 ){
            return "All fiedls are required";
        }

        if( ! is_numeric($year)){
            return "Position year must be numeric";
        }
    }
    return true;
}

function validateEdu(){
    for($i=1;$i<=9;$i++){
        if( ! isset($_POST['edu_year'.$i]) ) continue;
        if( ! isset($_POST['edu_school'.$i]) ) continue;
        $edu_year = $_POST['edu_year'.$i];
        $edu_school = $_POST['edu_school'.$i];
        
        if( strlen($edu_year) == 0 || strlen($edu_school) == 0 ){
            return "All fiedls are required";
        }

        if( ! is_numeric($edu_year)){
            return "Education year must be numeric";
        }
    }
    return true;
}

function loadPos($pdo , $profile_id){
    $sqlq = 'SELECT * FROM position WHERE profile_id = :xyz ORDER BY rank';
    $stmt = $pdo -> prepare($sqlq);
    $stmt -> execute(array(
        ':xyz' => $profile_id));
    $position = array();
    while( $row = $stmt -> fetch(PDO::FETCH_ASSOC)){
        $position[] = $row;
    }
    return $position;  
}

function loadEdu($pdo , $profile_id){
    $sqlq = 'SELECT year,name FROM institution JOIN education ON institution.institution_id = education.institution_id WHERE profile_id = :xyz ORDER BY rank';
    $stmt = $pdo -> prepare($sqlq);
    $stmt -> execute(array(
        ':xyz' => $profile_id));
    $education = array();
    while( $row = $stmt -> fetch(PDO::FETCH_ASSOC)){
        $education[] = $row;
    }
    return $education;  
}

function insertPositions($pdo, $profile_id){
    $rank = 1;

    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO Position  (profile_id, rank, year, description) 
            VALUES ( :pid, :rank, :year, :desc)');
        $stmt->execute(array(
            ':pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc)
        );
        $rank++;
    }
}

function insertEducations($pdo, $profile_id){
    $rank = 1;
    for($i=1; $i<=9; $i++){
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;
        $edu_year = $_POST['edu_year'.$i];
        $edu_school = $_POST['edu_school'.$i];

        //Lookup the school if it is there
        $institution_id = false;
        $stmt = $pdo -> prepare('SELECT institution_id FROM institution WHERE name = :name');
        $stmt -> execute(array( ':name' => $edu_school )); 
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if( $row !== false) $institution_id = $row['institution_id'];

        //If there is not institution then insert it
        if($institution_id === false){
            $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
            $stmt->execute(array(':name' => $edu_school));
            $institution_id = $pdo->lastInsertId();
        }

        $sqlq = 'INSERT INTO education (profile_id,institution_id,rank,year) VALUES (:pid,:iid,:rk,:yr)';
        $stmt = $pdo -> prepare($sqlq);
        $stmt -> execute(array(
            ':pid' => $profile_id,
            ':iid' => $institution_id,
            ':rk' => $rank,
            ':yr' => $edu_year
        ));
        $rank++;
    }
}
?>