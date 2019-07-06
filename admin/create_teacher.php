<?php
    /**
     * This page is used to create Teacher 
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    session_start();

    /**
     * Create Teacher in `teacher` table
     *
     * @param PDOObject $PDO
     * @param String $fullName - Teacher Name
     * @param String $username - Teacher Username
     * @param String $password - Teacher Password
     * @param String $email - Teacher Email Address
     *
     * @return Teacher $data
     *
     * @throws Exception // No Specefic Exception Defined
     *
    */
    function createTeacher($PDO, $fullName, $username, $password, $email) {
        $hashedPass = hash('sha256', $password);
        try {
            $stmt = $PDO->prepare("
                INSERT INTO `teacher` (`name`, `username`, `password`, `email_address`, `date_created`)
                            VALUES (:name, :username, :password, :email, :date_created)
            ");
            $stmt->execute(
                [
                    ':name' => $fullName,
                    ':username' => $username,
                    ':password' => $hashedPass,
                    ':email' => $email,
                    ':date_created' => ((string) date("Y-m-d"))
                ]
            );
            if ($stmt->rowCount() === 0) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    // logs out user if it's not a admin
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ../404.html');
        return;
    }

    // checks for logout variable in GET Request and if it's true logs out user
    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
            return;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fullName = $_POST['full_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];

        // check required fields
        if ($fullName === '' && $username === '' && $password === '' && $email === '') {
            $_SESSION['error'] = 'You forgot to enter the required fields';
            header('Location: create_teacher.php');
            return;
        }

        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to the database");
        }

        if (createTeacher($PDO, $fullName, $username, $password, $email)) {
            $_SESSION['success'] = 'Teacher User has been successfully created.';
            header('Location: create_teacher.php');
            return;
        } else {
            $_SESSION['error'] = 'Something went wrong';
            header('Location: create_teacher.php');
            return;
        }
    }

?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <title>Admin Panel | Create Teacher</title>
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
                                    Logs
                                </a>
                            </li>
                            <li class="nav-item active">
                                <a href="<?php echo $base_url ?>admin/create_teacher.php" class="nav-link">
                                    Create Teacher
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>admin/teachers.php" class="nav-link">
                                    View/Edit Teachers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>admin/students.php" class="nav-link">
                                    View Students
                                </a>
                            </li>
                            <li class="nav-item">
                                <a 
                                    href="<?php echo $base_url ?>admin/db_update.php"
                                    onclick="return window.confirm('Do you Really want to update the Database');"
                                    class="nav-link"
                                >
                                    Update DB
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
        </header>

        <section id="createTeacher" class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                        <div class="form-group">
                            <label for="full name" class="col-form-label">
                                Full Name<span class="text-danger">*</span>
                            </label>
                            <input type="text" name="full_name" id="" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="username" class="col-form-label">
                                Username<span class="text-danger">*</span>
                            </label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-form-label">
                                Password<span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-form-label">
                                Email Address<span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-success btn-block">Create Teacher</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

<?php require_once(__DIR__.'/../footer.php'); ?>
