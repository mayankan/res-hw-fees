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
        }
    }

    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't Connect to the database");
    }

    $maintenance = getLastMaintenance($PDO);
    $feeData = NULL;
    $currentMonthFeeData = NULL;
    $ifAllPaid = 0;
    $totalFeeData = 0;
    if (!is_null($maintenance)) {
        if ($maintenance['offline'] === 0) {
            $feeData = getFee($PDO, $_SESSION['data']['admission_no']);
            // idk why just assuming that feedata will not be NULL #badProgrammer

            $totalFeeData = count($feeData);
            // used to match for the current month and year
            $regex = "/^".date('Y').'-'.date('m').'-\d\d$/';
            for ($index = 0; $index < count($feeData); $index++) {
                if (!is_null($feeData[$index]['paid_at'])) {
                    $ifAllPaid += 1;
                    continue;
                }
                if (preg_match($regex, $feeData[$index]['month'])) {
                    $currentMonthFeeData = $feeData[$index];
                    array_splice($feeData, $index, 1);
                } else {
                    $totalDues += $feeData[$index]['total_fee'];
                }
            }
        }
    }

    if (!is_null($currentMonthFeeData)) {
        $currentDay = (int) date('d');
        if ($currentDay > 10) {
            $currentMonthFeeData['late_fee'] = 30;
            $_SESSION['late_fee'] = 30;
        }
        $currentMonthFeeData['total_fee'] = $currentMonthFeeData['late_fee'] + $currentMonthFeeData['total_fee'];
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $totalDues = 0;
        foreach ($feeData as $fee) {
            $totalDues += $fee['total_fee'];
        }
        $feeAmount = round(($currentMonthFeeData['total_fee'] + $totalDues)/ (1 - 1.18 * 1.9 / 100), 2);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.test.instamojo.com/api/1.1/payment-requests/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
                    array("X-Api-Key:test_7afc61bfde0049035de34445ae9",
                        "X-Auth-Token:test_f5bbfe4df819f8b14418d08496f"));
        $payload = Array(
            'purpose' => 'Online Fee Payment',
            'amount' => $feeAmount,
            'phone' => $_SESSION['data']['mobile_number'],
            'buyer_name' => $_SESSION['data']['name'],
            'redirect_url' => 'http://rainbowhomework.com/student/success.php',
            'webhook' => 'http://rainbowhomework.com/webhook.php',
            'email' => 'mail@rainbowschooljp.com',
            'send_email' => false,
            'send_sms' => true,
            'allow_repeated_payments' => false
        );
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch); 

        $response = json_decode($response, true);
        header('Location: ' . $response['payment_request']['longurl']);
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

        <?php if ($totalFeeData !== $ifAllPaid): ?>
        <section id="details" class="container mt-4">
            <h1 class="text-center m-0">
                <u>Fee Details</u>
            </h1>

            <?php if (!is_null($maintenance)): ?>
                <?php switch ($maintenance['offline']): case 0: ?>
                    <?php if (!is_null($feeData)): ?>
                    <div class="row mt-4 justify-content-center">
                        <?php foreach ($feeData as $fee): ?>
                            <div class="col-md-6">
                                <table class="table table-responsive">
                                    <thead>
                                        <th class="border">
                                            <?php echo getYearAndMonth($fee['month']) ?>
                                        </th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border">
                                                &#8377;&nbsp;<?php echo $fee['total_fee'] ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <?php if (!is_null($currentMonthFeeData)): ?>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <h3 class="text-center mb-4">
                                    <?php echo date('F Y'); ?>
                                </h3>
                                <ul class="list-group">
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Tuition Fee&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['tution_fee'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Examination Fee&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['examination_fee'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Refreshment Fee&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['refreshment_acc_fee'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Lab Fee&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['lab_fee'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Project Fee&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['project_fee'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Annual Charges&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['annual_charges'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Admin Charges&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['admin_charges'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Smart Classes Charges&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['smart_class_charges'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Computer Fee Yearly&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['computer_fee_yearly'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Computer Fee Monthly&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['computer_fee_monthly'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Development Charges Yearly&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['development_charges_yearly'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Transport Fee&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['transport_fee']; ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Portal Charges&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['portal_charges'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-primary">
                                        <div class="row">
                                            <div class="col-6">
                                                Late Fee&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['late_fee'] ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-info mt-2">
                                        <div class="row">
                                            <div class="col-6">
                                                Total Fee&nbsp;-&nbsp;
                                            </div>
                                            <div class="col-6 d-flex justify-content-end">
                                                <?php $_SESSION['feeData']['totalFee'] = $currentMonthFeeData['total_fee']; ?>
                                                &#8377;&nbsp;<?php echo $currentMonthFeeData['total_fee'] ?>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <!-- NOTE -->
                                <h3 class="mt-4">NOTE : </h3>
                                <p class="font-weight-bold"><?php echo nl2br($maintenance['bottom_message']); ?></p>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                    <button type="submit" class="btn btn-info btn-block">Pay Fee Online</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <a href="<?php echo $base_url ?>student/rtgs.php" class="btn btn-info btn-block">Pay Through RTGS/NEFT</a>
                            </div>
                        </div>
                        <?php endif ?>
                    <?php endif ?>
                <?php break; case 1: ?>
                <div class="row m-4 p-4">
                    <div class="col-12">
                        <h1 class="text-center font-weight-bold">Rainbow Online Fees Submission is down for maintenance.</h1>
                        <br>
                        <h2 class="text-center font-weight-bold">Please Check back again soon.</h2>
                    </div>
                </div>
                <?php break; case -1: ?>
                <div class="row m-4 p-4">
                    <div class="col-12">
                        <h1 class="text-center font-weight-bold">
                            <?php echo htmlentities($maintenance['custom_message']); ?>
                        </h1>
                    </div>
                </div>
                <?php endswitch ?>
            <?php endif ?>
        </section>
        <?php else: ?>
            <?php switch ($maintenance['offline']): case 0: ?>
            <div class="m-4">
                <h1 class="text-center">Your Fees is all paid.</h1>
            </div>
            <?php break; case 1: ?>
            <div class="row m-4 p-4">
                <div class="col-12">
                    <h1 class="text-center font-weight-bold">Rainbow Online Fees Submission is down for maintenance.</h1>
                    <br>
                    <h2 class="text-center font-weight-bold">Please Check back again soon.</h2>
                </div>
            </div>
            <?php break; case -1: ?>
            <div class="row m-4 p-4">
                <div class="col-12">
                    <h1 class="text-center font-weight-bold">
                        <?php echo htmlentities($maintenance['custom_message']); ?>
                    </h1>
                </div>
            </div>
            <?php endswitch ?>
        <?php endif ?>
<?php require_once(__DIR__.'/../footer.php'); ?>
