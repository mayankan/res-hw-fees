<?php 
    require(__DIR__.'/../config.php');
    session_start();

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

?>

<?php require_once(__DIR__.'/../header.html'); ?>
        <title>Student panel | Home</title>
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
                            <li class="nav-item active">
                                <a href="<?php echo $base_url ?>student/" class="nav-link">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> Homeworks
                                </a>
                            </li>
                            <li class="nav-item">
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

        

        <section id="homeworks">
            <div class="container-fluid">
                <div id="homeworks">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table responsive-table">
                                <thead>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Homework</th>
                                    <th class="text-center">Given By</th>
                                </thead>
                                <tbody>
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
            
<?php require_once(__DIR__.'/../footer.html');