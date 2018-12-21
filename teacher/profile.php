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

    function changePassword($PDO, $newPass) {
        try {
            $stmt = $PDO->prepare("UPDATE `teacher` SET `password` = :pass WHERE `id` = :id");
            $stmt->execute([':pass' => $newPass, ':id' => $_SESSION['data'][0]['id']]);
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    function updateInformation($PDO, $name, $email) {
        try {
            $stmt = $PDO->prepare("UPDATE `teacher` SET `name` = :name, `email_address` = :email WHERE `id` = :id");
            $stmt->execute([':name' => $name, ':email' => $email,':id' => $_SESSION['data'][0]['id']]);
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }
    
    if ($_SESSION['role'] !== 'teacher') {
        session_destroy();
        header('Location: ../');
    }

    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            addToLog($PDO, 'Teacher Logged out', $_SESSION['data'][0]['id']);
            session_destroy();
            header('Location: ../');
        }
    }

    $teacherData = NULL;
    if ($_SESSION['data'][0]['id']) {
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $teacherData = getTeacherData($PDO, $_SESSION['data'][0]['id']);
        unset($PDO);
    }

    if (isset($_POST['changePass'])) {
        if (empty($_POST['old_pass']) || empty($_POST['new_pass']) || empty($_POST['confirm_pass'])) {
            $_SESSION['error'] = 'You did not enter the required fields to change password';
            header('Location: profile.php');
            return;
        }
        $newPass = $_POST['new_pass'];
        $oldPass = $_POST['old_pass'];
        $confirmPass = $_POST['confirm_pass'];

        if ($confirmPass !== $newPass) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: profile.php');
            return;
        }

        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $teacherData = getTeacherData($PDO, $_SESSION['data'][0]['id']);

        if ($teacherData['password'] !== $oldPass) {
            $_SESSION['error'] = 'Old Password do not match';
            header('Location: profile.php');
            return;
        }

        if (changePassword($PDO, $newPass) !== NULL) {
            $_SESSION['success'] = "Successfully! Update the Password";
            header('Location: profile.php');
            return;
        } else {
            $_SESSION['success'] = "Something went wrong";
            header('Location: profile.php');
            return;
        }
    }

    if (isset($_POST['changeInfo'])) {
        if (empty($_POST['full_name']) || empty($_POST['email'])) {
            $_SESSION['error'] = 'You did not enter the required fields to update the information';
            header('Location: profile.php');
            return;
        }

        $name = $_POST['full_name'];
        $email = $_POST['email'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email is wrong';
            header('Location: profile.php');
            return; 
        }

        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }

        if (updateInformation($PDO, $name, $email) !== NULL) {
            $_SESSION['success'] = "Successfully! Update the Information";
            header('Location: profile.php');
            return;
        } else {
            $_SESSION['error'] = "Something went wrong";
            header('Location: profile.php');
            return;
        }

    }

?>

<?php require_once(__DIR__.'/../header.html'); ?>
        <title>Teacher panel | Profile</title>
    </head>
    <body>
        <header>
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
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Homeworks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>teacher/compose.php" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Compose
                                </a>
                            </li>
                            <li class="nav-item active">
                                <a href="<?php echo $base_url ?>teacher/profile.php" class="nav-link">
                                    <i class="fa fa-envelope-open" aria-hidden="true"></i> Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:{document.getElementById('logout').submit()}" class="nav-link">
                                    <i class="fa fa-sign-in" aria-hidden="true"></i> Logout
                                </a>
                                <form action="<? echo $_SERVER['PHP_SELF'] ?>" id="logout">
                                    <input type="hidden" name="logout" value="true">
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header> 
        
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

        <section id="profile" class="m-4">
            <div class="container-fluid">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="infoForm" method="POST">
                    <input type="hidden" name="changeInfo" value="1">
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
                    <div class="form-group row">
                        <label for="old_pass" class="col-form-label col-md-2">Old Password</label>
                        <input type="password" name="old_pass" class="form-control col-md-4" required>
                    </div>
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
            
<?php require_once(__DIR__.'/../footer.html');