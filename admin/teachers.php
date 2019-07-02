<?php
    /**
     * This page is used to view list of teachers
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    session_start();

    // logs out user if it's not a admin
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ../404.html');
        return;
    }

    /**
     * Get teachers from `teacher` table with role assigned "teacher"
     *
     * @param PDOObject $PDO
     * @param Number $start_limit
     *
     * @return Teacher $data
     *
     * @throws Exception // No Specefic Exception Defined
     *
    */
    function getTeachers($PDO, $start_limit=0) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `teacher` WHERE `role` = 'teacher' LIMIT :start_limit,10");
            $stmt->execute([':start_limit' => $start_limit]);
            if ($stmt->rowCount() == 0) {
                return NULL;
            }
            return $stmt->fetchAll();
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
            return;
        }
    }

    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't connect to the database");
    }
    $teachers = NULL;
    if (!isset($_GET['page_no'])) {
        $teachers = getTeachers($PDO);
        $_SESSION['page_no'] = 1;
    } else {
        $page_no = (int)$_GET['page_no'];
        if ($page_no <= 0) {
            header("Location: teachers.php?page_no=1");
            return;
        }
        $end_limit = $page_no * 10;
        $start_limit = $end_limit - 10;
        $teachers = getTeachers($PDO, $start_limit=$start_limit);
        if ($teachers === NULL && $page_no !== 1) {
            header('Location: teachers.php?page_no=' . (((int)$_GET['page_no']) - 1));
            return;
        }
        $_SESSION['page_no'] = $_GET['page_no'];
    }

?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <title>Admin Panel | View/Edit Teachers</title>
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
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>admin/create_teacher.php" class="nav-link">
                                    Create Teacher
                                </a>
                            </li>
                            <li class="nav-item active">
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
        </header>

        <section id="teachers" class="mt-2">
            <div class="container-fluid">
                <div class="row pb-2">
                    <div class="col-6 d-flex justify-content-start">
                        <?php if ($_SESSION['page_no'] <= 1): ?>
                        <a href="#" class="btn btn-outline-dark" disabled>
                            <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php else: ?>
                        <a href="<?php echo $base_url ?>admin/teachers.php?page_no=<?php echo $_SESSION['page_no'] - 1 ?>" class="btn btn-outline-dark">
                            <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php endif ?>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <a href="<?php echo $base_url ?>admin/teachers.php?page_no=<?php echo $_SESSION['page_no'] + 1 ?>" class="btn btn-outline-dark">
                            Next <i class="fa fa-arrow-right fa-1 mt-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <?php if (!is_null($teachers)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-responsive-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email Address</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><?php echo $teacher['name'] ?></td>
                                    <td><?php echo $teacher['username'] ?></td>
                                    <td><?php echo $teacher['email_address'] ?></td>
                                    <td>
                                        <a 
                                            href="<?php echo $base_url ?>admin/teacher.php?id=<?php echo $teacher['id'] ?>" 
                                            class="btn btn-outline-warning btn-block"
                                        >
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </section>
<?php require_once(__DIR__.'/../footer.php'); ?>
