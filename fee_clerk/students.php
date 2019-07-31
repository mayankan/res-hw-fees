<?php
    /**
     * This page is used to read all students in the database
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a admin
    if ($_SESSION['role'] !== 'fee_clerk') {
        header('Location: ../404.html');
        return;
    }

    /**
     * Get data for students with some filters
     *
     * @param PDOObject $PDO
     * @param Number $startLimit
     * @param String $name
     * @param String $admission_no
     * @param String $class_id
     *
     * @return Student $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function getStudents($PDO, $start_limit=0, $name="", $admission_no="", $class_id="") {
        $sql = "SELECT * FROM `student` WHERE 1=1 AND `date_deleted` IS NULL AND";
        $data = [];
        if ($name !== "") {
            $sql .= "`name` LIKE :name AND";
            $data[':name'] = '%' . $name . '%';
        }
        if ($admission_no !== "") {
            $sql .= "`admission_no` LIKE :adm_no AND";
            $data[':adm_no'] = '%' . $admission_no . '%';
        }
        if ($class_id !== "") {
            $sql .= "`class_id` = :class_id AND";
            $data[':class_id'] = $class_id;
        }
        $sql = substr($sql, 0, strlen($sql) - 4);
        $sql .= " LIMIT :start_limit,10;";
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

    // checks for logout variable in GET Request and if it's true logs out user
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
        $admission_no = isset($_GET['admission_no']) ? $_GET['admission_no'] : "";
        $name = isset($_GET['name']) ? $_GET['name'] : "";
        $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : "";
        /*
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
        */
        $students = getStudents($PDO, $start_limit=0, $name=$name, $admission_no=$admission_no, $class_id=$class_id);
        $_SESSION['page_no'] = 1;
    } else {
        $page_no = (int)$_GET['page_no'];
        if ($page_no <= 0) {
            header("Location: students.php?page_no=1");
            return;
        }
        $end_limit = $page_no * 10;
        $start_limit = $end_limit - 10;
        $admission_no = isset($_GET['admission_no']) ? $_GET['admission_no'] : "";
        $name = isset($_GET['name']) ? $_GET['name'] : "";
        $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : "";
        /*
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
        */
        $students = getStudents($PDO, $start_limit=$start_limit, $name=$name, $admission_no=$admission_no, $class_id=$class_id);
        if ($students === NULL && $page_no !== 1) {
            header('Location: students.php?page_no=' . (((int)$_GET['page_no']) - 1));
            return;
        }
        $_SESSION['page_no'] = $_GET['page_no'];
    }
?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container">
                <a href="#" class="navbar-brand">Fee Admin Panel</a>
                <button class="navbar-toggler" data-toggle="collapse" data-target="#teacher-nav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="teacher-nav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>fee_clerk/" class="nav-link">
                                View Fee
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>fee_clerk/upload_fee.php" class="nav-link">
                                Upload Fee
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>fee_clerk/maintenance.php" class="nav-link">
                                Maintenance
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?php echo $base_url ?>fee_clerk/students.php" class="nav-link">
                                View Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>fee_clerk/delete_fee.php" class="nav-link">
                                Delete Fee
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

        <section id="name" class="mt-2">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
                            <div class="form-group row d-flex justify-content-center">
                                <!-- Filter for Name -->
                                <?php if (isset($_GET['name'])): ?>
                                <input type="text" name="name" 
                                    class="form-control col-3 m-2" 
                                    value="<?php echo $_GET['name'] ?>" placeholder="Name"
                                >
                                <?php else: ?>
                                <input type="text" name="name" class="form-control col-3 m-2" placeholder="Name">
                                <?php endif ?>

                                <!-- Filter for Admission Number -->
                                <?php if (isset($_GET['admission_no'])): ?>
                                <input type="text" name="admission_no"
                                       class="form-control col-3 m-2" 
                                       value="<?php echo $_GET['admission_no'] ?>" placeholder="Admission Number">
                                <?php else: ?>
                                <input type="text" name="admission_no" class="form-control col-3 m-2" placeholder="Admission Number">
                                <?php endif ?>

                                <!-- Filter for Class -->
                                <?php if (isset($_GET['class_id'])): ?>
                                <select name="class_id" class="form-control col-3 m-2">
                                <?php $classes = getAllClasses($PDO); ?>
                                <option value="">Classes</option>
                                <?php while ($class = array_shift($classes)): ?>
                                    <?php if ($class['id'] == $_GET['class_id']): ?>
                                    <option value="<?php echo $class['id'] ?>" selected>
                                        <?php echo $class['class_name'] ?> - <?php echo $class['section'] ?>
                                    </option>
                                    <?php else: ?>
                                    <option value="<?php echo $class['id'] ?>"><?php echo $class['class_name'] ?> - <?php echo $class['section'] ?></option>
                                    <?php endif ?>
                                <?php endwhile ?>
                                </select>
                                <?php else: ?>
                                <select name="class_id" class="form-control col-3 m-2">
                                <?php $classes = getAllClasses($PDO); ?>
                                <option value="" selected>Classes</option>
                                <?php while ($class = array_shift($classes)): ?>
                                    <option value="<?php echo $class['id'] ?>">
                                        <?php echo $class['class_name'] ?> - <?php echo $class['section'] ?>
                                    </option>
                                <?php endwhile ?>
                                </select>
                                <?php endif ?>
                                <button class="btn btn-success col-3 m-2">Filter</button>
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
                            <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php else: ?>
                            <?php $backUrl = $base_url . "fee_clerk/students.php?page_no=" . ((int)$_SESSION['page_no'] - 1); ?>
                            <?php if (isset($_GET['name'])): ?>
                                <?php $backUrl .= "&name=" . $_GET['name']; ?>
                            <?php endif ?>
                            <?php if (isset($_GET['admission_no'])): ?>
                                <?php $backUrl .= "&admission_no=" . $_GET['admission_no']; ?>
                            <?php endif ?>
                            <?php if (isset($_GET['class_id'])): ?>
                                <?php $backUrl .= "&class_id=" . $_GET['class_id']; ?>
                            <?php endif ?>
                            <a href="<?php echo $backUrl ?>" class="btn btn-outline-dark">
                                <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                            </a>
                        <?php endif ?>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <?php $nextUrl = $base_url . "fee_clerk/students.php?page_no=" . ((int)$_SESSION['page_no'] + 1); ?>
                        <?php if (isset($_GET['name'])): ?>
                            <?php $nextUrl .= "&name=" . $_GET['name']; ?>
                        <?php endif ?>
                        <?php if (isset($_GET['admission_no'])): ?>
                            <?php $nextUrl .= "&admission_no=" . $_GET['admission_no']; ?>
                        <?php endif ?>
                        <?php if (isset($_GET['class_id'])): ?>
                            <?php $nextUrl .= "&class_id=" . $_GET['class_id']; ?>
                        <?php endif ?>
                        <a href="<?php echo $nextUrl ?>" class="btn btn-outline-dark">
                            Next <i class="fa fa-arrow-right fa-1 mt-1" aria-hidden="true"></i>
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
<?php unset($PDO); ?>
