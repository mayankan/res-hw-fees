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

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $email = isset($_POST['email']) ? $_POST['email'] : "";
        $utrNumber = isset($_POST['utr_number']) ? $_POST['utr_number'] : "";

        if ($email === "") {
            $_SESSION['error'] = "Email address is required.";
            header("Location: rtgs.php");
            exit();
        }

        if ($utrNumber === "") {
            $_SESSION['error'] = "UTR Number is required.";
            header("Location: rtgs.php");
            exit();
        }

        if (strlen($utrNumber) < 10 || strlen($utrNumber) > 20) {
            $_SESSION['error'] = "UTR Number should be of length greater than 10 and less than 20.";
            header("Location: rtgs.php");
            exit();
        }

        $to = 'shreyansjain68@gmail.com'; // Real email - feepayment@rainbowschooljp.com
        $subject = 'Mail Regarding Fee Payment';
        $body = "
            UTR Number - $utrNumber\n
            Student Admission Number - $_SESSION['data']['admission_no']\n
            Student Name - $_SESSION['data']['name']\n
            Total Fees - $_SESSION['feeData']['totalFee']
        ";

        $headers = "From: Payment Rainbow English School <payment-no-reply@rainbowschooljp.com>\r\n";
        if (mail('feepayment@rainbowschooljp.com', $subject, $body, implode("\r\n", $headers))) {
            $_SESSION['success'] = "Your Response has been sent and recorded";
            header('Location: rtgs.php');
            exit();
        } else {
            $errorMessage = error_get_last()['message'];
            $_SESSION['error'] = $errorMessage;
            header("Location: rtgs.php");
            exit();
        }
    }

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

        <section id="details" class="container mt-2">
            <h1 class="text-center m-0">
                <u>Payment using RTGS/NEFT</u>
            </h1>

            <div class="row d-flex justify-content-center mt-4">
                <div class="col-md-8">
                    <h2 class="text-center text-danger font-weight-bold">Payment Details</h2>
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                        <div class="form-group">
                            <label for="email" class="col-form-label">
                                Email Address&nbsp;<span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="utr_number" class="col-form-label">
                                RTGS/NEFT <b>UTR Number</b> after payment from your bank&nbsp;<span class="text-danger">*</span>
                            </label>
                            <input type="text" name="utr_number" class="form-control" id="utr_number" maxlength="20" minlength="10" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-block">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-4 d-flex justify-content-center">
                <div class="col-md-8">
                    <h4 class="text-danger mb-2">STEPS FOR NEFT/RTGS PAYMENT :-</h3>
                    <ul class="list-group">
                        <li class="list-group-item"><b>1.</b> Go to your Internet Banking/Bank. Initiate the RTGS/NEFT.</li>
                        <li class="list-group-item"><b>2.</b> Press Enter the UTR/Transaction Reference Number and Click Submit.</li>
                        <li class="list-group-item"><b>3.</b> School Generated Fee Receipt will be handed over to the child in 5 working days.</li>
                        <li class="list-group-item"><b>4.</b> Online Data will be updated by month end.</li>
                    </ul>
                </div>
            </div>

            <div class="row mt-4 d-flex justify-content-center">
                <div class="col-md-8">
                    <h4 class="text-danger mb-2">ACCOUNT DETAILS :-</h3>
                    <ul class="list-group">
                        <li class="list-group-item"><b>Bank Name</b> - Bank Of Baroda</li>
                        <li class="list-group-item"><b>Address</b> - B1, Janakpuri, New Delhi</li>
                        <li class="list-group-item"><b>IFSC Code</b> - BARB0JANAKP</li>
                        <li class="list-group-item"><b>Account Number</b> - 12870100015439</li>
                        <li class="list-group-item"><b>Account Name</b> - Rainbow English School</li>
                        <li class="list-group-item"><b>Branch Name</b> - B1 Janakpuri, New Delhi</li>
                    </ul>
                </div>
            </div>
        </section>
<?php require_once(__DIR__.'/../footer.php'); ?>
