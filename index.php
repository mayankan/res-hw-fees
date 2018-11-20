<?php

    session_start();
    if (isset($_SESSION['role'])) {
        header("Location: " . $_SESSION['role'] . "/");
    }

    function checkTeacher($username, $password, $PDO) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `teacher` WHERE `username` = :username AND `password` = :password AND `date_deleted` IS NULL");
            $stmt->execute([':username' => $username, ':password' => $password]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            } else {
                return $stmt->fetchAll();
            }
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    function checkStudent($username, $password, $PDO) {
        try {
            $pass = str_split($password);
            $admission_no = implode(array_slice($pass, 0, count($pass)-10));
            $mobile_no = implode(array_slice($pass, -10));
            if ($admission_no !== $username) {
                return NULL;
            }
            $stmt = $PDO->prepare("SELECT * FROM `student` WHERE `admission_no` = :username AND `mobile_number` = :mobile AND `date_deleted` IS NULL");
            $stmt->execute([':username' => $username, ':mobile' => $mobile_no]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            } else {
                return $stmt->fetch();
            }
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST)) {

            // check admin before to make things faster and secure
            if ($_POST['username'] === "admin" && $_POST['password'] === "rainbow@12345") {
                $_SESSION['role'] = 'admin';
                header('Location: admin/');
            }

            require(__DIR__ . '/db/db.connection.php');
            $PDO = getConnection();
            if (is_null($PDO)) {
                die("Can't connect to database");
            }


            $teacherData = checkTeacher($_POST['username'], $_POST['password'], $PDO);
            if ($teacherData != NULL) {
                $_SESSION['role'] = 'teacher';
                $_SESSION['data'] = $teacherData;
                $PDO = null;
                header('Location: teacher/');
            }
            else {

                $studentData = checkStudent($_POST['username'], $_POST['password'], $PDO);
                if ($studentData != NULL) {
                    $_SESSION['role'] = 'student';
                    $_SESSION['data'] = $studentData[0];
                    $PDO = null;
                    header('Location: student/');
                } 
                else {
                    $error = "Invalid username or password";
                }

            }
        }
    }
    
?>
<!-- Login page -->
<?php require_once(__DIR__.'/header.html'); ?>
        <title>Login</title>
        <style>
            .container, .row {
                height: 100vh;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-6">
                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong><?php echo $error ?></strong>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php endif ?> 
                    <h3 class="text-center pb-2">Enter your Login Credentials</h3>
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                        <div class="form-group">
                            <label for="username">Username/Admission number</label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-form-label">Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php require_once(__DIR__.'/footer.html'); ?>