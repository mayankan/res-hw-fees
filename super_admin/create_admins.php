<?php
    /**
     * This page is used to create a user
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    session_start();

    /**
     * Create User in `teacher` table
     *
     * @param PDOObject $PDO
     * @param String $fullName - User Name
     * @param String $username - User Username
     * @param String $password - User Password
     * @param String $email - User Email Address
     *
     * @return Teacher $data
     *
     * @throws Exception // No Specefic Exception Defined
     *
    */
    function createUser($PDO, $fullName, $username, $password, $email, $role) {
        $hashedPass = hash('sha256', $password);
        try {
            $stmt = $PDO->prepare("
                INSERT INTO `teacher` (`name`, `username`, `password`, `email_address`, `role`, `date_created`)
                            VALUES (:name, :username, :password, :email, :role, :date_created)
            ");
            $stmt->execute(
                [
                    ':name' => $fullName,
                    ':username' => $username,
                    ':password' => $hashedPass,
                    ':email' => $email,
                    ':role' => $role,
                    ':date_created' => ((string) date("Y-m-d h:i:s"))
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
    if ($_SESSION['role'] !== 'super_admin') {
        header('Location: ../404.html');
        exit();
    }

    // checks for logout variable in GET Request and if it's true logs out user
    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
            exit();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fullName = isset($_POST['full_name']) ? $_POST['full_name'] : "";
        $username = isset($_POST['username']) ? $_POST['username'] : "";
        $password = isset($_POST['password']) ? $_POST['password'] : "";
        $email = isset($_POST['email']) ? $_POST['email'] : "";
        $role = isset($_POST['role']) ? $_POST['role'] : "";

        // check required fields
        if ($fullName === "" || $username === "" || $password === "" || $email === "" || $role === "") {
            $_SESSION['error'] = 'You forgot to enter the required fields';
            header('Location: create_admins.php');
            exit();
        }

        $roles = ['admin', 'fee_clerk', 'teacher'];
        if (!in_array($role, $roles)) {
            $_SESSION['error'] = 'Role is required field.';
            header('Location: create_admins.php');
            exit();
        }

        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to the database");
        }

        if (createUser($PDO, $fullName, $username, $password, $email, $role)) {
            $_SESSION['success'] = 'User has been successfully created.';
            header('Location: create_admins.php');
            exit();
        } else {
            $_SESSION['error'] = 'Something went wrong';
            header('Location: create_admins.php');
            exit();
        }
        unset($PDO);
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
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/view_admins.php" class="nav-link">
                                View Admins
                            </a>
                        </li>
                        <li class="nav-item active">
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

        <section id="createUser" class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                        <div class="form-group">
                            <label for="full name" class="col-form-label">
                                Full Name&nbsp;<span class="text-danger">*</span>
                            </label>
                            <input type="text" name="full_name" id="" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="username" class="col-form-label">
                                Username&nbsp;<span class="text-danger">*</span>
                            </label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-form-label">
                                Password&nbsp;<span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-form-label">
                                Email Address&nbsp;<span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="role" class="col-form-label">
                                Role&nbsp;<span class="text-danger">*</span>
                            </label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="" selected disabled>Roles</option>
                                <option value="teacher">Teacher</option>
                                <option value="admin">Admin</option>
                                <option value="fee_clerk">Fee Clerk</option>
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-success btn-block">Create Teacher</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
<?php require_once(__DIR__.'/../footer.php'); ?>
