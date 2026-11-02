<?php
// Open once in browser: http://localhost/e-linx/admin/create_admin.php  -> then DELETE this file.
require_once 'includes/functions.php';
require_once 'includes/db.php';
if (count_of($conn, 'admins') > 0) exit('An admin already exists. Delete this file.');
$name = 'Admin'; $email = 'admin@elinx.com'; $pass = 'Admin@123';
$h = password_hash($pass, PASSWORD_DEFAULT);
$st = $conn->prepare('INSERT INTO admins (name,email,password) VALUES (?,?,?)');
$st->bind_param('sss', $name, $email, $h); $st->execute();
echo "Created. Login: $email / $pass  — now delete create_admin.php and change the password in Profile.";
