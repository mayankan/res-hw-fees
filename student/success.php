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

    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't Connect to the database");
    }
    

    $paymentId = isset($_GET['payment_id']) ? $_GET['payment_id'] : "";
    $paymentRequestId = isset($_GET['payment_request_id']) ? $_GET['payment_request_id'] : "";
    $status = isset($_GET['payment_status']) ? $_GET['payment_status'] : "";
    $totalAmount = 0;

    if ($paymentId === "" || $paymentRequestId === "" || $status === "") {
        header('Location: index.php');
        exit();
    }

    if ($status === 'Credit') {
        $feeData = getFee($PDO, $_SESSION['data']['admission_no']);
        foreach ($feeData as $fee) {
            $totalAmount += $fee['total_fee'];
        }
        $studentData = getAdmissionNumber($PDO, substr($data['buyer_phone'], -10), $data['buyer_name']);
        if (is_null($studentData)) {
            header('Location: ../404.html');
        }

        $currentDay = (int) date('d');
        $lateFee = 0;
        if ($currentDay > 10) {
            $lateFee = 30;
        }
        var_dump(!markPaidFee($PDO, $studentData['admission_no'], $lateFee, $_SESSION['total_fee']));
        exit();
        if (!markPaidFee($PDO, $studentData['admission_no'], $lateFee, $_SESSION['total_fee'])) {
            header('Location: ../404.html');
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

        <section id="payment-message" class="container-fluid mt-4">
            <?php if ($_GET['payment_status'] == 'Credit'): ?>
                <h1 class="text-center">Your fee amounting to ₹ <?php echo $totalAmount ?> has been successfully deposited with Payment ID - <?php echo $paymentId ?>.</h1>
            <?php else: ?>
                <h1 class="text-center">Your fee payment has failed. <br>Kindly notedown the following Payment ID for reference - <?php echo $paymentId ?>.</h1>
            <?php endif ?>
        </section>
<?php require_once(__DIR__.'/../footer.php'); ?>
