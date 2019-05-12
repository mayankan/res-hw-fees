<?php
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../helpers.php');
    require(__DIR__ . '/../db/db.connection.php');
    session_start();

    function deleteHomework($PDO, $homeworkId) {
        try {
            $stmt = $PDO->prepare("UPDATE `message` SET `date_deleted` = :date_deleted WHERE `id` = :id");
            $stmt->execute(['date_deleted' => date("Y/m/d h:i:s"), ':id' => $homeworkId]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    function updateHomework($PDO, $homeworkId, $message) {
        try {
            $stmt = $PDO->prepare("UPDATE `message` SET `message` = :message WHERE `id` = :id");
            $stmt->execute([':message' => $message, ':id' => $homeworkId]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    if ($_SESSION['role'] !== 'teacher') {
        session_destroy();
        header('Location: ../');
    }

    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            $PDO = getConnection();
            if (is_null($PDO)) {
                die("Can't connect to database");
            }
            addToLog($PDO, 'Teacher Logged out', $_SESSION['data']['id']);
            session_destroy();
            header('Location: ../');
        }
    }

    if (isset($_GET['homeworkId'])) {
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $homework = getHomework($PDO, $_GET['homeworkId']);
        $class = getClass($PDO, $homework['class_id']);
        if ($homework !== NULL) {
            if ($homework['teacher_id'] !== $_SESSION['data']['id']) {
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
            if ($class !== NULL) {
                $homework['class'] = $class['class_name'] . ' - ' .$class['section'];
            }
            $teacher = getTeacherName($PDO, $homework['teacher_id']);
            if ($teacher !== NULL) {
                $homework['teacher'] = $teacher;
            }
        }
        $_SESSION['homeworkId'] = $_GET['homeworkId'];
    } else if ($_SERVER['REQUEST_METHOD'] !== "POST") {
        header('Location: index.php');
    }

    // updating the message
    if (isset($_POST['homework_id']) && isset($_POST['message'])) {
        if ($_POST['homework_id'] !== $_SESSION['homeworkId']) {
            unset($_SESSION['homeworkId']);
            header('Location: index.php');
        }
        unset($_SESSION['homeworkId']);
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        if (updateHomework($PDO, $_POST['homework_id'], $_POST['message']) !== NULL) {
            addToLog($PDO, 'Updated Homework', $_SESSION['data']['id'], $message_id=$_POST['homework_id']);
            $_SESSION['success'] = 'Message has been updated';
            header("Location: homework.php?homeworkId=" . $_SESSION['homeworkId']);
        } else {
            $_SESSION['error'] = 'Message can\'t be updated';
            header("Location: homework.php?homeworkId=" . $_SESSION['homeworkId']);
        }
    }

    // deleting the whole homework
    if (isset($_POST['homeworkId'])) {
        if ($_POST['homeworkId'] !== $_SESSION['homeworkId']) {
            unset($_SESSION['homeworkId']);
            header('Location: index.php');
        }
        unset($_SESSION['homeworkId']);
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        if (deleteHomework($PDO, $_POST['homeworkId']) !== NULL) {
            addToLog($PDO, 'Deleted Homework', $_SESSION['data']['id'], $message_id=$_POST['homeworkId']);
            $_SESSION['success'] = 'Message has been deleted';
            header('Location: index.php');
        } else {
            $_SESSION['error'] = 'Message can\'t be deleted';
            header('Location: index.php');
        }
    }

?>

<?php require_once(__DIR__.'/../base_files/header.php'); ?>
        <title>Homework</title>
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
                                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="logout">
                                    <input type="hidden" name="logout" value="true">
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

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
                                    <div class="card-body" id="message">
                                        <h5 class="d-inline-block">Homework : </h5>
                                        <div id="show-div">
                                            <p><?php echo $homework['message']; ?></p>
                                        </div>
                                        <div id="form-div" style="display: none;">
                                            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                                                <input type="hidden" name="homework_id" value="<?php echo $_GET['homeworkId'] ?>">
                                                <textarea name="message" class="form-control" rows="8"><?php echo $homework['message'] ?></textarea>
                                                <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                <button type="button" id="cancel" class="btn btn-danger mt-2">Cancel</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-6">
                                        <a href="<?php echo $base_url; ?>teacher/" class="btn btn-info">Go Back</a>
                                    </div>
                                    <div class="col-6 d-flex justify-content-end">
                                        <button type="button" id="edit" class="btn btn-warning mx-2">Edit</button>
                                        <form class="d-inline-block" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                                            <input type="hidden" name="homeworkId" value="<?php echo $_GET['homeworkId'] ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script>
            $(document).ready(function () {
                $('#edit').click(function() {
                    $('#show-div').hide();
                    $('#form-div').show();
                    $('#edit').hide();
                });

                $('#cancel').click(function() {
                    $('#show-div').show();
                    $('#form-div').hide();
                    $('#edit').show();
                });
            });
        </script>
    </body>
<?php require_once(__DIR__.'/../base_files/footer.php');
