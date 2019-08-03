<?php
    /**
     * This is page is used to view and update teacher profile
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../helpers.php');
    require(__DIR__ . '/../db/db.connection.php');
    session_start();

    /**
     * Get data for a single Teacher
     *
     * @param PDOObject $PDO
     * @param Number $id - Teacher Id
     *
     * @return Teacher $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function getTeacherData($PDO, $id) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `teacher` WHERE `id` = :id");
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() <= 0) {
                return NULL;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * Change password of the teacher
     *
     * @param PDOObject $PDO
     * @param String $newPass
     *
     * @return Teacher $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function changePassword($PDO, $newPass) {
        $hashedPass = hash('sha256', $newPass);
        try {
            $stmt = $PDO->prepare("UPDATE `teacher` SET `password` = :pass WHERE `id` = :id");
            $stmt->execute([':pass' => $hashedPass, ':id' => $_SESSION['data']['id']]);
            // Add To Log - Changed Password
            addToLog($PDO, 'Teacher Changed Password', $_SESSION['data']['id']);
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * Update name and email of the teacher
     *
     * @param PDOObject $PDO
     * @param String $name
     * @param String Email
     *
     * @return Teacher $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function updateInformation($PDO, $name, $email) {
        try {
            $stmt = $PDO->prepare("UPDATE `teacher` SET `name` = :name, `email_address` = :email WHERE `id` = :id");
            $stmt->execute([':name' => $name, ':email' => $email,':id' => $_SESSION['data']['id']]);
            // Add To Log - Updated Profile, Mention Old Name & Old Email
            addToLog($PDO, "Teacher Updated Profile from Name - {$_SESSION['data']['name']}, Email - {$_SESSION['data']['email_address']}", $_SESSION['data']['id']);
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    // logs out user if it's not a teacher
    if ($_SESSION['role'] !== 'teacher') {
        header('Location: ../404.html');
        exit();
    }

    // checks for logout variable in GET Request and if it's true logs out user
    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            $PDO = getConnection();
            if (is_null($PDO)) {
                die("Can't connect to database");
            }
            addToLog($PDO, 'Teacher Logged out', $_SESSION['data']['id']);
            session_destroy();
            unset($PDO);
            header('Location: ../');
            exit();
        }
    }

    // Global setter for teacher data
    $teacherData = NULL;
    if ($_SESSION['data']['id']) {
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $teacherData = getTeacherData($PDO, $_SESSION['data']['id']);
        $_SESSION['data'] = $teacherData;
        unset($PDO);
    }

    // change password form
    if (isset($_POST['changePass'])) {
        // check for required fields
        if (empty($_POST['old_pass']) || empty($_POST['new_pass']) || empty($_POST['confirm_pass'])) {
            $_SESSION['error'] = 'Please enter the required fields to change password.';
            header('Location: profile.php');
            return;
        }
        $newPass = $_POST['new_pass'];
        $oldPass = $_POST['old_pass'];
        $confirmPass = $_POST['confirm_pass'];

        // check for passwords
        if ($confirmPass !== $newPass) {
            $_SESSION['error'] = 'Passwords do not match! Please Try again.';
            header('Location: profile.php');
            return;
        }

        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $teacherData = getTeacherData($PDO, $_SESSION['data']['id']);
        $hashedPass = hash('sha256', $oldPass);
        // check if old password in the database is correctly provided by the teacher
        if (!hash_equals($hashedPass, $teacherData['password'])) {
            $_SESSION['error'] = 'Old Password is incorrect! Please Try again.';
            header('Location: profile.php');
            return;
        }

        if (changePassword($PDO, $newPass) !== NULL) {
            $_SESSION['success'] = "Successfully Updated the Password";
            header('Location: profile.php');
            return;
        } else {
            $_SESSION['error'] = "Something went wrong";
            header('Location: profile.php');
            return;
        }
        unset($PDO);
    }

    // update information form
    if (isset($_POST['changeInfo'])) {
        // check for the required fields
        if (empty($_POST['full_name']) || empty($_POST['email'])) {
            $_SESSION['error'] = 'Please enter the required fields to update the information.';
            header('Location: profile.php');
            return;
        }

        $name = $_POST['full_name'];
        $email = $_POST['email'];

        // validate email for errors
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please Enter valid Email Address.';
            header('Location: profile.php');
            return;
        }

        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }

        if (updateInformation($PDO, $name, $email) !== NULL) {
            $_SESSION['success'] = "Successfully Updated the Information";
            header('Location: profile.php');
            return;
        } else {
            $_SESSION['error'] = "Something went wrong";
            header('Location: profile.php');
            return;
        }
        unset($PDO);
    }

?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container">
                <a href="#" class="navbar-brand">Teacher Panel</a>
                <button class="navbar-toggler" data-toggle="collapse" data-target="#teacher-nav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="teacher-nav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>teacher/" class="nav-link">
                                Homeworks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>teacher/compose.php" class="nav-link">
                                Compose
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?php echo $base_url ?>teacher/profile.php" class="nav-link">
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:{document.getElementById('logout').submit()}" class="nav-link ml-2 btn btn-primary text-white px-4">
                                <i class="fa fa-sign-in mt-1" aria-hidden="true"></i> Logout
                            </a>
                            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="logout">
                                <input type="hidden" name="logout" value="true">
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <section id="error" class="mt-2">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong><?php echo $_SESSION['error'] ?></strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <strong><?php echo $_SESSION['success'] ?></strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </section>

        <section id="profile" class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <input type="hidden" name="changeInfo" value="1">
                        <div class="form-group">
                            <label for="name" class="col-form-label">
                                Full Name<span class="text-danger"></span>
                            </label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo $teacherData['name'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username" class="col-form-label">
                                Username<span class="text-danger">*</span>
                            </label>
                            <input disabled type="text" name="username" class="form-control" value="<?php echo $teacherData['username'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-form-label">
                                Email Address<span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control" value="<?php echo $teacherData['email_address'] ?>" required>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-block">Update Information</button>
                            </div>
                            <div class="col-md-6">
                                <button 
                                    type="button" 
                                    class="btn btn-info btn-block"
                                    data-target="#changePasswordModal"
                                    data-toggle="modal"
                                >
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <div class="modal fade" id="changePasswordModal" style="z-index: 99999;" role="dialog" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <?php if ($teacherData['name']): ?>
                            <h5 class="modal-title">Change Password of <?php echo $teacherData['name'] ?></h5>
                        <?php else: ?>
                            <h4 class="modal-title">Change Password of Teacher</h4>
                        <?php endif ?>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="changePass" value="1">
                            <div class="form-group">
                                <label for="old_pass" class="col-form-label">Old Password</label>
                                <input type="password" name="old_pass" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="new_pass" class="col-form-label">New Password</label>
                                <input type="password" name="new_pass" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_pass" class="col-form-label">Confirm New Password</label>
                                <input type="password" name="confirm_pass" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php require_once(__DIR__.'/../footer.php'); ?>
