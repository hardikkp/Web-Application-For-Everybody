<?php
require_once 'pdo.php';
require_once "utils.php";
session_start();
accessDenied();
canCel();

// Person changes in url id for that play safe
if( ! isset($_GET['profile_id']) ){
    $_SESSION['error'] = 'Missing profile_id';
    header('Location: index.php');
    return;
}

if( isset($_POST['save'])  && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_REQUEST['profile_id']) ){
    
    $msg = validatePro();
    if ( is_string ($msg)){
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        return;
    }
    
    $msg = validatePos();
    if ( is_string ($msg)){
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        return;
    }
    $msg = validateEdu();
    if (is_string($msg)){
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        return;
    }
    
    // begin to update the data
    $sqlq = ' UPDATE profile SET 
        first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
        WHERE profile_id = :xyz AND user_id = :uid ';
    $stmt = $pdo -> prepare($sqlq);
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':xyz' => $_REQUEST['profile_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );
    
    // Clear out old position entry
    $stmt= $pdo -> prepare('DELETE FROM Position WHERE profile_id= :pid ');
    $stmt -> execute(array( ':pid' => $_REQUEST['profile_id']));

    // insert into position entries
    insertPositions($pdo,$_REQUEST['profile_id']);
    
    // Clear out old education entry
    $stmt= $pdo -> prepare('DELETE FROM education WHERE profile_id= :pid ');
    $stmt -> execute(array( ':pid' => $_REQUEST['profile_id']));
    
    // insert into education entries
    insertEducations($pdo,$_REQUEST['profile_id']);
    
    $_SESSION['success'] = 'Record updated';
    header('Location: index.php');  
    return;
}




$sqlq = 'SELECT * FROM profile WHERE profile_id = :xyz and user_id = :uid';
$stmt = $pdo -> prepare($sqlq);
$stmt -> execute(array(
    ':uid' => $_SESSION['user_id'],
    ':xyz' => $_GET['profile_id']));
$row = $stmt -> fetch(PDO::FETCH_ASSOC);
if( $row == false){
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $_REQUEST['profile_id']; 

$positions = loadPos($pdo, $_REQUEST['profile_id']);
$educations = loadEdu($pdo, $_REQUEST['profile_id']);

?>

<!DOCTYPE html>
<html>
<head>
<title>hardik patel's Profile Edit</title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1>Editing Profile for <?php echo($_SESSION['name']); ?></h1>
    
<?php
    flashMessages();
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
    <textarea name="summary" rows="8" cols="80"> <?= $su ?> </textarea>
    
    <p>Education:
    <input type="submit" id="addEdu" value="+" />
    <div id="edu_fields">
    <?php
    $edu = 0;
    foreach($educations as $education){
        $edu++;
        echo('<div id="edu'.$edu.'">');
        echo('<p> Year: <input type="text" name="edu_year'.$edu.'"');
        echo(' value="'.$education['year'].' "/>');
        echo('<input type="button" value="-"');
        echo('onclick="$(\'#edu'.$edu.'\').remove(); return false;">'.'<br>');
        echo('</p>');
        echo('<p>School:<input type="text" size="80" name="edu_school'.$edu.'" class="school"');
        echo('value="'.htmlentities($education['name']).'" />');
        echo('</p></div> <br>');
    }
    ?>
    </div></p>

    <p>Position: 
    <input type="submit" id="addPos" value="+" />
    <div id="position_fields">
    <?php
        $pos = 0;
        foreach($positions as $position){
            $pos++;
            echo('<div id="position'.$pos.'">');
            echo('<p> Year: <input type="text" name="year'.$pos.'"');
            echo(' value="'.$position['year'].' "/>');
            echo('<input type="button" value="-"');
            echo('onclick="$(\'#position'.$pos.'\').remove(); return false;">'.'<br>');
            echo('</p>');
            echo('<textarea name="desc'.$pos.'" rows="8" cols="80">');
            echo(htmlentities($position['description']));
            echo('</textarea></div> <br>');
        }
    ?>
    </div></p>

    <p>
    <input type="submit" name = "save" value="Save">
    <input type="submit" name="cancel" value="Cancel">
    </p>
    </form>

    <script type='text/javascript'>
    countPos = <?= $pos ?>;
    countEdu = <?= $edu ?>;

    $(document).ready(function(){
        console.log('Document ready called...');
        $('#addPos').click(function(event){
            event.preventDefault();
            if(countPos >= 9){
                alert("Maximum of nine position entries exceeded");
                return;
            }
            countPos++;
            console.log('Adding Position '+ countPos);
            
            $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>'
            );
        });

        $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        // Grab some HTML with hot spots and insert into the DOM
        var source  = $("#edu-template").html();
        $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));

        // Add the even handler to the new ones
            $('.school').autocomplete({
                source: "school.php"
            });

        });

        $('.school').autocomplete({
            source: "school.php"
        });

    });
</script>
<!-- HTML with Substitution hot spots -->
<script id="edu-template" type="text">
    <div id="edu@COUNT@">
    <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
    <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
    <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" />
    </p>
  </div>
</script>
</div>
</body>
</html>