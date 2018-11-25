<?php 
    require(__DIR__.'/../config.php');
    session_start();

    if ($_SESSION['role'] !== 'student') {
        session_destroy();
        header('Location: ../');
    }

    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
        }
    }

    function getClassByStudentId($PDO, $studentId) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `student` WHERE `id` = :id");
            $stmt->execute([':id' => $studentId]);
            return $stmt->fetch()['class_id'];
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    function getTeacherName($PDO, $teacherId) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `teacher` WHERE `id` = :id");
            $stmt->execute([':id' => $teacherId]);
            return $stmt->fetch()['name'];
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    function getHomeworks($PDO, $classId) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `message` WHERE `class_id` = :class_id AND date_deleted IS NULL ORDER BY `date_of_message` DESC");
            $stmt->execute([':class_id' => $classId]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    if (isset($_SESSION['data']['id'])) {
        require(__DIR__ . '/../db/db.connection.php');
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $classId = getClassByStudentId($PDO, $_SESSION['data']['id']);
        $homeworks = getHomeworks($PDO, $classId);
    }

?>

<?php require_once(__DIR__.'/../header.html'); ?>
        <title>Student panel | Home</title>
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

        

        <section id="homeworks">
            <div class="container-fluid">
                <div id="homeworks">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover responsive-table table-bordered">
                                <thead>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Homework</th>
                                    <th class="text-center">Given By</th>
                                </thead>
                                <tbody>
                                <?php if (!is_null($homeworks)): ?> 
                                <?php while ($homework = array_shift($homeworks)): ?>
                                <tr>
                                    <?php $date = date_create($homework['date_of_message']) ?>
                                    <td class="text-center"><?php print(date_format($date, 'Y-m-d')) ?></td>
                                    <td class="text-justify"><?php echo substr($homework['message'], 0, 50) ?></td>
                                    <?php $teacherName = getTeacherName($PDO, $homework['teacher_id']); ?>
                                    <?php if ($teacherName !== NULL): ?>
                                    <td class="text-center"><?php echo $teacherName; ?></td>
                                    <?php endif ?>
                                    <td>
                                        <form action="<?php echo $base_url ?>student/homework.php" method="GET">
                                            <input type="hidden" name="homeworkId" value="<?php echo $homework['id'] ?>">
                                            <button type="submit" class="btn btn-block btn-outline-warning">View</button>
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