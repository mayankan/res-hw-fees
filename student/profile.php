<?php
    /**
     * This Page is used to see profile of student
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

    // Global $studentData data for the response and server
    $studentData = NULL;
    if ($_SESSION['data']['id']) {
        require(__DIR__ . '/../db/db.connection.php');
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $studentData = getStudent($PDO, $_SESSION['data']['id']);
        $classData = NULL;
        // this shouldn't happen in normal conditions but still checking
        if ($studentData !== NULL) {
            $classData = getClass($PDO, $studentData['class_id']);
        }
        unset($PDO);
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
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>student/" class="nav-link">
                                Homeworks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>student/fee.php" class="nav-link">
                                Pay Fees
                            </a>
                        </li>
                        <li class="nav-item active">
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

        <?php if (!is_null($studentData)): ?>
        <section id="profile" class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8">
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                        <div class="form-group">
                            <!-- profile image -->
                        </div>
                        <div class="form-group">
                            <label for="admission_no" class="col-form-label">Admission Number</label>
                            <input disabled type="text" name="admission_no" class="form-control" 
                                value="<?php echo $studentData['admission_no'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-form-label">Full Name</label>
                            <input disabled type="text" name="full_name" class="form-control" 
                                value="<?php echo $studentData['name'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="father_name" class="col-form-label">Father's Name</label>
                            <input disabled type="text" father="father_name" class="form-control"  
                                value="<?php echo $studentData['father_name'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="mother_name" class="col-form-label">Mother's Name</label>
                            <input disabled type="text" father="mother_name" class="form-control" 
                                value="<?php echo $studentData['mother_name'] ?>">
                        </div>
                        <div class="form-group">
                            <?php $date = date_create($studentData['dob']) ?>
                            <label for="dob" class="col-form-label">Date of Birth</label>
                            <input disabled type="text" father="dob" class="form-control" 
                                value="<?php echo date_format($date, 'd F Y'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="gender" class="col-form-label">Gender</label>
                            <input disabled type="text" father="gender" class="form-control" 
                                value="<?php if ((int) $studentData['gender'] === 1): ?>Male<?php else: ?>Female<?php endif ?>">
                        </div>
                        <div class="form-group">
                            <label for="address" class="col-form-label">Address</label>
                            <textarea 
                                name="address" 
                                class="form-control" 
                                disabled 
                                cols="30" 
                                rows="6"
                            ><?php echo $studentData['address'] ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="mobile_number" class="col-form-label">Mobile Number</label>
                            <input disabled type="number" name="mobile_number" class="form-control" 
                                value="<?php echo $studentData['mobile_number'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="class" class="col-form-label">Class & Section</label>
                            <input disabled type="text" name="class" class="form-control" 
                                value="<?php echo $classData['class_name'] . ' - ' . $classData['section'] ?>">
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <?php else: ?>
        <section id="profile" class="m-4">
            <div class="container-fluid">
                <h1 class="display-2">Something went wrong try refreshing</h1>
            </div>
        </section>
        <?php endif ?>
<?php require_once(__DIR__.'/../footer.php');
