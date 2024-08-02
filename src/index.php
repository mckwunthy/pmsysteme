<?php
session_start();
require "include.php";
verifParams(); //check all informations send by website users

// print_r($_SERVER);
// echo  BASE_URL . SP . 'images' . SP . 'bg-1.jpg';
// exit();
//requests manager
if (isset($_SERVER['PATH_INFO'])) {
    $url = trim($_SERVER['PATH_INFO'], '/');
    $url = explode('/', $url);
} else {
    $url = array("connexion");
}

//toutes les pages ou routes
$route = array(
    "connexion", "authentification", "deconnexion", "createproject", "managetask", "dashboard", "messages", "createmember"
);
// print_r($url);
// exit();;

$action = $url[0];

// controller
if (!in_array($action, $route)) { //si la route n'existe pas
    $title = "Page Error";
    $content = "<h1>URL introuvable !</h1>";
} else { //si la route ou page existe
    //echo 'Bienvenu sur la page '.$action;
    $function = "display" . ucwords($action);
    $title = "Page " . $action;
    $content = $function();
}
require VIEWS . SP . "templates" . SP . "default.php";