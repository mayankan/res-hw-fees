<?php
    
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

    function addToLog($PDO, $message, $teacherId, $message_id=false) {
        if (!$message_id) {
            $stmt = $PDO->prepare("
                                INSERT INTO `log` (`log_action`, `teacher_id`) VALUES (:log_action, :teacher_id);
                            ");
            try {
                $stmt->execute([
                                ':log_action' => $message, ':teacher_id' => $teacherId
                            ]);
            } catch (Exception $e) {
                return false;
            }
            return true;
        } else {
            $stmt = $PDO->prepare("
                INSERT INTO `log` (`log_action`, `message_id` ,`teacher_id`) VALUES (:log_action, :message_id, :teacher_id);
            ");
            try {
            $stmt->execute([
                            ':log_action' => $message, 'message_id' => $message_id ,':teacher_id' => $teacherId
                        ]);
            } catch (Exception $e) {
                return false;
            }
            return true;   
        }
    }

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

    function getStudent($PDO, $studentId) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `student` WHERE `id` = :id");
            $stmt->execute([':id' => $studentId]);
            if ($stmt->rowCount() == 0) {
                return false;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

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

?>