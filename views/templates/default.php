<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--animate cdn link / bootstrap / main css-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css">
    <title> <?php echo $title ?> </title>
</head>

<body>
    <nav class="navbar navbar-expand navbar-primary bg-dark">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php
                    //page d'atterissage
                    echo '
                         <li class="nav-item">
                            <a class="nav-link" aria-current="page">S.G Projet</a>
                        </li>
                        ';

                    //profil si connecté
                    if (isset($_SESSION["user"])) {
                        echo '
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="' . BASE_URL . SP . 'messages">Messages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="' . BASE_URL . SP . 'createproject">Create Project</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="' . BASE_URL . SP . 'managetask">Manage task</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="' . BASE_URL . SP . 'dashboard">Dashboard</a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" aria-current="page"><em><strong class="emailMember">' . $_SESSION["user"]["email"] . '</strong></em></a>
                        </li>
                </ul>';

                        //deconnexion
                        echo '
                       <form class="d-flex" method="POST" action="deconnexion">
                        <button class="btn btn-outline-light me-2 bg-danger" type="submit">Deconnexion</button>
                        </form>
                       ';
                    }
                    ?>

            </div>
        </div>
    </nav>

    <div class="container">
        <div class="layout d-none"></div>
        <?php echo $content ?>
    </div>

    <!--jquery / bootstrap popper / bootstrap js / others js script-->
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <script src="<?php echo BASE_URL . SP; ?>js/app.js"></script>
    <script src="<?php echo BASE_URL . SP; ?>js/main.js"></script>
</body>

</html>