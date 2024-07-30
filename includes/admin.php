<?php

require_once "../includes/user.php";

if (User::getSessionUser()->role_id == 7)
{
    $base = explode(".", $_SERVER['SERVER_NAME'])[1];
    header("Location:http://$base");
    die();
}
