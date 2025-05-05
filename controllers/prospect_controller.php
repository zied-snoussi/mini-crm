<?php
require_once '../classes/Session.php';
require_once '../classes/Prospect.php';

$session = new Session();

// Check if user is logged in
if (!$session->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Handle AJAX requests
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get prospects with filter
        $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

        if ($action === 'get_prospects') {
            $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING) ?? null;
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $prospect = new Prospect();
            $result = $prospect->read($status, $limit, $offset);
            $total_prospects = $prospect->count($status);
            $total_pages = ceil($total_prospects / $limit);

            $prospects = [];
            while ($row = $result->fetch()) {
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
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle POST actions
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

        // Delete prospect
        if ($action === 'delete_prospect') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?? 0;

            if ($id <= 0) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Invalid prospect ID']);
                exit;
            }

            $prospect = new Prospect();
            $prospect->id = $id;

            if ($prospect->delete()) {
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
    throw new Exception('Invalid request');
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>