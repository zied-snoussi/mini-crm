<?php
require_once 'classes/Session.php';
require_once 'classes/Prospect.php';
require_once 'classes/Note.php';
require_once 'classes/Document.php';

$session = new Session();
$session->requireLogin();

// Check if prospect ID is provided
if(!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$prospect = new Prospect();
$prospect->id = $_GET['id'];

// Get prospect details
if(!$prospect->read_single()) {
    header('Location: dashboard.php');
    exit;
}

// Get notes for this prospect
$note = new Note();
$note->prospect_id = $prospect->id;
$notes = $note->read_by_prospect();

// Get documents for this prospect
$document = new Document();
$document->prospect_id = $prospect->id;
$documents = $document->read_by_prospect();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prospect Details - Mini CRM</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <?php include 'views/header.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1>Prospect Details</h1>
                <div>
                    <a href="prospect_form.php?id=<?php echo $prospect->id; ?>" class="btn btn-secondary">Edit</a>
                    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>
            
            <div class="prospect-details">
                <div class="prospect-info">
                    <h2><?php echo htmlspecialchars($prospect->name); ?></h2>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($prospect->company); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($prospect->email); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($prospect->phone); ?></p>
                    <p><strong>Status:</strong> <span class="status-badge status-<?php echo $prospect->status; ?>"><?php echo ucfirst($prospect->status); ?></span></p>
                    <p><strong>Created:</strong> <?php echo date('F j, Y', strtotime($prospect->created_at)); ?></p>
                </div>
            </div>
            
            <div class="tabs">
                <div class="tab-header">
                    <button class="tab-btn active" data-tab="notes">Notes</button>
                    <button class="tab-btn" data-tab="documents">Documents</button>
                </div>
                
                <div class="tab-content">
                    <div class="tab-pane active" id="notes">
                        <div class="section-header">
                            <h3>Notes</h3>
                        </div>
                        
                        <form id="add-note-form" class="form">
                            <input type="hidden" name="prospect_id" value="<?php echo $prospect->id; ?>">
                            <div class="form-group">
                                <textarea name="content" placeholder="Add a note..." required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Add Note</button>
                            </div>
                        </form>
                        
                        <div id="notes-container">
                            <?php if($notes->rowCount() > 0): ?>
                                <?php while($row = $notes->fetch()): ?>
                                    <div class="note">
                                        <div class="note-header">
                                            <span class="note-author"><?php echo htmlspecialchars($row['username']); ?></span>
                                            <span class="note-date"><?php echo date('M j, Y g:i A', strtotime($row['created_at'])); ?></span>
                                        </div>
                                        <div class="note-content"><?php echo nl2br(htmlspecialchars($row['content'])); ?></div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="no-data">No notes yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="documents">
                        <div class="section-header">
                            <h3>Documents</h3>
                        </div>
                        
                        <form id="upload-document-form" class="form" enctype="multipart/form-data">
                            <input type="hidden" name="prospect_id" value="<?php echo $prospect->id; ?>">
                            <div class="form-group">
                                <label for="document">Upload Document (PDF, DOCX, JPG, PNG)</label>
                                <input type="file" id="document" name="document" accept=".pdf,.docx,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </div>
                        </form>
                        
                        <div id="documents-container">
                            <?php if($documents->rowCount() > 0): ?>
                                <div class="documents-list">
                                    <?php while($row = $documents->fetch()): ?>
                                        <div class="document">
                                            <div class="document-info">
                                                <span class="document-name"><?php echo htmlspecialchars($row['original_filename']); ?></span>
                                                <span class="document-date"><?php echo date('M j, Y', strtotime($row['created_at'])); ?></span>
                                            </div>
                                            <div class="document-actions">
                                                <a href="uploads/<?php echo $row['filename']; ?>" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                                <button class="btn btn-sm btn-danger delete-document" data-id="<?php echo $row['id']; ?>">Delete</button>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-data">No documents yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/prospect_detail.js"></script>
</body>
</html>
