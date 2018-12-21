<?php
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    if ($_SESSION['role'] !== 'student') {
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

    $homework = NULL;

    if (isset($_GET['homeworkId'])) {
        require(__DIR__ . '/../db/db.connection.php');
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $homework = getHomework($PDO, $_GET['homeworkId']);
        $class = getClass($PDO, $homework['class_id']);
        if ($homework !== NULL) {
            if ($homework['student_id'] !== NULL) {
                if ($homework['student_id'] !== $_SESSION['data']['id']) {
                    if (isset($_SESSION)) {
                        header('Location: index.php');
                    } else {
                        header('Location: ../');
                    }
                }
                $student = getStudent($PDO, $homework['student_id']);
                if ($student !== NULL) {
                    $homework['student'] = $student['name'];
                }
            }
            if ($class !== NULL) {
                if ($_SESSION['data']['class_id'] !== $class['id']) {
                    if (isset($_SESSION)) {
                        header('Location: index.php');
                    } else {
                        header('Location: ../');
                    }
                }
                $homework['class'] = $class['class_name'] . ' - ' .$class['section'];
            }
            $teacher = getTeacherName($PDO, $homework['teacher_id']);
            if ($teacher !== NULL) {
                $homework['teacher'] = $teacher;
            }
        }
    } else {
        header('Location: index.php');
    }

?>

<?php require_once(__DIR__.'/../header.html'); ?>
        <title>Student panel | Homework</title>
        <style>
            .card-title {
                margin: 0;
            }
        </style>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-md navbar-dark bg-dark">
                <div class="container">
                    <a href="#" class="navbar-brand">Student Panel</a>
                    <button class="navbar-toggler" data-toggle="collapse" data-target="#student-nav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="student-nav">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item active">
                                <a href="<?php echo $base_url ?>student/" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Homeworks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>student/profile.php" class="nav-link">
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

        <section id="homework">
            <div class="container mt-5">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">By <?php echo $homework['teacher'] ?></h3>
                            </div>
                            <div class="card-body">
                                <h4 class="py-2">Details : </h4>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <?php $date = date_create($homework['date_of_message']) ?>
                                        <h5 class="d-inline-block">Date of Homework - </h5>
                                        <span><?php echo date_format($date, 'd F Y'); ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <h5 class="d-inline-block">For Class - </h5>
                                        <span><?php echo $homework['class']; ?></span>
                                    </li>
                                    <?php if ($homework['student_id'] !== NULL): ?>
                                    <li class="list-group-item">
                                        <h5 class="d-inline-block">For Student - </h5>
                                        <span><?php echo $homework['student'] ?></span>
                                    </li>
                                    <?php endif ?>
                                </ul>
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h5 class="d-inline-block">Homework : </h5>
                                        <p><?php echo $homework['message']; ?></p> 
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col">
                                        <a href="<?php echo $base_url; ?>student/" class="btn btn-info">Go Back</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
<?php require_once(__DIR__.'/../footer.html');
