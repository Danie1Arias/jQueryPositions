<?php
require_once "pdo.php";

session_start();

if ( isset($_POST['cancel'] ) ) {

    header("Location: index.php");
    return;
}

$guess = isset($_SESSION['name']) ? $_SESSION['name'] : '';
$message = isset($_SESSION['error']) ? $_SESSION['error'] : false;

$salt = 'XyZzy12*_';
$failure = false;  

if ( isset($_POST['email']) && isset($_POST['pass']) ) {

    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {

        $_SESSION['error'] = "User name and password are required";

    } else {

        if (!strpos($_POST['email'], '@')) {

            $_SESSION['error'] = "Email must have an at-sign (@)";

        } else {

            $check = hash('md5', $salt.$_POST['pass']);

            $stmt = $pdo->prepare('SELECT user_id, name FROM users
                WHERE email = :em AND password = :pw');
            $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ( $row !== false ) {
                $_SESSION['successLogin'] = true;
                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
                // Redirect the browser to index.php
                header("Location: index.php");
                return;

            } else {

                error_log("Login error ".$_POST['email']);
                $_SESSION['error'] = "Incorrect password";
            }
        }      
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
    <?php require_once "bootstrap.php"; ?>
    <title>Daniel Arias Cámara Login Page d3a8b7e7</title>
    
    </head>

    <body>
    <div class="container">
        <h1>Please Log In</h1>
        <form method="POST">
            <label for="nam">Email</label>
            <input type="text" name="email" id="email"><br/>
            <label for="id_1723">Password</label>
            <input type="text" name="pass" id="id_1723"><br/>
            <input type="submit" onclick="return doValidate();" value="Log In">
            <input type="submit" value="Cancel" name="cancel">
        </form>

        <script type="text/javascript">
            
            function doValidate() {
                console.log('Validating...');
                try {
                    addr = document.getElementById('email').value;
                    pw = document.getElementById('id_1723').value;
                    console.log("Validating addr="+addr+" pw="+pw);
                    if (addr == null || addr == "" || pw == null || pw == "") {
                        alert("Both fields must be filled out");
                        return false;
                    }
                    if ( addr.indexOf('@') == -1 ) {
                        alert("Invalid email address");
                        return false;
                    }
                    return true;
                } catch(e) {
                    return false;
                }
                return false;
            }

        </script>

    </div>
    </body>

