<?php 

    session_start();
    if (!(isset($_SESSION['role'])) && !(isset($_SESSION['id'])) ) {
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
        <title>Teacher panel</title>
    </head>
    <body>
        <ul id="slide-out" class="sidenav">
            <li>
                <div class="user-view">
                    <div class="background">
                        <img src="../static/images/background.jpg">
                    </div>
                    <a href=""><span class="white-text name"><?php echo $_SESSION['data']['name'] ?></span></a>
                    <?php if ($_SESSION['data']['email_address'] != NULL)  { ?>
                        <a href=""><span class="white-text email"><?php echo $_SESSION['data']['email_address'] ?></span></a>
                    <?php } else { ?>
                        <a href=""><span class="white-text email"> </span></a>
                    <?php } ?>
                </div>
            </li>
            <li>
                <a href="#!">
                    <i class="material-icons">assignment</i>Homeworks Given
                </a>
            </li>
            <li>
                <a href="#!">
                    <i class="material-icons">add_circle</i>Compose Homework
                </a>
            </li>
            <li>
                <a href="#!">
                    <i class="material-icons">account_circle</i>Profile
                </a>
            </li>
        </ul>
        <a href="#" data-target="slide-out" class="sidenav-trigger"><i class="material-icons">menu</i></a>

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="GET">
            <input type="hidden" value="true" name="logout">
            <button class="waves-effect waves-light btn">Logout</button>
        </form>
        
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>        
        <script src="../static/script.js"></script>
        <script>
            $(document).ready(function(){
                $('.sidenav').sidenav();
            });
        </script>
    </body>
</html>