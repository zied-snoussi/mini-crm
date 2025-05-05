<header class="header">
    <div class="logo">
        <h1>Mini CRM</h1>
    </div>
    <div class="user-menu">
        <!-- Display the username with a fallback -->
        <span class="username">
            <?php 
                $username = $session->get('username') ?? 'Guest';
                echo 'Welcome, ' . htmlspecialchars($username); 
            ?>
        </span>
        <!-- Logout button -->
        <a href="logout.php" class="btn btn-sm btn-secondary">Logout</a>
    </div>
</header>