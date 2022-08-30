<?php

require_once "pdo.php";
require_once "util.php";

session_start();

if ( ! isset($_SESSION['user_id']) ) {
    die('ACCESS DENIED');
}

if ( isset($_POST['cancel'])) {
    header( 'Location: index.php' );
    return;
}

if (!isset($_REQUEST['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM profile 
WHERE profile_id = :prof AND user_id = :uid");
$stmt->execute(array(":prof" => $_REQUEST['profile_id'],
    ":uid" => $_SESSION['user_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if ($profile === false) {
    $_SESSION['error'] = "Could not load profile";
    header('Location: index.php') ;
    return;
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && 
    isset($_POST['headline']) && isset($_POST['summary'])) {

    $msg = validateProfile();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']); 
        return;
    }

    $msg = validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']); 
        return;
    }

    $stmt = $pdo->prepare('UPDATE profile SET first_name = :fn,
    last_name = :ln, email = :em, headline = :he, summary = :su
    WHERE profile_id = :pid AND user_id = :uid');

    $stmt->execute(array(
    ':pid' => $_REQUEST['profile_id'],
    ':uid' => $_SESSION['user_id'],
    ':fn' => $_POST['first_name'],
    ':ln' => $_POST['last_name'],
    ':em' => $_POST['email'],
    ':he' => $_POST['headline'],
    ':su' => $_POST['summary']));

    $stmt = $pdo->prepare('DELETE FROM position
    WHERE profile_id = :pid');

    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year'.$i])) continue;
        if (!isset($_POST['desc'.$i])) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO position 
        (profile_id, rank, year, description) 
        VALUES ( :pid, :rank, :year, :desc)');

        $stmt->execute(array(
            ':pid' => $_REQUEST['profile_id'],
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc)
        );

        $rank++;
    }

    $_SESSION["success"] = "Profile updated";
    header("Location: index.php");
    return;

}

$positions = loadPos($pdo, $_REQUEST['profile_id']);

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);

?>

<!DOCTYPE html>
<html>
    <head>
    <title>Daniel Arias Severance's Resume Registry d3a8b7e7</title>
    <?php require_once "bootstrap.php"; ?>
    <link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

    <link rel="stylesheet" 
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
        integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
        crossorigin="anonymous">

    <script
    src="https://code.jquery.com/jquery-3.2.1.js"
    integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
    crossorigin="anonymous"></script>
</head>

<body>
<div class="container">

<?php
    if ( isset($_SESSION['name'])) {

        echo "<h1>Edit Profile for  ";
        echo htmlentities($_SESSION['name']);
        echo "</h1>\n";
    }

    // Flash pattern
    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
?>

<form method="post">
    <p>First Name: <input type="text" name="first_name" size="60" value="<?= $first_name ?>"></p>
    <p>Last Name: <input type="text" name="last_name" size="60" value="<?= $last_name ?>"></p>
    <p>Email: <input type="text" name="email" size="30" value="<?= $email ?>"></p>
    <p>Headline:<br><input type="text" name="headline" size="80" value="<?= $headline ?>"></p>
    <p>Summary:<br><textarea name="summary" rows="8" cols="80"><?= $summary ?></textarea></p>
    <input type="hidden" name="profile_id" value="<?= $_GET['profile_id']; ?>">
    <?php 

        $pos = 0;
        echo "<p>Position: <input type='submit' id='addPos' value='+' />";
        echo "<div id='position_fields'>";

        foreach( $positions as $position) {
            $pos++;
            echo "<div id='position". $pos ."'>";
            echo "<p>Year: <input type='text' name='year".$pos."'  value='".$position['year']."' />";
            echo "<input type='submit' value='-' onclick='$(\"#position".$pos."\").remove(); return false;' /></p>";
            echo "<p><textarea name='desc".$pos."' rows='8' cols='80'>".$position['description']."</textarea>";
            echo "</div>";
        }

        echo "</div></p>";
    ?>
    <p><input type="submit" value="Save"/>
    <input type="submit" value="Cancel" name="cancel">
</form>

<script>
    
    $(document).ready(function() {
        countPos = <?= $pos ?>;
        $(document).ready(function() {
            window.console && console.log('Document ready called');
            $('#addPos').click(function(event) {
                event.preventDefault();
                if (countPos >= 9) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countPos++;
                window.console && console.log("Adding position " + countPos);
                $('#position_fields').append(
                    '<div id="position' + countPos + '"> \
            <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
            <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
            </div>');
            });
        });
    });

</script>
</div>
</body>

