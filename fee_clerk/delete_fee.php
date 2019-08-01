  <?php
    /**
     * This is page is used to 
    */
    session_start();
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');

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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST)) {
            // checking for the required fields
            if (empty($_POST['month_of_fee'])) {
                $_SESSION['error'] = 'Month and Year of fee is Required.';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

            $PDO = getConnection();
            if (is_null($PDO)) {
                die("Can't connect to the database");
            }
            $_POST['month_of_fee'] = explode(' ', $_POST['month_of_fee']);
            $month = (string) date_parse($_POST['month_of_fee'][0])['month'];
            if ($month <= 10) {
                $month = '0' . $month;
            }
            if (
                !is_null(
                    deleteFee($PDO, $month, $_POST['month_of_fee'][1])
                )
            ) {
                $_SESSION['success'] = 'Successfully deleted Fee for ' . $_POST['month_of_fee'][0] . ' ' . $_POST['month_of_fee'][1];
                header('Location: delete_fee.php');
                exit();
            } else {
                $_SESSION['error'] = 'Something went wrong';
                header('Location: delete_fee.php');
                exit();
            }
        }
    }
    
?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <style>
        .ui-datepicker-calendar {
            display: none;
        }
        </style>
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
                        <li class="nav-item">
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
                        <li class="nav-item active">
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
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong><?php echo $_SESSION['error']; ?></strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <strong><?php echo $_SESSION['success']; ?></strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </section>

        <section id="uploadFee" class="container py-4">
            <h1 class="text-center"><u>Delete Fee</u></h1>
            <div class="row d-flex justify-content-center">
                <div class="col-md-8">
                    <form 
                        action="<?php echo $_SERVER['PHP_SELF'] ?>" 
                        method="POST"
                        onsubmit="return window.confirm('Do you really want to delete fees for the month?');"
                    >
                        <div class="form-group">
                            <label for="month and year" class="col-form-label">
                                Month and Year&nbsp;<span class="text-danger">*</span>
                            </label>
                            <input type="text" name="month_of_fee" class="form-control" id="datetime" required autocomplete="off">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-block">Delete Fees for the Month</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <script>
            $(document).ready(function() {
                $('#datetime').datepicker({
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    dateFormat: 'MM yy',
                    onClose: function(dateText, inst) { 
                        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                    }
                });
            });
        </script>
<?php require_once(__DIR__.'/../footer.php'); ?>
