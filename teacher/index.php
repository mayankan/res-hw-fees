<?php 
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    function getHomeworks($PDO, $teacherId) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `message` WHERE teacher_id = :teacher_id AND date_deleted IS NULL");
            $stmt->execute([':teacher_id' => $teacherId]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    function getClassById($PDO, $classId) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `class` WHERE `id` = :id");
            $stmt->execute([':id' => $classId]);
            if ($stmt->rowCount() == 0) {
                return false;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    function getStudentById($PDO, $studentId) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `student` WHERE `id` = :id");
            $stmt->execute([':id' => $studentId]);
            if ($stmt->rowCount() == 0) {
                return false;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    if ($_SESSION['role'] !== 'teacher') {
        session_destroy();
        header('Location: ../');
    }

    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            require(__DIR__ . '/../db/db.connection.php');
            $PDO = getConnection();
            if (is_null($PDO)) {
                die("Can't connect to database");
            }
            addToLog($PDO, 'Teacher Logged out', $_SESSION['data'][0]['id']);
            session_destroy();
            header('Location: ../');
        }
    }

    if (isset($_SESSION['data'][0]['id'])) {
        require(__DIR__ . '/../db/db.connection.php');
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $homeworks = getHomeworks($PDO, $_SESSION['data'][0]['id']);
    }

?>

<?php require_once(__DIR__.'/../header.html'); ?>
        <title>Teacher panel | Home</title>
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
                            <li class="nav-item active">
                                <a href="<?php echo $base_url ?>teacher/" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Homeworks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>teacher/compose.php" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Compose
                                </a>
                            </li>
                            <li class="nav-item">
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

        <section id="homeworks">
            <div class="container-fluid">
                <div id="homeworks">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover responsive-table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Homework</th>
                                        <th class="text-center">Class</th>
                                        <th class="text-center">Student</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!is_null($homeworks)): ?> 
                                <?php while ($homework = array_shift($homeworks)): ?>
                                <tr>
                                    <?php $date = date_create($homework['date_of_message']) ?>
                                    <td class="text-center"><?php print(date_format($date, 'Y-m-d')) ?></td>
                                    <td class="text-center"><?php echo substr($homework['message'], 0, 50) ?></td>
                                    <?php $classData = getClassById($PDO, $homework['class_id']); ?>
                                    <?php if ($classData != false) :?>
                                    <td class="text-center"><?php echo $classData['class_name'] . ' - ' . $classData['section']; ?></td>
                                    <?php endif ?>
                                    <?php if ($homework['student_id'] != NULL): ?>
                                    <?php $studentData = getStudentById($PDO, $homework['student_id']) ?>
                                    <?php if ($studentData != false): ?>
                                    <td class="text-center"><?php echo $studentData['name'] . ' - ' . $studentData['admission_no']; ?></td>
                                    <?php endif ?>
                                    <?php else: ?>
                                        <td class="text-center">All</td>
                                    <?php endif ?>
                                    <td>
                                        <form action="<?php echo $base_url ?>teacher/homework.php" method="GET">
                                            <input type="hidden" name="homeworkId" value="<?php echo $homework['id'] ?>">
                                            <button type="submit" class="btn btn-block btn-outline-warning">Edit</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile ?>
                                <?php endif ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
<?php require_once(__DIR__.'/../footer.html');