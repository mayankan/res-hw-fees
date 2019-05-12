<?php
    require(__DIR__.'/helpers.php');
    require(__DIR__.'/db/db.connection.php');
    session_start();
    if (isset($_SESSION['role'])) {
        header("Location: " . $_SESSION['role'] . "/");
    }

    function checkTeacher($username, $password, $PDO) {
        $password_hash = hash('sha256', $password);
        try {
            $stmt = $PDO->prepare("SELECT * FROM `teacher` WHERE `username` = :username AND `date_deleted` IS NULL");
            $stmt->execute([':username' => $username]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            $data = $stmt->fetch();
            if (hash_equals($password_hash, $data['password'])) {
                return $data;
            } else {
                return NULL;
            }
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    function checkStudent($username, $password, $PDO) {
        try {
            $pass = str_split($password);
            $admission_no = implode(array_slice($pass, 0, count($pass)-10));
            $mobile_no = implode(array_slice($pass, -10));
            if ($admission_no !== $username) {
                return NULL;
            }
            $stmt = $PDO->prepare("SELECT * FROM `student` WHERE `admission_no` = :username AND `mobile_number` = :mobile AND `date_deleted` IS NULL");
            $stmt->execute([':username' => $username, ':mobile' => $mobile_no]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            } else {
                return $stmt->fetch();
            }
        } catch (Exception $e) {
            print($e);
            return NULL;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST)) {

            // check admin before to make things faster and secure
            if (($_POST['username'] === "admin" && $_POST['password'] === "rainbow@123")||($_POST['username'] === "incharge" && $_POST['password'] === "rainbow@1")) {
                $_SESSION['role'] = 'admin';
                header('Location: admin/');
            }

            $PDO = getConnection();
            if (is_null($PDO)) {
                die("Can't connect to database");
            }


            $teacherData = checkTeacher($_POST['username'], $_POST['password'], $PDO);
            if ($teacherData != NULL) {
                $_SESSION['role'] = 'teacher';
                $_SESSION['data'] = $teacherData;
                addToLog($PDO, 'Teacher Logged in', $_SESSION['data']['id']);
                $PDO = null;
                header('Location: teacher/');
            }
            else {

                $studentData = checkStudent($_POST['username'], $_POST['password'], $PDO);
                if ($studentData != NULL) {
                    $_SESSION['role'] = 'student';
                    $_SESSION['data'] = $studentData;
                    $PDO = null;
                    header('Location: student/');
                } 
                else {
                    $_SESSION['error'] = "Invalid username or password";
                    header('Location: index.php');
                    return;
                }

            }
        }
    }
    
?>
<?php require_once(__DIR__.'/base_files/header.php'); ?>
        <title>Rainbow English Sr. Sec. School | Homework Login</title>
    </head>
    <body class="home-page">
	<div class="wrap-body">
		<header class="" style="background-color: red;">
			<div class="logo">
				<a href="#"><img src="images/logo.png" /></a>
				<span></span>
			</div>
			<div id="cssmenu" class="align-center">
				<ul>
					<li class="active has-sub"><a href="#"><span>Home</span></a>
					<ul>
							<li><a href="index"><span>Pay Fees</span></a></li>
							<li><a href="http://www.rainbowschooljp.com"><span>Back to RainbowSchoolJP.com</span></a></li>
					</ul></li>
					<li><a href="http://www.rainbowschooljp.com/admission"><span>Admission</span></a></li>
					<li><a href="http://www.rainbowschooljp.com/post-your-resume"><span>Careers</span></a></li>
					<li><a href="http://www.rainbowschooljp.com/download-links"><span>Downloads</span></a></li>
					<li><a href="http://www.rainbowschooljp.com/school-management-login-page"><span>Student Login</span></a></li>
					<li><a href="http://www.rainbowschooljp.com/event-gallery"><span>Events</span></a></li>
					<li class="has-sub"><a href="#"><span>About Us</span></a>
					<ul>
							<li><a href="http://www.rainbowschooljp.com/history"><span>History</span></a></li>
							<li><a href="http://www.rainbowschooljp.com/vision"><span>Vision and Mission</span></a></li>
							<li><a href="http://www.rainbowschooljp.com/emblem"><span>Emblem</span></a></li>
					</ul></li>
					<li class="last"><a href="http://www.rainbowschooljp.com/contact"><span>Contact Us</span></a></li>
				</ul>
				</div>
		</header>
		<br />
		<br />
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-6">
                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong><?php echo $_SESSION['error'] ?></strong>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                    <?php endif ?> 
                    <h3 class="text-center pb-2">Enter your Login Credentials</h3>
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                        <div class="form-group">
                            <label for="username">Username/Admission number</label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-form-label">Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
		<br />
		<br />
<?php require_once(__DIR__.'/base_files/footer.php'); ?>