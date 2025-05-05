<?php
require_once '../classes/Session.php';
require_once '../classes/Prospect.php';

$session = new Session();

// Check if user is logged in
if(!$session->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Handle AJAX requests
if($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get prospects with filter
    if(isset($_GET['action']) && $_GET['action'] === 'get_prospects') {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $prospect = new Prospect();
        $result = $prospect->read($status, $limit, $offset);
        $total_prospects = $prospect->count($status);
        $total_pages = ceil($total_prospects / $limit);
        
        $prospects = [];
        while($row = $result->fetch()) {
            $prospects[] = $row;
        }
        
        // Include the prospects table HTML
        ob_start();
        include '../views/prospects_table.php';
        $html = ob_get_clean();
        
        header('Content-Type: application/json');
        echo json_encode([
            'html' => $html,
            'total_pages' => $total_pages,
            'current_page' => $page
        ]);
        exit;
    }
} elseif($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete prospect
    if(isset($_POST['action']) && $_POST['action'] === 'delete_prospect') {
        $id = $_POST['id'] ?? 0;
        
        $prospect = new Prospect();
        $prospect->id = $id;
        
        if($prospect->delete()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to delete prospect']);
        }
        exit;
    }
}

// If we get here, it's an invalid request
header('Content-Type: application/json');
echo json_encode(['error' => 'Invalid request']);
exit;
?>
