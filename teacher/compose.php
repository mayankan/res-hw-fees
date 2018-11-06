<?php 
    require(__DIR__.'/../config.php');
    session_start();

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
                                <form action="<? echo $_SERVER['PHP_SELF'] ?>" id="logout">
                                    <input type="hidden" name="logout" value="true">
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>   

        

        <section id="compose" class="m-4">
            <div class="container-fluid">
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="class" class="col-form-label">Class</label>
                                <select name="class" class="form-control">
                                    <option value="class_id">class_name - section</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="student" class="col-form-label">Student</label>
                                <input type="text" class="form-control" name="student_id" list="students" oninput="console.log(this.value);">
                                <datalist id="students">
                                    <option value="all" selected>All</option>
                                    <option value="student_id">student_admission_no - student_name</option>
                                </datalist>
                                <script type="text/javascript">
    $(function() {
        $('input[name=student_id]').on('input',function() {
            var selectedOption = $('option[value="'+$(this).val()+'"]');
            console.log(selectedOption.length ? selectedOption.attr('id') : 'This opiton is not in the list!');
        });
    });
</script>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="homework" class="col-form-label">Homework</label>
                        <textarea name="homework" cols="30" rows="10" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
            
<?php require_once(__DIR__.'/../footer.html'); ?>

