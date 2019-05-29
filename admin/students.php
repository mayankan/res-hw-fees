<?php
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    if ($_SESSION['role'] !== 'admin') {
        header('Location: ../404.html');
        return;
    }

    function getStudents($PDO, $start_limit=0, $name="", $admission_no="") {
        $sql = "SELECT * FROM `student`";
        $data = [];
        if ($name !== "") {
            $sql .= " WHERE `name` LIKE :name";
            $data[':name'] = '%' . $name . '%';
        }
        if ($admission_no !== "") {
            if ($name === "") {
                $sql .= " WHERE `admission_no` LIKE :adm_no";
            } else {
                $sql .= " AND `admission_no` LIKE :adm_no";
            }
            $data[':adm_no'] = '%' . $admission_no . '%';
        }
        $sql .= " LIMIT :start_limit,10";
        $data[':start_limit'] = $start_limit;
        try {
            $stmt = $PDO->prepare($sql);
            $stmt->execute($data);
            if ($stmt->rowCount() == 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
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

    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't connect to the database");
    }
    $students = NULL;
    if (!isset($_GET['page_no'])) {
        if (isset($_GET['name'])) {
            if (isset($_GET['admission_no'])) {
                $students = getStudents($PDO, $start_limit=0, $name=$_GET['name'], $admission_no=$_GET['admission_no']);
            } else {
                $students = getStudents($PDO, $start_limit=0, $name=$_GET['name']);
            }
        } else {
            if (isset($_GET['admission_no'])) {
                $students = getStudents($PDO, $start_limit=0, $name="", $admission_no=$_GET['admission_no']);
            } else {
                $students = getStudents($PDO);
            }
        }
        $_SESSION['page_no'] = 1;
    } else {
        $page_no = (int)$_GET['page_no'];
        if ($page_no <= 0) {
            header("Location: students.php?page_no=1");
            return;
        }
        $end_limit = $page_no * 10;
        $start_limit = $end_limit - 10;
        if (isset($_GET['name'])) {
            if (isset($_GET['admission_no'])) {
                $students = getStudents($PDO, $start_limit=$start_limit, $name=$_GET['name'], $admission_no=$_GET['admission_no']);
            } else {
                $students = getStudents($PDO, $start_limit=$start_limit, $name=$_GET['name']);
            }
        } else {
            if (isset($_GET['admission_no'])) {
                $students = getStudents($PDO, $start_limit=$start_limit, $name="", $admission_no=$_GET['admission_no']);
            } else {
                $students = getStudents($PDO, $start_limit=$start_limit);
            }
        }
        if ($students === NULL) {
            header('Location: students.php?page_no=' . (((int)$_GET['page_no']) - 1));
            return;
        }
        $_SESSION['page_no'] = $_GET['page_no'];
    }
?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <title>Admin Panel | View/Edit Students</title>
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
                            <li class="nav-item">
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
                            <li class="nav-item active">
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

        <section id="name" class="mt-2">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
                            <div class="form-group row d-flex justify-content-center">
                                <!-- Filter for Name -->
                                <?php if (isset($_GET['name'])): ?>
                                <input type="text" name="name" class="form-control col-3 m-2" value="<?php echo $_GET['name'] ?>" placeholder="Name">
                                <?php else: ?>
                                <input type="text" name="name" class="form-control col-3 m-2" placeholder="Name">
                                <?php endif ?>

                                <!-- Filter for Admission Number -->
                                <?php if (isset($_GET['admission_no'])): ?>
                                <input type="text" name="admission_no" class="form-control col-3 m-2" value="<?php echo $_GET['admission_no'] ?>" placeholder="Admission Number">
                                <?php else: ?>
                                <input type="text" name="admission_no" class="form-control col-3 m-2" placeholder="Admission Number">
                                <?php endif ?>

                                <button class="btn btn-success col-4 m-2">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <div id="students" class="mt-2">
            <div class="container-fluid">
                <div class="row pb-2">
                    <div class="col-6 d-flex justify-content-start">
                        <?php if ($_SESSION['page_no'] <= 1): ?>
                        <a href="#" class="btn btn-outline-dark" disabled>
                            <i class="fa fa-arrow-left fa-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php else: ?>
                        <a href="<?php echo $base_url ?>admin/students.php?page_no=<?php echo $_SESSION['page_no'] - 1 ?>" class="btn btn-outline-dark">
                            <i class="fa fa-arrow-left fa-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php endif ?>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <a href="<?php echo $base_url ?>admin/students.php?page_no=<?php echo $_SESSION['page_no'] + 1 ?>" class="btn btn-outline-dark">
                            Next <i class="fa fa-arrow-right fa-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <?php if (!is_null($students)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-responsive-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Admission Number</th>
                                    <th>Name</th>
                                    <th>Class & Section</th>
                                    <th>Fathers Name</th>
                                    <th>Mobile Number</th>
                                    <th>Gender</th>
                                    <th>DOB</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['admission_no'] ?></td>
                                    <td><?php echo $student['name'] ?></td>
                                    <?php $class = getClass($PDO, $student['class_id']) ?>
                                    <td><?php echo $class['class_name'] . ' - ' . $class['section'] ?></td>
                                    <td><?php echo $student['father_name'] ?></td>
                                    <td><?php echo $student['mobile_number'] ?></td>
                                    <?php if ((int) $student['gender'] === 1): ?>
                                    <td>Male</td>
                                    <?php else: ?>
                                    <td>Female</td>
                                    <?php endif ?>
                                    <?php $date = date_create($student['dob']) ?>
                                    <td><?php echo date_format($date, 'd-m-Y') ?></td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </div>
<?php require_once(__DIR__.'/../footer.php'); ?>
