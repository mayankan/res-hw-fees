<?php
    /**
     * This Page is used to show remaining fees to student
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a student
    if ($_SESSION['role'] !== 'student') {
        header('Location: ../404.html');
        return;
    }

    // checks for logout variable in GET Request and if it's true logs out user
    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
        }
    }

    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't Connect to the database");
    }

    var_dump($_GET['payment_id']);
    var_dump($_GET['payment_request_id']);
    var_dump($_GET['payment_status']);
?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container">
                <a href="#" class="navbar-brand">Student Panel</a>
                <button class="navbar-toggler" data-toggle="collapse" data-target="#student-nav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="student-nav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>student/" class="nav-link">
                                Homeworks
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?php echo $base_url ?>student/fee.php" class="nav-link">
                                Pay Fees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>student/profile.php" class="nav-link">
                                Profile
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

        <section id="payment-message" class="container mt-4"></section>
<?php require_once(__DIR__.'/../footer.php'); ?>
