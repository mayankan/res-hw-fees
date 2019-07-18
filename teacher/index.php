<?php
    /**
     * This page is used to see all the homeworks provided by the teacher
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    /**
     * Get all Homeworks with limit
     *
     * @param PDOObject $PDO
     * @param Number $teacherId
     * @param Number $startLimit
     *
     * @return Homework $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function getHomeworks($PDO, $teacherId, $startLimit=0) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `message` WHERE teacher_id = :teacher_id AND date_deleted IS NULL LIMIT :start_limit, 10");
            $stmt->execute([':start_limit' => $startLimit, ':teacher_id' => $teacherId]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    // function getClassById($PDO, $classId) {
    //     try {
    //         $stmt = $PDO->prepare("SELECT * FROM `class` WHERE `id` = :id");
    //         $stmt->execute([':id' => $classId]);
    //         if ($stmt->rowCount() == 0) {
    //             return false;
    //         }
    //         return $stmt->fetch();
    //     } catch (Exception $e) {
    //         print($e);
    //         return false;
    //     }
    // }

    // function getStudentById($PDO, $studentId) {
    //     try {
    //         $stmt = $PDO->prepare("SELECT * FROM `student` WHERE `id` = :id");
    //         $stmt->execute([':id' => $studentId]);
    //         if ($stmt->rowCount() == 0) {
    //             return false;
    //         }
    //         return $stmt->fetch();
    //     } catch (Exception $e) {
    //         print($e);
    //         return false;
    //     }
    // }

    // logs out user if it's not a teacher
    if ($_SESSION['role'] !== 'teacher') {
        header('Location: ../404.html');
    }

    // checks for logout variable in GET Request and if it's true logs out user
    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            require(__DIR__ . '/../db/db.connection.php');
            $PDO = getConnection();
            if (is_null($PDO)) {
                die("Can't connect to database");
            }
            addToLog($PDO, 'Teacher Logged out', $_SESSION['data']['id']);
            session_destroy();
            header('Location: ../');
        }
    }

    if (isset($_SESSION['data']['id'])) {
        require(__DIR__ . '/../db/db.connection.php');
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        if (!isset($_GET['page_no'])) {
            $homeworks = getHomeworks($PDO, $_SESSION['data']['id']);
            $_SESSION['page_no'] = 1;
        } else {
            $pageNumber = (int)$_GET['page_no'];
            if ($pageNumber <= 0) {
                $pageNumber = 1;
            }
            $end_limit = $pageNumber * 10;
            $startLimit = $end_limit - 10;
            $homeworks = getHomeworks($PDO, $_SESSION['data']['id'], $startLimit=$startLimit);
            if ($homeworks == NULL) {
                header('Location: index.php?page_no=' . (((int)$_GET['page_no']) - 1));
            }
            $_SESSION['page_no'] = $_GET['page_no'];
        }
    }

?>

<?php require_once(__DIR__.'/../header.php'); ?>
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
                                Homeworks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>teacher/compose.php" class="nav-link">
                                Compose
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>teacher/profile.php" class="nav-link">
                                Profile
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

        <?php if (!is_null($homeworks)): ?>
        <section id="homeworks">
            <div class="container-fluid">
                <div id="homeworks">
                    <div class="row pb-2">
                        <div class="col-6 d-flex justify-content-start">
                            <?php if ($_SESSION['page_no'] <= 1): ?>
                            <a href="#" class="btn btn-outline-dark" disabled>
                                <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                            </a>
                            <?php else: ?>
                            <a href="<?php echo $base_url ?>teacher/index.php?page_no=<?php echo $_SESSION['page_no'] - 1 ?>" class="btn btn-outline-dark">
                                <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                            </a>
                            <?php endif ?>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <a href="<?php echo $base_url ?>teacher/index.php?page_no=<?php echo $_SESSION['page_no'] + 1 ?>" class="btn btn-outline-dark">
                                Next <i class="fa fa-arrow-right fa-1 mt-1" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover table-responsive-sm table-bordered">
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
                                <?php while ($homework = array_shift($homeworks)): ?>
                                <tr>
                                    <?php $date = date_create($homework['date_of_message']) ?>
                                    <td class="text-center">
                                        <?php print(date_format($date, 'Y-m-d')) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo substr($homework['message'], 0, 50) ?>
                                    </td>
                                    <?php $classData = getClass($PDO, $homework['class_id']); ?>
                                    <?php if ($classData != false) :?>
                                        <td class="text-center">
                                            <?php echo $classData['class_name'] . ' - ' . $classData['section']; ?>
                                        </td>
                                    <?php endif ?>

                                    <?php if ($homework['student_id'] != NULL): ?>
                                        <?php $studentData = getStudent($PDO, $homework['student_id']) ?>
                                        <?php if ($studentData != false): ?>
                                            <td class="text-center">
                                                <?php echo $studentData['name'] . ' - ' . $studentData['admission_no']; ?>
                                            </td>
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
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php else: ?>

        <?php endif ?>
<?php require_once(__DIR__.'/../footer.php'); ?>
<?php unset($PDO); ?>
