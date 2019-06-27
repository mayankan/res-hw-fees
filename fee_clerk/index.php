<?php
    /**
     * This is page is used to
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a fee clerk
    if ($_SESSION['role'] !== 'fee_clerk') {
        header('Location: ../404.html');
        return;
    }

    // checks for logout variable in GET Request and if it's true logs out user
    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
            return;
        }
    }
    
?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <title>Fee Clerk Panel</title>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-md navbar-dark bg-dark">
                <div class="container">
                    <a href="#" class="navbar-brand">Fee Clerk Panel</a>
                    <button class="navbar-toggler" data-toggle="collapse" data-target="#teacher-nav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="teacher-nav">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item active">
                                <a href="<?php echo $base_url ?>fee_clerk/" class="nav-link">
                                    View Fee
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>fee_clerk/upload_fee.php" class="nav-link">
                                    Upload Fee
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:{document.getElementById('logout').submit()}" class="nav-link ml-2 btn btn-primary text-white px-4">
                                    <i class="fa fa-sign-in mt-1" aria-hidden="true"></i> Logout
                                </a>
                                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="logout">
                                    <input type="hidden" name="logout" value="true">
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
    </body>
<?php require_once(__DIR__.'/../footer.php'); ?>