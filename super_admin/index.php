<?php
    /**
     * This is page is used to view fees
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a fee clerk
    if ($_SESSION['role'] !== 'super_admin') {
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

    function getFees($PDO, $startLimit=0, $admissionNumber="", $isPaid="", $monthAndYear="") {
        $sql = "SELECT * FROM `fee` WHERE `deleted_at` IS NULL AND";
        if ($admissionNumber !== "") {
            $sql .= " `admission_no` LIKE :adm_no AND";
            $data[':adm_no'] = '%' . $admissionNumber . '%';
        }
        if ($isPaid !== "") {
            if ($isPaid === "0") {
                $sql .= " `paid_at` IS NULL AND";
            } else if ($isPaid === "1") {
                $sql .= " `paid_at` IS NOT NULL AND";
            }
        }
        if ($monthAndYear !== "") {
            $monthAndYear = explode(' ', $monthAndYear);
            $month = (string) date_parse($monthAndYear[0])['month'];
            if ($month <= 10) {
                $month = '0' . $month;
            }
            $sql .= " `month` LIKE :month AND";
            $data[':month'] = $monthAndYear[1] . '-' . $month . '-__';
        }
        $sql = substr($sql, 0, strlen($sql) - 4);
        if ($startLimit !== NULL) {
            $sql .= " LIMIT :start_limit, 10";
            $data[':start_limit'] = $startLimit;
        }
        try {
            $stmt = $PDO->prepare($sql);
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

    function addClassAndName($PDO, &$feeData) {
        if (!is_null($feeData)) {
            for ($index = 0; $index < count($feeData); $index++) {
                $studentData = getStudent($PDO, NULL, $feeData[$index]['admission_no']);
                $classData = getClass($PDO, $studentData['class_id']);
                $feeData[$index]['name'] = $studentData['name'];
                $feeData[$index]['class'] = $classData['class_name'] . ' - ' . $classData['section'];
            }
        }
    }

    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't connect to the database");
    }

    $feedata = NULL;
    $monthAndYear = isset($_GET['month_of_fee']) ? $_GET['month_of_fee'] : "";
    $admissionNumber = isset($_GET['admission_no']) ? $_GET['admission_no'] : "";
    $isPaid = isset($_GET['paid']) ? $_GET['paid'] : "";

    if (isset($_GET['export'])) {
        $feeDataForExport = getFees($PDO, 
                                        $startLimit=NULL, 
                                        $admissionNumber=$admissionNumber, 
                                        $isPaid=$isPaid, 
                                        $monthAndYear=$monthAndYear
                                    );
        addClassAndName($PDO, $feeDataForExport);
        $_SESSION['feeData'] = $feeDataForExport;
        header('Location: export_fee.php');
        exit();
    }

    if (!isset($_GET['page_no'])) {
        $feeData = getFees($PDO, $startLimit=0, $admissionNumber=$admissionNumber, $isPaid=$isPaid, $monthAndYear=$monthAndYear);
        $_SESSION['page_no'] = 1;
    } else {
        $page_no = (int)$_GET['page_no'];
        if ($page_no <= 0) {
            $page_no = 1;
        }
        $startLimit = ($page_no * 10) - 10;
        $feeData = getFees($PDO, $startLimit=$startLimit, $admissionNumber=$admissionNumber, $isPaid=$isPaid, $monthAndYear=$monthAndYear);
        if ($feeData === NULL && $page_no !== 1) {
            header('Location: index.php?page_no=' . (((int)$_GET['page_no']) - 1));
            return;
        }
        $_SESSION['page_no'] = $page_no;
    }

    addClassAndName($PDO, $feeData);
    
?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <style>
        .ui-datepicker-calendar {
            display: none;
        }
        </style>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container">
                <a href="#" class="navbar-brand">Super Admin Panel</a>
                <button class="navbar-toggler" data-toggle="collapse" data-target="#teacher-nav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="teacher-nav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item active">
                            <a href="<?php echo $base_url ?>super_admin/" class="nav-link">
                                View Fee
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/teacher_logs.php" class="nav-link">
                                View Teacher Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/maintenance_logs.php" class="nav-link">
                                View Maintenance Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/create_admins.php" class="nav-link">
                                Create Admins
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
                <div class="row d-flex justify-content-center my-2">
                    <div class="col-4">
                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                            <div class="form-group row d-flex align-items-center justify-content-center">
                                <!-- Filter For Month of Fee -->
                                <?php if (isset($_GET['month_of_fee'])): ?>
                                <input type="text" name="month_of_fee" 
                                        class="form-control col-3 m-2" id="datetime" 
                                        autocomplete="off"
                                        value="<?php echo $_GET['month_of_fee'] ?>"
                                        placeholder="Month of Fee"
                                        readonly
                                >
                                <?php else: ?>
                                <input type="text" name="month_of_fee" 
                                        class="form-control col-3 m-2" id="datetime" 
                                        autocomplete="off"
                                        placeholder="Month of Fee"
                                        readonly
                                >
                                <?php endif ?>

                                <!-- Filter for Admission Number -->
                                <?php if (isset($_GET['admission_no'])): ?>
                                <input type="text" name="admission_no" 
                                        class="form-control col-3 m-2" 
                                        value="<?php echo $_GET['admission_no'] ?>" 
                                        placeholder="Admission Number"
                                >
                                <?php else: ?>
                                <input type="text" name="admission_no" 
                                        class="form-control col-3 m-2" 
                                        placeholder="Admission Number"
                                >
                                <?php endif ?>

                                <!-- Filter for if paid or not? -->
                                <?php if (isset($_GET['paid'])): ?>
                                    <?php if ($_GET['paid'] === "0"): ?>
                                    <select name="paid" class="form-control col-3 m-2">
                                        <option value="" disabled>Paid?</option>
                                        <option value="1">Yes</option>
                                        <option value="0" selected>No</option>
                                    </select>
                                    <?php elseif ($_GET['paid'] === "1"): ?>
                                    <select name="paid" class="form-control col-3 m-2">
                                        <option value="" disabled>Paid?</option>
                                        <option value="1" selected>Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <?php endif ?>
                                <?php else: ?>
                                <select name="paid" class="form-control col-3 m-2">
                                    <option value="" selected disabled>Paid?</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <?php endif ?>
                                <button class="btn btn-success col-3 m-2">Filter</button>
                                <?php $exportUrl = $base_url . "super_admin/index.php?export=1"; ?>
                                <?php if (isset($_GET['admission_no'])): ?>
                                    <?php $exportUrl .= "&admission_no=" . $_GET['admission_no']; ?>
                                <?php endif ?>
                                <?php if (isset($_GET['month_of_fee'])): ?>
                                    <?php $exportUrl .= "&month_of_fee=" . $_GET['month_of_fee']; ?>
                                <?php endif ?>
                                <?php if (isset($_GET['paid'])): ?>
                                    <?php $exportUrl .= "&paid=" . $_GET['paid']; ?>
                                <?php endif ?>
                                <a href="<?php echo $exportUrl ?>" class="btn btn-info">
                                    Export Fee Data
                                </a>
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
                            <?php $backUrl = $base_url . "super_admin/index.php?page_no=" . ((int)$_SESSION['page_no'] - 1); ?>
                            <?php if (isset($_GET['admission_no'])): ?>
                                <?php $backUrl .= "&admission_no=" . $_GET['admission_no']; ?>
                            <?php endif ?>
                            <?php if (isset($_GET['month_of_fee'])): ?>
                                <?php $backUrl .= "&month_of_fee=" . $_GET['month_of_fee']; ?>
                            <?php endif ?>
                            <?php if (isset($_GET['paid'])): ?>
                                <?php $backUrl .= "&paid=" . $_GET['paid']; ?>
                            <?php endif ?>
                            <a href="<?php echo $backUrl ?>" class="btn btn-outline-dark">
                                <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                            </a>
                        <?php endif ?>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <?php $nextUrl = $base_url . "super_admin/index.php?page_no=" . ((int)$_SESSION['page_no'] + 1); ?>
                        <?php if (isset($_GET['admission_no'])): ?>
                            <?php $nextUrl .= "&admission_no=" . $_GET['admission_no']; ?>
                        <?php endif ?>
                        <?php if (isset($_GET['month_of_fee'])): ?>
                            <?php $nextUrl .= "&month_of_fee=" . $_GET['month_of_fee']; ?>
                        <?php endif ?>
                        <?php if (isset($_GET['paid'])): ?>
                            <?php $nextUrl .= "&paid=" . $_GET['paid']; ?>
                        <?php endif ?>
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
                                    <th>Month</th>
                                    <th>Admission Number</th>
                                    <th>Name</th>
                                    <th>Class & Section</th>
                                    <th>Total Fees</th>
                                    <th>Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feeData as $fee): ?>
                                <tr>
                                    <td>
                                        <?php echo date_format(date_create($fee['month']), 'F Y'); ?>
                                    </td>
                                    <td>
                                        <?php echo $fee['admission_no']; ?>
                                    </td>
                                    <td>
                                        <?php echo $fee['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $fee['class']; ?>
                                    </td>
                                    <td>
                                        <?php echo $fee['total_fee']; ?>
                                    </td>
                                    <td>
                                        <?php if (is_null($fee['paid_at'])): ?>
                                            Not paid
                                        <?php else: ?>
                                            <?php echo date_format(date_create($fee['paid_at']), 'd F y H:i:s'); ?>
                                        <?php endif ?>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                    <h2 class="text-center">No Data Available</h2>
                <?php endif ?>
            </div>
        </div>
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
<?php unset($POD); ?>
