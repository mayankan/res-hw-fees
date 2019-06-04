<?php 
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../helpers.php');
    require(__DIR__ . '/../db/db.connection.php');
    session_start();

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

    function changePassword($PDO, $newPass, $id) {
        $hashedPass = hash('sha256', $newPass);
        try {
            $stmt = $PDO->prepare("UPDATE `teacher` SET `password` = :pass WHERE `id` = :id");
            $stmt->execute([':pass' => $hashedPass, ':id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    function updateInformation($PDO, $name, $email, $id) {
        try {
            $stmt = $PDO->prepare("UPDATE `teacher` SET `name` = :name, `email_address` = :email WHERE `id` = :id");
            $stmt->execute([':name' => $name, ':email' => $email,':id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }
    
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ../404.html');
    }

    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
        }
    }

    if (isset($_POST['changePass'])) {
        if (empty($_POST['new_pass']) || empty($_POST['confirm_pass']) || empty($_POST['id'])) {
            $_SESSION['error'] = 'Please Enter the required fields to Update the password.';
            header('Location: teacher.php?id=' . $_SESSION['teacher_id']);
            return;
        }

        $id = $_POST['id'];
        $newPass = $_POST['new_pass'];
        $confirmPass = $_POST['confirm_pass'];

        if ($_SESSION['teacher_id'] != $id) {
            $_SESSION['error'] = 'id is wrong';
            header('Location: teacher.php?id=' . $_SESSION['teacher_id']);
            return;
        }

        if ($confirmPass !== $newPass) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: teacher.php?id=' . $_SESSION['teacher_id']);
            return;
        }

        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }

        if (changePassword($PDO, $newPass, $id) !== NULL) {
            $_SESSION['success'] = "Successfully Updated the Password";
            header('Location: teacher.php?id=' . $_SESSION['teacher_id']);
            return;
        } else {
            $_SESSION['success'] = "Something went wrong";
            header('Location: teacher.php?id=' . $_SESSION['teacher_id']);
            return;
        }
    }

    if (isset($_POST['changeInfo'])) {
        if (empty($_POST['full_name']) || empty($_POST['email']) || empty($_POST['id'])) {
            $_SESSION['error'] = 'Please Enter the required fields to Update the Information.';
            header('Location: teacher.php?id=' . $_SESSION['teacher_id']);
            return;
        }

        $id = $_POST['id'];
        $name = $_POST['full_name'];
        $email = $_POST['email'];

        if ($_SESSION['teacher_id'] != $id) {
            $_SESSION['error'] = 'Teacher Id is invalid';
            header('Location: teacher.php?id=' . $_SESSION['teacher_id']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email Address is invalid';
            header('Location: teacher.php?id=' . $_SESSION['teacher_id']);            
            return; 
        }

        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }

        if (updateInformation($PDO, $name, $email, $id) !== NULL) {
            $_SESSION['success'] = "Successfully updated the Information";
            header('Location: teacher.php?id=' . $_SESSION['teacher_id']);            
            return;
        } else {
            $_SESSION['error'] = "Something went wrong";
            header('Location: teacher.php?id=' . $_SESSION['teacher_id']);            
            return;
        }

    }

    $teacherData = NULL;
    if ($_GET['id']) {
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $teacherData = getTeacherData($PDO, $_GET['id']);
        $_SESSION['teacher_id'] = $teacherData['id'];
        unset($PDO);
    } else {
        header("Location: index.php");
    }

?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <title>Admin Panel | Teacher Profile</title>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-md navbar-dark bg-dark">
                <div class="container">
                    <a href="#" class="navbar-brand">Admin Panel</a>
                    <button class="navbar-toggler" data-toggle="collapse" data-target="#teacher-nav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="teacher-nav">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>admin/" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Logs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>admin/create_teacher.php" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Create Teacher
                                </a>
                            </li>
                            <li class="nav-item active">
                                <a href="<?php echo $base_url ?>admin/teachers.php" class="nav-link">
                                    <i class="fa fa-envelope-open" aria-hidden="true"></i> View/Edit Teachers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>admin/students.php" class="nav-link">
                                    <i class="fa fa-envelope-open" aria-hidden="true"></i> View Students
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:{document.getElementById('logout').submit()}" class="nav-link">
                                    <i class="fa fa-sign-in" aria-hidden="true"></i> Logout
                                </a>
                                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="logout">
                                    <input type="hidden" name="logout" value="true">
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        
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

        <section id="profile" class="m-4">
            <div class="container-fluid">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="infoForm" method="POST">
                    <input type="hidden" name="changeInfo" value="1">
                    <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                    <div class="form-group row">
                        <label for="name" class="col-form-label col-md-2">Full Name</label>
                        <input type="text" name="full_name" class="form-control col-md-4" value="<?php echo $teacherData['name'] ?>" required>
                    </div>
                    <div class="form-group row">
                        <label for="username" class="col-form-label col-md-2">Username</label>
                        <input disabled type="text" name="username" class="form-control col-md-4" value="<?php echo $teacherData['username'] ?>">
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-form-label col-md-2">email</label>
                        <input type="email" name="email" class="form-control col-md-4" value="<?php echo $teacherData['email_address'] ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success">Update Information</button>
                    <button type="button" id="changePass" class="btn btn-info">Change Password</button>
                </form>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="passForm" style="display: none;" method="POST">
                    <input type="hidden" name="changePass" value="1">
                    <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                    <div class="form-group row">
                        <label for="new_pass" class="col-form-label col-md-2">New Password</label>
                        <input type="password" name="new_pass" class="form-control col-md-4" required>
                    </div>
                    <div class="form-group row">
                        <label for="confirm_pass" class="col-form-label col-md-2">Confirm New Password</label>
                        <input type="password" name="confirm_pass" class="form-control col-md-4" required>
                    </div>
                    <button type="submit" class="btn btn-success">Change Password</button>
                    <button type="button" id="updateInfo" class="btn btn-info">Update Information</button>
                </form>
            </div>
        </section>

        <script>
            $(function() {
                const UI_infoForm = $('#infoForm');
                const UI_passForm = $('#passForm');
                $('#changePass').click(function() {
                    UI_passForm.show();
                    UI_infoForm.hide();
                });

                $('#updateInfo').click(function() {
                    UI_passForm.hide();
                    UI_infoForm.show();
                });
            });
        </script>
            
<?php require_once(__DIR__.'/../footer.php');