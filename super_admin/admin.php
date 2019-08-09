<?php
    /**
     * This page is used to view a single user
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../helpers.php');
    require(__DIR__ . '/../db/db.connection.php');
    session_start();

    // logs out user if it's not a admin
    if ($_SESSION['role'] !== 'super_admin') {
        header('Location: ../404.html');
        exit();
    }

    /**
     * Get a single user from `teacher` table with role not assigned "teacher" and "super_admin"
     *
     * @param PDOObject $PDO
     * @param Number $id
     *
     * @return Teacher $data
     *
     * @throws Exception // No Specefic Exception Defined
     *
    */
    function getUserData($PDO, $id) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `teacher` WHERE `id` = :id AND `role` NOT IN ('teacher', 'super_admin')");
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
     * Change Password of a single user from `teacher` table with role not assigned "teacher" and "super_admin"
     *
     * @param PDOObject $PDO
     * @param String $newPass
     * @param Number $id
     *
     * @return Teacher $data
     *
     * @throws Exception // No Specefic Exception Defined
     *
    */
    function changePassword($PDO, $newPass, $id) {
        $hashedPass = hash('sha256', $newPass);
        try {
            $stmt = $PDO->prepare("UPDATE `teacher` SET `password` = :pass WHERE `id` = :id AND `role` NOT IN ('teacher', 'super_admin')");
            $stmt->execute([':pass' => $hashedPass, ':id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    /**
     * Update Information of a single user from `teacher` table with role not assigned "teacher" and "super_admin"
     *
     * @param PDOObject $PDO
     * @param String $name
     * @param String $email
     * @param Number $id
     *
     * @return Teacher $data
     *
     * @throws Exception // No Specefic Exception Defined
     *
    */
    function updateInformation($PDO, $name, $email, $id) {
        try {
            $stmt = $PDO->prepare("UPDATE `teacher` SET `name` = :name, `email_address` = :email WHERE `id` = :id AND `role` NOT IN ('teacher', 'super_admin')");
            $stmt->execute([':name' => $name, ':email' => $email,':id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }
    
    // checks for logout variable in GET Request and if it's true logs out user
    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
            exit();
        }
    }

    if (isset($_POST['changePass'])) {
        // check for empty fields
        if (empty($_POST['new_pass']) || empty($_POST['confirm_pass']) || empty($_POST['id'])) {
            $_SESSION['error'] = 'Please Enter the required fields to Update the password.';
            header('Location: admin.php?id=' . $_SESSION['teacher_id']);
            return;
        }

        $id = $_POST['id'];
        $newPass = $_POST['new_pass'];
        $confirmPass = $_POST['confirm_pass'];

        // if id doesn't match
        if ($_SESSION['teacher_id'] != $id) {
            $_SESSION['error'] = 'id is wrong';
            header('Location: admin.php?id=' . $_SESSION['teacher_id']);
            return;
        }

        // if new and confirm passwords do not match
        if ($confirmPass !== $newPass) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: admin.php?id=' . $_SESSION['teacher_id']);
            return;
        }

        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }

        if (changePassword($PDO, $newPass, $id) !== NULL) {
            $_SESSION['success'] = "Successfully Updated the Password";
            header('Location: admin.php?id=' . $_SESSION['teacher_id']);
            return;
        } else {
            $_SESSION['error'] = "Something went wrong";
            header('Location: admin.php?id=' . $_SESSION['teacher_id']);
            return;
        }
    }

    if (isset($_POST['changeInfo'])) {
        // check for required fields
        if (empty($_POST['full_name']) || empty($_POST['email']) || empty($_POST['id'])) {
            $_SESSION['error'] = 'Please Enter the required fields to Update the Information.';
            header('Location: admin.php?id=' . $_SESSION['teacher_id']);
            return;
        }

        $id = $_POST['id'];
        $name = $_POST['full_name'];
        $email = $_POST['email'];

        // if id doesn't match
        if ($_SESSION['teacher_id'] != $id) {
            $_SESSION['error'] = 'Teacher Id is invalid';
            header('Location: admin.php?id=' . $_SESSION['teacher_id']);
            return;
        }

        // validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email Address is invalid';
            header('Location: admin.php?id=' . $_SESSION['teacher_id']);            
            return; 
        }

        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }

        if (updateInformation($PDO, $name, $email, $id) !== NULL) {
            $_SESSION['success'] = "Successfully updated the Information";
            header('Location: admin.php?id=' . $_SESSION['teacher_id']);            
            return;
        } else {
            $_SESSION['error'] = "Something went wrong";
            header('Location: admin.php?id=' . $_SESSION['teacher_id']);            
            return;
        }

    }

    $teacherData = NULL;
    if ($_GET['id']) {
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $teacherData = getUserData($PDO, $_GET['id']);
        if (is_null($teacherData)) {
            header('Location: view_admins.php');
            exit();
        }
        $_SESSION['teacher_id'] = $teacherData['id'];
        unset($PDO);
    } else {
        header("Location: index.php");
    }

?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container">
                <a href="#" class="navbar-brand">Super Admin Panel</a>
                <button class="navbar-toggler" data-toggle="collapse" data-target="#teacher-nav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="teacher-nav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/" class="nav-link">
                                View Fee
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/teacher_logs.php" class="nav-link">
                                View Teacher Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/maintenance_logs.php" class="nav-link">
                                View Maintenance Logs
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?php echo $base_url ?>super_admin/view_admins.php" class="nav-link">
                                View Admins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/create_admins.php" class="nav-link">
                                Create Admins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a 
                                href="javascript:{document.getElementById('logout').submit()}" 
                                class="nav-link ml-2 btn btn-primary text-white px-4"
                            >
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
        
        <section id="error" class="mt-4">
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
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="infoForm" method="POST">
                        <input type="hidden" name="changeInfo" value="1">
                        <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                        <div class="form-group">
                            <label for="name" class="col-form-label">
                                Full Name<span class="text-danger">*</span>
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
                                <button type="button" id="changePass" class="btn btn-info btn-block" data-toggle="modal" data-target="#changePasswordModal">Change Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <div class="modal fade" id="changePasswordModal" tabindex="-1" style="z-index: 99999999;" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <?php if ($teacherData['name']): ?>
                            <h5 class="modal-title">Change Password of <?php echo $teacherData['name'] ?></h5>
                        <?php else: ?>
                            <h4 class="modal-title">Change Password of Teacher</h4>
                        <?php endif ?>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="changePass" value="1">
                            <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                            <div class="form-group">
                                <label for="new password" class="col-form-label">
                                    New Password<span class="text-danger">*</span>
                                </label>
                                <input type="password" name="new_pass" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_pass" class="col-form-label">
                                    Confirm New Password<span class="text-danger">*</span>
                                </label>
                                <input type="password" name="confirm_pass" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php require_once(__DIR__.'/../footer.php'); ?>