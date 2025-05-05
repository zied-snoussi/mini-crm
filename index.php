<?php
require_once 'config/env.php';

// Load environment variables
Env::load('.env');

require_once 'classes/Session.php';

$session = new Session();

// If user is not logged in, redirect to login page
if(!$session->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Redirect to dashboard
header('Location: dashboard.php');
exit;
?>
