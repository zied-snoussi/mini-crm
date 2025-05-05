<?php
require_once '../classes/Session.php';
require_once '../classes/Document.php';

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

        // Upload document
        if ($action === 'upload_document') {
            $prospect_id = filter_input(INPUT_POST, 'prospect_id', FILTER_VALIDATE_INT) ?? 0;

            // Check if file was uploaded
            if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('No file uploaded or upload error');
            }

            // Validate file type
            $allowed_types = [
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png'
            ];
            $file_type = mime_content_type($_FILES['document']['tmp_name']);
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception('Invalid file type. Only PDF, DOCX, JPG, and PNG are allowed');
            }

            // Generate unique filename
            $original_filename = basename($_FILES['document']['name']);
            $extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
            $allowed_extensions = ['pdf', 'docx', 'jpg', 'jpeg', 'png'];
            if (!in_array($extension, $allowed_extensions)) {
                throw new Exception('Invalid file extension');
            }

            $filename = uniqid() . '.' . $extension;
            $upload_path = '../uploads/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($_FILES['document']['tmp_name'], $upload_path)) {
                throw new Exception('Failed to upload document');
            }

            // Save document in database
            $document = new Document();
            $document->prospect_id = $prospect_id;
            $document->user_id = $session->get('user_id');
            $document->filename = $filename;
            $document->original_filename = $original_filename;
            $document->file_type = $file_type;

            if ($document->create()) {
                // Get all documents for this prospect
                $document->prospect_id = $prospect_id;
                $result = $document->read_by_prospect();
                $documents = [];
                while ($row = $result->fetch()) {
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
                throw new Exception('Failed to save document information');
            }
            exit;
        }

        // Delete document
        if ($action === 'delete_document') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?? 0;

            if ($id <= 0) {
                throw new Exception('Invalid document ID');
            }

            $document = new Document();
            $document->id = $id;

            if ($document->delete()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Failed to delete document');
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