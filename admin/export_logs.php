<?php 
    /**
     * This page is used to export logs from `log` table
    */
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a admin
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ../404.html');
        return;
    }

    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't connect to the database");
    }

    /**
     * put `message` data in csv file and provide it as a response
     *
     * @param PDOObject $PDO
     * @param Array $array - Homework data array
     * @param String $filename - Filename for the export
     *
     * @return File
     *
     * @throws Exception // No Specefic Exception Defined
     *
    */
    function array_to_csv_download($PDO, $array, $filename = "export.csv") {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');

        $f = fopen('php://memory', 'w');
        fputcsv($f, array(
            "Date of Log",
            "Action",
            "Homework",
            "Date of Homework",
            "Student Sent To",
            "Teacher Assigned",
            "IP Address"
        ));

        foreach ($array as $log) {
            $date = date_create($log['date_of_action']);
            if (!is_null($log['message_id'])) {
                $homework = getAllHomework($PDO, $log['message_id']);
                $teacherName = getTeacherName($PDO, $log['teacher_id']);
                $student = "";
                if (!is_null($homework['student_id'])) {
                    $student = getStudent($PDO, $homework['student_id']);
                }
                $string = array(
                    date_format($date, 'd F Y'),
                    $log['log_action'],
                    trim(preg_replace('/\s+/', ' ', $homework['message'])),
                    $homework['date_of_message'],
                    $student,
                    $teacherName,
                    $log['ip_address']
                );
            } else {
                $teacherName = getTeacherName($PDO, $log['teacher_id']);
                $string = array(
                    date_format($date, 'd F Y'),
                    $log['log_action'],
                    "",
                    "",
                    "",
                    $teacherName,
                    $log['ip_address']
                );
            }
            fputcsv($f, $string);
        }
    }   

    array_to_csv_download($PDO, getAllLogs($PDO), 'logs.csv');
    unset($PDO);

?>