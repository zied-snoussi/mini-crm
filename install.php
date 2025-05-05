<?php
require_once 'config/env.php';

// Check if .env file exists
if (!file_exists('.env')) {
    // Create .env file from example if it doesn't exist
    if (file_exists('.env.example')) {
        copy('.env.example', '.env');
        $env_created = true;
    } else {
        $env_error = "Could not find .env.example file. Please create a .env file manually.";
    }
}

// Load environment variables
Env::load('.env');

// Check if uploads directory exists
if (!is_dir('uploads')) {
    // Create uploads directory if it doesn't exist
    if (mkdir('uploads', 0755)) {
        $uploads_created = true;
    } else {
        $uploads_error = "Could not create uploads directory. Please create it manually and ensure it's writable.";
    }
}

// Check database connection
$db_connected = false;
$db_error = null;

try {
    $host = Env::get('DB_HOST', 'localhost');
    $username = Env::get('DB_USERNAME', 'root');
    $password = Env::get('DB_PASSWORD', '');
    $database = Env::get('DB_DATABASE', 'mini_crm');
    
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists
    $stmt = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
    $db_exists = $stmt->fetchColumn();
    
    if (!$db_exists) {
        // Create database if it doesn't exist
        $conn->exec("CREATE DATABASE `$database`");
        $db_created = true;
    }
    
    // Connect to the database
    $conn->exec("USE `$database`");
    $db_connected = true;
    
    // Check if tables exist
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) === 0) {
        // Import database schema if tables don't exist
        if (file_exists('database.sql')) {
            $sql = file_get_contents('database.sql');
            $conn->exec($sql);
            $tables_created = true;
        } else {
            $tables_error = "Could not find database.sql file. Please import it manually.";
        }
    }
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// Installation status
$installation_complete = $db_connected && !isset($uploads_error) && !isset($env_error) && !isset($tables_error);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Mini CRM</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .install-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .install-step {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .step-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .step-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .step-pending {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <h1>Mini CRM Installation</h1>
        
        <?php if ($installation_complete): ?>
            <div class="alert alert-success">
                <h2>Installation Complete!</h2>
                <p>The Mini CRM has been successfully installed. You can now <a href="index.php">login to your application</a>.</p>
                <p>Default login credentials:</p>
                <ul>
                    <li><strong>Username:</strong> admin</li>
                    <li><strong>Password:</strong> admin123</li>
                </ul>
                <p><strong>Important:</strong> For security reasons, please change the default password after your first login.</p>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <h2>Installation Incomplete</h2>
                <p>Please fix the errors below to complete the installation.</p>
            </div>
        <?php endif; ?>
        
        <h2>Installation Steps</h2>
        
        <!-- Environment Configuration -->
        <div class="install-step <?php echo isset($env_error) ? 'step-error' : 'step-success'; ?>">
            <h3>1. Environment Configuration</h3>
            <?php if (isset($env_error)): ?>
                <p class="text-danger"><?php echo $env_error; ?></p>
            <?php elseif (isset($env_created)): ?>
                <p>Created .env file from example. Please review and update the configuration as needed.</p>
            <?php else: ?>
                <p>Environment file (.env) exists.</p>
            <?php endif; ?>
        </div>
        
        <!-- Uploads Directory -->
        <div class="install-step <?php echo isset($uploads_error) ? 'step-error' : 'step-success'; ?>">
            <h3>2. Uploads Directory</h3>
            <?php if (isset($uploads_error)): ?>
                <p class="text-danger"><?php echo $uploads_error; ?></p>
            <?php elseif (isset($uploads_created)): ?>
                <p>Created uploads directory.</p>
            <?php else: ?>
                <p>Uploads directory exists.</p>
            <?php endif; ?>
        </div>
        
        <!-- Database Connection -->
        <div class="install-step <?php echo isset($db_error) ? 'step-error' : 'step-success'; ?>">
            <h3>3. Database Connection</h3>
            <?php if (isset($db_error)): ?>
                <p class="text-danger">Error connecting to database: <?php echo $db_error; ?></p>
                <p>Please check your database credentials in the .env file.</p>
            <?php elseif (isset($db_created)): ?>
                <p>Created database '<?php echo $database; ?>'.</p>
            <?php else: ?>
                <p>Connected to database '<?php echo $database; ?>'.</p>
            <?php endif; ?>
        </div>
        
        <!-- Database Tables -->
        <div class="install-step <?php echo isset($tables_error) ? 'step-error' : (isset($tables_created) ? 'step-success' : (count($tables ?? []) > 0 ? 'step-success' : 'step-pending')); ?>">
            <h3>4. Database Tables</h3>
            <?php if (isset($tables_error)): ?>
                <p class="text-danger"><?php echo $tables_error; ?></p>
            <?php elseif (isset($tables_created)): ?>
                <p>Created database tables and imported sample data.</p>
            <?php elseif (isset($tables) && count($tables) > 0): ?>
                <p>Database tables already exist.</p>
            <?php else: ?>
                <p>Database tables need to be created.</p>
            <?php endif; ?>
        </div>
        
        <?php if (!$installation_complete): ?>
            <div class="form-group">
                <a href="install.php" class="btn btn-primary">Retry Installation</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
