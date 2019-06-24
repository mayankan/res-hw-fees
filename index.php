<?php
    require(__DIR__.'/helpers.php');
    require(__DIR__.'/db/db.connection.php');
    session_start();

    // if session already exists redirect
    if (isset($_SESSION['role'])) {
        header("Location: " . $_SESSION['role'] . "/");
        exit();
    }

    /**
     * Login via Teacher Table's Username and Password
     *
     * @param String $username 
     * @param String $password 
     * @param PDOObject $PDO
     *
     * @return Teacher $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function checkTeacher($username, $password, $PDO) {
        $password_hash = hash('sha256', $password);
        try {
            $stmt = $PDO->prepare("SELECT * FROM `teacher` WHERE `username` = :username AND `date_deleted` IS NULL");
            $stmt->execute([':username' => $username]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            $data = $stmt->fetch();
            if (hash_equals($password_hash, $data['password'])) {
                return $data;
            } else {
                return NULL;
            }
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * Login via Students Table's Username and Password
     *
     * @param String $username 
     * @param String $password 
     * @param PDOObject $PDO
     *
     * @return Student $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function checkStudent($username, $password, $PDO) {
        try {
            // Get the mobile number by spliting the string from last 10 digits
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

    // checks if the request method is post to initiate the login process
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // to check if there are values inside $_POST variable
        if (isset($_POST)) {
            // get database connection
            $PDO = getConnection();
            if (is_null($PDO)) {
                die("Can't connect to database");
            }
            // check if it is the teacher that is trying to login else it is a student
            $teacherData = checkTeacher($_POST['username'], $_POST['password'], $PDO);
            if ($teacherData != NULL) {
                switch($teacherData['role']) {
                    case 'teacher':
                        $_SESSION['role'] = 'teacher';
                        $_SESSION['data'] = $teacherData;
                        addToLog($PDO, 'Teacher Logged in', $_SESSION['data']['id']);
                        header('Location: teacher/');
                        break;
                    case 'super_admin':
                        $_SESSION['role'] = 'super_admin';
                        $_SESSION['data'] = $teacherData;
                        header('Location: super_admin/');
                        break;
                    case 'fee_clerk':
                        $_SESSION['role'] = 'fee_clerk';
                        $_SESSION['data'] = $teacherData;
                        header('Location: fee_clerk/');
                        break;
                    case 'admin':
                        $_SESSION['role'] = 'admin';
                        $_SESSION['data'] = $teacherData;
                        header('Location: admin/');
                        break;
                }
                $PDO = null; // remove this if want global db connection
                exit();
            } else {
                // checks for the student else username or password is wrong
                // no specific checking for username and password
                $studentData = checkStudent($_POST['username'], $_POST['password'], $PDO);
                if ($studentData != NULL) {
                    $_SESSION['role'] = 'student';
                    $_SESSION['data'] = $studentData;
                    header('Location: student/');
                    $PDO = null; // remove this if want global db connection
                    exit();
                } 
                else {
                    $_SESSION['error'] = "Invalid username or password";
                    header('Location: index.php');
                    $PDO = null; // remove this if want global db connection
                    exit();
                }
            }
        }
    }
    
?>
<?php require_once(__DIR__.'/header.php'); ?>
		<br />
		<br />
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-6">
                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong><?php echo $_SESSION['error'] ?></strong>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
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
		<br />
		<br />
<?php require_once(__DIR__.'/footer.php'); ?>