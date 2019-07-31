<?php
    /**
     * This is page is used to manage maintenance modes
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a fee clerk
    if ($_SESSION['role'] !== 'fee_clerk') {
        header('Location: ../404.html');
        return;
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
        die("Can't Connect to the database");
    }

    $lastMaintenance = getLastMaintenance($PDO);
    if (is_null($lastMaintenance)) {
        $_SESSION['error'] = "Not able to fetch last maintenance mode";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $maintenanceMode = isset($_POST['mode']) ? $_POST['mode'] : "";
        $customMessage = isset($_POST['custom_message']) ? convertString($_POST['custom_message']) : "";
        $bottomMessage = isset($_POST['bottom_message']) ? $_POST['bottom_message'] : "";

        if (
            $maintenanceMode === ""
            ||
            ($maintenanceMode !== "-1" && $maintenanceMode !== "1" && $maintenanceMode !== "0")
        ) {
            $_SESSION['error'] = 'Maintenance Mode is required.';
            header('Location: maintenance.php');
            exit();
        }

        if ($bottomMessage === "") {
            $_SESSION['error'] = 'Bottom Message is required.';
            header('Location: maintenance.php');
            exit();
        }

        if ($maintenanceMode === "-1" && $customMessage === "") {
            $_SESSION['error'] = "Custom Message is required in Offline with custom message.";
            header('Location: maintenance.php');
            exit();
        }

        if (updateMaintenance($PDO, $maintenanceMode, $customMessage, $bottomMessage)) {
            $_SESSION['success'] = 'Maintenance Mode successfully updated.';
            header('Location: maintenance.php');
            exit();
        } else {
            $_SESSION['error'] = 'Something went wrong in updating';
            header('Location: maintenance.php');
            exit();
        }
    }

    unset($PDO);

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
                        <li class="nav-item active">
                            <a href="<?php echo $base_url ?>fee_clerk/maintenance.php" class="nav-link">
                                Maintenance
                            </a>
                        </li>
                        <li class="nav-item">
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

        <section id="error" class="mt-4">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong><?php echo $_SESSION['error']; ?></strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <strong><?php echo $_SESSION['success']; ?></strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </section>

        <section id="uploadFee" class="container py-4">
            <h1 class="text-center"><u>Fee Maintenance</u></h1>
            <div class="row d-flex justify-content-center">
                <div class="col-md-8">
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                        <div class="form-group">
                            <label for="mode" class="col-form-label">
                                Mode&nbsp;<span class="text-danger">*</span>
                            </label>
                            <?php if (!is_null($lastMaintenance['offline'])): ?>
                                <?php switch($lastMaintenance['offline']): case "-1": ?>
                                    <select name="mode" id="mode" class="form-control" required>
                                        <option value="1">Offline</option>
                                        <option value="-1" selected>Offline with custom message</option>
                                        <option value="0">Online</option>
                                    </select>
                                    <?php break; case "1": ?>
                                    <select name="mode" id="mode" class="form-control" required>
                                        <option value="1" selected>Offline</option>
                                        <option value="-1">Offline with custom message</option>
                                        <option value="0">Online</option>
                                    </select>
                                    <?php break; case "0": ?>
                                    <select name="mode" id="mode" class="form-control" required>
                                        <option value="1">Offline</option>
                                        <option value="-1">Offline with custom message</option>
                                        <option value="0" selected>Online</option>
                                    </select>
                                <?php endswitch ?>
                            <?php else: ?>
                            <?php endif ?>
                        </div>
                        <div class="form-group" id="custom_message_textarea">
                            <label for="custom_message" class="col-form-label">
                                Custom Message&nbsp;<span class="text-danger">*</span>
                            </label>
                            <?php if (!is_null($lastMaintenance['custom_message'])): ?>
                            <textarea 
                                name="custom_message" 
                                id="custom_message" 
                                cols="30" 
                                rows="10" 
                                class="form-control"
                                required
                            ><?php echo $lastMaintenance['custom_message'] ?></textarea>
                            <?php else: ?>
                            <textarea 
                                name="custom_message" 
                                id="custom_message" 
                                cols="30" 
                                rows="10" 
                                class="form-control"
                                required
                            ></textarea>
                            <?php endif ?>
                        </div>
                        <div class="form-group">
                            <label for="bottom_message" class="col-form-label">
                                Bottom Message&nbsp;<span class="text-danger">*</span>
                            </label>
                            <?php if (!is_null($lastMaintenance['bottom_message'])): ?>
                            <textarea 
                                name="bottom_message" 
                                id="bottom_message" 
                                cols="30" 
                                rows="10" 
                                class="form-control"
                                required
                            ><?php echo $lastMaintenance['bottom_message'] ?></textarea>
                            <?php else: ?>
                            <textarea 
                                name="bottom_message" 
                                id="bottom_message" 
                                cols="30" 
                                rows="10" 
                                class="form-control"
                                required
                            ></textarea>
                            <?php endif ?>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-block">Upload Fees</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <script>
            $(document).ready(function () {

                const UIcustomText = $('#custom_message_textarea');
                const UIselectMode = $('select.form-control');
                UIselectMode[0].value === "-1" ? UIcustomText.show() : UIcustomText.hide();

                UIselectMode.on('change', function() {
                    if (this.value === '-1') {
                        UIcustomText.show();
                    } else {
                        UIcustomText.hide();
                    }
                });

            });
        </script>
<?php require_once(__DIR__.'/../footer.php'); ?>
