<?php
require_once '../classes/Session.php';
require_once '../classes/Note.php';

$session = new Session();

// Check if user is logged in
if (!$session->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Handle AJAX requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

        // Add note
        if ($action === 'add_note') {
            $prospect_id = filter_input(INPUT_POST, 'prospect_id', FILTER_VALIDATE_INT) ?? 0;
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);

            if (empty($content)) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Note content is required']);
                exit;
            }

            $note = new Note();
            $note->prospect_id = $prospect_id;
            $note->user_id = $session->get('user_id');
            $note->content = $content;

            if ($note->create()) {
                // Get all notes for this prospect
                $note->prospect_id = $prospect_id;
                $result = $note->read_by_prospect();
                $notes = [];
                while ($row = $result->fetch()) {
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
        if ($action === 'delete_note') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?? 0;

            if ($id <= 0) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Invalid note ID']);
                exit;
            }

            $note = new Note();
            $note->id = $id;

            if ($note->delete()) {
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
    throw new Exception('Invalid request');
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>