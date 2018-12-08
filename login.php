<?php
session_start();
//usuario/login.php

// verificar que se esta logueado
if ($_SESSION['login']){
  header("Location: ./index.php");
} else { //pedir datos
    header("Location: login.html");
}