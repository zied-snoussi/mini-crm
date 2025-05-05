<?php
require_once 'classes/Session.php';
require_once 'classes/Prospect.php';

$session = new Session();
$session->requireLogin();

$prospect = new Prospect();
$error = '';
$success = '';

// Check if editing an existing prospect
$editing = false;
if(isset($_GET['id'])) {
    $prospect->id = $_GET['id'];
    if($prospect->read_single()) {
        $editing = true;
    } else {
        header('Location: dashboard.php');
        exit;
    }
}

// Process form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prospect->name = $_POST['name'] ?? '';
    $prospect->company = $_POST['company'] ?? '';
    $prospect->phone = $_POST['phone'] ?? '';
    $prospect->email = $_POST['email'] ?? '';
    $prospect->status = $_POST['status'] ?? 'new';
    $prospect->user_id = $session->get('user_id');
    
    if(empty($prospect->name) || empty($prospect->email)) {
        $error = 'Name and email are required fields';
    } else {
        if($editing) {
            if($prospect->update()) {
                $success = 'Prospect updated successfully';
            } else {
                $error = 'Failed to update prospect';
            }
        } else {
            if($prospect->create()) {
                $success = 'Prospect created successfully';
                // Reset form
                $prospect = new Prospect();
            } else {
                $error = 'Failed to create prospect';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editing ? 'Edit' : 'Add'; ?> Prospect - Mini CRM</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'views/header.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1><?php echo $editing ? 'Edit' : 'Add'; ?> Prospect</h1>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="form">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($prospect->name ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company" value="<?php echo htmlspecialchars($prospect->company ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($prospect->phone ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($prospect->email ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="new" <?php echo (isset($prospect->status) && $prospect->status === 'new') ? 'selected' : ''; ?>>New</option>
                        <option value="contacted" <?php echo (isset($prospect->status) && $prospect->status === 'contacted') ? 'selected' : ''; ?>>Contacted</option>
                        <option value="in negotiation" <?php echo (isset($prospect->status) && $prospect->status === 'in negotiation') ? 'selected' : ''; ?>>In Negotiation</option>
                        <option value="won" <?php echo (isset($prospect->status) && $prospect->status === 'won') ? 'selected' : ''; ?>>Won</option>
                        <option value="lost" <?php echo (isset($prospect->status) && $prospect->status === 'lost') ? 'selected' : ''; ?>>Lost</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><?php echo $editing ? 'Update' : 'Create'; ?> Prospect</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
