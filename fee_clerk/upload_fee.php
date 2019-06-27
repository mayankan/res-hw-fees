<?php
    /**
     * This is page is used to upload fee for a month
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['month_of_fee'])) {
            $_SESSION['error'] = 'Month and Year of fee is Required.';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        if ($_FILES['fee_file']['error'] > 0) {
            $_SESSION['error'] = 'CSV file is required for Fee Upload. ERROR CODE - ' . $_FILES['fee_file']['error'];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't Connect to the Database");
        }

        $feeData = [];
        $errorInFeeData = [];
        $csvFile = fopen($_FILES['fee_file']['tmp_name'], "r");
        $feeData[] = fgetcsv($csvFile);

        while (!feof($csvFile)) {
            $data = fgetcsv($csvFile);
            if ($data !== false) {
                $studentData = getStudent($PDO, $studentId=NULL, $admissionNumber=$data[0]);
                $data['student_id'] = $studentData !== false ? $studentData['id'] : NULL;
                if ($data['student_id'] === NULL) {
                    $errorInFeeData[] = $data[0];
                }
                $feeData[] = [
                    'student_id' => $data['student_id'],
                    'admission_no' => $data[0],
                    'examination_fee' => $data[1],
                    'tution_fee' => $data[2],
                    'refreshment_acc_fee' => $data[3],
                    'lab_fee' => $data[4],
                    'project_fee' => $data[5],
                    'annual_charges' => $data[6],
                    'admin_charges' => $data[7],
                    'smart_class_charges' => $data[8],
                    'computer_fee_yearly' => $data[9],
                    'computer_fee_montly' => $data[10],
                    'development_charges_yearly' => $data[11],
                    'transport_fee' => $data[12],
                    'portal_charges' => $data[13],
                    'late_fee' => $data[14],
                    'total_fee' => $data[15]
                ];
            }
        }
        if (!empty($errorInFeeData)) {
            $_SESSION['fee_data']['errors'] = $errorInFeeData;
            header('Location: errors_in_fee.php');
            exit();
        } else {
            $_SESSION['fee_data']['data'] = $feeData;
            header('Location: fee_data.php');
            exit();
        }
        unset($PDO);
    }
    
?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container">
                <a href="#" class="navbar-brand">Fee Clerk Panel</a>
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
            <h1 class="text-center"><u>Upload Fee</u></h1>
            <div class="row d-flex justify-content-center">
                <div class="col-md-8">
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="month and year" class="col-form-label">
                                Month and Year<span class="text-danger">*</span>
                            </label>
                            <input type="text" name="month_of_fee" class="form-control" id="datetime" required autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="csv File" class="col-form-label">
                                CSV File<span class="text-danger">*</span>
                            </label>
                            <input type="file" name="fee_file" class="form-control-file" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-block">Upload Fees</button>
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