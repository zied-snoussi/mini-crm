<?php if (count($documents) > 0): ?>
    <div class="documents-list">
        <?php foreach ($documents as $doc): ?>
            <div class="document">
                <div class="document-info">
                    <!-- Display the original filename and creation date -->
                    <span class="document-name"><?php echo htmlspecialchars($doc['original_filename']); ?></span>
                    <span class="document-date"><?php echo date('M j, Y', strtotime($doc['created_at'])); ?></span>
                </div>
                <div class="document-actions">
                    <!-- View document link -->
                    <a href="uploads/<?php echo htmlspecialchars(basename($doc['filename'])); ?>" 
                       target="_blank" 
                       class="btn btn-sm btn-secondary">View</a>
                    
                    <!-- Delete document button -->
                    <button class="btn btn-sm btn-danger delete-document" 
                            data-id="<?php echo htmlspecialchars($doc['id']); ?>" 
                            onclick="confirmDelete(this)">Delete</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <!-- No documents message -->
    <p class="no-data">No documents have been uploaded yet. Start by adding one!</p>
<?php endif; ?>

<script>
    /**
     * Confirm before deleting a document.
     * @param {HTMLElement} button - The delete button element.
     */
    function confirmDelete(button) {
        const documentId = button.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this document?')) {
            // Trigger the delete action (this should be handled via AJAX or form submission)
            deleteDocument(documentId);
        }
    }

    /**
     * Handle document deletion (example implementation).
     * Replace this with your actual AJAX or form submission logic.
     * @param {string} documentId - The ID of the document to delete.
     */
    function deleteDocument(documentId) {
        console.log(`Document with ID ${documentId} will be deleted.`);
        // Add your AJAX or form submission logic here.
    }
</script>