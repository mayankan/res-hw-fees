<?php
    /**
     * This Page is used to show students all the homeworks for them
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a student
    if ($_SESSION['role'] !== 'student') {
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

    /**
     * Get all homeworks for the student
     *
     * @param PDOObject $PDO
     * @param Number $classId
     * @param Number $startLimit
     *
     * @return Homeworks $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function getHomeworks($PDO, $classId, $startLimit=0) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `message` WHERE `class_id` = :class_id AND date_deleted IS NULL ORDER BY `date_of_message` DESC LIMIT :startLimit, 10");
            $stmt->execute([':class_id' => $classId, ':startLimit' => $startLimit]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    // Global $homeworks data for the response and server
    $homeworks = NULL;
    if (isset($_SESSION['data']['id'])) {
        require(__DIR__ . '/../db/db.connection.php');
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $classId = getStudent($PDO, $_SESSION['data']['id'])['class_id'];
        if (!isset($_GET['page_no'])) {
            $homeworks = getHomeworks($PDO, $classId);
            $_SESSION['page_no'] = 1;
        } else {
            $pageNumber = (int)$_GET['page_no'];
            if ($pageNumber <= 0) {
                $pageNumber = 1;
            }
            $startLimit = ($pageNumber * 10) - 10;
            $homeworks = getHomeworks($PDO, $classId, $startLimit=$startLimit);
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
                <a href="#" class="navbar-brand">Student Panel</a>
                <button class="navbar-toggler" data-toggle="collapse" data-target="#student-nav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="student-nav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item active">
                            <a href="<?php echo $base_url ?>student/" class="nav-link">
                                Homeworks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>student/fee.php" class="nav-link">
                                Pay Fees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>student/profile.php" class="nav-link">
                                Profile
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

        <section id="homeworks">
            <div class="container-fluid">
                <div id="homeworks">
                    <div class="row py-2">
                        <div class="col-6 d-flex justify-content-start">
                            <?php if ($_SESSION['page_no'] <= 1): ?>
                            <a href="#" class="btn btn-outline-dark" disabled>
                                <i class="fa mt-1 fa-arrow-left fa-1" aria-hidden="true"></i> Prev
                            </a>
                            <?php else: ?>
                            <a href="<?php echo $base_url ?>student/index.php?page_no=<?php echo $_SESSION['page_no'] - 1 ?>" class="btn btn-outline-dark">
                                <i class="fa mt-1 fa-arrow-left fa-1" aria-hidden="true"></i> Prev
                            </a>
                            <?php endif ?>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <a href="<?php echo $base_url ?>student/index.php?page_no=<?php echo $_SESSION['page_no'] + 1 ?>" class="btn btn-outline-dark">
                                Next <i class="fa mt-1 fa-arrow-right fa-1" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover responsive-table table-bordered">
                                <thead>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Homework</th>
                                    <th class="text-center">Given By</th>
                                    <th></th>
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
<?php require_once(__DIR__.'/../footer.php'); ?>
<?php unset($PDO); ?>
