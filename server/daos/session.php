<?php
session_start();

function isLoggedIn()
{
    return isset($_SESSION['user']);
}

function isAdmin()
{
    return isLoggedIn() && $_SESSION['user']['role'] === 'admin';
}

function getUser()
{
    return $_SESSION['user'] ?? null;
}
