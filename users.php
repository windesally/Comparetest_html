<?php
$users = [
    "admin" => ["password" => password_hash("p@ssw0rd", PASSWORD_DEFAULT), "level" => "admin"],
    "win" => ["password" => password_hash("win", PASSWORD_DEFAULT), "level" => "admin"],
    "user" => ["password" => password_hash("1234", PASSWORD_DEFAULT), "level" => "staff"],
    "hello" => ["password" => password_hash("hi", PASSWORD_DEFAULT), "level" => "staff"]
];
?>