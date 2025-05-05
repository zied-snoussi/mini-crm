<?php
require_once '../classes/Session.php';
require_once '../classes/Note.php';

$session = new Session();

// Check if user is logged in
if(!$session->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Handle AJAX requests
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add note
    if(isset($_POST['action']) && $_POST['action'] === 'add_note') {
        $prospect_id = $_POST['prospect_id'] ?? 0;
        $content = $_POST['content'] ?? '';
        
        if(empty($content)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Note content is required']);
            exit;
        }
        
        $note = new Note();
        $note->prospect_id = $prospect_id;
        $note->user_id = $session->get('user_id');
        $note->content = $content;
        
        if($note->create()) {
            // Get the newly created note with username
            $note->prospect_id = $prospect_id;
            $result = $note->read_by_prospect();
            $notes = [];
            while($row = $result->fetch()) {
                $notes[] = $row;
            }
            
            // Include the notes HTML
            ob_start();
            include '../views/notes_list.php';
            $html = ob_get_clean();
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'html' => $html]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to add note']);
        }
        exit;
    }
    
    // Delete note
    if(isset($_POST['action']) && $_POST['action'] === 'delete_note') {
        $id = $_POST['id'] ?? 0;
        
        $note = new Note();
        $note->id = $id;
        
        if($note->delete()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to delete note']);
        }
        exit;
    }
}

// If we get here, it's an invalid request
header('Content-Type: application/json');
echo json_encode(['error' => 'Invalid request']);
exit;
?>
