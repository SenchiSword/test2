<?php
require_once __DIR__ . '/config.php';

function authenticate() {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        redirect(isAdmin() ? '/admin/dashboard.php' : '/client/dashboard.php');
    }
}