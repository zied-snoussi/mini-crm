<?php
require_once '../classes/Session.php';
require_once '../classes/Document.php';

$session = new Session();

// Check if user is logged in
if(!$session->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Handle AJAX requests
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload document
    if(isset($_POST['action']) && $_POST['action'] === 'upload_document') {
        $prospect_id = $_POST['prospect_id'] ?? 0;
        
        // Check if file was uploaded
        if(!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No file uploaded or upload error']);
            exit;
        }
        
        // Check file type
        $allowed_types = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
        $file_type = $_FILES['document']['type'];
        
        if(!in_array($file_type, $allowed_types)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid file type. Only PDF, DOCX, JPG, and PNG are allowed']);
            exit;
        }
        
        // Generate unique filename
        $original_filename = $_FILES['document']['name'];
        $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $upload_path = '../uploads/' . $filename;
        
        // Move uploaded file
        if(move_uploaded_file($_FILES['document']['tmp_name'], $upload_path)) {
            // Save document in database
            $document = new Document();
            $document->prospect_id = $prospect_id;
            $document->user_id = $session->get('user_id');
            $document->filename = $filename;
            $document->original_filename = $original_filename;
            $document->file_type = $file_type;
            
            if($document->create()) {
                // Get all documents for this prospect
                $document->prospect_id = $prospect_id;
                $result = $document->read_by_prospect();
                $documents = [];
                while($row = $result->fetch()) {
                    $documents[] = $row;
                }
                
                // Include the documents HTML
                ob_start();
                include '../views/documents_list.php';
                $html = ob_get_clean();
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'html' => $html]);
            } else {
                // Delete the uploaded file if database insert fails
                unlink($upload_path);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Failed to save document information']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to upload document']);
        }
        exit;
    }
    
    // Delete document
    if(isset($_POST['action']) && $_POST['action'] === 'delete_document') {
        $id = $_POST['id'] ?? 0;
        
        $document = new Document();
        $document->id = $id;
        
        if($document->delete()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to delete document']);
        }
        exit;
    }
}

// If we get here, it's an invalid request
header('Content-Type: application/json');
echo json_encode(['error' => 'Invalid request']);
exit;
?>
