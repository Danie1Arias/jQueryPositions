<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {

    $sql = "DELETE FROM profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['success'] = 'Profile deleted';
    header( 'Location: index.php' ) ;
    return;
}

if ( isset($_POST['cancel'])) {

  header( 'Location: index.php' ) ;
  return;
}

// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {

  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT first_name, last_name, profile_id FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>

<!DOCTYPE html>
<html>
    <head>
    <title>Daniel Arias Severance's Resume Registry 54a4b351</title>
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

<h1>Deleteing Profile</h1>
<form method="post" action="delete.php">
  <p>First Name: <?= htmlentities($row['first_name']) ?></p>
  <p>Last Name: <?= htmlentities($row['last_name']) ?></p>
  <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
  <input type="submit" value="Delete" name="delete">
  <input type="submit" value="Cancel" name="cancel">
</form>

</div>
</body>