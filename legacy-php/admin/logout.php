<?php
require_once 'includes/functions.php';
session_unset(); session_destroy();
session_start();
flash('success', 'You have been logged out.');
redirect('login.php');
