<?php
    /**
     * This is page is used to see errors found in uploaded csv for admission number
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a fee clerk
    if ($_SESSION['role'] !== 'fee_clerk') {
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

    // if there are any errors redirect to errors page
    if (!isset($_SESSION['fee_data']['errors'])) {
        header('Location: upload_fee.php');
        exit();
    }

    // post reequest to this page deletes the data in session and returns to upload form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        unset($_SESSION['fee_data']);
        header("Location: upload_fee.php");
        exit();
    }
?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container">
                <a href="#" class="navbar-brand">Fee Admin Panel</a>
                <button class="navbar-toggler" data-toggle="collapse" data-target="#teacher-nav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="teacher-nav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>fee_clerk/" class="nav-link">
                                View Fee
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?php echo $base_url ?>fee_clerk/upload_fee.php" class="nav-link">
                                Upload Fee
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>fee_clerk/maintenance.php" class="nav-link">
                                Maintenance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>fee_clerk/students.php" class="nav-link">
                                View Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>fee_clerk/delete_fee.php" class="nav-link">
                                Delete Fee
                            </a>
                        </li>
                        <li class="nav-item">
                            <a 
                                href="javascript:{document.getElementById('logout').submit()}" 
                                class="nav-link ml-2 btn btn-primary text-white px-4"
                            >
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

        <section id="error" class="mt-4">
            <div class="container">
                <h2 class="text-center">There are some errors in uploaded CSV listed below:</h2>
            </div>
        </section>

        <section class="container mt-4">
            <div class="row d-flex justify-content-center">
                <div class="col-md-6">
                    <ul class="list-group">
                        <?php $errorsLength = count($_SESSION['fee_data']['errors']) ?>
                        <?php for ($i = 0; $i < $errorsLength; $i++): ?>
                        <li class="list-group-item"><?php echo $_SESSION['fee_data']['errors'][$i] ?></li>
                        <?php endfor ?>
                    </ul>
                    <div class="mt-4">
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                            <input type="hidden" name="re_upload">
                            <button type="submit" class="btn btn-info btn-block">Re-Upload File</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
<?php require_once(__DIR__.'/../footer.php'); ?>
