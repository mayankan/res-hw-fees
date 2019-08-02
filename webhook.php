<?php
    require(__DIR__.'/helpers.php');

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        /*
        Basic PHP script to handle Instamojo RAP webhook.
        */
        $data = $_POST;
        $mac_provided = $data['mac'];  // Get the MAC from the POST data
        unset($data['mac']);  // Remove the MAC key from the data.
        $ver = explode('.', phpversion());
        $major = (int) $ver[0];
        $minor = (int) $ver[1];
        if ($major >= 5 and $minor >= 4) {
            ksort($data, SORT_STRING | SORT_FLAG_CASE);
        } else {
            uksort($data, 'strcasecmp');
        }
        // You can get the 'salt' from Instamojo's developers page(make sure to log in first): https://www.instamojo.com/developers
        // Pass the 'salt' without <>
        $mac_calculated = hash_hmac("sha1", implode("|", $data), "846ae3004a174e1894893a19e6dd304a");
        if ($mac_provided == $mac_calculated) {
            http_response_code(300);
            if ($data['status'] == "Credit") {
                // Payment was successful, mark it as successful in your database.
                // You can acess payment_request_id, purpose etc here.
                $PDO = getConnection();
                $studentData = getAdmissionNumber($PDO, $data['buyer_phone'], $data['buyer_name']);
                if (is_null($studentData)) {
                    http_response_code(400);
                }

                if (!markPaidFee($PDO, $studentData['admission_no'])) {
                    http_response_code(400);
                }
                http_response_code(200);
            } else {
                // Payment was unsuccessful, mark it as failed in your database.
                // You can acess payment_request_id, purpose etc here.
                http_response_code(200);
            }
        } else {
            echo "MAC mismatch";
            http_response_code(200);
        }
    } else {
	http_response_code(404);
        header("Location: 404.html");
        exit();
    }
?>
