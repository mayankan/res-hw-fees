<?php
    /**
     * This Page is used to fetch and show one homework specified with a ID
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a student
    if ($_SESSION['role'] !== 'student') {
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

    // Global $homework data for the response and server
    $homework = NULL;
    if (isset($_GET['homeworkId'])) {
        require(__DIR__ . '/../db/db.connection.php');
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database"); // still need to fix this
        }
        $homework = getHomework($PDO, $_GET['homeworkId']);
        // Shreyans is assuming that every homework is attached to some class in `class` table
        $class = getClass($PDO, $homework['class_id']);
        if ($homework !== NULL) {
            if ($homework['student_id'] !== NULL) {
                /**
                 * this logic checks if the student id 
                 * is actually associated with the student actually
                 * logged in
                */
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
                /**
                 * this logic checks if the student logged in
                 * is actually associated with the class for which
                 * the homework is fetched upon
                */
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
            // this shouldn't happen in normal conditions but still checking in case of database inconsistency fed directly
            if ($teacher !== NULL) {
                $homework['teacher'] = $teacher;
            }
        }
    } else {
        header('Location: index.php');
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
                            <a href="<?php echo $base_url ?>student/profile.php" class="nav-link">
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>student/fee.php" class="nav-link">
                                Pay Fees
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

        <section id="homework">
            <div class="container mt-5">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title m-0">By <?php echo $homework['teacher'] ?></h3>
                            </div>
                            <div class="card-body">
                                <h4 class="py-2">Details: </h4>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex align-items-center">
                                        <?php $date = date_create($homework['date_of_message']) ?>
                                        <h5 class="d-inline-block">Date of Homework&nbsp;-&nbsp;</h5>
                                        <span><?php echo date_format($date, 'd F Y'); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center">
                                        <h5 class="d-inline-block">For Class&nbsp;-&nbsp;</h5>
                                        <span><?php echo $homework['class']; ?></span>
                                    </li>
                                    <?php if ($homework['student_id'] !== NULL): ?>
                                    <li class="list-group-item d-flex align-items-center">
                                        <h5 class="d-inline-block">For Student&nbsp;-&nbsp;</h5>
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
<?php require_once(__DIR__.'/../footer.php');
