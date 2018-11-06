<?php 
    $base_url = '/homework/';
    session_start();

    function getTeacherData() {
        require_once(__DIR__ . '/../db/db.connection.php');
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }

        $stmt = $PDO->prepare("SELECT * FROM `teacher` WHERE `id` = :teacherid");
        $stmt->execute([':teacherid' => $_SESSION['id']]);
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return NULL;
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

?>

<?php require_once(__DIR__.'/../header.html'); ?>
        <title>Teacher panel | Profile</title>
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
                            <li class="nav-item">
                                <a href="<?php echo $base_url ?>teacher/compose.php" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Compose
                                </a>
                            </li>
                            <li class="nav-item active">
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

        <section id="profile">
            <div class="container-fluid">
                
            </div>
        </section>
            
<?php require_once(__DIR__.'/../footer.html');