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

    if (!isset($_SESSION['fee_data']['data'])) {
        header('Location: upload_fee.php');
        exit();
    }

    $start = 0;
    $end = 0;
    if (!isset($_GET['page_no'])) {
        $start = 1;
        $end = 10;
        $_SESSION['page_no'] = 1;
    } else {
        if ((int) $_GET['page_no'] === 1) {
            $start = 1;
            $end = 10;
        } else {
            $end = ((int) $_GET['page_no']) * 10;
            $start = $end - 10;
            if (!isset($_SESSION['fee_data']['data'][$start])) {
                header('Location: fee_data.php?page_no='. ((int) $_GET['page_no'] - 1));
                exit();
            }
        }
        $_SESSION['page_no'] = (int) $_GET['page_no'];
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

        <section id="teachers" class="mt-2">
            <div class="container-fluid">
                <div class="row pb-2">
                    <div class="col-3 d-flex justify-content-start">
                        <?php if ($_SESSION['page_no'] <= 1): ?>
                        <a href="#" class="btn btn-outline-dark" disabled>
                            <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php else: ?>
                        <a href="<?php echo $base_url ?>fee_clerk/fee_data.php?page_no=<?php echo $_SESSION['page_no'] - 1 ?>" class="btn btn-outline-dark">
                            <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php endif ?>
                    </div>
                    <div class="col-3">
                        <form onsubmit="return window.confirm('Do you really want to accept the data?');" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="confirm">
                            <button type="submit" class="btn btn-success btn-block">Confirm Data</button>
                        </form>
                    </div>
                    <div class="col-3">
                        <form onsubmit="return window.confirm('Do you really want to reject the data?');" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="cancel">
                            <button type="submit" class="btn btn-danger btn-block">Cancel Insertion</button>
                        </form>
                    </div>
                    <div class="col-3 d-flex justify-content-end">
                        <a href="<?php echo $base_url ?>fee_clerk/fee_data.php?page_no=<?php echo $_SESSION['page_no'] + 1 ?>" class="btn btn-outline-dark">
                            Next <i class="fa fa-arrow-right fa-1 mt-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <table class="table table-hover table-responsive-sm table-bordered">
                            <thead>
                                <th>#</th>
                                <th>Admission Number</th>
                                <th>Total Fees</th>
                            </thead>
                            <tbody>
                                <?php for ($i = $start; $i < $end; $i++): ?>
                                    <?php if (isset($_SESSION['fee_data']['data'][$i])): ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $_SESSION['fee_data']['data'][$i]['admission_no']; ?></td>
                                        <td><?php echo $_SESSION['fee_data']['data'][$i]['total_fee']; ?></td>
                                    </tr>
                                    <?php else: ?>
                                        <?php continue; ?>
                                    <?php endif ?>
                                <?php endfor ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
<?php require_once(__DIR__.'/../footer.php'); ?>
