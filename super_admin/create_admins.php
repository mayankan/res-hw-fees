<?php
    /**
     * This is page is used to create admins
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a fee clerk
    if ($_SESSION['role'] !== 'super_admin') {
        header('Location: ../404.html');
        exit();
    }

    // checks for logout variable in GET Request and if it's true logs out user
    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
            exit();
        }
    }

?>

