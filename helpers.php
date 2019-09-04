<?php
    
    /**
     * Get last row from the table specified
     * DEPRECATED (not sure)
     *
     * @param PDOObject $PDO
     * @param String $tableName 
     *
     * @return Table $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function getLastRow($PDO, $tableName) {
        try {
            $stmt = $PDO->prepare("
                                    SELECT * FROM `$tableName` ORDER BY `id` LIMIT 1;
                                ");
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get Clients Real IP Address
     *
     * @return String $ip
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function getRealIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Add to the `log` Table in the database
     *
     * @param PDOObject $PDO
     * @param String $message - message of the log
     * @param Number $teacherId
     * @param Number $message_id - ID for the homework if it's attached
     *
     * @return Table $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function addToLog($PDO, $message, $teacherId, $message_id=false) {
        if (!$message_id) {
            $stmt = $PDO->prepare("
                                INSERT INTO `log` (`log_action`, `teacher_id`, `ip_address`, `date_of_action`) VALUES (:log_action, :teacher_id, :ip_address, :date_of_action);
                            ");
            try {
                $stmt->execute([
                                ':log_action' => $message, ':teacher_id' => $teacherId, ':date_of_action' => date("Y/m/d h:i:s"), ':ip_address' => getRealIpAddr()
                            ]);
            } catch (Exception $e) {
                return false;
            }
            return true;
        } else {
            $stmt = $PDO->prepare("
                INSERT INTO `log` (`log_action`, `message_id` ,`teacher_id`, `ip_address`, `date_of_action`) VALUES (:log_action, :message_id, :teacher_id, :ip_address, :date_of_action);
            ");
            try {
            $stmt->execute([
                            ':log_action' => $message, 'message_id' => $message_id ,':teacher_id' => $teacherId, 
                            ':date_of_action' => date("Y/m/d h:i:s"), ':ip_address' => getRealIpAddr()
                        ]);
            } catch (Exception $e) {
                return false;
            }
            return true;   
        }
    }

    /**
     * Get all logs from `log` table
     *
     * @param PDOObject $PDO
     *
     * @return Table $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function getAllLogs($PDO) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `log`");
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * Get a homework data
     * IF IT's NOT DELETED
     *
     * @param PDOObject $PDO
     * @param Number $id - homework ID
     *
     * @return Table $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function getHomework($PDO, $id) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `message` WHERE id = :id AND date_deleted IS NULL");
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * Get a homework data
     * WHETHER DELETED or NOT
     *
     * @param PDOObject $PDO
     * @param Number $id - homework ID
     *
     * @return Table $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function getAllHomework($PDO, $id) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `message` WHERE id = :id");
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * Get a student data
     *
     * @param PDOObject $PDO
     * @param Number $id - student ID
     *
     * @return Table $data
     *
     * @throws Exception //No Specefic Exception Defined
    */
    function getStudent($PDO, $studentId, $admissionNumber=NULL) {
        $sql = "";
        $data = [];
        if ($studentId !== NULL && $admissionNumber === NULL) {
            $sql = "SELECT * FROM `student` WHERE `id` = :id";
            $data = [':id' => $studentId];
        } else if ($studentId === NULL && $admissionNumber !== NULL) {
            $sql = "SELECT * FROM `student` WHERE `admission_no` = :adm_no";
            $data = [':adm_no' => $admissionNumber];
        }
        try {
            $stmt = $PDO->prepare($sql);
            $stmt->execute($data);
            if ($stmt->rowCount() == 0) {
                return false;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    /**
     * Get a student data
     *
     * @param PDOObject $PDO
     * @param Number $mobileNumber
     * @param String $name
     *
     * @return Student $data
     *
     * @throws Exception //No Specefic Exception Defined
    */
    function getAdmissionNumber($PDO, $mobileNumber, $name) {
        try {
            $stmt = $PDO->prepare(
                "SELECT * FROM `student` WHERE `mobile_number` = :mobile_no AND `name` = :name"
            );
            $stmt->execute([
                ':mobile_no' => $mobileNumber,
                ':name' => $name
            ]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * Get a class data
     *
     * @param PDOObject $PDO
     * @param Number $id - class ID
     *
     * @return Table $data
     *
     * @throws Exception //No Specefic Exception Defined
    */
    function getClass($PDO, $classId) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `class` WHERE `id` = :id");
            $stmt->execute([':id' => $classId]);
            if ($stmt->rowCount() == 0) {
                return false;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    /**
     * Get a teacher name
     *
     * @param PDOObject $PDO
     * @param Number $id - teacher ID
     *
     * @return Table $data
     *
     * @throws Exception //No Specefic Exception Defined
    */
    function getTeacherName($PDO, $teacherId) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `teacher` WHERE `id` = :id");
            $stmt->execute([':id' => $teacherId]);
            return $stmt->fetch()['name'];
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * Get all classes data
     *
     * @param PDOObject $PDO
     *
     * @return Table $data
     *
     * @throws Exception //No Specefic Exception Defined
    */
    function getAllClasses($PDO) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `class`");
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * Fetches data from `fee` table on the basis of admission number
     * with month and year provided
     *
     * @param PDOObject $PDO
     * @param Number $admissionNumber
     * @param Number $month
     * @param Number $year
     *
     * @return Fee $data
     *
     * @throws Exception //No Specefic Exception Defined
    */
    function getFee($PDO, $admissionNumber, $month='__', $year='____') {
        try {
            $stmt = $PDO->prepare(
                        "SELECT * FROM `fee` WHERE `admission_no` = :adm_no AND `month` LIKE :month AND `deleted_at` IS NULL;"
                    );
            $stmt->execute(
                [
                    ':adm_no' => $admissionNumber,
                    ':month' => $year . '-' . $month . '-__'
                ]
            );
            if ($stmt->rowCount() == 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    function deleteFee($PDO, $month, $year) {
        try {
            $stmt = $PDO->prepare(
                "UPDATE `fee` SET `deleted_at` = :current_date WHERE `month` LIKE :month_and_year"
            );
            $stmt->execute([
                ':current_date' => (string) date('Y-m-d h:i:s'),
                ':month_and_year' => $year . '-' . $month . '-__'
            ]);
            if ($stmt->rowCount() == 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * inserts a fee row in `fee` Table
     *
     * @param PDOObject $PDO
     * @param Number $admissionNumber
     * @param Number $month
     * @param Number $year
     *
     * @return Boolean
     *
     * @throws Exception //No Specefic Exception Defined
    */
    function insertFee(
        $PDO, $admissionNumber, $month, $year,
        $portalCharge, $examinationFee, $tutionFee, $refreshmentAccFee,
        $labFee, $projectFee, $annualCharges, $adminCharges,
        $smartClassCharges, $computerFeeYearly, $computerFeeMonthly,
        $developmentChargesYearly, $transportFee, $lateFee, $totalFee,
        $studentId
    ) {
        $sql = "INSERT INTO `fee` (
                    `admission_no`, `month`, `portal_charges`, `examination_fee`, `tution_fee`, `refreshment_acc_fee`,
                    `lab_fee`, `project_fee`, `annual_charges`, `admin_charges`, `smart_class_charges`, `computer_fee_yearly`,
                    `computer_fee_monthly`, `development_charges_yearly`, `transport_fee`, `late_fee`, `total_fee`, `student_id`,
                    `date_updated`, `date_created`
                ) VALUES (
                    :adm_no, :month, :portal_charges, :examination_fee, :tution_fee, :refreshment_acc_fee, :lab_fee, 
                    :project_fee, :annual_charges, :admin_charges, :smart_class_charges, :computer_fee_yearly,
                    :computer_fee_monthly, :development_charges_yearly, :transport_fee, :late_fee, :total_fee, :student_id,
                    :date_updated, :date_created
                );";
        $data = [
            ':adm_no' => $admissionNumber,
            ':month' => $year . '-' . $month . '-01',
            ':portal_charges' => $portalCharge,
            ':examination_fee' => $examinationFee,
            ':tution_fee' => $tutionFee,
            ':refreshment_acc_fee' => $refreshmentAccFee,
            ':lab_fee' => $labFee,
            ':project_fee' => $projectFee,
            ':annual_charges' => $annualCharges,
            ':admin_charges' => $adminCharges,
            ':smart_class_charges' => $smartClassCharges,
            ':computer_fee_yearly' => $computerFeeYearly,
            ':computer_fee_monthly' => $computerFeeMonthly,
            ':development_charges_yearly' => $developmentChargesYearly,
            ':transport_fee' => $transportFee,
            ':late_fee' => $lateFee,
            ':total_fee' => $totalFee,
            ':student_id' => $studentId,
            ':date_updated' => date('Y-m-d h:i:s'),
            ':date_created' => date('Y-m-d h:i:s')
        ];

        try {
            $stmt = $PDO->prepare($sql);
            $stmt->execute($data);
            if ($stmt->rowCount() === 0) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    /**
     * updates a fee row in `fee` Table
     *
     * @param PDOObject $PDO
     * @param Number $admissionNumber
     * @param Number $month
     * @param Number $year
     *
     * @return Boolean
     *
     * @throws Exception //No Specefic Exception Defined
    */
    function updateFee(
        $PDO, $admissionNumber, $month, $year,
        $portalCharge, $examinationFee, $tutionFee, $refreshmentAccFee,
        $labFee, $projectFee, $annualCharges, $adminCharges,
        $smartClassCharges, $computerFeeYearly, $computerFeeMonthly,
        $developmentChargesYearly, $transportFee, $lateFee, $totalFee,
        $studentId
    ) {
        $sql = "UPDATE `fee` SET
                    `month` = :month,
                    `portal_charges` = :portal_charges,
                    `examination_fee` = :examination_fee,
                    `tution_fee` = :tution_fee,
                    `refreshment_acc_fee` = :refreshment_acc_fee,
                    `lab_fee` = :lab_fee,
                    `project_fee` = :project_fee,
                    `annual_charges` = :annual_charges,
                    `admin_charges` = :admin_charges,
                    `smart_class_charges` = :smart_class_charges,
                    `computer_fee_yearly` = :computer_fee_yearly,
                    `computer_fee_monthly` = :computer_fee_monthly,
                    `development_charges_yearly` = :development_charges_yearly,
                    `transport_fee` = :transport_fee,
                    `late_fee` = :late_fee,
                    `total_fee` = :total_fee,
                    `date_updated` = :date_updated
                WHERE
                    `student_id` = :student_id AND `admission_no` = :adm_no AND `deleted_at` IS NULL
                ;";
        $data = [
            ':adm_no' => $admissionNumber,
            ':month' => $year . '-' . $month . '-01',
            ':portal_charges' => $portalCharge,
            ':examination_fee' => $examinationFee,
            ':tution_fee' => $tutionFee,
            ':refreshment_acc_fee' => $refreshmentAccFee,
            ':lab_fee' => $labFee,
            ':project_fee' => $projectFee,
            ':annual_charges' => $annualCharges,
            ':admin_charges' => $adminCharges,
            ':smart_class_charges' => $smartClassCharges,
            ':computer_fee_yearly' => $computerFeeYearly,
            ':computer_fee_monthly' => $computerFeeMonthly,
            ':development_charges_yearly' => $developmentChargesYearly,
            ':transport_fee' => $transportFee,
            ':late_fee' => $lateFee,
            ':total_fee' => $totalFee,
            ':student_id' => $studentId,
            ':date_updated' => date('Y-m-d h:i:s')
        ];

        try {
            $stmt = $PDO->prepare($sql);
            $stmt->execute($data);
            if ($stmt->rowCount() === 0) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    /**
     * updates a fee row in `fee` Table
     *
     * @param PDOObject $PDO
     * @param Number $admissionNumber
     *
     * @return Boolean
     *
     * @throws Exception //No Specefic Exception Defined
    */
    function markPaidFee($PDO, $admissionNumber, $lateFee, $totalFee, $datePaid, $monthPayment) {
        try {
            $monthPayment = substr($monthPayment,5,2);
            if($monthPayment === date('m')) {
                $totalFee = $totalFee+$lateFee;
                $stmt = $PDO->prepare("UPDATE `fee` SET `paid_at` = :current_date, `late_fee` = :late_fee, `total_fee` = :total_fee WHERE `admission_no` = :adm_no AND `month` LIKE :month AND `deleted_at` IS NULL;");
                $a = $stmt->execute([
                    ':current_date' => $datePaid,
                    ':late_fee' => $lateFee,
                    ':total_fee' => $totalFee,
                    ':adm_no' => $admissionNumber,
                    ':month' => $monthPayment
                ]);
                if ($a == 0) {
                    return false;
                }
                return true;
            }
            else {
                $stmt = $PDO->prepare("UPDATE `fee` SET `paid_at` = :current_date, `late_fee` = :late_fee, `total_fee` = :total_fee WHERE `admission_no` = :adm_no AND `month` LIKE :month AND `deleted_at` IS NULL;");
                $a = $stmt->execute([
                    ':current_date' => $datePaid,
                    ':late_fee' => $lateFee,
                    ':total_fee' => $totalFee,
                    ':adm_no' => $admissionNumber,
                    ':month' => $monthPayment
                ]);
                if ($a == 0) {
                    return false;
                }
                return true;
            }
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    /**
     * Get last updated maintenance row
     *
     * @param PDOObject $PDO
     *
     * @return Maintenance $data
     *
     * @throws Exception //No Specefic Exception Defined
    */
    function getLastMaintenance($PDO) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `maintenance_fee` ORDER BY `id` DESC LIMIT 1");
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * insert to update maintenance mode
     *
     * @param PDOObject $PDO
     * @param String maintenaceMode
     * @param String customMessage
     * @param String bottomMessage
     *
     * @return Boolean
     *
     * @throws Exception //No Specefic Exception Defined
    */
    function updateMaintenance($PDO, $maintenanceMode, $customMessage, $bottomMessage) {
        try {
            $stmt = $PDO->prepare(
                "INSERT INTO `maintenance_fee` (`bottom_message`, `offline`, `custom_message`, `date_created`, `date_updated`) VALUES (:bottom_message, :mode, :custom_message, :date_created, :date_updated)"
            );
            $stmt->execute([
                ':bottom_message' => $bottomMessage,
                ':mode' => $maintenanceMode,
                ':custom_message' => $customMessage,
                ':date_created' => date('Y-m-d h:i:s'),
                ':date_updated' => date('Y-m-d h:i:s'),
            ]);
            if ($stmt->rowCount() === 0) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    /**
     * Get logs from `maintenance_fee` table
     *
     * @param PDOObject $PDO
     * @param Number $start_limit
     *
     * @return Teacher $data
     *
     * @throws Exception // No Specefic Exception Defined
     *
    */
    function getMaintenanceLogs($PDO, $start_limit=0) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `maintenance_fee` LIMIT :start_limit,10");
            $stmt->execute([':start_limit' => $start_limit]);
            if ($stmt->rowCount() == 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }


    function getYearAndMonth($date) {
        $dateArray = explode('-', $date);
        return date('F', mktime(0, 0, 0, $dateArray[1], 0, 0)) . ' ' . $dateArray[0];
    }
?>
