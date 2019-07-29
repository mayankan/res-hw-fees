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

    function getFees($PDO, $startLimit=0) {
        $data = [
            ':start_limit' => $startLimit
        ];
        try {
            $stmt = $PDO->prepare("SELECT 
                                    `admission_no`,
                                     `total_fee`,
                                     `date_created`,
                                     `paid_at` FROM `fee` WHERE `deleted_at` IS NULL LIMIT :start_limit, 10");
            $stmt->execute($data);
            if ($stmt->rowCount() == 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't connect to the database");
    }

    $feedata = NULL;
    if (!isset($_GET['page_no'])) {
        $feeData = getFees($PDO, $startLimit=0);
    } else {
        $page_no = (int)$_GET['page_no'];
        if ($page_no <= 0) {
            $page_no = 1;
        }
        $startLimit = ($page_no * 10) - 10;
        $feeData = getFees($PDO, $startLimit=$startLimit);
        if ($feeData === NULL && $page_no !== 1) {
            header('Location: index.php?page_no=' . (((int)$_GET['page_no']) - 1));
            return;
        }
        $_SESSION['page_no'] = $page_no;
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
                            <a href="<?php echo $base_url ?>fee_clerk/index.php" class="nav-link">
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

        <section id="filters" class="mt-2">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
                            <div class="form-group row d-flex justify-content-center">
                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <div id="students" class="mt-2">
            <div class="container-fluid">
                <div class="row pb-2">
                    <div class="col-6 d-flex justify-content-start">
                        <?php if ($_SESSION['page_no'] <= 1): ?>
                        <a href="#" class="btn btn-outline-dark" disabled>
                            <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php else: ?>
                            <?php $backUrl = $base_url . "fee_clerk/index.php?page_no=" . ((int)$_SESSION['page_no'] - 1); ?>
                            <a href="<?php echo $backUrl ?>" class="btn btn-outline-dark">
                                <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                            </a>
                        <?php endif ?>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <?php $nextUrl = $base_url . "fee_clerk/index.php?page_no=" . ((int)$_SESSION['page_no'] + 1); ?>
                        <a href="<?php echo $nextUrl ?>" class="btn btn-outline-dark">
                            Next <i class="fa fa-arrow-right fa-1 mt-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <?php if (!is_null($feeData)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-responsive-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Admission Number</th>
                                    <th>Name</th>
                                    <th>Total Fees</th>
                                    <th>Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feeData as $fee): ?>
                                <tr>
                                    <td>
                                        <?php echo date_format(date_create($fee['date_created']), 'd F y'); ?>
                                    </td>
                                    <td>
                                        <?php echo $fee['admission_no']; ?>
                                    </td>
                                    <td>
                                        <?php $studentData = getStudent($PDO, $studentId=NULL, $admissionNumber=$fee['admission_no']); ?>
                                        <?php echo $studentData['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $fee['total_fee']; ?>
                                    </td>
                                    <td>
                                        <?php if (is_null($fee['paid_at'])): ?>
                                            Not paid
                                        <?php else: ?>
                                            <?php echo date_format(date_create($fee['paid_at']), 'd F y'); ?>
                                        <?php endif ?>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </div>
<?php require_once(__DIR__.'/../footer.php'); ?>
<?php unset($POD); ?>
