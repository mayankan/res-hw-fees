<?php 

    session_start();
    if (($_SESSION['role'])!='admin') {
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
        <title>Admin panel</title>
    </head>
    <body>
        <div class="container"></div>

<?php require_once(__DIR__.'/../footer.html'); ?>
