<?php
    /**
     * This page is used to view all logs for the homework system
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a admin
    if ($_SESSION['role'] !== 'super_admin') {
        header('Location: ../404.html');
        return;
    }
    
    /**
     * Get data for all logs
     *
     * @param PDOObject $PDO
     * @param Number $startLimit
     *
     * @return Logs $data
     *
     * @throws Exception //No Specefic Exception Defined
     *
    */
    function getLogs($PDO, $classId="", $startLimit=0) {
        $sql = "SELECT * FROM `log` ORDER BY `date_of_action` DESC LIMIT :start_limit, 10";
        $data = [];
        $data[':start_limit'] = $startLimit;
        try {
            $stmt = $PDO->prepare($sql);
            $stmt->execute($data);
            if ($stmt->rowCount() === 0) {
                return NULL;
            }
            return $stmt->fetchAll();
        } catch(Exception $e) {
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
    $logs = NULL;
    if (!isset($_GET['page_no'])) {
        $logs = getLogs($PDO);
        $_SESSION['page_no'] = 1;
    } else {
        $pageNumber = (int)$_GET['page_no'];
        if ($pageNumber <= 0) {
            $pageNumber = 1;
        }
        $end_limit = $pageNumber * 10;
        $startLimit = $end_limit - 10;
        $logs = getLogs($PDO, $startLimit=$startLimit);
        if ($logs === NULL) {
            header('Location: teacher_logs.php?page_no=' . (((int)$_GET['page_no']) - 1));
            return;
        }
        $_SESSION['page_no'] = $_GET['page_no'];
    }

?>

<?php require_once(__DIR__.'/../header.php'); ?>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container">
                <a href="#" class="navbar-brand">Super Admin Panel</a>
                <button class="navbar-toggler" data-toggle="collapse" data-target="#teacher-nav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="teacher-nav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/" class="nav-link">
                                View Fee
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?php echo $base_url ?>super_admin/teacher_logs.php" class="nav-link">
                                View Teacher Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/maintenance_logs.php" class="nav-link">
                                View Maintenance Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/view_admins.php" class="nav-link">
                                View Admins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/create_admins.php" class="nav-link">
                                Create Admins
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

        <section id="error" class="mt-4">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong><?php echo $_SESSION['error'] ?></strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                        <?php endif ?> 
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <strong><?php echo $_SESSION['success'] ?></strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                        <?php endif ?> 
                    </div>
                </div>
            </div>
        </section>

        <?php if (!is_null($logs)): ?>
        <section id="logs" class="mb-2">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-4 d-flex justify-content-start">
                        <?php if ($_SESSION['page_no'] <= 1): ?>
                        <a href="#" class="btn btn-outline-dark" disabled>
                            <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php else: ?>
                        <a href="<?php echo $base_url ?>super_admin/teacher_logs.php?page_no=<?php echo $_SESSION['page_no'] - 1 ?>" class="btn btn-outline-dark">
                            <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php endif ?>
                    </div>
                    <div class="col-4">
                        <a href="<?php echo $base_url ?>super_admin/export_logs.php" class="btn btn-success btn-block">Export Logs</a>
                    </div>
                    <div class="col-4 d-flex justify-content-end">
                        <a href="<?php echo $base_url ?>super_admin/teacher_logs.php?page_no=<?php echo $_SESSION['page_no'] + 1 ?>" class="btn btn-outline-dark">
                            Next <i class="fa fa-arrow-right fa-1 mt-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-responsive-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Date of Log</th>
                                    <th>Action</th>
                                    <th>Homework</th>
                                    <th>Date of Homework</th>
                                    <th>Class & Section</th>
                                    <th>Student Sent to</th>
                                    <th>Teacher Assigned</th>
                                    <th>IP Address</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <?php $homework = NULL; ?>
                                    <tr>
                                        <td>
                                            <?php echo $log['date_of_action'] ?>
                                        </td>
                                        <td>
                                            <?php echo $log['log_action'] ?>
                                        </td>

                                        <?php if (is_null($log['message_id'])): ?>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        <?php else: ?>
                                            <?php $homework = getAllHomework($PDO, $log['message_id']) ?>
                                            <td>
                                                <?php echo substr($homework['message'], 0, 50) ?>
                                            </td>
                                            <td>
                                                <?php echo $homework['date_of_message'] ?>
                                            </td>
                                            <td>
                                                <?php $class = getClass($PDO, $homework['class_id']); ?>
                                                <?php echo $class['class_name'] . ' - ' . $class['section']; ?>
                                            </td>
                                        <?php endif ?>

                                        <?php if (is_null($homework['student_id'])): ?>
                                            <td></td>
                                        <?php else: ?>
                                            <td>
                                                <?php echo getStudent($PDO, $homework['student_id'])['name'] ?>
                                            </td>
                                        <?php endif ?>

                                        <td>
                                            <?php echo getTeacherName($PDO, $log['teacher_id']); ?>
                                        </td>
                                        <td>
                                            <?php echo $log['ip_address'] ?>
                                        </td>

                                        <?php if (isset($_GET['page_no'])): ?>
                                            <td>
                                                <a 
                                                    href="<?php echo $base_url ?>super_admin/log.php?homeworkId=<?php echo $log['id'] ?>&page_no=<?php echo $_GET['page_no'] ?>" 
                                                    class="btn btn-outline-warning btn-block"
                                                >
                                                        View
                                                </a>
                                            </td>
                                            <?php else: ?>
                                            <td>
                                                <a 
                                                    href="<?php echo $base_url ?>super_admin/log.php?homeworkId=<?php echo $log['id'] ?>" 
                                                    class="btn btn-outline-warning btn-block"
                                                >
                                                        View
                                                </a>
                                            </td>
                                        <?php endif ?>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    <?php endif ?>
<?php require_once(__DIR__.'/../footer.php'); ?>
<?php unset($PDO); ?>
