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
                            ':log_action' => $message, 'message_id' => $message_id ,':teacher_id' => $teacherId, ':date_of_action' => date("Y/m/d h:i:s"), ':ip_address' => getRealIpAddr()
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

?>
