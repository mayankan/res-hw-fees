<?php
    /**
     * This Page is used to show students all the homeworks for them
    */
    require(__DIR__.'/../config.php');
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a student
    if ($_SESSION['role'] !== 'student') {
        header('Location: ../404.html');
        return;
    }

    // checks for logout variable in GET Request and if it's true logs out user
    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
        }
    }

    echo date('');

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
                            <a href="<?php echo $base_url ?>student/profile.php" class="nav-link">
                                Profile
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?php echo $base_url ?>student/fee.php" class="nav-link">
                                Pay Fees
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

        <section id="details" class="container mt-4">
            <h1 class="text-center m-0">
                <u>Fee Details</u>
            </h1>
            <ul class="list-group mt-4">
                <li class="list-group-item">
                    Admission Number&nbsp;-&nbsp;
                </li>
                <li class="list-group-item">
                    Student Name&nbsp;-&nbsp;
                </li>
                <li class="list-group-item">
                    Mobile Number&nbsp;-&nbsp;
                </li>
            </ul>
            <div class="row mt-4">
                <div class="col">
                    <table class="table table-responsive">
                        <thead>
                            <th class="border">April 2019</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border">&#8377; 2180831</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h3 class="text-center mb-4">July 2019</h3>
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Portal Charges&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 d-flex justify-content-end">
                                    &#8377;&nbsp;320
                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Examination Fee&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Refreshment Account Fee&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Lab Fee&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Project Fee&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Annual Charges&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Admin Charges&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Smart Classes Charges&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Computer Fee Yearly&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Computer Fee Monthly&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Development Charges Yearly&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Transport Fee&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-primary">
                            <div class="row">
                                <div class="col-6">
                                    Late Fee&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                        <li class="list-group-item list-group-item-info mt-2">
                            <div class="row">
                                <div class="col-6">
                                    Total Fee&nbsp;-&nbsp;
                                </div>
                                <div class="col-6 justify-content-end">

                                </div>
                            </div>
                            
                        </li>
                    </ul>
                </div>
            </div>
        </section>
<?php require_once(__DIR__.'/../footer.php'); ?>
