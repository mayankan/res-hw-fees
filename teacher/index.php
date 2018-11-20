<?php 
    require(__DIR__.'/../config.php');
    session_start();

    function getHomeworks($PDO, $teacherId) {
        try {
            $stmt = $PDO->prepare("SELECT * FROM `message` WHERE teacher_id = :teacher_id AND date_deleted IS NULL");
            $stmt->execute([':teacher_id' => $teacherId]);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetchAll();
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
            session_destroy();
            header('Location: ../');
        }
    }
    if (isset($_SESSION['data'][0]['id'])) {
        require(__DIR__ . '/../db/db.connection.php');
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }
        $homeworks = getHomeworks($PDO, $_SESSION['data'][0]['id']);
    }

?>

<?php require_once(__DIR__.'/../header.html'); ?>
        <title>Teacher panel | Home</title>
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
                                <form action="<? echo $_SERVER['PHP_SELF'] ?>" id="logout">
                                    <input type="hidden" name="logout" value="true">
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>   

        <section id="homeworks">
            <div class="container-fluid">
                <div id="homeworks">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table responsive-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Homework</th>
                                        <th class="text-center">Class</th>
                                        <th class="text-center">Student</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($homework = array_shift($homeworks)): ?>
                                <tr>
                                    <?php $date = date_create($homework['date_created']) ?>
                                    <td class="text-center"><?php print(date_format($date, 'Y-m-d')) ?></td>
                                    <td class="text-justify"><?php echo substr($homework['message'], 0, 50) ?></td>
                                    <td class="text-center"><?php echo $homework['class_id'] ?></td>
                                    <td class="text-center"><?php echo $homework['student_id'] ?></td>
                                </tr>
                                <?php endwhile ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
<?php require_once(__DIR__.'/../footer.html');