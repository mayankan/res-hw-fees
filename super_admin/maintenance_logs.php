<?php
    /**
     * This page is used to view list of users
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a admin
    if ($_SESSION['role'] !== 'super_admin') {
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

    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't connect to the database");
    }
    $maintenanceLogs = NULL;
    if (!isset($_GET['page_no'])) {
        $maintenanceLogs = getMaintenanceLogs($PDO);
        $_SESSION['page_no'] = 1;
    } else {
        $page_no = (int)$_GET['page_no'];
        if ($page_no <= 0) {
            $page_no = 1;
        }
        $end_limit = $page_no * 10;
        $start_limit = $end_limit - 10;
        $maintenanceLogs = getMaintenanceLogs($PDO, $start_limit=$start_limit);
        if ($maintenanceLogs === NULL && $page_no !== 1) {
            header('Location: maintenance_logs.php?page_no=' . (((int)$_GET['page_no']) - 1));
            exit();
        }
        $_SESSION['page_no'] = $_GET['page_no'];
    }
    unset($PDO);

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
                        <li class="nav-item">
                            <a href="<?php echo $base_url ?>super_admin/teacher_logs.php" class="nav-link">
                                View Teacher Logs
                            </a>
                        </li>
                        <li class="nav-item active">
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

        <section id="teachers" class="mt-2">
            <div class="container-fluid">
                <div class="row pb-2">
                    <div class="col-6 d-flex justify-content-start">
                        <?php if ($_SESSION['page_no'] <= 1): ?>
                        <a href="#" class="btn btn-outline-dark" disabled>
                            <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php else: ?>
                        <a 
                            href="<?php echo $base_url ?>super_admin/maintenance_logs.php?page_no=<?php echo $_SESSION['page_no'] - 1 ?>" 
                            class="btn btn-outline-dark"
                        >
                            <i class="fa fa-arrow-left fa-1 mt-1" aria-hidden="true"></i> Prev
                        </a>
                        <?php endif ?>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <a 
                            href="<?php echo $base_url ?>super_admin/maintenance_logs.php?page_no=<?php echo $_SESSION['page_no'] + 1 ?>" 
                            class="btn btn-outline-dark"
                        >
                            Next <i class="fa fa-arrow-right fa-1 mt-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <?php if (!is_null($maintenanceLogs)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-responsive-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Bottom Message</th>
                                    <th>Custom Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($maintenanceLogs as $user): ?>
                                <tr>
                                    <td><?php echo date_format(date_create($user['date_created']), 'd F y'); ?></td>
                                    <td>
                                        <?php if ($user['offline'] === -1): ?>
                                        Offline with Custom Message
                                        <?php elseif ($user['offline'] === 1): ?>
                                        Offline
                                        <?php elseif ($user['offline'] === 0): ?>
                                        Online
                                        <?php endif ?>
                                    </td>
                                    <td><?php echo nl2br($user['bottom_message']); ?></td>
                                    <td><?php echo nl2br($user['custom_message']); ?></td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </section>
<?php require_once(__DIR__.'/../footer.php'); ?>
