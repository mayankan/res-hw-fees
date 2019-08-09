<?php
    require(__DIR__.'/../db/db.connection.php');
    $msdb = getMSSQLCon();
    if ($msdb === NULL) {
        die("Can't connect to the MS SQL db");
    }

    $mysqldb = getConnection();
    if ($mysqldb === NULL) {
        die("Can't connect to the main database");
    }

    $mysqldb->query("SET FOREIGN_KEY_CHECKS = 0;");
    $mysqldb->query("TRUNCATE table class;");
    $mysqldb->query("SET FOREIGN_KEY_CHECKS = 1;");

    $mysqldb->query("SET FOREIGN_KEY_CHECKS = 0;");
    $mysqldb->query("TRUNCATE table student;");
    $mysqldb->query("SET FOREIGN_KEY_CHECKS = 1;");

    function insertClass($PDO, $id, $class_name, $section, $dateCreated, $dateModified) {
        try {
            $stmt = $PDO->prepare("
                                INSERT INTO `class` (`id`, `class_name`, `section`, `date_created`, `date_modified`)
                                VALUES (:id, :class_name, :section, :date_created, :date_modified)
                                ");
            $stmt->execute([
                ':id' => $id,
                ':class_name' => $class_name,
                ':section' => $section,
                ':date_created' => $dateCreated,
                ':date_modified' => $dateModified
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

    function insertStudent($PDO, $id, $admission_no, $name, $father_name, $mother_name,
                            $dob, $gender, $address, $mobile_number, $class_id,
                            $date_created, $date_modified) {
        try {
            $stmt = $PDO->prepare("
                                INSERT INTO `student` 
                                (`id`, `admission_no`, `name`, `father_name`, `mother_name`,
                                `dob`, `gender`, `address`, `mobile_number`, `class_id`, `date_created`, `date_modified`)
                                VALUES (:id, :admission_no, :name, :father_name, :mother_name,
                                :dob, :gender, :address, :mobile_number, :class_id, :date_created, :date_modified);
                    ");
            $stmt->execute([
                ':id' => $id,
                ':admission_no' => $admission_no,
                ':name' => $name,
                ':father_name' => $father_name,
                ':mother_name' => $mother_name,
                ':dob' => $dob,
                ':address' => $address,
                ':gender' => $gender,
                ':mobile_number' => $mobile_number,
                ':class_id' => $class_id,
                ':date_created' => $date_created,
                ':date_modified' => $date_modified
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

    $stmt = $msdb->prepare('SELECT * FROM dbo.Class WHERE IsDeleted = 0 AND SessionId = 2');
    $stmt->execute();
    $originalClassData = $stmt->fetchAll();
    foreach ($originalClassData as $class) {
        $originalStudentData = NULL;
        if (insertClass(
                $mysqldb, 
                $class['Id'], 
                $class['Class'], 
                $class['Section'], 
                $class['DateCreated'], 
                $class['DateModified'])
        ) {
            try {
                $stmt = $msdb->prepare('SELECT * FROM dbo.Student WHERE StudentCategoryId != 6 AND IsDeleted = 0 AND ClassId = :id');
                $stmt->execute([':id' => $class['Id']]);
                $originalStudentData = $stmt->fetchAll();
            } catch (Exception $e) {
                die("Something went wrong");
            }
        } else {
            echo 'Something went wrong';
        }

        if ($originalStudentData !== NULL) {
            foreach ($originalStudentData as $student) {
                insertStudent(
                    $mysqldb, $student['Id'], $student['AdmissionNo'], $student['StudentName'],
                    $student['FatherName'], $student['MotherName'], $student['DOB'], $student['Gender'], $student['Address'],
                    $student['FatherMobileNumber'], $class['Id'], $student['DateCreated'], $student['DateModified']
                );
            }
        }
    }

    $_SESSION['success'] = 'Database has been successfully updated.';
    unset($msdb);
    unset($mysqldb);
    header('Location: index.php');
    exit();

?>
