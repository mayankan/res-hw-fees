<?php 
    
    session_start();
    if ((($_SESSION['role'])!='student') && !(isset($_SESSION['id'])) ) {
        header('Location: ../');
    }

    if (isset($_GET['logout'])) {
        if ($_GET['logout'] === 'true') {
            session_destroy();
            header('Location: ../');
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">        
        <link rel="stylesheet" href="../static/main.css" type="text/css">
        <title>Student panel</title>
    </head>
    <body>
        
    <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <!-- or idk name of student? like hello, something -->
                    <h1 class="display-4">Student Panel</h1>
                </div>
                <div class="col-md-6">
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" class="pt-4" method="GET">
                        <input type="hidden" value="true" name="logout">
                        <button class="right waves-effect waves-light btn deep-purple lighten-1">Logout</button>
                    </form>
                </div>
            </div>
            <hr>
        </div>

        <div class="container">
            <div class="row">
                <ul class="tabs">
                    <li class="tab col-md-6">
                        <a href="#homeworks" class="active black-text">Homeworks</a>
                    </li>
                    <li class="tab col-md-6">
                        <a href="#profile" class="black-text">Profile</a>
                    </li>
                </ul>
            </div>
            
            <div id="homeworks">
                <div class="row">
                    <div class="col-md-12">
                        <table class="higlight responsive-table">
                            <thead>
                                <th>Message</th>
                                <th>Date of Message</th>
                                <th>Sent By</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div id="profile">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Profile</h4>
                        
                    </div>
                </div>
            </div>
        </div>
        
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>        
        <script src="../static/script.js"></script>
        <script>
            $(document).ready(function() {
                $('ul.tabs').tabs();
            });
        </script>
    </body>
</html>