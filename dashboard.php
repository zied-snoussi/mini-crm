<?php
require_once 'classes/Session.php';
require_once 'classes/Prospect.php';

$session = new Session();
$session->requireLogin();

$prospect = new Prospect();

// Default values for pagination
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
$page = max(1, $page); // Ensure page is at least 1
$limit = 10;
$offset = ($page - 1) * $limit;

// Get status filter if provided
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING) ?? null;

// Get prospects
$result = $prospect->read($status, $limit, $offset);
$total_prospects = $prospect->count($status);
$total_pages = ceil($total_prospects / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mini CRM</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <?php include 'views/header.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1>Prospects</h1>
                <a href="prospect_form.php" class="btn btn-primary">Add New Prospect</a>
            </div>
            
            <div class="filters">
                <div class="status-filters">
                    <button class="btn btn-filter <?php echo $status === null ? 'active' : ''; ?>" data-status="">All</button>
                    <button class="btn btn-filter <?php echo $status === 'new' ? 'active' : ''; ?>" data-status="new">New</button>
                    <button class="btn btn-filter <?php echo $status === 'contacted' ? 'active' : ''; ?>" data-status="contacted">Contacted</button>
                    <button class="btn btn-filter <?php echo $status === 'in negotiation' ? 'active' : ''; ?>" data-status="in negotiation">In Negotiation</button>
                    <button class="btn btn-filter <?php echo $status === 'won' ? 'active' : ''; ?>" data-status="won">Won</button>
                    <button class="btn btn-filter <?php echo $status === 'lost' ? 'active' : ''; ?>" data-status="lost">Lost</button>
                </div>
            </div>
            
            <div id="prospects-container">
                <?php include 'views/prospects_table.php'; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $status ? '&status=' . htmlspecialchars($status) : ''; ?>" 
                       class="<?php echo $page === $i ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="assets/js/dashboard.js"></script>
</body>
</html>