<?php 
    require(__DIR__.'/../config.php');
    session_start();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ../404.html');
        return;
    }

    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
            return;
        }
    }

    if (!isset($_GET['page_no'])) {
        // $homeworks = getHomeworks($PDO, $_SESSION['data']['id']);
        $_SESSION['page_no'] = 1;
    } else {
        $page_no = (int)$_GET['page_no'];
        if ($page_no <= 0) {
            header("Location: index.php?page_no=1");
            return;
        }
        $end_limit = $page_no * 10;
        $start_limit = $end_limit - 10;
        // $homeworks = getHomeworks($PDO, $_SESSION['data']['id'], $start_limit=$start_limit, $end_limit=$end_limit);
        // if ($homeworks == NULL) {
        //     header('Location: index.php?page_no=' . (((int)$_GET['page_no']) - 1));
        //     return;
        // }
        $_SESSION['page_no'] = $_GET['page_no'];
    }
?>

<?php require_once(__DIR__.'/../header.html'); ?>
        <title>Admin panel | Logs</title>
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
                            <li class="nav-item active">
                                <a href="<?php echo $base_url ?>admin/" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Logs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>admin/create_teacher.php" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Create Teacher
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>admin/teachers.php" class="nav-link">
                                    <i class="fa fa-envelope-open" aria-hidden="true"></i> View/Edit Teachers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>admin/students.php" class="nav-link">
                                    <i class="fa fa-envelope-open" aria-hidden="true"></i> View/Edit Students
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

        <section id="logs" class="mt-2">
            <div class="container-fluid">
                <div class="row pb-2">
                    <div class="col-6 d-flex justify-content-start">
                        <?php if ($_SESSION['page_no'] <= 1): ?>
                        <a href="#" class="btn btn-outline-dark" disabled>
                            <i class="fa fa-arrow-left fa-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php else: ?>
                        <a href="<?php echo $base_url ?>teacher/index.php?page_no=<?php echo $_SESSION['page_no'] - 1 ?>" class="btn btn-outline-dark">
                            <i class="fa fa-arrow-left fa-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php endif ?>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <a href="<?php echo $base_url ?>teacher/index.php?page_no=<?php echo $_SESSION['page_no'] + 1 ?>" class="btn btn-outline-dark">
                            Next <i class="fa fa-arrow-right fa-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-responsive-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Date of Log</th>
                                    <th>Action</th>
                                    <th>Homework</th>
                                    <th>Date of Homework</th>
                                    <th>Student Sent to</th>
                                    <th>Teacher Assigned</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

<?php require_once(__DIR__.'/../footer.html'); ?>
