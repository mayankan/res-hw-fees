<?php
    
    function addToLog($PDO, $message, $teacherId) {
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
    }

?>