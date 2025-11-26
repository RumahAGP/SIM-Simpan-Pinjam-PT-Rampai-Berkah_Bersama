<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function cekLoginAdmin() {
    if (empty($_SESSION['user_login']) || ($_SESSION['role'] ?? '') !== 'Admin') {
        header("Location: ../index.php"); 
        exit;
    }
}

function cekLoginNasabah() {
    if (empty($_SESSION['user_login']) || ($_SESSION['role'] ?? '') !== 'Nasabah') {
        header("Location: ../index.php");
        exit;
    }
}
