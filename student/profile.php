<?php 
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    function getStudentData($PDO, $id) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `student` WHERE `id` = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

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
    $studentData = NULL;
    if ($_SESSION['data']['id']) {
        require(__DIR__ . '/../db/db.connection.php');
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $studentData = getStudentData($PDO, $_SESSION['data']['id']);
        $classData = NULL;
        if ($studentData !== NULL) {
            $classData = getClass($PDO, $studentData['class_id']);
        }
    }

?>

<?php require_once(__DIR__.'/../header.html'); ?>
        <title>Student panel | Profile</title>
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
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>student/" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Homeworks
                                </a>
                            </li>
                            <li class="nav-item active">
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

        

        <section id="profile" class="m-4">
            <div class="container-fluid">
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="form-group">
                        <!-- profile image -->
                    </div>
                    <div class="form-group row">
                        <label for="admission_no" class="col-form-label col-md-2">Admission Number</label>
                        <input disabled type="text" name="admission_no" class="form-control col-md-4" value="<?php echo $studentData['admission_no'] ?>">
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-form-label col-md-2">Full Name</label>
                        <input disabled type="text" name="full_name" class="form-control col-md-4" value="<?php echo $studentData['name'] ?>">
                    </div>
                    <div class="form-group row">
                        <label for="father_name" class="col-form-label col-md-2">Father's Name</label>
                        <input disabled type="text" father="father_name" class="form-control col-md-4" value="<?php echo $studentData['father_name'] ?>">
                    </div>
                    <div class="form-group row">
                        <label for="mother_name" class="col-form-label col-md-2">Mother's Name</label>
                        <input disabled type="text" father="mother_name" class="form-control col-md-4" value="<?php echo $studentData['mother_name'] ?>">
                    </div>
                    <div class="form-group row">
                        <?php $date = date_create($studentData['dob']) ?>
                        <label for="dob" class="col-form-label col-md-2">Date of Birth</label>
                        <input disabled type="text" father="dob" class="form-control col-md-4" value="<?php echo date_format($date, 'd F Y'); ?>">
                    </div>
                    <div class="form-group row">
                        <label for="gender" class="col-form-label col-md-2">Gender</label>
                        <input disabled type="text" father="gender" class="form-control col-md-4" value="<?php echo $studentData['gender'] ?>">
                    </div>
                    <div class="form-group row">
                        <label for="address" class="col-form-label col-md-2">address</label>
                        <textarea name="address" class="form-control col-md-4" disabled cols="30" rows="6"><?php echo $studentData['address'] ?></textarea>
                    </div>
                    <div class="form-group row">
                        <label for="mobile_number" class="col-form-label col-md-2">Mobile Number</label>
                        <input disabled type="number" name="mobile_number" class="form-control col-md-4" value="<?php echo $studentData['mobile_number'] ?>">
                    </div>
                    <div class="form-group row">
                        <label for="class" class="col-form-label col-md-2">Class & Section</label>
                        <input disabled type="text" name="class" class="form-control col-md-4" value="<?php echo $classData['class_name'] . ' - ' . $classData['section'] ?>">
                    </div>
                </form>
            </div>
        </section>
            
<?php require_once(__DIR__.'/../footer.html');