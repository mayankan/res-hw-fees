<?php
    /**
     * This is page is used to
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a super admin
    if ($_SESSION['role'] !== 'fee_clerk') {
        header('Location: ../404.html');
        return;
    }
?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <title>Fee Clerk Panel</title>
    </head>
    <body>
        <h1>Fee Clerk</h1>
    </body>
<?php require_once(__DIR__.'/../footer.php'); ?>