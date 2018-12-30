<?php
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    if ($_SESSION['role'] !== 'teacher') {
		header('Location: ../404.html');
    }

    require(__DIR__.'/../db/db.connection.php');
    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't connect to database");
    }

    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            addToLog($PDO, 'Teacher Logged out', $_SESSION['data']['id']);
            session_destroy();
            header('Location: ../');
        }
    }

    if (isset($_SERVER["HTTP_ORIGIN"])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    }


    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 86400");

    if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"])) {
            header("Access-Control-Allow-Methods: GET");
        }

        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
        exit(0);
    }

    if (isset($_GET['class_id'])) {
        $students_data = getStudentsByClass($PDO, $_GET['class_id']);
        if ($students_data === NULL) {
            header("Status: 404 Not Found");
            return;
        } else {
            header('Content-Type: application/json');
            echo json_encode($students_data);
        }
        return;
    }

    function getClasses($PDO) {
        try {
            $stmt = $PDO->prepare("
                                    SELECT `id`, `class_name`, `section` FROM class
                                ");
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    function getStudentsByClass($PDO, $class_id) {
        try {
            $stmt = $PDO->prepare("SELECT `id`, `name`, `admission_no` FROM `student` WHERE `class_id` = :class_id AND `date_deleted` IS NULL");
            $stmt->execute([':class_id' => $class_id]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    function checkStudentId($PDO, $studentId) {

    }

    function checkTeacherId($PDO, $teacherId) {

    }

    function submitHomework($PDO, $data, $student_id=false) {
        $date = $data['date_of_message'];
        if (!$student_id) {
            try {
                $stmt = $PDO->prepare("
                                    INSERT INTO `message`
                                    (`message`, `date_of_message`, `student_id`, `class_id`, `teacher_id`, `date_created`, `date_modified`)
                                    VALUES (:message, :date_of_message, :student_id, :class_id, :teacher_id, :date_created, :date_modified)
                                ");
                $stmt->execute([
                                ':message' => $data['message'], ':date_of_message' => $date,
                                ':student_id' => NULL, ':class_id' => $data['class_id'], ':teacher_id' => $_SESSION['data']['id'],
                                ':date_created' => (string) date("Y-m-d"), ':date_modified' => ((string) date("Y-m-d"))
                            ]);
                $lastMessage = getLastRow($PDO, 'message');
                addToLog($PDO, 'added homework', $_SESSION['data']['id'], $message_id=$lastMessage['id']);
                return $stmt->rowCount();
            } catch (Exception $e) {
                print($e);
                return NULL;
            }
        } else {
            try {
                $stmt = $PDO->prepare("
                                    INSERT INTO `message`
                                    (`message`, `date_of_message`, `student_id`, `class_id`, `teacher_id`, `date_created`, `date_modified`)
                                    VALUES (:message, :date_of_message, :student_id, :class_id, :teacher_id, :date_created, :date_modified)
                                ");
                $stmt->execute([
                                ':message' => $data['message'], ':date_of_message' => $date,
                                ':student_id' => $student_id, ':class_id' => $data['class_id'], ':teacher_id' => $_SESSION['data']['id'],
                                ':date_created' => ((string) date("Y-m-d")), ':date_modified' => ((string) date("Y-m-d"))
                            ]);
                $lastMessage = getLastRow($PDO, 'message');
                addToLog($PDO, 'added homework', $_SESSION['data']['id'], $message_id=$lastMessage['id']);
                return $stmt->rowCount();
            } catch (Exception $e) {
                print($e);
                return NULL;
            }
        }

    }

    // for loading the data with the class data
    $classes = getClasses($PDO);

    // for submiting a homework to the database
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST)) {
            $dateSubmitted = $_POST['date_of_homework'];
            $class_id = filter_var($_POST['class'], FILTER_SANITIZE_NUMBER_INT);
            $student_id = $_POST['student'];
            $homework = filter_var($_POST['homework'], FILTER_SANITIZE_STRING);
            try {
                $date = strtotime($dateSubmitted);
                $date = (string) date('Y-m-d', $date);
            } catch (Exception $e) {
                $error = "Date is wrong.. Please Enter a Valid Date!";
                return;
            }
            if (empty($student_id)) {
                $data = ['message' => $homework, 'date_of_message' => $date, 'class_id' => $class_id];
                if (!is_null(submitHomework($PDO, $data))) {
                    $success = 'Homework sent successfully. The page will refresh in 5 seconds.';
                    header("refresh:5;url=compose.php");
                } else {
                    $error = 'Something went wrong.. Try again';
                }

            } else {
                $data = ['message' => $homework, 'date_of_message' => $date, 'class_id' => $class_id];
                if (!is_null(submitHomework($PDO, $data, $student_id=$student_id))) {
                    $success = 'Homework sent successfully. The page will refresh in 5 seconds.';
                    header("refresh:5;url=compose.php");
                } else {
                    $error = 'Something went wrong.. Try again';
                }
            }
        }
    }
?>

<?php require_once(__DIR__.'/../header.html'); ?>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <title>Teacher panel | Compose</title>
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
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>teacher/" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Homeworks
                                </a>
                            </li>
                            <li class="nav-item active">
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
                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong><?php echo $error ?></strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <strong><?php echo $success ?></strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </section>

        <section id="compose" class="m-4">
            <div class="container-fluid">
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="form-group row">
                        <label for="date_of_homework" class="col-form-label col-md-2">Date of Homework*</label>
                        <input type="text" name="date_of_homework" class="form-control col-md-4" id="datetime" required autocomplete="off">
                    </div>
                    <div class="form-group row">
                        <label for="class" class="col-form-label col-md-2">Class*</label>
                        <select name="class" id="class" class="form-control col-md-4" required>
                            <option value="" selected>--</option>
                            <?php while ($class = array_shift($classes)): ?>
                                <option value="<?php echo $class['id'] ?>"><?php echo $class['class_name'] ?> - <?php echo $class['section'] ?></option>
                            <?php endwhile ?>
                        </select>
                    </div>
                    <div class="form-group row">
                        <label for="student" class="col-form-label col-md-2">Student</label>
                        <select name="student" id="students" class="form-control col-md-4">
                            <option value="" selected>All</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="homework" class="col-form-label">Homework*</label>
                        <textarea name="homework" autocomplete="off" cols="30" rows="10" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </section>
        <script>
            $(document).ready(function() {
                $('#datetime').datepicker();
                $('#class').change(function(e) {
                    $.ajax({
                        url: "<?php echo $_SERVER['PHP_SELF'] ?>",
						type: "GET",
						headers: {
							'Content-type': 'application/json'
						},
                        data: {
                            'class_id': e.target.value,
                        },
                        success: function(results) {
                            console.log(results);
                            if (results === '') {
                                return;
                            }
                            var UI_studentsSelect = $('#students');
                            UI_studentsSelect.empty();
                            UI_studentsSelect.append(new Option(`All`, ``, true, true));
                            try {
                                results = JSON.parse(results);
                            } catch(e) {

                            } finally {
                                results.forEach(function(result) {
                                    UI_studentsSelect.append(new Option(`${result.name} - ${result.admission_no}`, `${result.id}`));
                                });
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    })
                });
            });

        </script>
<?php require_once(__DIR__.'/../footer.html'); ?>
