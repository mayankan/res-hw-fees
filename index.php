<?php

    function checkTeacher($username, $password, $PDO) {
        $stmt = $PDO->prepare("SELECT * FROM `teacher` WHERE `name` = :username AND `password` = :password");
        $stmt->execute([':username' => $username, ':password' => $password]);
        if ($stmt->rowCount() === 0) {
            return NULL;
        } else {
            return $stmt->fetch();
        }
    }

    function checkStudent($username, $password, $PDO) {
        $pass = str_split($password);
        $admission_no = implode(array_slice($pass, 0, count($pass)-10));
        $mobile_no = implode(array_slice($pass, -10));
        if ($admission_no !== $username) {
            return NULL;
        }
        $stmt = $PDO->prepare("SELECT * FROM `student` WHERE `admission_no` = :username AND `mobile_number` = :mobile");
        $stmt->execute([':username' => $username, ':mobile' => $mobile_no]);
        if ($stmt->rowCount() === 0) {
            return NULL;
        } else {
            return $stmt->fetch();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST)) {
            require(__DIR__ . '/db/db.connection.php');
            $PDO = getConnection();
            if (is_null($PDO)) {
                die("Can't connect to database");
            }
            $teacherData = checkTeacher($_POST['username'], $_POST['password'], $PDO);
            if($_POST['username']==="admin" && $_POST['password']==="rainbow@12345")
            {
                print_r("Admin Panel");
            } elseif($teacherData != NULL) {
                print_r($teacherData);
            } else
            {
                $studentData = checkStudent($_POST['username'], $_POST['password'], $PDO);
                if($studentData!=NULL)
                {
                    print_r($studentData);
                } else
                {
                    $error = "Invalid username or password";
                    echo $error;
                }
            }
        }
    }
    
?>



<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Login</title>
    <style>
        .container, .row {
            height: 100vh;
        }
    </style>
  </head>
  <body>
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6">
            <h3 class="text-center">Enter your Login Credentials</h3>
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                    <div class="form-group">
                        <label for="username" class="col-form-label">Username/Admission number</label>
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

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>