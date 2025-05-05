<header class="header">
    <div class="logo">
        <h1>Mini CRM</h1>
    </div>
    <div class="user-menu">
        <span class="username"><?php echo htmlspecialchars($session->get('username')); ?></span>
        <a href="logout.php" class="btn btn-sm btn-secondary">Logout</a>
    </div>
</header>
