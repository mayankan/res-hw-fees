<?php 

    session_start();
    function getTeacherData() {
        require_once(__DIR__ . '/../db/db.connection.php');
        $PDO = getConnection();
        if (is_null($PDO)) {
            die("Can't connect to database");
        }

        $stmt = $PDO->prepare("SELECT * FROM `teacher` WHERE `id` = :teacherid");
        $stmt->execute([':teacherid' => $_SESSION['id']]);
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return NULL;
    }

    getTeacherData();

    echo $_SESSION['role'];
    echo $_SESSION['id'];
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

<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">    
        <link rel="stylesheet" href="../static/main.css" type="text/css">
        <title>Teacher panel</title>
    </head>
    <body>
        <br>
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <!-- or idk name of teacher? like hello, something -->
                    <h1 class="display-4">Teacher Panel</h1>
                </div>
                <div class="col-md-6">
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" class="pt-2" method="GET">
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
                    <li class="tab col-md-4">
                        <a href="#homeworks" class="active black-text">Homeworks</a>
                    </li>
                    <li class="tab col-md-4">
                        <a href="#compose" class="black-text">Compose</a>
                    </li>
                    <li class="tab col-md-4">
                        <a href="#profile" class="black-text">Profile</a>
                    </li>
                </ul>
            </div>
            
            <div id="homeworks">
                <div class="row">
                    <div class="col-md-12">
                        <table class="higlight responsive-table">
                            <thead>
                                <th class="text-center">Date</th>
                                <th class="text-center">Homework</th>
                                <th class="text-center">Given By</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                                <tr>
                                    <td>lorem lorem lorem</td>
                                    <td>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore, fugit quisquam suscipit commodi recusandae consequatur ea officiis labore, modi dicta earum velit ipsa id iste, illum molestiae facilis mollitia ut.
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellat consequuntur minus asperiores ratione est adipisci id laudantium quasi sunt officiis rem eaque numquam at, provident, fugit quisquam! Voluptates, at quas.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis aliquid, odit alias eaque hic magnam commodi incidunt voluptatum obcaecati corrupti autem odio temporibus, maxime error sunt iure adipisci. Non, nesciunt!</td>
                                    <td>lorem lorem lorem lorem</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="compose">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Compose Homework</h4>
                    </div>
                </div>
            </div>

            <!-- Profile data fetching -->
            <?php 
                $profileData = getTeacherData();
            ?>
            <div id="profile">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Placeholder image -->
                        <div class="row justify-content-center">
                            <div class="col-xs-1">
                                <i class="large material-icons">account_circle</i>
                                <p class="ml-4 pt-4">Profile</p>
                            </div>
                        </div>
                        <!-- Teacher Data -->
                        <div class="row justify-content-center">
                            <!-- for changing the main data of that particular teacher -->
                            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" id="profileForm" class="col-md-6">
                                <input type="hidden" name="teacherDataUpdate" value="true">
                                <div class="input-field pb-4">
                                    <input type="text" id="name" name="teacherName" class="validate" value="<?php echo $profileData['name'] ?>">
                                    <label for="name">Name</label>
                                </div>
                                <div class="input-field pb-4">
                                    <input type="text" id="username" name="username" class="validate" value="lol">
                                    <label for="username">Username</label>
                                </div>
                                <div class="input-field pb-4">
                                    <input type="email" id="email" name="email" class="validate" value="lol">
                                    <label for="email">Email Address</label>
                                </div>
                                <div class="input-field pb-4">
                                    <button type="submit" class="btn waves-effect deep-purple lighten-1">Update</button>
                                    <button type="button" id="changePasswordBtn" class="btn waves-effect deep-purple lighten-1">Change Password</button>
                                </div>
                            </form>
                            <!-- for changing the password of the teacher -->
                            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" id="changePassForm" class="col-md-6">
                                <input type="hidden" name="teacherDataUpdate" value="false">
                                <div class="input-field pb-4">
                                    <input type="password" id="currentpassword" name="currentPass" class="validate">
                                    <label for="currentpassword">Current Password</label>
                                </div>
                                <div class="input-field pb-4">
                                    <input type="password" id="newpassword" name="newPass" class="validate">
                                    <label for="newpassword">New Password</label>
                                </div>
                                <div class="input-field pb-4">
                                    <input type="password" id="newconfirmpass" name="confirmNewPass" class="validate">
                                    <label for="confirmnewpass">Confirm new Password</label>
                                </div>
                                <div class="input-field pb-4">
                                    <button type="submit" class="btn waves-effect deep-purple lighten-1">Change Password</button>
                                    <button type="button" id="goBack" class="btn waves-effect deep-purple lighten-1">GO BACK</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>        
        <script src="../static/script.js"></script>
        <script>
            $(document).ready(function() {
                $('ul.tabs').tabs();
                $('#changePassForm').hide();

                $('#changePasswordBtn').on('click', function() {
                    $('#profileForm').hide();
                    $('#changePassForm').show();
                });

                $('#goBack').on('click', function() {
                    $('#profileForm').show();
                    $('#changePassForm').hide();
                });

            });
        </script>
    </body>
</html>