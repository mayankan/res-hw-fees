<?php
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    if ($_SESSION['role'] !== 'admin') {
        header('Location: ../404.html');
        return;
    }

    function getLog($PDO, $id) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `log` WHERE `id` = :id");
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetch();
        } catch(Exception $e) {
            print($e);
            return NULL;
        }
    }

    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
            return;
        }
    }

    $log = NULL;
    if (isset($_GET['homeworkId'])) {
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to the database");
        }
        $log = getLog($PDO, $_GET['homeworkId']);
        $log['teacher_name'] = getTeacherName($PDO, $log['teacher_id']);
        if (!is_null($log['message_id'])) {
            $log['homework'] = getAllHomework($PDO, $log['message_id']);
            if ($log['homework'] !== NULL) {
                $class = getClass($PDO, $log['homework']['class_id']);
                $log['homework']['class'] = $class['class_name'] . ' - ' .$class['section'];

                if ($log['homework']['student_id'] !== NULL) {
                    $student = getStudent($PDO, $homework['student_id']);
                    if ($student !== NULL) {
                        $log['homework']['student'] = $student['name'];
                    }
                }
            }

        }
    }

?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <title>Admin panel | Log</title>
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

        <?php if (!is_null($log)): ?>
        <section id="log">
            <div class="container mt-5">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Teacher - <?php echo $log['teacher_name'] ?></h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <?php $date = date_create($log['date_of_action']) ?>
                                        <h5 class="d-inline-block">Date of Action - </h5>
                                        <span><?php echo date_format($date, 'd F Y'); ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <h5 class="d-inline-block">Action - </h5>
                                        <span><?php echo $log['log_action'] ?></span>
                                    </li>
                                </ul>
                                <?php if (!is_null($log['message_id'])): ?>
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h4 class="card-title">Homework Details</h4>
                                    </div>
                                    <div class="card-body">
                                        <h4 class="py-2">Details : </h4>
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <?php $date = date_create($log['homework']['date_of_message']) ?>
                                                <h5 class="d-inline-block">Date of Homework - </h5>
                                                <span><?php echo date_format($date, 'd F Y'); ?></span>
                                            </li>
                                            <li class="list-group-item">
                                                <h5 class="d-inline-block">For Class - </h5>
                                                <span><?php echo $log['homework']['class']; ?></span>
                                            </li>
                                            <?php if ($log['homework']['student_id'] !== NULL): ?>
                                            <li class="list-group-item">
                                                <h5 class="d-inline-block">For Student - </h5>
                                                <span><?php echo $log['homework']['student'] ?></span>
                                            </li>
                                            <?php endif ?>
                                        </ul>
                                        <div class="card mt-3">
                                            <div class="card-body">
                                                <h5 class="d-inline-block">Homework : </h5>
                                                <p><?php echo $log['homework']['message']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif ?>
                                <div class="row mt-4">
                                    <div class="col">
                                        <?php if (isset($_GET['page_no'])): ?>
                                        <a href="<?php echo $base_url; ?>admin?page_no=<?php echo $_GET['page_no'] ?>" class="btn btn-info">Go Back</a>
                                        <?php else: ?>
                                        <a href="<?php echo $base_url; ?>admin" class="btn btn-info">Go Back</a>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif ?>
<?php require_once(__DIR__.'/../footer.php'); ?>