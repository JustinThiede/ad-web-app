<?php
    if (!isset($_SESSION['USER'])) {
        header('Location: /');
    }
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf=8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Superuser</title>
        <script defer src="https://use.fontawesome.com/releases/v5.2.0/js/all.js" integrity="sha384-4oV5EgaV02iISL2ban6c/RmotsABqE4yZxZLcYMAdG7FAPsyHYAPpywE9PJo+Khy" crossorigin="anonymous"></script>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,800' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css" integrity="sha384-SI27wrMjH3ZZ89r4o+fGIJtnzkAnFs3E4qz9DIYioCQ5l9Rd/7UAa8DHcaL8jkWt" crossorigin="anonymous">
        <link rel="stylesheet" href="/template/css/main.css">
        <link rel="stylesheet" href="/template/css/spectrum.css">
    </head>

    <body>
        <div class="site-wrapper">
            <div class="row h-100 no-gutters">
                <div class="col-xl-2 col-12">
                    <div class="side-nav">
                        <a class="d-block" href="/user/index">Benutzer</a>
                        <a class="d-block" href="/group/index">Gruppen</a>
                        <?php require_once("pages/view/_partials/logout_form.php"); ?>
                    </div>
                </div>
