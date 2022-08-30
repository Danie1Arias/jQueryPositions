<?php
    require_once "pdo.php";
    session_start();

    if ( ! isset($_SESSION['successLogin']) ) {
        echo("<!DOCTYPE html>
        <html>
        <head>
        <title>Daniel Arias Severance's Resume Registry d3a8b7e7</title>");
        require_once 'bootstrap.php'; 
        echo("</head>
        <body>
        <div class='container'>
        <h1>Daniel Arias Severance's Resume Registry d3a8b7e7</h1>
        <p><a href='login.php'>Please log in</a></p>");

        $stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");

        if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

            echo('<table border="1">'."\n");
            echo("<tr><th>Name</th><th>Headline</th>");
            echo "<tr><td>";
            $name = $row['first_name'] . " " . $row['last_name'];
            echo('<a href="view.php?profile_id=' . $row['profile_id'] . '">' . $name . '</a> ');
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td></tr>\n");
        
            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

                echo "<tr><td>";
                $name = $row['first_name'] . " " . $row['last_name'];
                echo('<a href="view.php?profile_id=' . $row['profile_id'] . '">' . $name . '</a> ');
                echo("</td><td>");
                echo(htmlentities($row['headline']));
                echo("</td></tr>\n");
            }

            echo("</table>");

            } 

            echo("
            </div>
            </body>");

    } else {

        echo("<!DOCTYPE html>
        <html>
        <head>
        <title>Daniel Arias Severance's Resume Registry d3a8b7e7</title>");
        require_once 'bootstrap.php'; 
        echo("</head>
        <body>
        <div class='container'>
        <h1>Daniel Arias Severance's Resume Registry d3a8b7e7</h1>");

        if ( isset($_SESSION['error']) ) {
            echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
            unset($_SESSION['error']);
        }

        if ( isset($_SESSION['success']) ) {
            echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
            unset($_SESSION['success']);
        }

        echo("<p><a href='logout.php'>Log out</a></p>");

        $stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        echo('<table border="1">'."\n");
        echo("<tr><th>Name</th><th>Headline</th><th>Action</th></tr>");
        echo "<tr><td>";
        $name = $row['first_name'] . " " . $row['last_name'];
        echo('<a href="view.php?profile_id=' . $row['profile_id'] . '">' . $name . '</a> ');
        echo("</td><td>");
        echo(htmlentities($row['headline']));
        echo("</td><td>");
        echo('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / ');
        echo('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
        echo("</td></tr>\n");
        
        while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

            echo "<tr><td>";
            $name = $row['first_name'] . " " . $row['last_name'];
            echo('<a href="view.php?profile_id=' . $row['profile_id'] . '">' . $name . '</a> ');
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td><td>");
            echo('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / ');
            echo('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
            echo("</td></tr>\n");
        }

        echo("</table>");

        } 

        echo("
        <br>
        <p><a href='add.php'>Add New Entry</a></p>
        </div>
        </body>");

    }

?>


