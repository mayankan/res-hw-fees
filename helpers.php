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

?>