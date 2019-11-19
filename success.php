<?php
    /**
     * This Page is used to show remaining fees to student
    */
    require(__DIR__.'/config.php');
    require(__DIR__.'/db/db.connection.php');
    require(__DIR__.'/helpers.php');
    session_start();

    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't Connect to the database");
    }

    $paymentId = isset($_GET['payment_id']) ? $_GET['payment_id'] : "";
    $paymentRequestId = isset($_GET['payment_request_id']) ? $_GET['payment_request_id'] : "";
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payment-requests/'.$paymentRequestId.'/'.$paymentId.'/');
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
                    array("X-Api-Key:74daa5061b049d6cdc8540a79cfd7a1a",
                        "X-Auth-Token:a1ff98eeb01b5358e479494464b62849"));
                    // array("X-Api-Key:test_7afc61bfde0049035de34445ae9",
                    //     "X-Auth-Token:test_f5bbfe4df819f8b14418d08496f"));
    $response = curl_exec($ch);
    curl_close($ch); 
    $pieces = explode(',', $response);
    foreach($pieces as $pair)
    {
        list($key, $value) = explode(': ', $pair);
        $variables = ['{','}','"'];
        $key = str_replace($variables,'',$key);
        $key = preg_replace('/\s+/', '', $key);
        $value = str_replace($variables,'',$value);
        $value = preg_replace('/\s+/', '', $value);
        $curlResponse[$key]= $value;
        if($key==='created_at') {
        }
    }
    $status = $curlResponse['success'];
    if ($status === 'true') {
        $studentAdmissionNo = getAdmissionNumber($PDO, substr($curlResponse['buyer_phone'], -10), $curlResponse['buyer_name']);
        if (is_null($studentAdmissionNo)) {
            header('Location: 404.html');
            exit();
        }
        $dateofpayment = substr($curlResponse['created_at'],0,10);
        $timeofpayment = substr($curlResponse['created_at'],11,8);
        $datetimeofpayment = $dateofpayment.' '.$timeofpayment;
        $currentDay = (int) substr($dateofpayment,-2);
        $lateFee = 0;
        if ($currentDay > 10) {
            $lateFee = 30;
        }
        $feeData = getFee($PDO, $studentAdmissionNo['admission_no']);
        foreach ($feeData as $fee) {
            $totalAmount += $fee['total_fee'];
        }
        foreach ($feeData as $fee) {
            $FeePaid = markPaidFee($PDO, $studentAdmissionNo['admission_no'], $lateFee, $fee['total_fee'], $datetimeofpayment , $fee['month']);
            if ($FeePaid===false) {
                header('Location: 404.html');
                exit();
            }
        }
    }
    else {
        header('Location: 404.html');
        exit();
    }
?>

<?php require_once(__DIR__.'/header.php'); ?>
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
            <?php if ($status === 'true'): ?>
                <h1 class="text-center">Your fee amounting to ₹ <?php echo $totalAmount ?> has been successfully deposited with Payment ID - <?php echo $paymentId ?>.</h1>
            <?php else: ?>
                <h1 class="text-center">Your fee payment has failed. <br>Kindly note down the following Payment ID for reference - <?php echo $paymentId ?>.</h1>
            <?php endif ?>
        </section>
<?php require_once(__DIR__.'/footer.php'); ?>